<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title   = 'Detail Edit Setting';
$baseUrl       = Url::base();
$csrfToken     = Yii::$app->request->csrfToken;
$csrfParam     = Yii::$app->request->csrfParam;

$this->registerCssFile('@web/thememain/css/flatpickr.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css',   ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css',  ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/select2.min.js',        ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$record          = $record ?: [];
$des_id          = $record['des_id']          ?? '';
$tabid           = $record['tabid']           ?? '';
$module_name     = $record['module_name']     ?? '';
$stage_field     = $record['stage_field']     ?? '';
$stage_value     = $record['stage_value']     ?? '';
$view_allow      = !empty($record['view_allow']);
$edit_allow      = !empty($record['edit_allow']);
$admin_allow     = !empty($record['admin_allow']);
$super_allow     = !empty($record['superadmin_allow']);

$selectedStageValues = !empty($record['stage_value']) ? explode(',', $record['stage_value']) : [];
$selectedRoles       = !empty($record['user_role']) ? explode(',', $record['user_role']) : [];
$selectedUsers       = !empty($record['user_id']) ? explode(',', $record['user_id']) : [];
$this->registerJs("
    window.detaileditInitialStageField  = " . json_encode($stage_field) . ";
    window.detaileditInitialStageValues = " . json_encode($selectedStageValues) . ";
", \yii\web\View::POS_HEAD);

?>

<div class="container-fluid detailedit-page">
    <div class="row">
        <div class="col-lg-10 col-md-12">
            <div class="card detailedit-card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Edit Setting</h4>
                </div>

                <div class="card-body">
                    <form id="detailedit-form"
                        method="post"
                        action="<?= Url::to(['modulesetting/detailedit-' . $mode, 'id' => $des_id]) ?>">

                        <input type="hidden"
                            name="<?= Html::encode($csrfParam) ?>"
                            value="<?= Html::encode($csrfToken) ?>">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tab <span class="text-danger">*</span></label>
                                <select name="tabid" id="tabid"
                                    class="form-control custom-input  select2-single">
                                    <option value="">Select Tab</option>
                                    <?php foreach ($tabList as $tab): ?>
                                        <option value="<?= $tab['tabid'] ?>"
                                            data-tablename="<?= Html::encode($tab['tablename']) ?>"
                                            data-name="<?= Html::encode($tab['name']) ?>"
                                            <?= $tabid == $tab['tabid'] ? 'selected' : '' ?>>
                                            <?= Html::encode($tab['tablabel']) ?> (<?= Html::encode($tab['name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Module Name</label>
                                <input type="text"
                                    name="module_name"
                                    id="module_name"
                                    class="form-control custom-input select2-single"
                                    value="<?= Html::encode($module_name) ?>"
                                    readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Title</label>
                                <input type="text"
                                    name="title"
                                    id="title"
                                    class="form-control custom-input"
                                    value="<?= Html::encode($record['title'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea name="description"
                                    id="description"
                                    class="form-control custom-input"
                                    rows="3"><?= Html::encode($record['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Stage Field</label>
                                <select name="stage_field"
                                    id="stage_field"
                                    class="form-control custom-input  select2-single"
                                    data-initial="<?= Html::encode($stage_field) ?>">

                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label" for="stage_value">Stage Value</label>
                                <select id="stage_value"
                                    class="form-control select2-single"
                                    name="stage_value"
                                    data-initial='<?= json_encode($selectedStageValues) ?>'>
                                </select>
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="control-label" for="user_role">User Role</label>
                                <select id="user_role"
                                    class="form-control select2-multi"
                                    name="user_role[]"
                                    multiple
                                    size="4">
                                    <?php foreach ($roleList as $role): ?>
                                        <option value="<?= $role['roleid'] ?>"
                                            <?= in_array($role['roleid'], $selectedRoles) ? 'selected' : '' ?>>
                                            <?= Html::encode($role['rolename']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-block"></div>
                            </div>

                            <div class="form-group col-lg-6 col-md-6">
                                <label class="control-label" for="user_id">User</label>
                                <select id="user_id"
                                    class="form-control select2-multi"
                                    name="user_id[]"
                                    multiple
                                    size="4">
                                    <?php foreach ($userList as $user): ?>
                                        <option value="<?= $user['id'] ?>"
                                            <?= in_array($user['id'], $selectedUsers) ? 'selected' : '' ?>>
                                            <?= Html::encode($user['first_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <!-- Row: Checkboxes -->
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input type="checkbox"
                                        name="view_allow"
                                        id="view_allow"
                                        class="form-check-input"
                                        <?= $view_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="view_allow">View Allow</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input type="checkbox"
                                        name="edit_allow"
                                        id="edit_allow"
                                        class="form-check-input"
                                        <?= $edit_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="edit_allow">Edit Allow</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input type="checkbox"
                                        name="admin_allow"
                                        id="admin_allow"
                                        class="form-check-input"
                                        <?= $admin_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="admin_allow">Admin Allow</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input type="checkbox"
                                        name="superadmin_allow"
                                        id="superadmin_allow"
                                        class="form-check-input"
                                        <?= $super_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="superadmin_allow">Superadmin Allow</label>
                                </div>
                            </div>
                        </div>

                        <!-- Footer buttons -->
                        <div class="detailedit-footer mt-3 text-right">
                            <button type="submit" class="btn btn-primary custom-save-btn">
                                <i class="fa fa-save"></i> Save
                            </button>
                            <a href="<?= Url::to(['modulesetting/index']) ?>"
                                class="btn btn-secondary">
                                Close
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .detailedit-card {
        background: #ffffff;
        border-radius: 8px;
        padding: 0;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.08);
        border: 1px solid #e3e6ef;
    }

    .detailedit-card .card-header {
        padding: 10px 16px;
        border-bottom: 1px solid #f0f2f5;
    }

    .detailedit-card .card-body {
        padding: 16px 18px;
    }

    .detailedit-card h4 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2933;
    }

    .detailedit-page .form-group {
        margin-bottom: 12px;
    }

    .detailedit-page label {
        font-weight: 500;
        font-size: 13px;
        color: #4b5675;
        margin-bottom: 4px;
    }

    .detailedit-page .form-control {
        border-radius: 4px;
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid #ccd3e0;
    }

    .detailedit-page .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.15);
    }

    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        border-radius: 4px;
        border: 1px solid #ccd3e0;
        min-height: 34px;
        font-size: 13px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        padding-left: 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4e73df;
        border: none;
        color: #fff;
        padding: 2px 6px;
        font-size: 12px;
        margin-top: 3px;
    }

    .detailedit-footer .btn-primary {
        padding: 5px 14px;
        font-size: 13px;
    }

    .detailedit-footer .btn-secondary {
        padding: 5px 12px;
        font-size: 13px;
    }

    @media (max-width: 767.98px) {
        .detailedit-card .card-body {
            padding: 12px 10px;
        }
    }
</style>

<?php

$js = <<<JS

function initDetailEditForm() {
    if (\$.fn.select2) {
        // Multi-selects (User Role, User)
        \$('#detailedit-form .select2-multi').each(function () {
            var \$el = $(this);
            if (\$el.data('select2')) {
                \$el.select2('destroy');
            }
            \$el.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select value(s)'
            });
        });

        // Single-select: Tab
        var \$tab = $('#tabid');
        if (\$tab.length) {
            if (\$tab.data('select2')) {
                \$tab.select2('destroy');
            }
            \$tab.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select Tab',
                minimumResultsForSearch: 0
            });
        }
        var \$sf = $('#stage_field');
        if (\$sf.length) {
            if (\$sf.data('select2')) {
                \$sf.select2('destroy');
            }
            \$sf.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select stage value',
                minimumResultsForSearch: 0
            });
        }

        var \$sv = $('#stage_value');
        if (\$sv.length) {
            if (\$sv.data('select2')) {
                \$sv.select2('destroy');
            }
            \$sv.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select stage value',
                minimumResultsForSearch: 0
            });
        }
    }

    var tabId = $('#tabid').val();
    if (tabId) {
        $('#tabid').trigger('change');
    }
}


