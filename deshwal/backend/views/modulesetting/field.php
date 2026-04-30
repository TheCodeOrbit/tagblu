<?php

use backend\assets\AdminAsset;
use backend\components\SvgRenderHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Field Setting';
$baseUrl = Url::base();
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;
$baseUrl = Url::base();
$logo = 'modulesetting';
// echo $baseUrl;die;
$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>

<style>
   body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif}.fields-table{display:none}.field-block-header{cursor:pointer}.card{max-width:auto;margin:auto;background:#fff;border-radius:10px;box-shadow:0 8px 24px #0f172a14;border:1px solid #e5e7eb}.card-header{padding:16px 22px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:12px}.card-header h1{margin:0;font-size:20px;font-weight:600;color:#111827;flex:1}.card-body{padding:18px 22px 22px;background:#f9fafb}.add-btn,.add-field-btn{padding:6px 14px;cursor:pointer;background:#2563eb;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:500;display:inline-flex;align-items:center;gap:6px;box-shadow:0 1px 2px #0f172a1f;transition:background .15s ease,transform .05s ease,box-shadow .15s ease}.add-btn:hover,.add-field-btn:hover{background:#1d4ed8;transform:translateY(-1px);box-shadow:0 4px 10px #2563eb40}.search-input{padding:6px 10px;border-radius:999px;border:1px solid #d1d5db;font-size:13px;min-width:230px;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;background:#f9fafb}.search-input:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px #2563eb40}.fields-table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 1px 3px #0f172a0a}.fields-table th,.fields-table td{border:1px solid #e5e7eb;padding:8px 10px;font-size:13px}.fields-table th{background:#f3f4f6;font-weight:600;color:#374151}.drag-handle{text-align:center;cursor:move;color:#9ca3af;width:26px}.field-row{background:#fff}.field-row.drag-over{background:#eff6ff;box-shadow:inset 0 0 0 1px #2563eb66}.field-seq-label{font-weight:500;color:#111827}.edit-field-btn{padding:4px 10px;border-radius:999px;border:none;font-size:12px;font-weight:500;cursor:pointer;background:#10b981;color:#fff}.edit-field-btn:hover{background:#059669}.field-modal{display:none;position:fixed;z-index:2000;inset:0;align-items:center;justify-content:center;font-family:inherit}.field-modal-backdrop{position:absolute;inset:0;background:#0f172a73}.field-modal-content{position:relative;background:#fff;border-radius:10px;max-width:720px;width:96%;max-height:90vh;margin:0 auto;padding:24px 24px 20px;box-shadow:0 18px 45px #0f172a59;display:flex;flex-direction:column;overflow:hidden}.field-modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}.field-modal-header h3{margin:0;font-size:20px;font-weight:600;color:#111827}.field-modal-close{font-size:22px;cursor:pointer;color:#6b7280;padding:4px 8px;border-radius:999px;transition:background .15s ease,color .15s ease}.field-modal-close:hover{background:#e5e7eb;color:#111827}.field-modal-body{overflow-y:auto;padding-top:4px;padding-right:4px}.flex-form{display:flex;flex-wrap:wrap;gap:16px 24px}.field-column{flex:1 1 260px;min-width:240px;max-width:320px;display:flex;flex-direction:column;gap:8px}.field-column label{font-size:13px;font-weight:500;color:#374151;margin-bottom:2px}.field-modal-content input[type="text"],.field-modal-content input[type="number"],.field-modal-content textarea{width:100%;padding:7px 10px;border-radius:6px;border:1px solid #d1d5db;font-size:13px;background:#f9fafb;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease}.field-modal-content input[type="text"]:focus,.field-modal-content input[type="number"]:focus,.field-modal-content textarea:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 2px #2563eb40}.field-modal-content textarea{min-height:60px;resize:vertical}.field-modal-actions{margin-top:16px;display:flex;justify-content:flex-end;gap:10px}.save-btn,.cancel-btn{padding:7px 16px;border-radius:999px;border:none;font-size:13px;font-weight:500;cursor:pointer}.save-btn{background:#10b981;color:#fff}.save-btn:hover{background:#059669}.cancel-btn{background:#e5e7eb;color:#374151}.cancel-btn:hover{background:#d1d5db}#block-container .field-block{border:1px solid #e5e7eb;border-radius:10px;margin-bottom:16px;background:#fff;box-shadow:0 2px 6px #0f172a0a}#block-container .field-block-header{padding:8px 12px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:8px;background:#f9fafb}#block-container .field-block-header .block-drag{cursor:move;color:#9ca3af}#block-container .field-block-header .block-title{font-size:13px;font-weight:600;color:#374151}#block-container .field-block.drag-over{border-color:#2563eb;box-shadow:0 0 0 1px #2563eb59}.field-tbody{min-height:32px}.field-block.drag-over{border-color:#2563eb;box-shadow:0 0 0 1px #2563eb59;background:#eff6ff}.module-switcher{display:inline-flex;align-items:center;gap:10px;padding:6px 10px;border-radius:999px;background:linear-gradient(135deg,#f9fafb,#e5e7eb);border:1px solid #d1d5db;box-shadow:0 2px 5px #0f172a1f;margin:0 16px 12px 0}.module-switcher-label{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;font-weight:600}.module-switcher-select-wrap{position:relative;display:inline-flex;align-items:center}.module-switcher-select{appearance:none;-webkit-appearance:none;-moz-appearance:none;border-radius:999px;border:1px solid transparent;padding:4px 26px 4px 10px;font-size:13px;background:#fff;color:#111827;outline:none;cursor:pointer;min-width:160px}.module-switcher-select:hover{border-color:#9ca3af}.module-switcher-select:focus{border-color:#2563eb;box-shadow:0 0 0 1px #2563eb40}.module-switcher-caret{position:absolute;right:8px;pointer-events:none;font-size:11px;color:#6b7280}.head-img{position:static}@media (max-width: 768px){.module-switcher{width:100%;justify-content:space-between}.module-switcher-select{min-width:0;width:100%}}
</style>


<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add" style="
    padding: 12px;
    margin-top: 10px;
">
         <span class="icons-coll head-img" >
            <?= SvgRenderHelper::renderIcon($logo.'.svg',true); ?>
        </span>
        <span class="sm-modname"><strong><?= $this->title; ?></strong></span>
        <br>
      </div>
    </div>
  </div>
</div>
 
<div class="card">
    <div class="card-header">
        <h1>Fields for Tab: <?= Html::encode($tab->tablabel ?: $tab->name) ?></h1>
        <?php
        $currentTabId = (int)$tab->tabid;
        ?>

        <div class="module-switcher">
            <div class="module-switcher-label">Module</div>
            <div class="module-switcher-select-wrap">
                <select id="tab-switcher-select" class="module-switcher-select">
                    <?php foreach ($tabList as $id => $label): ?>
                        <option value="<?= (int)$id ?>"
                            <?= $id == $currentTabId ? 'selected' : '' ?>>
                            <?= Html::encode($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="module-switcher-caret">▾</span>
            </div>
        </div>

        <button class="add-field-btn" style="display:none;" data-tabid="<?= $tab->tabid ?>">Add Field</button>

        <input type="text" id="field-search" class="search-input"
            placeholder="Search fields...">
        <button id="add-block-btn" class="add-btn" style="margin-left:8px;">
            Add Block
        </button>
        <!-- <button id="update-field-seq-btn" class="add-btn" style="margin-left:auto;">
                Update Field Seq
            </button> -->

        <button id="update-block-seq-btn" class="add-btn" style="margin-left:8px;display:none;">
            Update Block Seq
        </button>
    </div>


    <div class="card-body" id="block-container">
        <?php foreach ($blocksWithFields as $blockid => $blockData): ?>
            <?php $b = $blockData['info'];  ?>
            <div class="field-block"
                draggable="true"
                data-blockid="<?= (int)$b['blockid'] ?>"
                data-blocklabel="<?= Html::encode($b['blocklabel']) ?>">

                <div class="field-block-header">
                    <span class="block-drag">≡</span>
                    <span class="block-title">
                        <?= Html::encode($b['blocklabel']) ?>
                        (Seq: <span class="block-seq-label"><?= (int)($b['sequence']) ?></span>)
                        <span class="block-id">(Block <?= (int)$b['blockid'] ?>)</span>
                    </span>

                    <button type="button"
                        class="btn btn-primary fields-btn edit-block-btn"
                        style="margin-left:auto;"
                        data-blockid="<?= (int)$b['blockid'] ?>"
                        data-label="<?= Html::encode($b['blocklabel']) ?>"
                        data-blocktype="<?= Html::encode($b['blocktype']) ?>"
                        data-visible="<?= (int)(!$b['visible'] ? 0 : 1) ?>"
                        data-show_title="<?= (int)(!$b['show_title'] ? 0 : 1) ?>"
                        data-blocktype="<?= Html::encode($b['blocktype']) ?>"
                        data-create-view="<?= (int)(!$b['create_view'] ? 0 : 1) ?>"
                        data-edit-view="<?= (int)(!$b['edit_view'] ? 0 : 1) ?>"
                        data-detail-view="<?= (int)(!$b['detail_view'] ? 0 : 1) ?>"
                        data-display-status="<?= (int)($b['display_status'] ? 1 : 0) ?>"
                        data-iscustom="<?= (int)(!$b['iscustom'] ? 0 : 1) ?>">
                        Edit Block
                    </button>
                    <button type="button"
                        class="add-btn update-block-fields-seq-btn"
                        data-blockid="<?= (int)$b['blockid'] ?>"
                        style="display:none;margin-left:6px;">
                        Update Field Sequence
                    </button>
                </div>

                <table class="fields-table" data-blockid="<?= (int)$b['blockid'] ?>">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th>Name</th>
                            <th>Header View</th>
                            <th>List View</th>
                            <th>Create View</th>
                            <th>Edit View</th>
                            <th>Readonly</th>
                            <th>Mandatory</th>
                            <th style="width:80px;">Seq</th>
                            <th style="width:70px;">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="field-tbody " data-blockid="<?= (int)$b['blockid'] ?>">
                        <?php foreach ($blockData['fields'] as $field): ?>
                            <tr class="field-row"
                                data-id="<?= $field->fieldid ?>"
                                data-label="<?= Html::encode($field->fieldlabel ?: $field->columnname) ?>"
                                data-blockid="<?= (int)$b['blockid'] ?>"
                                draggable="true">
                                <td class="drag-handle">≡</td>

                                <td><?= Html::encode($field->fieldlabel) ?></td>

                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->headerview == 1 ? 'checked' : '' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->list_view == 1 ? 'checked' : '' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->create_view == 1 ? 'checked' : '' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->edit_view == 1 ? 'checked' : '' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->readonly == 0 ? 'checked' : '' ?> disabled>
                                </td>

                                <td>
                                    <input type="checkbox" class="field-check" name="mandatory"
                                        <?= $field->mandatory == 1 ? 'checked' : '' ?> disabled>
                                </td>

                                <td>
                                    <span class="field-seq-label"><?= (int)$field->sequence ?></span>
                                    <input type="hidden"
                                        class="field-seq-input"
                                        data-id="<?= $field->fieldid ?>"
                                        value="<?= (int)$field->sequence ?>">
                                </td>
                                <td>
                                    <?php ?>
                                    <button class="btn btn-info edit-field-btn"
                                    data-blocklabel="<?= Html::encode($b['blocklabel']) ?>"
                                    data-blockType="<?= Html::encode($b['blocktype']) ?>"
                                        data-field='<?= json_encode([
                                                        'block'         => (int)$b['blockid'],
                                                        'fieldid'       => $field->fieldid,
                                                        'columnname'    => $field->columnname,
                                                        'tablename'     => $field->tablename,
                                                        'readonly'      => $field->readonly,
                                                        'mandatory'     => $field->mandatory,
                                                        'masseditable'  => $field->masseditable,
                                                        'summaryfield'  => $field->summaryfield,
                                                        'list_view'     => $field->list_view,
                                                        'uitype'        => $field->uitype,
                                                        'fieldlabel'    => $field->fieldlabel,
                                                        'sequence'    => $field->sequence,
                                                        'fieldname'     => $field->fieldname,
                                                        'maximumlength' => $field->maximumlength,
                                                        'defaultvalue'  => $field->defaultvalue,
                                                        'typeofdata'    => $field->typeofdata,
                                                        'description'   => $field->description,
                                                        'edit_view'     => $field->edit_view,
                                                        'create_view'   => $field->create_view,
                                                        'detail_view'   => $field->detail_view,
                                                        'is_conditional' => $field->is_conditional,
                                                        'single_edit'   => $field->single_edit,
                                                        'dynamic_class' => $field->dynamic_class,
                                                        'validator_name' => $field->validator_name,
                                                        'kanbanview'    => $field->kanbanview,
                                                        'headerview'    => $field->headerview,
                                                        'export'        => $field->export,
                                                        'import'        => $field->import,
                                                        'admin_edit_allow'        => $field->admin_edit_allow,
                                                        'kanbanviewfield' => $field->kanbanviewfield,
                                                        'displaytype' => $field->displaytype,
                                                    ]) ?>'>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <?php if (!empty($unassignedFields)): ?>
            <?php
            $unassignedBlockId = 0;
            ?>
            <div class="field-block"
                draggable="true"
                data-blockid="<?= $unassignedBlockId ?>"
                data-blocklabel="Unassigned Fields">

                <div class="field-block-header">
                    <span class="block-drag">≡</span>
                    <span class="block-title">
                        Unassigned Fields
                        (Seq: <span class="block-seq-label">0</span>)
                        <span class="block-id">(Virtual Block)</span>
                    </span>

                    <button type="button"
                        class="add-btn update-block-fields-seq-btn"
                        data-blockid="<?= $unassignedBlockId ?>"
                        style="display:none;margin-left:6px;">
                        Update Field Sequence
                    </button>
                </div>

                <table class="fields-table">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th>Name</th>
                            <th>Table Name</th>
                            <th>Header View</th>
                            <th>List View</th>
                            <th>Create View</th>
                            <th>Edit View</th>
                            <th>Readonly</th>
                            <th>Mandatory</th>
                            <th style="width:80px;">Seq</th>
                            <th style="width:70px;">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="field-tbody" data-blockid="<?= $unassignedBlockId ?>">
                        <?php foreach ($unassignedFields as $field): ?>
                            <tr class="field-row"
                                data-id="<?= (int)$field->fieldid ?>"
                                data-label="<?= Html::encode($field->fieldlabel ?: $field->columnname) ?>"
                                data-blockid="<?= $unassignedBlockId ?>"
                                draggable="true">
                                <td class="drag-handle">≡</td>

                                <td><?= Html::encode($field->fieldlabel) ?></td>
                                 <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->headerview ? '' : 'checked' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->list_view ? '' : 'checked' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->create_view ? '' : 'checked' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->edit_view ? '' : 'checked' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="readonly"
                                        <?= $field->readonly ? '' : 'checked' ?> disabled>
                                </td>
                                <td>
                                    <input type="checkbox" class="field-check" name="mandatory"
                                        <?= $field->mandatory ? '' : 'checked' ?> disabled>
                                </td>

                                <td>
                                    <span class="field-seq-label"><?= (int)$field->sequence ?></span>
                                    <input type="hidden"
                                        class="field-seq-input"
                                        data-id="<?= (int)$field->fieldid ?>"
                                        value="<?= (int)$field->sequence ?>">
                                </td>
                                <td>
                                    <button class="btn btn-info edit-field-btn"
                                        data-blockType="<?= Html::encode($b['blocktype']) ?>"
                                        data-systemblock="<?= ($b['blocklabel'] === 'SYSTEM GENERATED') ? 1 : 0 ?>"
                                        data-field='<?= json_encode([
                                                        'block'         => (int)$b['blockid'],
                                                        'fieldid'       => $field->fieldid,
                                                        'columnname'    => $field->columnname,
                                                        'tablename'     => $field->tablename,
                                                        'readonly'      => $field->readonly,
                                                        'mandatory'     => $field->mandatory,
                                                        'masseditable'  => $field->masseditable,
                                                        'summaryfield'  => $field->summaryfield,
                                                        'list_view'     => $field->list_view,
                                                        'uitype'        => $field->uitype,
                                                        'fieldlabel'    => $field->fieldlabel,
                                                        'sequence'    => $field->sequence,
                                                        'fieldname'     => $field->fieldname,
                                                        'maximumlength' => $field->maximumlength,
                                                        'defaultvalue'  => $field->defaultvalue,
                                                        'typeofdata'    => $field->typeofdata,
                                                        'description'   => $field->description,
                                                        'edit_view'     => $field->edit_view,
                                                        'create_view'   => $field->create_view,
                                                        'detail_view'   => $field->detail_view,
                                                        'is_conditional' => $field->is_conditional,
                                                        'single_edit'   => $field->single_edit,
                                                        'dynamic_class' => $field->dynamic_class,
                                                        'validator_name' => $field->validator_name,
                                                        'kanbanview'    => $field->kanbanview,
                                                        'headerview'    => $field->headerview,
                                                        'export'        => $field->export,
                                                        'import'        => $field->import,
                                                        'admin_edit_allow'        => $field->admin_edit_allow,
                                                        'kanbanviewfield' => $field->kanbanviewfield,
                                                        'displaytype' => $field->displaytype,
                                                    ]) ?>'>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>


