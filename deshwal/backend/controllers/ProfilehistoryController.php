<?php

namespace backend\controllers;

use app\models\Profile;
use app\models\ProfileModtrackerBasic;
use app\models\ProfileModtrackerDetail;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * ProfilehistoryController implements the CRUD actions for ProfileModtrackerBasic model.
 */
class ProfilehistoryController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ProfileModtrackerBasic models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProfileModtrackerBasic::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);
        $this->layout = '@app/views/layouts/main-one';
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProfileModtrackerBasic model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($Record)
    {
        $result = [];

        // $details = ProfileModtrackerDetail::find()
        //     ->alias('d')
        //     ->innerJoin('field pf', 'pf.fieldid = d.fieldid')
        //     ->leftJoin('tab t', 't.tabid = pf.tabid') // adjust table name
        //     ->select([
        //         'd.id', 
        //         't.tablabel',        // tab name
        //         'pf.fieldlabel',     // field label
        //         'd.prevalue',
        //         'd.postvalue'
        //     ])
        //     ->where(['d.id' => $Record])
        //     ->asArray()
        //     ->all();

        $details = (new \yii\db\Query())
        ->from(['d' => 'profile_modtracker_detail'])
        ->innerJoin(['pf' => 'field'], 'pf.fieldid = d.fieldid')
        ->leftJoin(['t' => 'tab'], 't.tabid = pf.tabid')
        ->select([
            't.tablabel',
            'pf.fieldlabel',
            'd.prevalue',
            'd.postvalue'
        ])
        ->where(['d.id' => $Record]) // or basicid
        ->all();

        foreach ($details as $row) {
            $tab = $row['tablabel'] ?? '';

            
            $pre = explode(",", $row['prevalue']);
            $post = explode(",", $row['postvalue']);
            $result[$tab][] = [
                'field' => $row['fieldlabel'],
                'pre_visible' => $pre[0] == '0'
                    ? '<span class="badge bg-success">Show</span>'
                    : '<span class="badge bg-danger">Hide</span>',

                'pre_readonly' => $pre[1] == '0'
                    ? '<span class="badge bg-secondary">False</span>'
                    : '<span class="badge bg-warning text-dark">True</span>',

                // POST
                'post_visible' => $post[0] == '0'
                    ? '<span class="badge bg-success">Show</span>'
                    : '<span class="badge bg-danger">Hide</span>',

                'post_readonly' => $post[1] == '0'
                    ? '<span class="badge bg-secondary">False</span>'
                    : '<span class="badge bg-warning text-dark">True</span>',
            ];
        }

        $this->layout = '@app/views/layouts/main-one';
        return $this->render('view', [
            //     'model' => $this->findModel($Record),
            'data' => $result
        ]);
    }

    /**
     * Creates a new ProfileModtrackerBasic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProfileModtrackerBasic();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProfileModtrackerBasic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProfileModtrackerBasic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProfileModtrackerBasic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProfileModtrackerBasic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProfileModtrackerBasic::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetallprofilehistory()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $rows = ProfileModtrackerBasic::find()
            ->alias('pmb')
            ->select([
                'pmb.crmid',
                'pmb.id',
                'pmb.whodid',
                'pmb.changedon',
                'pmb.status',
                'p.profilename'
            ])
            ->innerJoin('profile p', 'p.profileid = pmb.crmid')
            ->asArray()
            ->all();

        foreach ($rows as &$row) {
            // Get module name
            $row['crmid'] = $row['profilename'] ?? '-';
            $user = User::find()->select(['first_name', 'last_name'])->where(["id" => $row['whodid']])->asArray()->one();
            // echo "<pre>";print_r($user);
            $row['whodid'] = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
            $row['status'] = $row['status'] == 0 ? 'Create' : 'Update';
            // $row['view'] = Html::a(
            //     'detial',
            //     ['profilehistory/view', 'id' => $row['id']],
            //     ['class' => 'btn btn-sm btn-primary']
            // );
        }

        return [
            'data' => $rows   // DataTables ONLY needs this
        ];
    }
    // function formatValue($value)
    // {
    //     if (empty($value)) return '-';

    //     $map = [
    //         'visible' => ['0' => 'Show', '1' => 'Hide'],
    //         'readonly' => ['0' => 'False', '1' => 'True'],
    //     ];

    //     $parts = explode(',', $value);

    //     $result = [];
    //     $keys = array_keys($map);

    //     foreach ($parts as $i => $val) {
    //         $key = $keys[$i] ?? null;

    //         if ($key && isset($map[$key][$val])) {
    //             $result[] = ucfirst($key) . ': ' . $map[$key][$val];
    //         } else {
    //             $result[] = $val;
    //         }
    //     }

    //     return implode(', ', $result);
    // }
    // function formatValue($value)
    // {
    //     if (!$value) return '-';

    //     list($visible, $readonly) = explode(',', $value);

        
    //     $visibleText  = $visible == '0' ? '<span style="background-color: green; color: white; padding: 2px 6px; border-radius: 10px;">Show</span>' : 'Hide';
    //     $readonlyText = $readonly == '0' ? 'False' : 
    //                         '<span style="background-color: orange; color: white; padding: 2px 6px; border-radius: 10px;">True</span>';
    //     // return "visible readonly\n{$visibleText} {$readonlyText}";
    //     // return 
    //     //     'Visible: ' . ($visible == '0' ? '<span style="background-color: green; color: white; padding: 2px 6px; border-radius: 10px;">Show</span>' : 'Hide') . ', ' .
    //     //     'Readonly: ' . ($readonly == '0' ? 'False' : 
    //     //                     '<span style="background-color: orange; color: white; padding: 2px 6px; border-radius: 10px;">True</span>');
    //     return "
    //     <table style='border-collapse: collapse;'>
    //         <tr>
    //             <td style='padding:2px 6px;'>{$visibleText}</td>
    //             <td style='padding:2px 6px;'>{$readonlyText}</td>
    //         </tr>
    //     </table>
    // ";
    // }

    function formatValue($value)
    {
        if (!$value) return '-';

        [$visible, $readonly] = explode(',', $value);

        $visibleText = $visible == '0'
            ? '<span class="badge bg-success">Show</span>'
            : '<span class="badge bg-danger">Hide</span>';

        $readonlyText = $readonly == '0'
            ? '<span class="badge bg-secondary">False</span>'
            : '<span class="badge bg-warning text-dark">True</span>';

        return "
            <div style='display:flex; gap:10px; align-items:center;'>
                <div style='min-width:60px; text-align:center;'>{$visibleText}</div>
                <div style='min-width:70px; text-align:center;'>{$readonlyText}</div>
            </div>
        ";
    }
}