\$(document).on('change', '#tabid', function(){
    var \$opt    = \$(this).find('option:selected');
    var tabId    = \$(this).val();
    var modName  = \$opt.data('name') || '';
    \$('#module_name').val(modName);

    var name = \$opt.data('name') || '';
    if (!\$('#title').val()) {
        \$('#title').val(name + ' Settings');
    }

    var initialField = \$('#stage_field').data('initial') || '';

    \$('#stage_field').empty().append('<option value="">Loading...</option>');
    \$('#stage_value').empty().trigger('change');

    if (!tabId) {
        \$('#stage_field').html('<option value="">Select Stage Field</option>');
        return;
    }

    \$.ajax({
        url: '{$baseUrl}/modulesetting/get-stage-fields',
        type: 'GET',
        dataType: 'json',
        data: {tabid: tabId},
        success: function(res){
            var \$sf = \$('#stage_field');
            \$sf.empty().append('<option value="">Select Stage Field</option>');
            if (res.success && res.fields) {
                \$.each(res.fields, function(i, f){
                    \$sf.append('<option value=\"'+f.fieldid+'\">'+f.fieldlabel+'</option>');
                });

                if (initialField) {
                    \$sf.val(initialField);
                    \$sf.trigger('change');
                }
            } else {
                \$sf.append('<option value=\"\">No fields found</option>');
            }
        }
    });
});

// On Stage Field change
\$(document).on('change', '#stage_field', function(){
    var fieldId       = \$(this).val();
    var initialValues = window.detaileditInitialStageValues || [];
    initialValues     = initialValues.map(String);

    var \$sv = \$('#stage_value');
    \$sv.empty().trigger('change');

    if (!fieldId) return;

    \$.ajax({
        url: '{$baseUrl}/modulesetting/get-stage-picklist',
        type: 'GET',
        dataType: 'json',
        data: {fieldid: fieldId},
        success: function(res){
            if (res.success && res.values) {
                \$sv.append('<option value=\"\">Select Stage Value</option>');
                \$.each(res.values, function(i, item){
                    var isSelected = initialValues.includes(String(item.id)) ? 'selected' : '';
                    \$sv.append('<option value=\"'+item.id+'\" '+isSelected+'>'+item.label+'</option>');
                });
                \$sv.trigger('change');
            }
        }
    });
});

\$(document).on('submit', '#detailedit-form', function(e){
});

initDetailEditForm();
JS;

$this->registerJs($js);