</div>

<?php
echo $this->render('_field_form', ['csrfToken' => $csrfToken, 'uiTypes' => $uiTypes, 'blockList' => $blockList, 'dataTypes' => $dataTypes, 'dynamicClass' => $dynamicClass]);
echo $this->render('_block_form', ['tab' => $tab]);
?>

<?php
$editFieldUrl       = Url::to(['modulesetting/edit-field']);
$saveBlockUrl        = Url::to(['modulesetting/save-block']);
$addFieldUrl = Url::to(['modulesetting/add-field']);
$updateFieldSeqUrl = Url::to(['modulesetting/update-field-sequence']);
$updateBlockSeqUrl = Url::to(['modulesetting/update-block-sequence']);
$fieldsUrl    = Url::to(['modulesetting/fields']);
$js = <<<JS
var csrfTokenField    = '$csrfToken';
var csrfParamField    = '$csrfParam';
var editFieldUrl      = '$editFieldUrl';
var updateBlockSeqUrl = '$updateBlockSeqUrl';
var saveBlockUrl       = '$saveBlockUrl';
var addFieldUrl      = '$addFieldUrl';
var updateFieldSeqUrl = '$updateFieldSeqUrl';
var fieldsBaseUrl = '$fieldsUrl';
var currentTabId = '$currentTabId';
$(document).on('change', '#tab-switcher-select', function () {
    var tabid = $(this).val();
    if (!tabid) return;
    window.location.href = fieldsBaseUrl + '?tabid=' + encodeURIComponent(tabid);
});



