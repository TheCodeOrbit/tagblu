<?php

namespace backend\controllers;

use app\models\Attachments;
use app\models\Detaileditsetting;
use Yii;
use yii\web\Controller;
use app\models\Tab;
use app\models\Field;
use app\models\SiteSetting;
use backend\models\AccessCheck;
use backend\models\ModuleLog;
use yii\bootstrap5\Html;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ModulesettingController extends Controller
{
    public function actionIndex()
    {
        $modulename = $this->getmodulenames();

        $tabs = Tab::find()
            ->alias('t')
            ->where(['t.presence' => 0])
            ->orderBy(['t.tabsequence' => SORT_ASC])
            ->all();

        $parentRows = (new \yii\db\Query())
            ->from('parenttab')
            ->orderBy(['sequence' => SORT_ASC])
            ->all();

        $parentMap  = [];
        $parentMeta = [];
        foreach ($parentRows as $row) {
            $parentMap[$row['parenttabid']] = $row['parenttab_label'];
            $parentMeta[$row['parenttabid']] = [
                'sequence' => (int)$row['sequence'],
                'icon'     => $row['icon'],
            ];
        }

        $tabTree = [];

        foreach ($parentRows as $row) {
            $pid = (int)$row['parenttabid'];
            $tabTree[$pid] = [
                'parent_id'    => $pid,
                'parent_label' => $row['parenttab_label'],
                'sequence'     => (int)$row['sequence'],
                'visible'      => (int) $row['visible'],
                'icon'         => $row['icon'],
                'children'     => [],
            ];
        }

        if (!isset($tabTree['_no_parent'])) {
            $tabTree['_no_parent'] = [
                'parent_id'    => null,
                'parent_label' => 'No Parent',
                'sequence'     => 0,
                'icon'         => null,
                'children'     => [],
            ];
        }

        foreach ($tabs as $tab) {
            $parentId  = $tab->parent;
            $parentKey = $parentId ?: '_no_parent';

            if (!isset($tabTree[$parentKey])) {
                $tabTree[$parentKey] = [
                    'parent_id'    => $parentId,
                    'parent_label' => $parentId && isset($parentMap[$parentId])
                        ? $parentMap[$parentId]
                        : 'No Parent',
                    'sequence'     => $parentId && isset($parentMeta[$parentId])
                        ? (int)$parentMeta[$parentId]['sequence']
                        : 0,
                    'icon'         => $parentId && isset($parentMeta[$parentId])
                        ? $parentMeta[$parentId]['icon']
                        : null,
                    'children'     => [],
                ];
            }

            $tabTree[$parentKey]['children'][] = $tab;
        }

        foreach ($tabTree as $key => $group) {
            usort($tabTree[$key]['children'], function ($a, $b) {
                $sa = (int)$a->tabsequence;
                $sb = (int)$b->tabsequence;
                if ($sa === $sb) {
                    return $a->tabid <=> $b->tabid;
                }
                return $sa <=> $sb;
            });
        }

        uksort($tabTree, function ($a, $b) use ($tabTree) {
            if ($a === '_no_parent' && $b !== '_no_parent') return 1;
            if ($b === '_no_parent' && $a !== '_no_parent') return -1;

            $sa = (int)($tabTree[$a]['sequence'] ?? 0);
            $sb = (int)($tabTree[$b]['sequence'] ?? 0);
            if ($sa === $sb) {
                return strcmp((string)$a, (string)$b);
            }
            return $sa <=> $sb;
        });
        $activeLogo = SiteSetting::find()
            ->where(['active' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $logoPath = $activeLogo
            ? $activeLogo->logo_path     // e.g. /thememain/img/login/logo_xxx.png
            : '/thememain/img/login/logo.png';
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'tabs'        => $tabs,
            'tabTree'     => $tabTree,
            'parentMap'   => $parentMap,
            'modulenames' => $modulename,
            'logoPath' => $logoPath
        ]);
    }

    public function actionDownload($type, $filename)
    {
        $this->layout = false;

        $filename = basename($filename);

        switch ($type) {
            case 'parenttab':
                $basePath = Yii::getAlias('@backend/web/thememain/img');
                break;
            case 'tab':
                $basePath = Yii::getAlias('@backend/web/thememain/img/module-icon');
                break;
            case 'block':
                $basePath = Yii::getAlias('@backend/web/thememain/img/module-icon');
                break;
            case 'field':
                $basePath = Yii::getAlias('@backend/web/thememain/img/module-icon');
                break;
            case 'attachment':
                $basePath = Yii::getAlias('@backend/web');
                $attachment = (new \yii\db\Query())
                    ->from('attachments')
                    ->where(['attachmentsid' => (int)$filename])
                    ->one();
                if (!$attachment || empty($attachment['path'])) {
                    throw new \yii\web\NotFoundHttpException('File not found');
                }
                $filename = $attachment['path'];
                break;
            default:
                throw new \yii\web\BadRequestHttpException('Invalid type');
        }

        $fullPath = $basePath . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($fullPath)) {
            throw new \yii\web\NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile($fullPath, $filename);
    }


    protected function getmodulenames()
    {
        $id = Yii::$app->user->id;
        $allowedModules = [];

        $model = new AccessCheck();
        $Modules = Tab::find()
            ->where(['visible' => 0, 'presence' => 0])
            ->all();

        $dropdowns = Field::find()
            ->alias('f')
            ->select(['f.tabid', 't.tablabel'])
            ->distinct()
            ->innerJoin(Tab::tableName() . ' t', 't.tabid = f.tabid')
            ->where(['f.uitype' => 8])
            ->asArray()
            ->all();

        foreach ($Modules as $Module) {
            $allowedModules[$Module->tabid] = $Module->tablabel;
        }

        if (!empty($allowedModules)) {
            return $allowedModules;
        }

        return 'No module Found';
    }


    public function actionFields($tabid = 1)
    {
        $tab = Tab::findOne($tabid);

        $blocks = (new \yii\db\Query())
            ->from('blocks')
            ->where(['tabid' => $tabid])
            ->orderBy(['sequence' => SORT_ASC])
            ->all();

        $fields = Field::find()
            ->where(['tabid' => $tabid])
            ->orderBy(['block' => SORT_ASC, 'sequence' => SORT_ASC])
            ->all();

        $blocksWithFields = [];
        foreach ($blocks as $b) {
            $blocksWithFields[$b['blockid']] = [
                'info'   => $b,
                'fields' => [],
            ];
        }

        $unassignedFields = [];
        foreach ($fields as $f) {
            $bid = (int)$f->block;
            if (isset($blocksWithFields[$bid])) {
                $blocksWithFields[$bid]['fields'][] = $f;
            } else {
                $unassignedFields[] = $f;
            }
        }

        $blockList = [];
        foreach ($blocks as $b) {
            $blockList[$b['blockid']] = $b['blocklabel'];
        }

        $uiTypes = (new \yii\db\Query())
            ->from('fieldtype')
            ->orderBy(['uitype' => SORT_ASC])
            ->all();

        $dataTypes = (new \yii\db\Query())
            ->from('datatype')
            ->orderBy(['code' => SORT_ASC])
            ->all();

        $dynamicClass = (new \yii\db\Query())
            ->from('dynamic_class')
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $tabs = (new \yii\db\Query())
            ->from('tab')
            ->select(['tabid', 'tablabel', 'name'])
            ->orderBy(['tabsequence' => SORT_ASC])
            ->all();

        $tabList = [];
        foreach ($tabs as $t) {
            $label = $t['tablabel'] ?: $t['name'];
            $tabList[$t['tabid']] = $label;
        }

        $this->layout = '@app/views/layouts/main-one';
        return $this->render('field', [
            'tab'             => $tab,
            'blocksWithFields' => $blocksWithFields,
            'blockList'       => $blockList,
            'uiTypes'         => $uiTypes,
            'dataTypes'       => $dataTypes,
            'dynamicClass'    => $dynamicClass,
            'tabList'         => $tabList,
            'unassignedFields' => $unassignedFields,
        ]);
    }



    public function actionEditBlockSequence()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $updates = Yii::$app->request->post('updates', []);
        $success = true;

        foreach ($updates as $item) {
            $id  = $item['id'] ?? null;
            $seq = $item['sequence'] ?? null;
            if (!$id || $seq === null || !is_numeric($seq)) {
                $success = false;
                break;
            }

            Yii::$app->db->createCommand()
                ->update('blocks', ['sequence' => (int)$seq], ['blockid' => (int)$id])
                ->execute();
        }

        return ['success' => $success];
    }


    public function actionToggleVisible()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $visible = Yii::$app->request->post('visible');
        $visible = !$visible ? 0 : 1;
        $tab = Tab::findOne($id);
        if ($tab) {
            $tab->visible = $visible;
            if ($tab->save(false)) {
                return ['success' => true];
            }
        }
        return ['success' => false];
    }


    public function actionAddField()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $req   = Yii::$app->request;
        $tabid = (int)$req->post('tabid');
        $name  = $req->post('fieldname');

        if (!$tabid || !$name) {
            return ['success' => false, 'message' => 'Tab and field name are required'];
        }

        $field = new Field();
        $field->tabid      = $tabid;
        $field->fieldname  = $name;
        $field->fieldlabel = $req->post('fieldlabel', $name);
        $field->tablename  = $req->post('tablename', '');
        $field->block      = (int)$req->post('block', 0);
        $field->uitype     = $req->post('uitype', '');

        $mandatory = (int)$req->post('mandatory');
        $field->mandatory = $mandatory;

        $td = $req->post('typeofdata', '');

        $field->typeofdata = $td;

        $field->masseditable = (int)$req->post('masseditable', 1);
        $field->summaryfield = (int)$req->post('summaryfield', 0);
        $field->list_view    = (int)$req->post('list_view', 1);
        $field->export       = (int)$req->post('export', 1);
        $field->import       = (int)$req->post('import', 1);
        $field->admin_edit_allow       = (int)$req->post('admin_edit_allow', 1);

        $field->dynamic_class = $req->post('dynamic_class', $field->dynamic_class);
        if ($field->save(false)) {
            $this->logModuleAction('field', 'add', $field->fieldid, $tabid, $field->block, $field->fieldid);
            return ['success' => true, 'fieldid' => $field->fieldid];
        }
        return ['success' => false];
    }


    public function actionEditField()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $fieldid = Yii::$app->request->post('fieldid');
        $field   = Field::findOne($fieldid);
        if (!$field) {
            return ['success' => false];
        }
        $oldData = $field->getAttributes();
        $req = Yii::$app->request;

        $field->fieldname  = $req->post('fieldname',  $field->fieldname);
        $field->fieldlabel = $req->post('fieldlabel', $field->fieldlabel);
        $field->tablename  = $req->post('tablename',  $field->tablename);
        $field->block      = (int)$req->post('block', $field->block);
        $field->uitype     = $req->post('uitype',     $field->uitype);
        $field->displaytype     = $req->post('displaytype',     $field->displaytype);
        $field->dynamic_class     = $req->post('dynamic_class',     $field->dynamic_class);
        $field->maximumlength     = $req->post('maximumlength',     $field->maximumlength);
        $field->defaultvalue     = $req->post('defaultvalue',     $field->defaultvalue);
        $field->description     = $req->post('description',     $field->description);
        $field->columnname     = $req->post('columnname',     $field->columnname);
        $field->typeofdata = $req->post('typeofdata', $field->typeofdata);

        $field->summaryfield = (int)$req->post('summaryfield') ? 1 : 0;
        $field->readonly    = (int)!$req->post('readonly') ? 0 : 1;
        $field->isunique    = (int)$req->post('isunique') ? 1 : 0;
        $field->mandatory    = (int)$req->post('mandatory') ? 1 : 0;
        $field->headerview    = (int)$req->post('headerview') ? 1 : 0;
        $field->is_conditional    = (int)$req->post('is_conditional') ? 1 : 0;
        $field->kanbanview   = (int)$req->post('kanbanview') ? 1 : 0;
        $field->kanbanviewfield    = (int)$req->post('kanbanviewfield') ? 1 : 0;
        $field->single_edit    = (int)!$req->post('single_edit') ? 0 : 1;
        $field->list_view    = (int)$req->post('list_view') ? 1 : 0;
        $field->edit_view    = (int)$req->post('edit_view') ? 1 : 0;
        $field->create_view    = (int)$req->post('create_view') ? 1 : 0;
        $field->detail_view    = (int)$req->post('detail_view') ? 1 : 0;
        $field->export    = (int)$req->post('export') ? 1 : 0;
        $field->import    = (int)$req->post('import') ? 1 : 0;
        $field->admin_edit_allow    = (int)$req->post('admin_edit_allow') ? 1 : 0;

        if ($field->save(false)) {
            $newData = $field->getAttributes();
            $this->logModuleAction('field', 'update', $fieldid, $field->tabid, $field->block, $fieldid, null, $oldData, $newData);
            return ['success' => true];
        }
        return ['success' => false];
    }


    public function actionEditFieldSequence()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $updates = Yii::$app->request->post('updates', []);
        $success = true;

        foreach ($updates as $item) {
            $id  = $item['id'] ?? null;
            $seq = $item['sequence'] ?? null;

            if (!$id || $seq === null) {
                $success = false;
                break;
            }

            $field = Field::findOne($id);
            if (!$field) {
                $success = false;
                break;
            }

            if (is_numeric($seq)) {
                $field->sequence = (int)$seq;
            }

            if ($field->save(false) === false) {
                $success = false;
                break;
            }
        }

        return ['success' => $success];
    }

    public function actionUpdateParentSequence()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $updates = Yii::$app->request->post('updates', []);
        $success = true;
        $sequenceChanges = [];

        foreach ($updates as $item) {
            $id = $item['id'] ?? null;
            $seq = $item['sequence'] ?? null;
            if (!$id || $seq === null) {
                $success = false;
                break;
            }

            $oldData = (new \yii\db\Query())->from('parenttab')->where(['parenttabid' => $id])->one();
            if ($oldData) {
                $sequenceChanges[$id] = [
                    'old_sequence' => (int)$oldData['sequence'],
                    'new_sequence' => (int)$seq
                ];
            }

            Yii::$app->db->createCommand()
                ->update('parenttab', ['sequence' => (int)$seq], ['parenttabid' => $id])
                ->execute();
        }

        if ($success) {
            $this->logModuleAction('sequence', 'sequence', 0, null, null, null, 0, null, null, $sequenceChanges);
        }

        return ['success' => $success];
    }

    public function actionEditTab()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $tabid = Yii::$app->request->post('tabid');
        $tab   = Tab::findOne($tabid);
        if (!$tab) {
            return ['success' => false, 'message' => 'Invalid tabid'];
        }

        $request = Yii::$app->request;
        $oldData = $tab->getAttributes();
        $tab->tablabel   = $request->post('tablabel',  $tab->tablabel);
        $tab->name       = $request->post('name',      $tab->name);
        $tab->parent     = $request->post('parent',    $tab->parent);
        $tab->submenu    = $request->post('submenu',   $tab->submenu);
        $tab->tablename  = $request->post('tablename', $tab->tablename);
        $tab->tablekeyid = $request->post('tablekeyid', $tab->tablekeyid);

        $tabSeq = $request->post('tabsequence', null);
        if ($tabSeq !== null && $tabSeq !== '' && is_numeric($tabSeq)) {
            $tab->tabsequence = (int)$tabSeq;
        }

        $tab->default_view = $request->post('default_view', $tab->default_view);
        $tab->source       = $request->post('source',       $tab->source);
        $tab->module_type  = $request->post('module_type', $tab->module_type);

        $tab->issyncable   = !$request->post('issyncable') ? 0 : 1;
        $tab->allowduplicates = !$request->post('allowduplicates') ? 0 : 1;
        $tab->sync_action_for_duplicates = !$request->post('sync_action_for_duplicates') ? 0 : 1;
        $tab->customized   = !$request->post('customized') ? 0 : 1;
        $tab->import_allowed = !$request->post('import_allowed') ? 0 : 1;
        $tab->export_allowed = !$request->post('export_allowed') ? 0 : 1;
        $tab->search_allowed = !$request->post('search_allowed') ? 0 : 1;
        $tab->visible = !$request->post('visible') ? 0 : 1;
        $tab->presence = !$request->post('presence') ? 0 : 1;

        $tab->modifiedby   = Yii::$app->user->id ?: $tab->modifiedby;
        $tab->modifiedtime = date('Y-m-d H:i:s');
        $iconFile = UploadedFile::getInstanceByName('icon_file');
        if ($iconFile) {
            if (!empty($tab->name)) {
                $iconFile->name     = $tab->name . '.' . $iconFile->extension;
            }

            $saved = $this->saveIconFile($iconFile, true);
            if ($saved) {
                $tab->icon_name = $saved;
            }
        } else {
            $iconNamePost = $request->post('icon_name', null);
            if ($iconNamePost !== null) {
                $tab->icon_name = $iconNamePost;
            }
        }
        if ($tab->save(false)) {
            $newData = $tab->getAttributes();
            $this->logModuleAction('tab','update',$tabid,$tabid,null,null,null,$oldData,$newData);
            return ['success' => true];
        }
        return ['success' => false];
    }


    public function actionEditParenttab()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('parenttabid');
        if (!$id) {
            return ['success' => false, 'message' => 'Invalid parenttabid'];
        }

        $row = (new \yii\db\Query())
            ->from('parenttab')
            ->where(['parenttabid' => $id])
            ->one();

        if (!$row) {
            return ['success' => false, 'message' => 'Parent tab not found'];
        }
        $oldData = $row;
        $request = Yii::$app->request;
        $label   = $request->post('parenttab_label', $row['parenttab_label']);
        $seq     = $request->post('sequence', $row['sequence']);
        $visible = !$request->post('visible') ? 0 : 1;

        $iconFile = UploadedFile::getInstanceByName('icon_file');
        $iconName = $row['icon'];
        if ($iconFile) {
            if (!empty($label)) {
                $iconFile->name     = $label . '.' . $iconFile->extension;
            }

            $saved = $this->saveIconFile($iconFile);
            if ($saved) {
                $iconName = $saved;
            }
        }
        Yii::$app->db->createCommand()->update('parenttab', [
            'parenttab_label' => $label,
            'icon'            => $iconName,
            'sequence'        => $seq,
            'visible'         => $visible,
        ], ['parenttabid' => $id])->execute();
        $newData = (new \yii\db\Query())
        ->from('parenttab')
        ->where(['parenttabid' => $id])
        ->one();
        $this->logModuleAction('parenttab', 'update', $id, null, null, null, $id, $oldData,$newData);
        return ['success' => true];
    }


    protected function saveIconFile(UploadedFile $file = null, $isTabIcon = false)
    {
        if (!$file) {
            return null;
        }
        $basePath = Yii::getAlias('@backend/web/thememain/img');
        if ($isTabIcon) {
            $basePath = Yii::getAlias('@backend/web/thememain/img/module-icon');
        }
        if (!is_dir($basePath)) {
            @mkdir($basePath, 0775, true);
        }

        $safeName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->baseName);
        $fileName = $safeName . '.' . $file->extension;
        $fullPath = $basePath . DIRECTORY_SEPARATOR . $fileName;

        if ($file->saveAs($fullPath)) {
            return $fileName;
        }

        return null;
    }

    public function actionAddParenttab()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $label   = $request->post('parenttablabel', '');
        $seq     = $request->post('sequence', null);
        $visible = $request->post('visible', 0);

        if (trim($label) === '') {
            return ['success' => false, 'message' => 'Parenttab label is required'];
        }

        $sequence = 0;
        if ($seq !== null && $seq !== '' && is_numeric($seq)) {
            $sequence = (int) $seq;
        } else {
            $maxSeq = (new \yii\db\Query())
                ->from('parenttab')
                ->max('sequence');
            $sequence = (int)$maxSeq + 1;
        }

        $iconFile = UploadedFile::getInstanceByName('icon_file');
        $iconName = '';
        if ($iconFile) {
            if (!empty($label)) {
                $iconFile->name     = $label . '.' . $iconFile->extension;
            }

            $saved = $this->saveIconFile($iconFile);
            if ($saved) {
                $iconName = $saved;
            }
        }

        Yii::$app->db->createCommand()->insert('parenttab', [
            'parenttab_label' => $label,
            'icon'            => $iconName,
            'sequence'        => $sequence,
            'visible'         => !$visible ? 0 : 1,
        ])->execute();

        $id = Yii::$app->db->getLastInsertID();
        $this->logModuleAction('parenttab', 'add', $id, null, null, null, $id);
        return ['success' => true, 'id' => $id];
    }



    public function actionAddTab()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $name    = $request->post('name');
        if (!$name) {
            return ['success' => false, 'message' => 'Tab name is required'];
        }

        $tab = new Tab();
        $tab->name       = $name;
        $tab->tablabel   = $request->post('tablabel', $name);
        $tab->parent     = $request->post('parent', null);
        $tab->tablename  = $request->post('tablename', '');
        $tab->tablekeyid = $request->post('tablekeyid', '');
        $tab->visible    = !$request->post('visible') ? 0 : 1;
        $tab->presence   = !$request->post('presence') ? 0 : 1;
        $tabSeq = $request->post('tabsequence', null);
        if ($tabSeq !== null && $tabSeq !== '' && is_numeric($tabSeq)) {
            $tab->tabsequence = (int)$tabSeq;
        } else {
            $maxSeq = (new \yii\db\Query())
                ->from('tab')
                ->max('tabsequence');
            $tab->tabsequence = (int)$maxSeq + 1;
        }
        $tab->customized   = (int)$request->post('customized', 0);
        $tab->default_view = $request->post('default_view', $tab->default_view);
        $tab->source       = $request->post('source', $tab->source);
        $tab->issyncable   = !$request->post('issyncable') ? 0 : 1;
        $tab->allowduplicates = !$request->post('allowduplicates') ? 0 : 1;
        $tab->sync_action_for_duplicates = !$request->post('sync_action_for_duplicates') ? 0 : 1;
        $tab->module_type  = $request->post('module_type', $tab->module_type);
        $tab->import_allowed = !$request->post('import_allowed') ? 0 : 1;
        $tab->export_allowed = !$request->post('export_allowed') ? 0 : 1;
        $tab->search_allowed = !$request->post('search_allowed') ? 0 : 1;
        $tab->visible = !$request->post('visible') ? 0 : 1;
        $tab->presence = !$request->post('presence') ? 0 : 1;
        // audit fields
        $tab->modifiedby   = Yii::$app->user->id ?: $tab->modifiedby;
        $tab->modifiedtime = date('Y-m-d H:i:s');
        $iconFile = UploadedFile::getInstanceByName('icon_file');
        $iconName = $request->post('icon_name', '');
        if ($iconFile) {
            if (!empty($name)) {
                $iconFile->name     = $name . '.' . $iconFile->extension;
            }

            $saved = $this->saveIconFile($iconFile, true);
            if ($saved) {
                $iconName = $saved;
            }
        }
        $tab->icon_name = $iconName;

        if ($tab->save(false)) {
            $newData = $tab->getAttributes();

        $this->logModuleAction('tab','add',$tab->tabid,$tab->tabid,null,null,null,null,$newData);

        return ['success' => true, 'tabid' => $tab->tabid];
        }
        $this->logModuleAction('tab', 'add', $tab->tabid, $tab->tabid);
        return ['success' => false];
    }


    public function actionSaveBlock()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = \Yii::$app->request;
        $blockId = (int)$request->post('blockid');
        $tabid   = (int)$request->post('tabid');
        $label   = trim($request->post('blocklabel', ''));
        $visible = (int)!$request->post('visible') ? 0 : 1;
        $showTitle = (int)!$request->post('show_title') ? 0 : 1;
        $blocktype = $request->post('blocktype', 'default');
        $createView = (int)!$request->post('create_view') ? 0 : 1;
        $editView   = (int)!$request->post('edit_view') ? 0 : 1;
        $detailView = (int)!$request->post('detail_view') ? 0 : 1;
        $displayStatus = (int)$request->post('display_status') ? 1 : 0;
        $iscustom = (int)!$request->post('iscustom') ? 0 : 1;
        if ($label === '') {
            return ['success' => false, 'message' => 'Block label is required'];
        }

        if ($blockId && $blockId != '' && is_numeric($blockId)) {
            $row = (new \yii\db\Query())
                ->from('blocks')
                ->where(['blockid' => $blockId])
                ->one();

            if (!$row) {
                return ['success' => false, 'message' => 'Block not found'];
            }

            \Yii::$app->db->createCommand()->update('blocks', [
                'blocklabel' => $label,
                'visible'    => !$visible ? 0 : 1,
                'show_title' => !$showTitle ? 0 : 1,
                'create_view' => !$createView ? 0 : 1,
                'edit_view' => !$editView ? 0 : 1,
                'detail_view' => !$detailView ? 0 : 1,
                'display_status' => !$displayStatus ? 0 : 1,
                'iscustom' => !$iscustom ? 0 : 1,
                'blocktype'  => $blocktype,
            ], ['blockid' => $blockId])->execute();

           $newData = (new \yii\db\Query())
            ->from('blocks')
            ->where(['blockid' => $blockId])
            ->one();

            $this->logModuleAction('block','update',$blockId,$tabid,$blockId,null,null,$row,$newData);
            return ['success' => true, 'id' => $blockId];
        }

        if (!$tabid) {
            return ['success' => false, 'message' => 'Tab is required'];
        }

        $maxSeq = (new \yii\db\Query())
            ->from('blocks')
            ->where(['tabid' => $tabid])
            ->max('sequence');
        $sequence = (int)$maxSeq + 1;

        \Yii::$app->db->createCommand()->insert('blocks', [
            'tabid'          => $tabid,
            'blocklabel'     => $label,
            'sequence'       => $sequence,
            'show_title'     => !$showTitle ? 0 : 1,
            'visible'        => !$visible ? 0 : 1,
            'create_view'    => !$createView ? 0 : 1,
            'edit_view'      => !$editView ? 0 : 1,
            'detail_view'    => !$detailView ? 0 : 1,
            'display_status' => !$displayStatus ? 0 : 1,
            'iscustom'       => !$iscustom ? 0 : 1,
            'blocktype'      => $blocktype,
        ])->execute();

        $id = \Yii::$app->db->getLastInsertID();
        $this->logModuleAction('block', 'add', $id, $tabid, $id);
        return ['success' => true, 'id' => $id];
    }

    public function actionUpdateTabSequence()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $itemsJson = $request->post('items', '[]');
        $parentId = (int)$request->post('parentid', 0);
        $items = json_decode($itemsJson, true);

        if (!is_array($items) || !$parentId) {
            return ['success' => false, 'message' => 'Invalid data'];
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $sequenceChanges = [];

        try {
            // Capture old sequences
            foreach ($items as $row) {
                $tabid = (int)($row['tabid'] ?? 0);
                if ($tabid) {
                    $oldTab = Tab::findOne($tabid);
                    if ($oldTab) {
                        $sequenceChanges[$tabid] = [
                            'old_sequence' => (int)$oldTab->tabsequence,
                            'new_sequence' => (int)$row['sequence']
                        ];
                    }
                }
            }

            foreach ($items as $row) {
                $tabid = (int)($row['tabid'] ?? 0);
                $sequence = (int)($row['sequence'] ?? 0);
                if (!$tabid || !$sequence) continue;

                $db->createCommand()
                    ->update('tab', ['tabsequence' => $sequence], [
                        'tabid' => $tabid,
                        'parent' => $parentId,
                    ])->execute();
            }

            $transaction->commit();
            $this->logModuleAction('sequence', 'sequence', $parentId, $parentId, null, null, $parentId, null, null, $sequenceChanges);
            return ['success' => true];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'DB error'];
        }
    }


    public function actionUpdateFieldSequence()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req = Yii::$app->request;

        try {
            $itemsJson = $req->post('items', '[]');
            $blockId = (int)$req->post('blockid', 0);
            $tabId = $req->post('tabid');
            $items = json_decode($itemsJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($items) || !$blockId) {
                return ['success' => false, 'message' => 'Invalid data'];
            }

            $db = Yii::$app->db;
            $tx = $db->beginTransaction();
            $updatedCount = 0;
            $sequenceChanges = [];

            // Capture old sequences first
            foreach ($items as $row) {
                $fieldid = (int)$row['id'];
                if ($fieldid) {
                    $oldField = Field::findOne($fieldid);
                    if ($oldField) {
                        $sequenceChanges[$fieldid] = [
                            'old_sequence' => (int)$oldField->sequence,
                            'new_sequence' => (int)$row['sequence']
                        ];
                    }
                }
            }

            foreach ($items as $row) {
                $fieldid = (int)$row['id'];
                $seq = (int)$row['sequence'];
                if (!$fieldid || !$seq) continue;

                $result = $db->createCommand()
                    ->update('field', ['sequence' => $seq], [
                        'fieldid' => $fieldid,
                        'block' => $blockId,
                    ])->execute();
                if ($result > 0) $updatedCount++;
            }

            $tx->commit();

            // Log with detailed sequence changes
            $this->logModuleAction('sequence', 'sequence', $blockId, $tabId, $blockId, null, null, null, null, $sequenceChanges);

            return [
                'success' => true,
                'message' => "Updated {$updatedCount} fields for block {$blockId}"
            ];
        } catch (\Exception $e) {
            if (isset($tx)) $tx->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function actionUpdateBlockSequence()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req = Yii::$app->request;
        $itemsJson = $req->post('items', '[]');
        $tabId = (int)$req->post('tabid', 0);
        $items = json_decode($itemsJson, true);

        if (!is_array($items) || !$tabId) {
            return ['success' => false, 'message' => 'Invalid data'];
        }

        $db = Yii::$app->db;
        $tx = $db->beginTransaction();
        $sequenceChanges = [];

        try {
            // Capture old sequences
            foreach ($items as $row) {
                $blockId = (int)($row['blockid'] ?? 0);
                if ($blockId) {
                    $oldBlock = (new \yii\db\Query())->from('blocks')->where(['blockid' => $blockId])->one();
                    if ($oldBlock) {
                        $sequenceChanges[$blockId] = [
                            'old_sequence' => (int)$oldBlock['sequence'],
                            'new_sequence' => (int)$row['sequence']
                        ];
                    }
                }
            }

            foreach ($items as $row) {
                $blockId = (int)($row['blockid'] ?? 0);
                $seq = (int)($row['sequence'] ?? 0);
                if (!$blockId || !$seq) continue;

                $db->createCommand()
                    ->update('blocks', ['sequence' => $seq], [
                        'blockid' => $blockId,
                        'tabid' => $tabId,
                    ])->execute();
            }

            $tx->commit();

            $this->logModuleAction('sequence', 'sequence', $tabId, $tabId, $tabId, null, null, null, null, $sequenceChanges);
            return ['success' => true];
        } catch (\Throwable $e) {
            $tx->rollBack();
            return ['success' => false, 'message' => 'DB error'];
        }
    }

    public function actionPdf()
    {
        $baseUrl   = '';
        $csrfToken = Yii::$app->request->csrfToken;
        $csrfParam = Yii::$app->request->csrfParam;

        $pdfConfigs = (new \yii\db\Query())
            ->select([
                'phf.id',
                'phf.tab_id',
                'phf.name',
                'phf.header_content',
                'phf.footer_content',
                'phf.status',
                't.tablabel',
            ])
            ->from('pdf_headers_footers phf')
            ->leftJoin('tab t', 't.tabid = phf.tab_id')
            ->orderBy(['phf.id' => SORT_ASC])
            ->all();
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('pdf', [
            'baseUrl'    => $baseUrl,
            'csrfToken'  => $csrfToken,
            'csrfParam'  => $csrfParam,
            'pdfConfigs' => $pdfConfigs,
        ]);
    }

    public function actionPdfForm($id = null)
    {

        $csrfToken = Yii::$app->request->csrfToken;
        $csrfParam = Yii::$app->request->csrfParam;

        $tabs = (new \yii\db\Query())
            ->select(['tabid', 'tablabel'])
            ->from('tab')
            ->where(['presence' => 0])
            ->orderBy(['tabsequence' => SORT_ASC])
            ->all();

        $model = null;
        $stampAttachment = null;
        if ($id !== null) {
            $model = (new \yii\db\Query())
                ->from('pdf_headers_footers')
                ->where(['id' => (int)$id])
                ->one();
            if ($model && !empty($model['stamp'])) {
                $stampAttachment = (new \yii\db\Query())
                    ->select(['path', 'name', 'attachmentsid'])
                    ->from('attachments')
                    ->where(['attachmentsid' => (int)$model['stamp']])
                    ->andWhere(['status' => 0])
                    ->one();
            }
        }

        $this->layout = '@app/views/layouts/main-one';
        return $this->render('_pdf_form', [
            'csrfToken' => $csrfToken,
            'csrfParam' => $csrfParam,
            'tabs'      => $tabs,
            'model'     => $model,
            'stampAttachment' => $stampAttachment,
        ]);
    }


    public function actionSavePdfHeaderFooter()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req = Yii::$app->request;

        $id            = (int)$req->post('id');
        $tabId         = (int)$req->post('tab_id');
        $name          = trim($req->post('name', ''));
        $headerContent = $req->post('header_content', '');
        $footerContent = $req->post('footer_content', '');
        $status        = (int)$req->post('status', 1);

        if (!$tabId || $name === '') {
            return [
                'success' => false,
                'message' => 'Please select a tab and enter a name.',
            ];
        }

        $query = (new \yii\db\Query())
            ->from('pdf_headers_footers')
            ->where([
                'tab_id' => $tabId,
                'name'   => $name,
                'status' => 1,
            ]);

        if ($id > 0) {
            $query->andWhere(['<>', 'id', $id]);
        }

        if ((int)$query->count() > 0) {
            return [
                'success' => false,
                'message' => 'An active PDF configuration with the same Tab and Name already exists.',
            ];
        }

        $db = Yii::$app->db;

        $existing = null;
        if ($id > 0) {
            $existing = (new \yii\db\Query())
                ->from('pdf_headers_footers')
                ->where(['id' => $id])
                ->one();
            if (!$existing) {
                return ['success' => false, 'message' => 'Record not found.'];
            }
        }

        $stampId   = $existing['stamp'] ?? null;

        $stampFile = UploadedFile::getInstanceByName('stamp_file');
        if ($stampFile) {
            $res = $this->saveStampAttachment($stampFile);
            if (!$res['success']) {
                return [
                    'success' => false,
                    'message' => $res['message'] ?? 'Failed to upload stamp file.',
                ];
            }
            $stampId = $res['attachment_id'];
        }

        $data = [
            'tab_id'         => $tabId,
            'name'           => $name,
            'header_content' => $headerContent,
            'footer_content' => $footerContent,
            'status'         => $status,
            'stamp'          => $stampId,
        ];

        if ($id > 0) {
            $db->createCommand()
                ->update('pdf_headers_footers', $data, ['id' => $id])
                ->execute();
        } else {
            $db->createCommand()
                ->insert('pdf_headers_footers', $data)
                ->execute();
        }

        return [
            'success' => true,
            'message' => 'Saved successfully.',
        ];
    }


    protected function saveStampAttachment(UploadedFile $file)
    {
        if (empty($file)) {
            return ['success' => false, 'message' => 'No file uploaded.'];
        }

        $maxFileSize = 100 * 1024;

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
        ];

        $fileExtension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
            return ['success' => false, 'message' => "Invalid file type: {$fileExtension} is not allowed."];
        }

        if ($file->size > $maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds the maximum allowed size of 100kb.'];
        }

        $year  = date('Y');
        $month = date('m');
        $week  = date('W');

        $baseUploadPath = Yii::getAlias('@webroot/uploads');
        $targetPath     = $baseUploadPath . "/$year/$month/week_$week/";

        if (!is_dir($targetPath) && !mkdir($targetPath, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directories.'];
        }

        $fileName    = uniqid('stamp_', true) . '.' . $fileExtension;
        $filePath    = $targetPath . $fileName;
        $filesavepath = "uploads/$year/$month/week_$week/" . $fileName;

        if (!$file->saveAs($filePath)) {
            return ['success' => false, 'message' => 'Failed to save the file.'];
        }

        $modelAttach = new Attachments();
        $modelAttach->name       = $file->name;
        $modelAttach->type       = $file->type;
        $modelAttach->path       = $filesavepath;
        $modelAttach->storedname = $fileName;

        if ($modelAttach->validate() && $modelAttach->save()) {
            return [
                'success'   => true,
                'attachment_id' => $modelAttach->attachmentsid,
            ];
        }

        return ['success' => false, 'message' => 'Failed to save attachment record.'];
    }
   
    private function logModuleAction($logType, $action, $recordId, $tabId = null, $blockId = null, $fieldId = null, $parentTabId = null, $oldData = null, $newData = null, $sequenceChanges = [])
    {
        $user = Yii::$app->user->identity;
        $username = $user ? $user->username : 'System';

        // Merge sequence changes into new_data
        if (!empty($sequenceChanges) && $newData) {
            $newData['sequence_changes'] = $sequenceChanges;
        } elseif (!empty($sequenceChanges)) {
            $newData = ['sequence_changes' => $sequenceChanges];
        }

        Yii::$app->db->createCommand()->insert('module_logs', [
            'log_type' => $logType,
            'action' => $action,
            'record_id' => (int)$recordId,
            'tab_id' => $tabId ? (int)$tabId : null,
            'block_id' => $blockId ? (int)$blockId : null,
            'field_id' => $fieldId ? (int)$fieldId : null,
            'parenttab_id' => $parentTabId ? (int)$parentTabId : null,
            'old_values' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_values' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'user_id' => Yii::$app->user->id,
            'username' => $username,
            'ip_address' => Yii::$app->request->userIP,
            'user_agent' => Yii::$app->request->userAgent,
        ])->execute();
    }


    public function actionLogs()
    {
        $request = Yii::$app->request;
        $page    = max(1, (int)$request->get('page', 1));
        $pageSize = 50;

        $type   = $request->get('type', '');
        $action = $request->get('action', '');
        $search = $request->get('search', '');

        $db = Yii::$app->db;

        // Base query
        $where  = ['deleted' => 0];          // adjust if you have deleted flag
        $params = [];

        $sql = "FROM module_logs WHERE 1=1";

        if ($type !== '') {
            $sql    .= " AND log_type = :type";
            $params[':type'] = $type;
        }

        if ($action !== '') {
            $sql    .= " AND action = :action";
            $params[':action'] = $action;
        }

        if ($search !== '') {
            $sql .= " AND (username LIKE :search OR record_id LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Total count
        $countSql = "SELECT COUNT(*) " . $sql;
        $totalRecords = (int)$db->createCommand($countSql, $params)->queryScalar();

        $totalPages = max(1, (int)ceil($totalRecords / $pageSize));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $pageSize;

        // Data rows
        $dataSql = "SELECT id, log_type, action, record_id, tab_id, parenttab_id,
                        username, created_at
                    " . $sql . "
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

        $params[':limit']  = $pageSize;
        $params[':offset'] = $offset;

        $rows = $db->createCommand($dataSql, $params)->queryAll();

        $logTypes = \backend\models\ModuleLog::getLogTypeLabels();
        $actions  = \backend\models\ModuleLog::getActionLabels();
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('module_logs', [
            'rows'         => $rows,
            'page'         => $page,
            'pageSize'     => $pageSize,
            'totalRecords' => $totalRecords,
            'totalPages'   => $totalPages,
            'logTypes'     => $logTypes,
            'actions'      => $actions,
            'filters'      => [
                'type'   => $type,
                'action' => $action,
                'search' => $search,
            ],
        ]);
    }



    public function actionLogDetails($id)
    {
        $this->layout = false;
        $log = ModuleLog::findOne($id);

        if (!$log) {
            return '<div class="alert alert-danger">Log not found</div>';
        }

        $html = '<div class="row">';
        $html .= '<div class="col-md-6"><h5>Basic Info</h5>';
        $html .= '<table class="table table-sm">';
        $html .= '<tr><td><strong>Type:</strong></td><td>' . ModuleLog::getLogTypeLabels()[$log->log_type] . '</td></tr>';
        $html .= '<tr><td><strong>Action:</strong></td><td>' . ModuleLog::getActionLabels()[$log->action] . '</td></tr>';
        $html .= '<tr><td><strong>Record ID:</strong></td><td>' . $log->record_id . '</td></tr>';
        $html .= '<tr><td><strong>User:</strong></td><td>' . Html::encode($log->username) . '</td></tr>';
        $html .= '<tr><td><strong>Date:</strong></td><td>' . Yii::$app->formatter->asDatetime($log->created_at) . '</td></tr>';
        $html .= '</table></div>';

        $html .= '<div class="col-md-6"><h5>Context</h5>';
        $html .= '<table class="table table-sm">';
        if ($log->tab_id) $html .= '<tr><td>Tab ID:</td><td>' . $log->tab_id . '</td></tr>';
        if ($log->block_id) $html .= '<tr><td>Block ID:</td><td>' . $log->block_id . '</td></tr>';
        if ($log->parenttab_id) $html .= '<tr><td>ParentTab ID:</td><td>' . $log->parenttab_id . '</td></tr>';
        $html .= '</table></div></div>';

        if ($log->old_values) {
            $old = json_decode($log->old_values, true);
            $html .= '<div class="row mt-3"><div class="col-12"><h5>Old Values</h5><pre class="bg-light p-3 rounded">' . Html::encode(json_encode($old, JSON_PRETTY_PRINT)) . '</pre></div></div>';
        }

        if ($log->new_values) {
            $new = json_decode($log->new_values, true);
            if (isset($new['sequence_changes']) && is_array($new['sequence_changes'])) {
                $html .= '<div class="row mt-3"><div class="col-12"><h5>Sequence Changes</h5>';
                $html .= '<div class="table-responsive"><table class="table table-sm table-striped">';
                $html .= '<thead><tr><th>ID</th><th>Old Sequence</th><th>New Sequence</th></tr></thead><tbody>';
                foreach ($new['sequence_changes'] as $id => $change) {
                    $html .= "<tr><td>{$id}</td><td><strong>{$change['old_sequence']}</strong></td><td><span class='badge bg-success'>{$change['new_sequence']}</span></td></tr>";
                }
                $html .= '</tbody></table></div></div></div>';
            } else {
                $html .= '<div class="row mt-3"><div class="col-12"><h5>New Values</h5><pre class="bg-light p-3 rounded">' . Html::encode(json_encode($new, JSON_PRETTY_PRINT)) . '</pre></div></div>';
            }
        }

        return $html;
    }

    public function actionViewsharinglist()
    {
        $this->layout = '@app/views/layouts/main-one';

        $request  = Yii::$app->request;
        $pageSize = (int)$request->get('per-page', 10);
        if ($pageSize <= 0) {
            $pageSize = 10;
        }
        $q = trim($request->get('q', ''));

        $params = [];
        $where  = '';
        if ($q !== '') {
            $where  = "WHERE title LIKE :q OR module_name LIKE :q OR description LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }

        $totalCount = (int)Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM detaileditsetting {$where}"
        )
            ->bindValues($params)
            ->queryScalar();

        $pagination = new Pagination([
            'totalCount'      => $totalCount,
            'defaultPageSize' => $pageSize,
            'pageSizeLimit'   => [2, 100],
            'pageSizeParam'   => 'per-page',
            'pageParam'       => 'page',
        ]);

        $rows = Yii::$app->db->createCommand(
            "SELECT * FROM detaileditsetting {$where} ORDER BY des_id ASC LIMIT :limit OFFSET :offset"
        )
            ->bindValues($params)
            ->bindValue(':limit',  $pagination->limit,  \PDO::PARAM_INT)
            ->bindValue(':offset', $pagination->offset, \PDO::PARAM_INT)
            ->queryAll();

        $tabList = Yii::$app->db->createCommand(
            'SELECT tabid, name, tablabel FROM tab ORDER BY name'
        )->queryAll();

        $roleList = Yii::$app->db->createCommand(
            'SELECT roleid, rolename 
            FROM role
            WHERE is_deleted = 0 AND enabled = 1
            ORDER BY rolename'
        )->queryAll();

        $userList = Yii::$app->db->createCommand(
            'SELECT id, first_name 
            FROM user
            WHERE deleted = 0 AND status = 10
            ORDER BY first_name'
        )->queryAll();

        $tabMap  = ArrayHelper::map($tabList, 'tabid', 'tablabel');
        $roleMap = ArrayHelper::map($roleList, 'roleid', 'rolename');
        $userMap = ArrayHelper::map($userList, 'id', 'first_name');

        return $this->render('moduleviewsharinglist', [
            'rows'       => $rows,
            'tabList'    => $tabList,
            'roleList'   => $roleList,
            'userList'   => $userList,
            'tabJson'    => json_encode($tabMap,  JSON_UNESCAPED_UNICODE),
            'roleJson'   => json_encode($roleMap, JSON_UNESCAPED_UNICODE),
            'userJson'   => json_encode($userMap, JSON_UNESCAPED_UNICODE),
            'pagination' => $pagination,
            'pageSize'   => $pageSize,
            'totalCount' => $totalCount,
            'q'          => $q,
        ]);
    }

    public function actionDetaileditCreate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $data = $request->post();
            $roleIds = isset($data['user_role']) ? (array)$data['user_role'] : [];
            $userIds = isset($data['user_id']) ? (array)$data['user_id'] : [];
            $title       = Yii::$app->request->post('title');
            $description = Yii::$app->request->post('description');
            Yii::$app->db->createCommand()->insert('detaileditsetting', [
                'tabid'              => (int)($data['tabid'] ?? 0),
                'module_name'        => $data['module_name'] ?? '',
                'stage_field'        => $data['stage_field'] ?? '',
                'stage_value'        => $data['stage_value_joined'] ?? '',
                'view_allow'         => !empty($data['view_allow']) ? 1 : 0,
                'edit_allow'         => !empty($data['edit_allow']) ? 1 : 0,
                'admin_allow'        => !empty($data['admin_allow']) ? 1 : 0,
                'superadmin_allow'   => !empty($data['superadmin_allow']) ? 1 : 0,
                'title'         => $title,
                'description'   => $description,
                'user_role'          => implode(',', $roleIds),
                'user_id'            => implode(',', $userIds),
            ])->execute();
            $insertId = Yii::$app->db->getLastInsertID();
            $record   = Detaileditsetting::findOne($insertId);
            $newrecord = $record->getAttributes();
            $this->logModuleAction(
                'moduleviewsharing',
                'add',
                $insertId,
                (int)($data['tabid'] ?? 0),
                null,
                null,
                null,
                null,
                $newrecord
            );
            return $this->redirect(['modulesetting/viewsharinglist']);

        }

        $tabList = Yii::$app->db->createCommand(
            'SELECT tabid, name, tablabel, tablename FROM tab ORDER BY name'
        )->queryAll();

        $roleList = Yii::$app->db->createCommand(
            'SELECT roleid, rolename FROM role 
         WHERE is_deleted = 0 AND enabled = 1 ORDER BY rolename'
        )->queryAll();

        $userList = Yii::$app->db->createCommand(
            'SELECT id, first_name FROM user 
         WHERE deleted = 0 AND status = 10 ORDER BY first_name'
        )->queryAll();
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('moduleviewsharing_form', [
            'mode'     => 'create',
            'record'   => null,
            'tabList'  => $tabList,
            'roleList' => $roleList,
            'userList' => $userList,
        ]);
    }

    public function actionDetaileditUpdate($id)
    {
        $this->layout = '@app/views/layouts/main-one';
        $request = Yii::$app->request;
        $id = (int)$id;
        $title       = Yii::$app->request->post('title');
        $description = Yii::$app->request->post('description');
        $record   = Detaileditsetting::findOne($id);
        $oldData = $record->getAttributes();
        if ($request->isPost) {
            $data = $request->post();
            $roleIds = isset($data['user_role']) ? (array)$data['user_role'] : [];
            $userIds = isset($data['user_id']) ? (array)$data['user_id'] : [];

            Yii::$app->db->createCommand()->update(
                'detaileditsetting',
                [
                    'tabid'              => (int)($data['tabid'] ?? 0),
                    'module_name'        => $data['module_name'] ?? '',
                    'stage_field'        => $data['stage_field'] ?? '',
                    'stage_value'        => $data['stage_value_joined'] ?? '',
                    'view_allow'         => !empty($data['view_allow']) ? 1 : 0,
                    'edit_allow'         => !empty($data['edit_allow']) ? 1 : 0,
                    'admin_allow'        => !empty($data['admin_allow']) ? 1 : 0,
                    'superadmin_allow'   => !empty($data['superadmin_allow']) ? 1 : 0,
                    'title'         => $title,
                    'description'   => $description,
                    'user_role'          => implode(',', $roleIds),
                    'user_id'            => implode(',', $userIds),
                ],
                ['des_id' => $id]
            )->execute();
            $record   = Detaileditsetting::findOne($id);
            $newrecord = $record->getAttributes();
       

            $this->logModuleAction(
                'moduleviewsharing',
                'update',
                $id,
                (int)($data['tabid'] ?? 0),
                null,
                null,
                null,
                $oldData,
                $newrecord
            );
            return $this->redirect(['modulesetting/viewsharinglist']);

        }

        $record = Yii::$app->db->createCommand(
            'SELECT * FROM detaileditsetting WHERE des_id = :id',
            [':id' => $id]
        )->queryOne();

        if (!$record) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        $tabList = Yii::$app->db->createCommand(
            'SELECT tabid, name, tablabel, tablename FROM tab ORDER BY name'
        )->queryAll();

        $roleList = Yii::$app->db->createCommand(
            'SELECT roleid, rolename FROM role 
         WHERE is_deleted = 0 AND enabled = 1 ORDER BY rolename'
        )->queryAll();

        $userList = Yii::$app->db->createCommand(
            'SELECT id, first_name FROM user 
         WHERE deleted = 0 AND status = 10 ORDER BY first_name'
        )->queryAll();
        
        return $this->render('moduleviewsharing_form', [
            'mode'     => 'update',
            'record'   => $record,
            'tabList'  => $tabList,
            'roleList' => $roleList,
            'userList' => $userList,
        ]);
    }



    public function actionDetaileditTabinfo($tabid)
    {
        $tabid = (int)$tabid;

        $tab = Yii::$app->db->createCommand(
            'SELECT name, tablabel FROM tab WHERE tabid = :id',
            [':id' => $tabid]
        )->queryOne();

        if (!$tab) {
            return $this->asJson(['success' => false]);
        }

        return $this->asJson([
            'success'    => true,
            'modulename' => $tab['name'],
            'tablabel'   => $tab['tablabel'],
        ]);
    }

    public function actionGetStageFields($tabid)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $tabid = (int)$tabid;
        if (!$tabid) {
            return ['success' => false, 'fields' => []];
        }

        $fields = (new \yii\db\Query())
            ->from('field')
            ->where(['tabid' => $tabid])
            ->orderBy(['block' => SORT_ASC, 'sequence' => SORT_ASC])
            // ->asArray()
            ->all();

        $out = [];
        foreach ($fields as $f) {
            $out[] = [
                'fieldid'   => $f['fieldid'],
                'fieldname' => $f['fieldname'],
                'fieldlabel' => $f['fieldlabel'],
                'columnname' => $f['columnname'],
                'tablename' => $f['tablename'],
            ];
        }

        return ['success' => true, 'fields' => $out];
    }

    public function actionGetStagePicklist($fieldid)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $fieldid = (int)$fieldid;
        if (!$fieldid) {
            return ['success' => false, 'values' => []];
        }

        $field = (new \yii\db\Query())
            ->from('field')
            ->where(['fieldid' => $fieldid])
            ->one();

        if (!$field) {
            return ['success' => false, 'values' => []];
        }

        $pickMeta = (new \yii\db\Query())
            ->from('picklist')
            ->where(['fieldid' => $fieldid])
            ->one();

        if (!$pickMeta || empty($pickMeta['targettable'])) {
            return ['success' => false, 'values' => []];
        }

        $targetTable = $pickMeta['targettable'];

        $rows = (new \yii\db\Query())
            ->from($targetTable)
            // ->orderBy(['sortorderid' => SORT_ASC]) // remove if not present
            ->all();

        $values = [];
        foreach ($rows as $r) {
            $cols = array_keys($r);
            if (count($cols) < 2) {
                continue;
            }

            $idCol    = $cols[0]; 
            $labelCol = $cols[1]; 

            $values[] = [
                'id'    => $r[$idCol],
                'label' => $r[$labelCol],
            ];
        }

        return ['success' => true, 'values' => $values];
    }


    public function actionSaveDetailEdit()
    {
        $request = Yii::$app->request;
        $post    = $request->post('DetailEdit', []);
        $id      = $post['des_id'] ?? null;

        $model = $id
            ? Detaileditsetting::findOne((int)$id)
            : new Detaileditsetting();

        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Record not found');
        }

        $model->tabid        = (int)($post['tabid'] ?? 0);
        $model->module_name  = $post['module_name'] ?? '';
        $model->stage_field  = $post['stage_field'] ?? '';

        $model->stage_value  = $post['stage_value_joined'] ?? '';
        $model->user_role    = $post['user_role_joined'] ?? '';
        $model->user_id      = $post['user_id_joined'] ?? '';

        $model->edit_allow       = !empty($post['edit_allow']) ? 1 : 0;
        $model->view_allow       = !empty($post['view_allow']) ? 1 : 0;
        $model->admin_allow      = !empty($post['admin_allow']) ? 1 : 0;
        $model->superadmin_allow = !empty($post['superadmin_allow']) ? 1 : 0;

        if ($model->save(false)) {
            return $this->redirect(['modulesetting/moduleviewsharinglist']);
        }

        throw new \yii\web\ServerErrorHttpException('Cannot save detail edit');
    }
    public function actionCompanysetting()
{
    $this->layout = '@app/views/layouts/main-one';

    $request  = Yii::$app->request;
    $pageSize = (int)$request->get('per-page', 10);
    if ($pageSize <= 0) {
        $pageSize = 10;
    }
    
    $q = trim($request->get('q', ''));

    $params = [];
    $where  = '';
    if ($q !== '') {
        $where = " WHERE company LIKE :q OR logo_path LIKE :q ";
        $params[':q'] = '%' . $q . '%';
    }

    $totalCount = (int) Yii::$app->db->createCommand(
        "SELECT COUNT(*) FROM site_setting {$where}"
    )->bindValues($params)->queryScalar();

    $pagination = new Pagination([
        'totalCount' => $totalCount,
        'defaultPageSize' => $pageSize,
        'pageSizeLimit' => [2, 100],
        'pageSizeParam' => 'per-page',
        'pageParam' => 'page',
    ]);

    $pagination->params = [
        'q' => $q,
        'per-page' => $pageSize,
    ];

    $rows = Yii::$app->db->createCommand(
        "SELECT * FROM site_setting {$where} ORDER BY active DESC, id DESC LIMIT :limit OFFSET :offset"
    )
        ->bindValues($params)
        ->bindValue(':limit', $pagination->limit, \PDO::PARAM_INT)
        ->bindValue(':offset', $pagination->offset, \PDO::PARAM_INT)
        ->queryAll();

    return $this->render('companysettinglist', [
        'rows' => $rows,
        'pagination' => $pagination,
        'pageSize' => $pageSize,
        'totalCount' => $totalCount,
        'q' => $q,
    ]);
}

    public function actionCompanysettingform($id = null, $mode = 'create')
    {
        $this->layout = '@app/views/layouts/main-one';

        $record = [];
        if (!empty($id)) {
            $record = Yii::$app->db->createCommand(
                "SELECT * FROM site_setting WHERE id = :id"
            )
                ->bindValue(':id', $id, \PDO::PARAM_INT)
                ->queryOne();

            if (!$record) {
                throw new NotFoundHttpException('Company setting record not found.');
            }
        }

        return $this->render('companysetting_form', [
            'record' => $record,
            'mode'   => $mode,
        ]);
    }

    public function actionSavecompanysetting()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id       = (int) Yii::$app->request->post('id', 0);
        $company  = trim(Yii::$app->request->post('company', ''));
        $active   = (int) Yii::$app->request->post('active', 0);

        if ($company === '') {
            return ['success' => false, 'message' => 'Company is required.'];
        }

        $file = UploadedFile::getInstanceByName('logo_file');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $logoPath = null;

            if ($file) {
                $allowedExt = ['png', 'jpg', 'jpeg', 'svg'];
                $ext = strtolower($file->extension);

                if (!in_array($ext, $allowedExt, true)) {
                    return ['success' => false, 'message' => 'Only PNG, JPG, JPEG, SVG are allowed.'];
                }

                $uploadDir = Yii::getAlias('@webroot/thememain/img/login');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $fileName = 'logo_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($company)) . '_' . time() . '.' . $ext;
                $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                $logoPath = '/thememain/img/login/' . $fileName;

                if (!$file->saveAs($fullPath)) {
                    return ['success' => false, 'message' => 'Failed to save uploaded file.'];
                }
            }

            if ($active === 1) {
                Yii::$app->db->createCommand()->update('site_setting', ['active' => 0])->execute();
            }

            if ($id > 0) {
                $oldRecord = Yii::$app->db->createCommand(
                    "SELECT * FROM site_setting WHERE id = :id"
                )
                    ->bindValue(':id', $id, \PDO::PARAM_INT)
                    ->queryOne();

                if (!$oldRecord) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'Record not found.'];
                }

                $updateData = [
                    'company' => $company,
                    'active'  => $active,
                ];

                if ($logoPath !== null) {
                    $updateData['logo_path'] = $logoPath;
                }

                Yii::$app->db->createCommand()->update('site_setting', $updateData, ['id' => $id])->execute();

                $newRecord = Yii::$app->db->createCommand(
                    "SELECT * FROM site_setting WHERE id = :id"
                )
                    ->bindValue(':id', $id, \PDO::PARAM_INT)
                    ->queryOne();

                if (method_exists($this, 'logModuleAction')) {
                    $this->logModuleAction(
                        'companysetting',
                        'update',
                        $id,
                        null,
                        null,
                        null,
                        null,
                        $oldRecord,
                        $newRecord
                    );
                }
            } else {
                $insertData = [
                    'company'   => $company,
                    'logo_path' => $logoPath ?: '',
                    'active'    => $active,
                ];

                Yii::$app->db->createCommand()->insert('site_setting', $insertData)->execute();
                $newId = Yii::$app->db->getLastInsertID();

                $newRecord = Yii::$app->db->createCommand(
                    "SELECT * FROM site_setting WHERE id = :id"
                )
                    ->bindValue(':id', $newId, \PDO::PARAM_INT)
                    ->queryOne();

                if (method_exists($this, 'logModuleAction')) {
                    $this->logModuleAction(
                        'companysetting',
                        'create',
                        $newId,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $newRecord
                    );
                }
            }

            $transaction->commit();

            return [
                'success' => true,
                'message' => $id > 0 ? 'Company setting updated successfully.' : 'Company setting created successfully.',
                'redirect' => Url::to(['modulesetting/companysetting']),
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    public function actionUpdatecompanystatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int) Yii::$app->request->post('id', 0);
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Please select a valid record.'];
        }

        $record = Yii::$app->db->createCommand(
            "SELECT * FROM site_setting WHERE id = :id"
        )
            ->bindValue(':id', $id, \PDO::PARAM_INT)
            ->queryOne();

        if (!$record) {
            return ['success' => false, 'message' => 'Record not found.'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            Yii::$app->db->createCommand()->update('site_setting', ['active' => 0])->execute();
            Yii::$app->db->createCommand()->update('site_setting', ['active' => 1], ['id' => $id])->execute();

            $updatedRecord = Yii::$app->db->createCommand(
                "SELECT * FROM site_setting WHERE id = :id"
            )
                ->bindValue(':id', $id, \PDO::PARAM_INT)
                ->queryOne();

            if (method_exists($this, 'logModuleAction')) {
                $this->logModuleAction(
                    'companysetting',
                    'status_update',
                    $id,
                    null,
                    null,
                    null,
                    null,
                    $record,
                    $updatedRecord
                );
            }

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Active status updated successfully.',
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    public function actionGetSiteLogo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $company = trim(Yii::$app->request->get('company', ''));
        if ($company === '') {
            return [
                'success' => false,
                'message' => 'Company is required.'
            ];
        }

        $model = SiteSetting::find()
            ->where(['company' => $company, 'active' => 1])
            ->one();

        if (!$model) {
            $model = SiteSetting::find()
                ->where(['company' => $company])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        }

        if (!$model) {
            return [
                'success' => false,
                'message' => 'No logo found for this company.'
            ];
        }

        return [
            'success'   => true,
            'logo_path' => $model->logo_path,
            'url'       => Url::to($model->logo_path, true),
            'company'   => $model->company,
            'active'    => (int)$model->active,
        ];
    }

    public function actionUpdateLogo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $company = trim(Yii::$app->request->post('company', ''));
        if ($company === '') {
            return ['success' => false, 'message' => 'Company is required.'];
        }

        $file = UploadedFile::getInstanceByName('logo_file');
        if (!$file) {
            return ['success' => false, 'message' => 'Logo file is required.'];
        }

        $allowedExt = ['png', 'jpg', 'jpeg', 'svg'];
        $ext = strtolower($file->extension);
        if (!in_array($ext, $allowedExt, true)) {
            return ['success' => false, 'message' => 'Only PNG, JPG, JPEG, SVG are allowed.'];
        }

        $uploadDir = Yii::getAlias('@webroot/thememain/img/login');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = 'logo_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($company)) . '_' . time() . '.' . $ext;
        $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $relativePath = '/thememain/img/login/' . $fileName;

        if (!$file->saveAs($fullPath)) {
            return ['success' => false, 'message' => 'Failed to save uploaded file.'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $oldActiveRecord = SiteSetting::find()
                ->where(['active' => 1])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            $oldData = $oldActiveRecord ? [
                'id' => $oldActiveRecord->id,
                'company' => $oldActiveRecord->company,
                'logo_path' => $oldActiveRecord->logo_path,
                'active' => $oldActiveRecord->active,
            ] : null;

            SiteSetting::updateAll(['active' => 0]);

            $model = SiteSetting::find()->where(['company' => $company])->orderBy(['id' => SORT_DESC])->one();
            if (!$model) {
                $model = new SiteSetting();
                $model->company = $company;
            }

            $model->logo_path = $relativePath;
            $model->active = 1;

            if (!$model->save(false)) {
                $transaction->rollBack();
                @unlink($fullPath);
                return ['success' => false, 'message' => 'Failed to save site setting.'];
            }

            $newrecord = [
                'id' => $model->id,
                'company' => $model->company,
                'logo_path' => $model->logo_path,
                'active' => $model->active,
            ];

            if (method_exists($this, 'logModuleAction')) {
                $this->logModuleAction(
                    'companylogo',
                    'update',
                    $model->id,
                    null,
                    null,
                    null,
                    null,
                    $oldData,
                    $newrecord
                );
            }

            $transaction->commit();

            return [
                'success'   => true,
                'logo_path' => $model->logo_path,
                'url'       => Url::to($model->logo_path, true),
                'company'   => $model->company,
                'active'    => (int)$model->active,
                'message'   => 'Logo updated successfully.',
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            @unlink($fullPath);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
}