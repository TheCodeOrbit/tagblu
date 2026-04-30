<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Module View Sharing Edit';
$baseUrl     = Url::base();
$csrfToken   = Yii::$app->request->csrfToken;
$csrfParam   = Yii::$app->request->csrfParam;
$mode = (Yii::$app->request->get('mode')) == 'edit' ? 'update' : 'create';
$this->registerCssFile('@web/thememain/css/flatpickr.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css',   ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css',  ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/select2.min.js',        ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$record              = $record ?: [];
$des_id              = $record['des_id']          ?? '';
$tabid               = $record['tabid']           ?? '';
$module_name         = $record['module_name']     ?? '';
$stage_field         = $record['stage_field']     ?? '';
$stage_value         = $record['stage_value']     ?? '';
$view_allow          = !empty($record['view_allow']);
$edit_allow          = !empty($record['edit_allow']);
$admin_allow         = !empty($record['admin_allow']);
$super_allow         = !empty($record['superadmin_allow']);

$selectedStageValues = !empty($record['stage_value']) ? explode(',', $record['stage_value']) : [];
$selectedRoles       = !empty($record['user_role'])   ? explode(',', $record['user_role'])   : [];
$selectedUsers       = !empty($record['user_id'])     ? explode(',', $record['user_id'])     : [];

$this->registerJs("
    window.detaileditInitialStageField  = " . json_encode($stage_field) . ";
    window.detaileditInitialStageValues = " . json_encode($selectedStageValues) . ";
", \yii\web\View::POS_HEAD);

?>

<div class="container-fluid detailedit-page">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-9 col-md-12">
            <div class="card detailedit-card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Module View Sharing</h5>
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
                                <label for="tabid">Select Module <span class="text-danger">*</span></label>
                                <select name="tabid" id="tabid"
                                        class="form-control custom-input select2-single">
                                    <?php foreach ($tabList as $tab): ?>
                                        <option value="<?= $tab['tabid'] ?>"
                                                data-tablename="<?= Html::encode($tab['tablename']) ?>"
                                                data-name="<?= Html::encode($tab['name']) ?>"
                                                <?= $tabid == $tab['tabid'] ? 'selected' : '' ?>>
                                            <?= Html::encode($tab['tablabel']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6 d-none">
                                <label for="module_name">Module Name</label>
                                <input type="text"
                                       name="module_name"
                                       id="module_name"
                                       class="form-control custom-input"
                                       value="<?= Html::encode($module_name) ?>"
                                       readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="title">Title</label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       class="form-control custom-input"
                                       value="<?= Html::encode($record['title'] ?? '') ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Description</label>
                                <textarea name="description"
                                          id="description"
                                          class="form-control custom-input"
                                          rows="2"><?= Html::encode($record['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="stage_field">Stage Field</label>
                                <select name="stage_field"
                                        id="stage_field"
                                        class="form-control custom-input select2-single"
                                        data-initial="<?= Html::encode($stage_field) ?>">
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="stage_value">Stage Value</label>
                                <select id="stage_value"
                                        class="form-control select2-single"
                                        name="stage_value"
                                        data-initial='<?= json_encode($selectedStageValues) ?>'>
                                </select>
                                <div class="help-block small text-danger"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="user_role">User's Role</label>
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
                                <div class="help-block small text-danger"></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="user_id">User's Name</label>
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
                                <div class="help-block small text-danger"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="d-block mb-2">Permissions</label>
                            <div class="border rounded p-2">
                                <div class="form-check form-check-inline mr-4">
                                    <input type="checkbox"
                                           name="view_allow"
                                           id="view_allow"
                                           class="form-check-input"
                                           <?= $view_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="view_allow">View</label>
                                </div>

                                <div class="form-check form-check-inline mr-4">
                                    <input type="checkbox"
                                           name="edit_allow"
                                           id="edit_allow"
                                           class="form-check-input"
                                           <?= $edit_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="edit_allow">Edit</label>
                                </div>

                                <div class="form-check form-check-inline mr-4">
                                    <input type="checkbox"
                                           name="admin_allow"
                                           id="admin_allow"
                                           class="form-check-input"
                                           <?= $admin_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="admin_allow">Admin</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="checkbox"
                                           name="superadmin_allow"
                                           id="superadmin_allow"
                                           class="form-check-input"
                                           <?= $super_allow ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="superadmin_allow">Superadmin</label>
                                </div>
                            </div>
                        </div>

                        <div class="detailedit-footer mt-3 text-right">
                            <?php if (Yii::$app->request->get('mode') !== 'view'): ?>
                                <button type="submit" class="btn btn-primary custom-save-btn">
                                    <i class="fa fa-save mr-1"></i> Save
                                </button>
                                <a href="<?= Url::to(['modulesetting/viewsharinglist']) ?>"
                                   class="btn btn-outline-secondary ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <a href="<?= Url::to(['modulesetting/viewsharinglist']) ?>"
                                   class="btn btn-outline-secondary">
                                    Back
                                </a>
                            <?php endif; ?>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ERROR MODAL (REUSED FOR FORM ERRORS) -->
<div class="modal fade" id="importErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Validation Errors</h5>
        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="errRowWrapper" class="d-none"><b>Row: </b><span id="errRow"></span></div>
        <div id="errFieldWrapper" class="d-none"><b>Field: </b><span id="errField"></span></div>
        <div id="errValueWrapper" class="d-none"><b>Value: </b><span id="errValue"></span></div>
        <p class="mb-1"><b>Reason: </b></p>
        <ul id="errReason" class="mb-0 pl-3"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php

$js = <<<JS
function initDetailEditForm() {
    if ($.fn.select2) {
        $('#detailedit-form .select2-multi').each(function () {
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

        var \$tab = $('#tabid');
        if (\$tab.length) {
            if (\$tab.data('select2')) {
                \$tab.select2('destroy');
            }
            \$tab.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select Module',
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
                placeholder: 'Select stage field',
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

// showConfirm as given
function showConfirm(msg) {
    return new Promise(function (resolve) {
        var modal = document.createElement('div');
        modal.id = 'confirmModal';
        modal.style.cssText = `
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 99999;
            display: flex; align-items: center; justify-content: center;
            font-family: Arial, sans-serif;
        `;

        var content = document.createElement('div');
        content.style.cssText = `
            background: white; padding: 24px; border-radius: 8px;
            min-width: 320px; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            text-align: center;
        `;

        var text = document.createElement('p');
        text.id = 'confirmText';
        text.textContent = msg;
        text.style.cssText = 'margin: 0 0 20px 0; font-size: 16px; line-height: 1.4;';

        var yesBtn = document.createElement('button');
        yesBtn.textContent = 'Yes';
        yesBtn.style.cssText = `
            background: var(--color-primary) !important; color: #fff; border: none; padding: 10px 24px;
            margin-right: 12px; border-radius: 4px; cursor: pointer; font-size: 14px;
            min-width: 70px;
        `;

        var noBtn = document.createElement('button');
        noBtn.textContent = 'No';
        noBtn.classList = 'mod-close btn btn-secondary';
        noBtn.style.cssText = `
            background: #ffffff; color: black; border: none; padding: 10px 24px;
            border-radius: 4px; cursor: pointer; font-size: 14px; min-width: 70px;
        `;

        content.appendChild(text);
        content.appendChild(yesBtn);
        content.appendChild(noBtn);
        modal.appendChild(content);
        document.body.appendChild(modal);

        function cleanup(result) {
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
            resolve(result);
        }

        yesBtn.onclick = () => cleanup(true);
        noBtn.onclick = () => cleanup(false);

        modal.onclick = (e) => { if (e.target === modal) cleanup(false); };

        var escHandler = (e) => { if (e.key === 'Escape') cleanup(false); };
        document.addEventListener('keydown', escHandler);

        modal._cleanup = () => {
            document.removeEventListener('keydown', escHandler);
            cleanup(false);
        };
    });
}

// helper: show error modal with list of messages
function showErrorModal(errors) {
    var \$modal = $('#importErrorModal');
    // hide row/field/value blocks for this use case
    $('#errRowWrapper, #errFieldWrapper, #errValueWrapper').addClass('d-none');
    var \$list = $('#errReason');
    \$list.empty();
    errors.forEach(function(msg){
        \$list.append('<li>' + msg + '</li>');
    });
    \$modal.modal('show');
}

// VALIDATION + CONFIRMATION ON SUBMIT
$(document).on('submit', '#detailedit-form', function(e){
    e.preventDefault();

    var form = this;
    var isValid = true;
    var errors  = [];

    $('.help-block').html('');

    var title       = $('#title').val().trim();
    var description = $('#description').val().trim();

    if (!title) {
        errors.push('Title is required');
        isValid = false;
    }
    if (!description) {
        errors.push('Description is required');
        isValid = false;
    }

    var viewAllow = $('#view_allow').is(':checked');
    var editAllow = $('#edit_allow').is(':checked');

    if (!viewAllow && !editAllow) {
        errors.push('Either "View" or "Edit" permission must be checked');
        isValid = false;
    }

    var roleVal        = $('#user_role').val();
    var userVal        = $('#user_id').val();
    var roleCount      = roleVal ? roleVal.length : 0;
    var userCount      = userVal ? userVal.length : 0;
    var adminAllow     = $('#admin_allow').is(':checked');
    var superAdminAllow= $('#superadmin_allow').is(':checked');

    if (roleCount === 0 && userCount === 0 && !adminAllow && !superAdminAllow) {
        errors.push('At least one of User Role, User, Admin, or Superadmin must be selected/enabled');
        isValid = false;
    }

    if (!isValid) {
        showErrorModal(errors);
        return false;
    }

    // confirmation via showConfirm
    showConfirm('Are you sure you want to save these settings?').then(function(ok){
        if (ok) {
            // avoid infinite loop: unbind handler and submit
            $(form).off('submit');
            form.submit();
        }
    });

    return false;
});

// TAB CHANGE
$(document).on('change', '#tabid', function(){
    var \$opt   = $(this).find('option:selected');
    var tabId   = $(this).val();
    var modName = \$opt.data('name') || '';

    $('#module_name').val(modName);

    var initialField = $('#stage_field').data('initial') || '';

    $('#stage_field').empty().append('<option value="">Loading...</option>');
    $('#stage_value').empty().trigger('change');

    if (!tabId) {
        $('#stage_field').html('<option value="">Select Stage Field</option>');
        return;
    }

    $.ajax({
        url: '{$baseUrl}/modulesetting/get-stage-fields',
        type: 'GET',
        dataType: 'json',
        data: {tabid: tabId},
        success: function(res){
            var \$sf = $('#stage_field');
            \$sf.empty().append('<option value="">Select Stage Field</option>');
            if (res.success && res.fields) {
                $.each(res.fields, function(i, f){
                    \$sf.append('<option value="'+f.fieldid+'">'+f.fieldlabel+'</option>');
                });

                if (initialField) {
                    \$sf.val(initialField);
                    \$sf.trigger('change');
                }
            } else {
                \$sf.append('<option value="">No fields found</option>');
            }
        }
    });
});

// STAGE FIELD CHANGE
$(document).on('change', '#stage_field', function(){
    var fieldId       = $(this).val();
    var initialValues = window.detaileditInitialStageValues || [];
    initialValues     = initialValues.map(String);

    var \$sv = $('#stage_value');
    \$sv.empty().trigger('change');

    if (!fieldId) return;

    $.ajax({
        url: '{$baseUrl}/modulesetting/get-stage-picklist',
        type: 'GET',
        dataType: 'json',
        data: {fieldid: fieldId},
        success: function(res){
            if (res.success && res.values) {
                \$sv.append('<option value="">Select Stage Value</option>');
                $.each(res.values, function(i, item){
                    var isSelected = initialValues.includes(String(item.id)) ? 'selected' : '';
                    \$sv.append('<option value="'+item.id+'" '+isSelected+'>'+item.label+'</option>');
                });
                \$sv.trigger('change');
            }
        }
    });
});
 $(".btn-close, .btn-secondary").click(function () {
    $("#importErrorModal").modal("hide");
  });
initDetailEditForm();
JS;

$this->registerJs($js);
?>