var originalFieldOrderByBlock = {};

function captureOriginalFieldOrder() {
    originalFieldOrderByBlock = {};
    $('.field-tbody').each(function () {
        var blockId = $(this).data('blockid');
        if (!blockId) return;

        var ids = [];
        $(this).find('.field-row').each(function () {
            ids.push($(this).data('id'));
        });
        originalFieldOrderByBlock[blockId] = ids.join(',');
    });
}
var originalBlockOrder = '';

function captureOriginalBlockOrder() {
    var ids = [];
    $('.field-block').each(function () {
        var blockId = $(this).data('blockid');
        if (!blockId) return;
        ids.push(blockId);
    });
    originalBlockOrder = ids.join(',');
}

$(function () {
    captureOriginalFieldOrder();
    captureOriginalBlockOrder();
});

$(document).on('click', '.edit-field-btn', function () {
    var raw = $(this).attr('data-field');
    var isSystemBlock = $(this).attr('data-blocklabel') === 'SYSTEM GENERATED' ? 1 : 0;
    var isMultipleBlock = $(this).attr('data-blockType') === 'Multiple' ? 1 : 0;
    try {
        var f = JSON.parse(raw);
        $('#modal_fieldid').val(f.fieldid);
        $('#modal_columnname').val(f.columnname);
        $('#modal_tablename').val(f.tablename);
        $('#field-readonly').prop('checked', f.readonly == 0);
        $('#field-presence').prop('checked', f.presence == 1);
        $('#field-mandatory').prop('checked', f.mandatory == 1);
        $('#field-masseditable').prop('checked', f.masseditable == 1);
        $('#field-summaryfield').prop('checked', f.summaryfield == 0);
        $('#field-list-view').prop('checked', f.list_view == 1);
        $('#field-uitype').val(f.uitype);
        $('#field-block').val(f.block);
        $('#modal_fieldlabel').val(f.fieldlabel);
        $('#modal_fieldname').val(f.fieldname);
        $('#modal_maximumlength').val(f.maximumlength);
        $('#modal_sequence').val(f.sequence);
        $('#modal_displaytype').val(f.displaytype);
        $('#modal_defaultvalue').val(f.defaultvalue);
        $('#modal_description').val(f.description);
        
        $('#field-edit-view').prop('checked', f.edit_view == 1);
        $('#field-create-view').prop('checked', f.create_view == 1);
        $('#field-detail-view').prop('checked', f.detail_view == 1);
        $('#field-kanban-view').prop('checked', f.kanbanview == 1);
        $('#field-header-view').prop('checked', f.headerview == 1);
        $('#field-is-conditional').prop('checked', f.is_conditional == 1);
        $('#field-is-singleedit').prop('checked', f.single_edit == 0);
        $('#field-export').prop('checked', f.export == 1);
        $('#field-import').prop('checked', f.import == 1);
        $('#field-admin_edit_allow').prop('checked', f.admin_edit_allow == 1);
        $('#field-kanbanviewfield').prop('checked', f.kanbanviewfield == 1);
        $('#modal_dynamic_class').val(f.dynamic_class);
        $('#modal_validator_name').val(f.validator_name);
        $('#fieldModal').css('display', 'flex');
        var tdParts  = (f.typeofdata || '').split('~');
        var baseType = tdParts[0] || '';  
        var flag     = (f.mandatory == 1) ? 'M' : 'O';

        var finalTypeOfData = baseType ? (baseType + '~' + flag) : '';

        var selectDataType = $('#modal-typeofdata');

        selectDataType.find('option').each(function () {
            var val  = $(this).val() || '';
            var base = val.split('~')[0] || val;

            if (base === baseType) {
                $(this)
                    .val(finalTypeOfData)     
                    .text(baseType + ' ~ ' + flag); 
            }
        });

        selectDataType.val(finalTypeOfData);
        if(isMultipleBlock && isMultipleBlock === 1){
            $('#modal_dynamic_class').prop('disabled', false);
        }else{
            $('#modal_dynamic_class').prop('disabled', true);
        }
    } catch (e) {
        console.warn(e);
    }
});

