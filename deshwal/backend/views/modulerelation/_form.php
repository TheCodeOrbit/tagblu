<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Exportrequest $model */
/** @var yii\widgets\ActiveForm $form */
$baseUrl = Url::base();
// echo $baseUrl;die;
// $this->registerCssFile('@web/thememain/css/bootstrap.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multiple.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);

?>
<div class="module-relation-form">

    <?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>

    <div class="row mb-2">
        <div class="col-md-3">
            <div class="form-group field-picklist-module_name">
                <label class="control-label" for="picklist-module_name">
                    Module Name <span class="red">*</span>
                </label><br>
                <select id="source_module"
                    name="Modulerelation[source_module]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value=""> Select </option>
                    <?php foreach ($tablabels as $value): ?>
                        <option value="<?= $value->tabid ?>">
                            <?= htmlspecialchars($value->tablabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group field-picklist-table_name">
                <label class="control-label" for="picklist-table_name">
                    Select Related Module <span class="red">*</span>
                </label><br>
                <select id="related_module"
                    name="Modulerelation[related_module]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value=""> Select </option>
                    <?php foreach ($tablabels as $value): ?>
                        <option value="<?= $value->tabid ?>">
                            <?= htmlspecialchars($value->tablabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group field-picklist-table_name">
                <label class="control-label" for="picklist-table_name">
                    Select Related Field <span class="red">*</span>
                </label><br>
                <select id="related_fieldname"
                    name="Modulerelation[related_fieldname]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value="">Select Field</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group field-picklist-table_name">
                <label class="control-label" for="picklist-table_name">
                    Select Related Record Field <span class="red">*</span>
                </label><br>
                <select id="related_recordfieldnme"
                    name="Modulerelation[related_recordfieldnme]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value="">Select Field</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="row mb-2">

        <div class="col-md-3">
            <div class="form-group field-picklist-table_name">
                <label class="control-label" for="picklist-table_name">
                    Action <span class="red">*</span>
                </label><br>
                <select id="relatedaction"
                    name="Modulerelation[action]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value="">Select </option>
                    <option value="Add">Add </option>
                    <option value="Select">Select </option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group field-picklist-table_name">
                <label class="control-label" for="picklist-table_name">
                    Select Related Column <span class="red">*</span>
                </label><br>
                <select id="related_columns" multiple 
                    name="Modulerelation[related_columns]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value="">Select Field</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
    </div>

    <!-- <?= $form->field($model, 'related_table')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'related_tablekeyid')->textInput(['maxlength' => true]) ?> -->
    <!-- <?= $form->field($model, 'related_fieldname')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'related_recordfieldnme')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'relation_with_account')->textInput(['maxlength' => true]) ?> -->


    <!-- <?= $form->field($model, 'actions')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'related_columns')->textInput(['maxlength' => true]) ?> -->


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
// $this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/modulerelation/edit.js', ['depends' => [AdminAsset::class]]);
?>