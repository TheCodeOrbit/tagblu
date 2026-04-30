<?php
$baseUrl = Yii::$app->HomeUrl; ?>

<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Detail Edit Setting';
$baseUrl = Url::base();
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;

$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
?>



<div class="row">
    <div class="col-11">
        <img src="<?= $baseUrl ?>/thememain/img/module-icon/Field_Setting.svg" class="head-img">
        <span class="sm-modname"><?= Html::encode($this->title) ?></span>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row bg-white">
                    <div class="col-6"></div>
                    <div class="col-6 text-right">


                        <?= Html::a(
                            'Add <i class="fa fa-plus"></i>',
                            ['modulesetting/detailedit-create'],
                            [
                                'class' => 'btn btn-sm btn-outline-primary',
                                'title' => 'Add Detail Edit',
                                'data-pjax' => '0',
                            ]
                        ) ?>


                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Module Name</th>
                                <!-- <th>Stage Field</th>
                                <th>Stage Value</th> -->
                                <th>Edit Allow</th>
                                <th>View Allow</th>
                                <th>Admin Allow</th>
                                <th>Superadmin Allow</th>
                                <th>User Role</th>
                                <!-- <th>User ID</th> -->
                                <th>Description</th>
                                <th width="90">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php $i = 1;
                                foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= Html::encode($row['title']) ?></td>
                                        <td><?= Html::encode($row['module_name']) ?></td>
                                        <!-- <td><?= Html::encode($row['stage_field']) ?></td>
                                        <td><?= Html::encode($row['stage_value']) ?></td> -->
                                        <td><?= $row['edit_allow'] ? 'Yes' : 'No' ?></td>
                                        <td><?= $row['view_allow'] ? 'Yes' : 'No' ?></td>
                                        <td><?= $row['admin_allow'] ? 'Yes' : 'No' ?></td>
                                        <td><?= $row['superadmin_allow'] ? 'Yes' : 'No' ?></td>
                                        <td><?= Html::encode($row['user_role']) ?></td>
                                        <!-- <td><?= Html::encode($row['user_id']) ?></td> -->
                                        <td><?= Html::encode($row['description']) ?></td>
                                        <td>
                                            <?= Html::a(
                                                '<i class="fa fa-edit"></i>',
                                                ['modulesetting/detailedit-update', 'id' => $row['des_id'], 'mode' => 'edit'],
                                                [
                                                    'class' => 'btn btn-sm btn-outline-primary',
                                                    'title' => 'Edit',
                                                    'data-pjax' => '0',
                                                ]
                                            ) ?>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade custom-modal" id="detailedit-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h4 class="modal-title" id="detailedit-modal-title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body custom-modal-body" id="detailedit-modal-body"></div>
            <div class="modal-footer custom-modal-footer"></div>
        </div>
    </div>
</div>
<?php
$js = <<<JS

initDetailEditForm();

function initDetailEditForm() {
    if (\$.fn.select2) {
        \$('#detailedit-form .select2-multi').each(function(){
            var \$el = \$(this);
            \$el.attr('multiple', 'multiple');
            if (\$el.data('select2')) {
                \$el.select2('destroy');
            }
            \$el.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select value(s)'
            });
        });
    }

    var tabId = \$('#tabid').val();
    if (tabId) {
        \$('#tabid').trigger('change');
    }
}


\$(document).on('change', '#tabid', function(){
    var \$opt   = \$(this).find('option:selected');
    var tabId   = \$(this).val();
    var modName = \$opt.data('name') || '';
    \$('#module_name').val(modName);
    let name = $(this).find(':selected').data('name');
    if (!$('#title').val()) {
        $('#title').val(name + ' Settings');
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
            \$sf.empty().append('<option value=\"\">Select Stage Field</option>');
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
\$(document).on('change', '#stage_field', function(){
    var fieldId = \$(this).val();
    var initialValues = $('#stage_value').data('initial') || [];
    initialValues = initialValues.map(String);

    \$('#stage_value').empty().trigger('change');
    if (!fieldId) return;

    \$.ajax({
        url: '{$baseUrl}/modulesetting/get-stage-picklist',
        type: 'GET',
        dataType: 'json',
        data: {fieldid: fieldId},
        success: function(res){
            var \$sv = \$('#stage_value');
            \$sv.empty();
            if (res.success && res.values) {
                \$.each(res.values, function(i, item){
                    console.log(item,'item');
                    console.log(initialValues,'initialValues');
                    var isSelected = initialValues.includes(String(item.id)) ? 'selected' : '';
                    \$sv.append('<option value="'+item.id+'" '+isSelected+'>'+item.label+'</option>');
                });
                    \$sv.trigger('change');
               
            }
        }
    });
});
$(document).on('submit', '#detailedit-form', function(e){
    e.preventDefault();
    var \$form = $(this);

    var stageVals = $('#stage_value').val() || [];
    $('<input>').attr({
        type: 'hidden',
        name: 'stage_value_joined',
        value: stageVals.join(',')
    }).appendTo(\$form);

    var roleVals = $('#user_role').val() || [];
    $('<input>').attr({
        type: 'hidden',
        name: 'user_role_joined',
        value: roleVals.join(',')
    }).appendTo(\$form);

    var userVals = $('#user_id').val() || [];
    $('<input>').attr({
        type: 'hidden',
        name: 'user_id_joined',
        value: userVals.join(',')
    }).appendTo(\$form);

    $.post(\$form.attr('action'), \$form.serialize(), function(resp){
        if (resp.success) {
            $('#detailedit-modal').modal('hide');
            location.reload();
        } else {
            alert('Save failed');
        }
    }, 'json');
    return false;
});
JS;

$this->registerJs($js);
?>