$(document).on('change', '#field-uitype', function () {
    var uitype = $('#field-uitype').val();
    $('#modal_dynamic_class').prop('disabled', uitype != '24');
});

$(document).on('change', '#field-mandatory', function () {
    var selectMand = $('#modal-typeofdata');

    var curVal  = selectMand.val() || '';
    var curBase = curVal.split('~')[0]; 

    var isMandatory = $(this).is(':checked'); 
    var flag        = isMandatory ? 'M' : 'O';

    selectMand.find('option').each(function () {
        var val  = $(this).val() || '';
        var base = val.split('~')[0];  

        if (!base) return;

        var newVal   = base + '~' + flag;
        var newLabel = base + ' ~ ' + flag;

        $(this).val(newVal).text(newLabel);
    });

    if (curBase) {
        selectMand.val(curBase + '~' + flag);
    }
});

$(document).on('click', '#closeFieldModal, #cancelModalBtn, .field-modal-backdrop', function () {
    $('#fieldModal').hide();
});

$('#fieldSaveBtn').on('click', function () {
    var fieldid = $('#modal_fieldid').val();
    var isNew   = !fieldid;
    $('#modal_dynamic_class').disabled = false;
    $('#modal-typeofdata').disabled = false;
    var postUrl = isNew ? addFieldUrl : editFieldUrl;
    var data = {
        columnname:    $('#modal_columnname').val(),
        tablename:     $('#modal_tablename').val(),
        uitype:        $('#field-uitype').val(),
        fieldlabel:    $('#modal_fieldlabel').val(),
        fieldname:     $('#modal_fieldname').val(),
        maximumlength: $('#modal_maximumlength').val(),
        defaultvalue:  $('#modal_defaultvalue').val(),
        typeofdata:    $('#modal-typeofdata').val(),
        description:   $('#modal_description').val(),
        block:         $('#field-block').val(),
        readonly:  $('#field-readonly').is(':checked') ? 0 : 1,     
        presence:  $('#field-presence').is(':checked') ? 1 : 0,   
        mandatory:  $('#field-mandatory').is(':checked') ? 1 : 0,
        masseditable:  $('#field-masseditable').is(':checked') ? 1 : 0,
        summaryfield:  $('#field-summaryfield').is(':checked') ? 1 : 0,
        list_view:  $('#field-list-view').is(':checked') ? 1 : 0,
        displaytype: $('#modal_displaytype').val(),
        edit_view:  $('#field-edit-view').is(':checked') ? 1 :0,
        create_view:  $('#field-create-view').is(':checked') ? 1 : 0,
        detail_view:  $('#field-detail-view').is(':checked') ? 1 : 0,
        kanbanview:  $('#field-kanban-view').is(':checked') ? 1 : 0,
        headerview:  $('#field-header-view').is(':checked') ? 1 : 0,
        is_conditional:  $('#field-is-conditional').is(':checked') ? 1 : 0,
        single_edit:  $('#field-is-singleedit').is(':checked') ? 0 :1,
        kanbanviewfield:  $('#field-kanbanviewfield').is(':checked') ? 1 : 0,
        dynamic_class: $('#modal_dynamic_class').val(),
        validator_name: $('#modal_validator_name').val(),
        export:  $('#field-export').is(':checked') ? 1 : 0,
        import:  $('#field-import').is(':checked') ? 1 : 0,
        admin_edit_allow:  $('#field-admin_edit_allow').is(':checked') ? 1 : 0,
  };

    if (isNew) {
        data['tabid'] = $('#modal_tabid').val();     
        data['name']  = data.columnname;            
        data['value'] = data.defaultvalue;           
    } else {
        data['fieldid'] = fieldid;                
    }

    data[csrfParamField] = csrfTokenField;

    if ($('#modal_columnname').is(':visible') && !data.columnname) {
        alert('Please enter column name!');
        $('#modal_columnname').focus();
        return;
    }

    $.ajax({
        type: 'POST',
        url: postUrl,
        data: data,
        dataType: 'json',
        success: function (resp) {
            if (resp.success) {
                alert(isNew ? 'Field added successfully!' : 'Field updated successfully!');
                location.reload();
            } else {
                alert('Error saving field');
            }
        },
        error: function () {
            alert('Error occurred. Please try again.');
        }
    });
});


$('#field-search').on('input', function () {
    var q = $(this).val().toLowerCase().trim();

    $('#block-container .field-block').each(function () {
        var blockFieldSearch      = $(this);
        var blockTitle  = blockFieldSearch.find('.block-title').text().toLowerCase();
        var showBlock   = blockTitle.indexOf(q) !== -1;
        var anyFieldVis = false;

        blockFieldSearch.find('.field-row').each(function () {
            var label = ($(this).data('label') || '').toString().toLowerCase();
            var match = label.indexOf(q) !== -1;
            $(this).toggle(match || !q);
            if (match) anyFieldVis = true;
        });

        blockFieldSearch.toggle(showBlock || anyFieldVis || !q);
    });
});


let draggedField = null;
let fieldSnapshotHtml = null;
let fieldOriginalSeq = null;
let draggedFieldBlock = null;
function takeFieldSnapshot() {
    fieldSnapshotHtml = $('#block-container').html();
    fieldOriginalSeq = {};
    $('.field-row').each(function () {
        var id  = $(this).data('id').toString();
        var seq = parseInt($('.field-seq-input[data-id=' + id + ']').val(), 10) || 0;
        fieldOriginalSeq[id] = seq;
    });
}

function restoreFieldSnapshot() {
    if (fieldSnapshotHtml !== null) {
        $('#block-container').html(fieldSnapshotHtml);
        fieldSnapshotHtml = null;
        fieldOriginalSeq = null;
    }
}

$(document).on('dragstart', '.field-row', function (e) {
    var ev   = e.originalEvent || e;
    var rowDropStart = $(this);

    draggedField = rowDropStart[0];
     var target = e.target;
    console.log('Drag started on:', target);
    if ($(target).hasClass('field-row')) {
        console.log('Dragging field row:', $(draggedField).data('id'));
        draggedField = target;
        var blockId = $(draggedField).data('blockid');
        console.log('.update-block-fields-seq-btn[data-blockid="' + blockId + '"]');
        $('.update-block-fields-seq-btn').hide();
        $('.update-block-fields-seq-btn[data-blockid="' + blockId + '"]').show();
    }
    var blockId = rowDropStart.data('blockid');
    // $('.update-block-fields-seq-btn').hide();
    $('.update-block-fields-seq-btn[data-blockid="' + blockId + '"]').show();

    if (ev.dataTransfer) {
        ev.dataTransfer.effectAllowed = 'move';
        try { ev.dataTransfer.setData('text/plain', 'field'); } catch (err) {}
    }
});

document.addEventListener('dragover', function (e) {
    if (!draggedField) return;

    var targetDrop = $(e.target).closest('.field-row, .field-tbody');
    if (!targetDrop.length || !targetDrop.closest('.field-tbody').length) return;

    e.preventDefault();

    if (targetDrop.hasClass('field-row')) {
        var target = targetDrop[0];
        if (target === draggedField) return;

        var rect   = target.getBoundingClientRect();
        var offset = e.clientY - rect.top;
        var tbody  = target.parentNode; 

        if (offset < rect.height / 2) {
            tbody.insertBefore(draggedField, target);
        } else {
            tbody.insertBefore(draggedField, target.nextSibling);
        }
    }
}, false);

document.addEventListener('drop', function (e) {
    if (!draggedField) return;

    e.preventDefault();

    var tbodyfield = $(e.target).closest('.field-tbody');

    if (!tbodyfield.length) {
        var blocktfield = $(e.target).closest('.field-block');
        if (blocktfield.length) {
            tbodyfield = blocktfield.find('.field-tbody').first();
        }
    }

    if (tbodyfield.length) {
        var newBlockId = tbodyfield.data('blockid');

        if (!$.contains(tbodyfield[0], draggedField)) {
            tbodyfield[0].appendChild(draggedField);
        }

        $(draggedField)
            .data('blockid', newBlockId)
            .attr('data-blockid', newBlockId);

        // $('.update-block-fields-seq-btn').hide();
        $('.update-block-fields-seq-btn[data-blockid="' + newBlockId + '"]').show();
    }

    draggedField = null;
}, false);

function updateFieldSequencesForBody(tbodySeq) {
    var seq = 1;
    tbodySeq.find('.field-row').each(function () {
        var id = $(this).data('id').toString();
        $(this).find('.field-seq-label').text(seq);
        $('.field-seq-input[data-id=' + id + ']').val(seq);
        seq++;
    });
}

function handleFieldDropConfirmation(rowDrop) {
    var id = rowDrop.data('id').toString();

    var tbodyDrops = rowDrop.closest('tbody');
    var seq = 1;
    var newSeq = null;
    tbodyDrops.find('.field-row').each(function () {
        var fid = $(this).data('id').toString();
        if (fid === id) newSeq = seq;
        seq++;
    });

    var oldSeq = fieldOriginalSeq && fieldOriginalSeq[id]
        ? fieldOriginalSeq[id]
        : parseInt($('.field-seq-input[data-id=' + id + ']').val(), 10) || 0;

    var msg = 'Do you want to change sequence for this field from '
        + oldSeq + ' to ' + newSeq + '?';

    if (!confirm(msg)) {
        restoreFieldSnapshot();
        return;
    }

    updateFieldSequencesInDOM();
}

function updateFieldSequencesInDOM() {
    $('.field-tbody').each(function () {
        var seq = 1;
        $(this).find('.field-row').each(function () {
            var id = $(this).data('id').toString();
            $(this).find('.field-seq-label').text(seq);
            $('.field-seq-input[data-id=' + id + ']').val(seq);
            seq++;
        });
    });
}
$(document).on('dragover', '.field-tbody', function (e) {
    e.preventDefault();
    e.originalEvent.dataTransfer.dropEffect = 'move';
    $(this).addClass('drag-over');
});

$(document).on('dragleave', '.field-tbody', function (e) {
    if (e.target !== this) return; 
    $(this).removeClass('drag-over');
});

$(document).on('drop', '.field-tbody', function (e) {
    e.preventDefault();
    $('.field-tbody').removeClass('drag-over');
    if (!draggedField) return;

    var targetBodyTbody = $(this);
    var dragBodyTbody   = $(draggedField).closest('.field-tbody');

    if (targetBodyTbody[0] === dragBodyTbody[0] && targetBodyTbody.find('.field-row').length > 0) {
        return;
    }

    targetBodyTbody.append(draggedField);

    updateFieldSequencesForBody(dragBodyTbody);
    if (targetBodyTbody[0] !== dragBodyTbody[0]) {
        updateFieldSequencesForBody(targetBodyTbody);
    }
});

$(document).on('click', '.update-block-fields-seq-btn', function () {
    var updates = [];
    var ids = [];
    var blockId = $(this).data('blockid');
    var pos = 1;
    if (!blockId || blockId == 0 || blockId == '') {
        alert('Please move these into block');
        return;
    }
    
    var tableBlock = $('.field-block[data-blockid="' + blockId + '"]');
    tableBlock.find('.field-row').each(function () {
        var id = $(this).data('id');
        if (id) { 
            ids.push(id);
            var seq = pos++; 
            updates.push({ id: id, sequence: seq });
        }
    });

    if (!updates.length) {
        alert('No fields found.');
        return;
    }
    
    var currentKey = ids.join(',');
    var originalKey = originalFieldOrderByBlock[blockId] || '';
    
    if (currentKey === originalKey) {
        alert('No field sequence change to update.');
        return;
    }
    
    if (!confirm('Are you sure you want to update field sequences for this Block?')) {
        location.reload();
        return;
    }
    
    updates[csrfParamField] = csrfTokenField;
    
    $.ajax({
        url: updateFieldSeqUrl,
        type: 'POST',
        data: {
            [csrfParamField]: csrfTokenField,  
            blockid: blockId,
            tabid: currentTabId,              
            items: JSON.stringify(updates)     
        },
        dataType: 'json',
        success: function (resp) {
            stopLoading();
            if (resp && resp.success) {  
                originalFieldOrderByBlock[blockId] = currentKey;
                $('.update-block-fields-seq-btn[data-blockid="' + blockId + '"]').hide();
            } 
            else {
                alert((resp) || 'Failed to update field sequence'); 
                location.reload();
            }
        }
        ,
        error: function (resp) {
            stopLoading();
            alert(resp);
            location.reload();
        }
    });
});

/* ---------- Block drag & drop (reorder blocks only) ---------- */
$(document).on('dragstart', '.field-block', function (e) {
    draggedFieldBlock = this;
    e.originalEvent.dataTransfer.effectAllowed = 'move';
    var target = e.target;

    $('#update-block-seq-btn').show();
});

$(document).on('dragover', '.field-block', function (e) {
    e.preventDefault();
    e.originalEvent.dataTransfer.dropEffect = 'move';
});

$(document).on('drop', '.field-block', function (e) {
    e.preventDefault();
    if (draggedFieldBlock && draggedFieldBlock !== this) {
        $(this).before(draggedFieldBlock);
        draggedFieldBlock = null;
        return;
    }

    // Existing logic for dropping a field into an empty block
    if (draggedField) {
        var targetBlockSeq = $(this);
        var targetBodySeq  = targetBlockSeq.find('.field-tbody').first();
        var dragBodySw    = $(draggedField).closest('.field-tbody');

        targetBodySeq.append(draggedField);

        updateFieldSequencesForBody(dragBodySw);
        if (targetBodySeq[0] !== dragBodySw[0]) {
            updateFieldSequencesForBody(targetBodySeq);
        }
        draggedField = null;
    }
});



function updateBlockSequencesInDOM() {
    var seq = 1;
    $('#block-container .field-block').each(function () {
        $(this).find('.block-seq-label').text(seq);
        $(this).data('sequence', seq);
        seq++;
    });
}


$('#add-block-btn').on('click', function () {
    $('#block_id').val('');          
    $('#block_label').val('');
    $('#block_type').val('default');
    $('#block_visible').prop('checked', true);
    $('#block_show_title').prop('checked', true);
    $('#blockModal').css('display', 'flex');
});

$(document).on('click', '[data-close-block-modal]', function () {
    $('#blockModal').hide();
});

$('#save-block-btn').on('click', function () {
    var tabid      = $('#block_tabid').val();
    var blockid    = $('#block_id_edit').val();
    var label      = $('#block_label').val();
    var blocktype  = $('#block_type').val() || 'default';
    var visible    = $('#block_visible').is(':checked') ? 0 : 1;
    var show_title = $('#block_show_title').is(':checked') ? 0 : 1;

    if (!label) {
        alert('Block label is required.');
        return;
    }

    var data = {};
    data['blockid']     = blockid;  
    data['tabid']       = tabid;
    data['blocklabel']  = label;
    data['blocktype']   = blocktype;
    data['visible']     = visible;
    data['show_title']  = show_title;
    data[csrfParamField]= csrfTokenField;
    data['visible']  = $('#block-visible').is(':checked') ? 0 : 1; 
    data['show_title']  = $('#block-show-title').is(':checked') ? 0 : 1;
    data['create_view']  = $('#block-create-view').is(':checked') ? 0 : 1;
    data['edit_view']  = $('#block-edit-view').is(':checked') ? 0 : 1;
    data['detail_view']  = $('#block-detail-view').is(':checked') ? 0 : 1;
    data['display_status']  = $('#block-display-status').is(':checked') ? 1 : 0;
    data['iscustom']  = $('#block-iscustom').is(':checked') ? 0 : 1;
    
    $.post(saveBlockUrl, data, function (resp) {
        if (resp && resp.success) {
            alert(blockid ? 'Block updated successfully.' : 'Block added successfully.');
            location.reload();
        } else {
            alert((resp && resp.message) || 'Failed to save block.');
        }
    }, 'json').fail(function () {
        alert('Error while saving block.');
    });
});
$('.add-field-btn').on('click', function () {
    var tabid = $(this).data('tabid');

    $('#modal_fieldid').val('');
    $('#modal_columnname').val('');
    $('#modal_tablename').val('');
    $('#field_readonly').prop('checked', true);
    $('#field_mandatory').prop('checked', true);
    $('#modal_uitype').val('');
    $('#modal_fieldlabel').val('');
    $('#modal_fieldname').val('');
    $('#modal_maximumlength').val('');
    $('#modal_defaultvalue').val('');
    $('#modal_typeofdata').val('');
    $('#modal_description').val('');
    $('#field-visible').prop('checked', true);
    $('#field-show-title').prop('checked', true);
    $('#field-create-view').prop('checked', true);
    $('#field-edit-view').prop('checked', true);
    $('#field-detail-view').prop('checked', true);
    $('#field-display-status').prop('checked', true);
    $('#field-iscustom').prop('checked', true);

    

    if (!$('#modal_tabid').length) {
        $('<input>').attr({
            type: 'hidden',
            id: 'modal_tabid'
        }).appendTo('#fieldModal');
    }
    $('#modal_tabid').val(tabid);

    $('#fieldModal').css('display', 'flex');
});

$('#save-field-btn').on('click', function () {
    startLoading();

    var id        = $('#field-id').val();
    var fieldname = $('#field-name').val();
    var fieldlabel= $('#field-label').val();
    var tablename = $('#field-tablename').val();
    var block     = $('#field-block').val();
    var uitype    = $('#field-uitype').val();
    var typeofdata= $('#modal-typeofdata').val();
    var readonly  = $('#field-readonly').is(':checked') ? 0 : 1;
    var mandatory = $('#field-mandatory').is(':checked') ? 1 : 0;
    var defaultvalue = $('#field-default-value').val();
    var maximumlength = $('#field-maximum-length').val();
    var description   = $('#field-description').val();
    var formData = new FormData();
    formData.append(csrfParamField, csrfTokenField);
    formData.append('fieldid', $('#field-id').val());
    formData.append('fieldname', $('#field-name').val());
    formData.append('fieldlabel', $('#field-label').val());
    formData.append('tablename', $('#field-tablename').val());
    formData.append('blockid', $('#field-block').val());
    formData.append('uitype', $('#field-uitype').val());
    formData.append('typeofdata', $('#modal-typeofdata').val());

    formData.append('readonly', $('#field-readonly').is(':checked') ? 0 : 1);
    formData.append('mandatory', $('#field-mandatory').is(':checked') ? 1 : 0);
    formData.append('defaultvalue', $('#field-default-value').val());
    formData.append('maximumlength', $('#field-maximum-length').val());
    formData.append('description', $('#field-description').val());
    formData.append('displaytype', $('#modal_displaytype').val());
    formData.append('edit_view', $('#field-edit-view').is(':checked') ? 1 : 0);
    formData.append('create_view', $('#field-create-view').is(':checked') ? 1 : 0);
    formData.append('detail_view', $('#field-detail-view').is(':checked') ? 1 : 0);
    formData.append('kanbanview', $('#field-kanban-view').is(':checked') ? 1 : 0);
    formData.append('headerview', $('#field-header-view').is(':checked') ? 1 : 0);
    formData.append('is_conditional', $('#field-is-conditional').is(':checked') ? 1 : 0);
    formData.append('single_edit', $('#field-is-singleedit').is(':checked') ? 0 : 1);
    formData.append('kanbanviewfield', $('#field-kanbanviewfield').is(':checked') ? 1 : 0);
    formData.append('dynamic_class', $('#field-dynamic-class').val());
    formData.append('validator_name', $('#field-validator-name').val());
    formData.append('export', $('#field-export').is(':checked') ? 1 : 0);
    formData.append('import', $('#field-import').is(':checked') ? 1 : 0);
    formData.append('admin_edit_allow', $('#field-admin_edit_allow').is(':checked') ? 1 : 0);
    formData.append('masseditable', $('#field-masseditable').is(':checked') ? 1 : 0);
    formData.append('summaryfield', $('#field-summaryfield').is(':checked') ? 1 : 0);
    formData.append('list_view', $('#field-list-view').is(':checked') ? 1 : 0);
    formData.append('presence', $('#field-presence').is(':checked') ? 1 : 0);  
    formData.append('tabid', $('#modal_tabid').val());
    formData.append('columnname', fieldname);
    console.log(formData,'formData');
    if (!fieldname) {
        alert('Field name is required.');
        stopLoading();
        return;
    }

    var url = id ? editFieldUrl : addFieldUrl;

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
            stopLoading();
            if (resp && resp.success) {
                location.reload();
            } else {
                alert((resp && resp.message) || 'Failed to save field');
            }
        },
        error: function () {
            stopLoading();
            alert('Error while saving field');
        }
    });
});

$('#update-block-seq-btn').on('click', function () {
    var ids = [];
    var seqData = [];
    var pos = 1;
    
    $('.field-block').each(function () {
        var blockId = $(this).data('blockid');
        if(blockId != 0 && blockId != null && blockId != ''){
            ids.push(blockId);
        }
        seqData.push({
            blockid:  blockId,
            sequence: pos++
        });
    });

    if (!seqData.length) {
        alert('No blocks to update.');
        return;
    }
    
    var currentKey = ids.join(',');
   
    if (currentKey === originalBlockOrder) {
        alert('No block sequence change to update.');
        return;
    }
    if (!confirm('Are you sure you want to Block Order!!!')) {
        location.reload();
        return;
    }
    startLoading();
    $.ajax({
        url: updateBlockSeqUrl,      
        type: 'POST',
        data: {
            [csrfParamField]: csrfTokenField,
            tabid: currentTabId,       
            items: JSON.stringify(seqData)
        },
        dataType: 'json',
        success: function (res) {
            stopLoading();
            if (res && res.success) {
                originalBlockOrder = currentKey;   
                window.location.reload();
            } else {
                alert((res && res.message) || 'Failed to update block sequence');
            }
        },
        error: function () {
            stopLoading();
            alert('Error while updating block sequence');
        }
    });
});

(function initSelect2() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        setTimeout(initSelect2, 100);
    } else {
        $('#tab-switcher-select').select2({
            placeholder: 'Choose Field',
            allowClear: true,
            width: '100%'
        });
    }
})();
$(document).on('click', '.edit-block-btn', function () {
    var btnEditBlockBtn = $(this);
    $('#block_id').val(btnEditBlockBtn.data('blockid'));   
    $('#block_label').val(btnEditBlockBtn.data('label') || '');
    $('#block_id_edit').val(btnEditBlockBtn.data('blockid') || '');
    $('#block_type').val(btnEditBlockBtn.data('blocktype') || 'default');
    $('#block-visible').prop('checked', btnEditBlockBtn.data('visible') ==0);
    $('#block-show-title').prop('checked', btnEditBlockBtn.data('show_title') ==0);
    $('#block-create-view').prop('checked', btnEditBlockBtn.data('create-view') ==0);
    $('#block-edit-view').prop('checked', btnEditBlockBtn.data('edit-view') ==0);
    $('#block-detail-view').prop('checked', btnEditBlockBtn.data('detail-view') ==0);
    $('#block-display-status').prop('checked', btnEditBlockBtn.data('display-status') ==1);
    $('#block-iscustom').prop('checked', btnEditBlockBtn.data('iscustom') ==0);
    $('#blockModal').css('display', 'flex');
});
$(document).ready(function () {
    $('.fields-table').hide();

    $(document).on('click', '.field-block-header', function (e) {
        if ($(e.target).closest('.edit-block-btn').length) return;

        let block = $(this).closest('.field-block');
        let table = block.find('.fields-table');

        table.slideToggle(200);
    });
});

JS;

$this->registerJs($js, \yii\web\View::POS_END);
$this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>