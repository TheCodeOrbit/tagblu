<style>
    #tableContainer {
    position: relative;
     max-height: 400px; 
     overflow: auto; 
}

#paginationContainer{
    width: 100%;
    background: #fff;
    padding: 12px 0 0 0;
    position: sticky;
    bottom: 0;
    left: 0;
    z-index: 5;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.02);
    border-top: 1px solid #eee;
}
.footer-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px 0 16px;
    position: sticky;
    bottom: 0;
    left: 0;
    z-index: 5;
    background: #fff;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.02);
    border-top: 1px solid #eee;
    width: 100%;
}

.left-controls, .right-controls {
    flex: 1 1 0;
}

.left-controls {
    justify-content: flex-start;
    display: flex;
    align-items: center;
}

.right-controls {
    justify-content: flex-end;
    display: flex;
    align-items: center;
    text-align: right;
}


</style>
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

// $this->registerCssFile('@web/thememain/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]); 
$this->registerCssFile('@web/thememain/css/picklist_screen.css', ['depends' => [AdminAsset::class]]); ?>
<div class="picklist-form">

    <?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>
    <div class="row mb-2">
        <div class="col-lg-2 col-md-2 col-sm-2"></div>
        <div class="col-md-4">
            <div class="form-group field-picklist-module_name">
                <label class="control-label" for="picklist-module_name">
                    Module Name <span class="red">*</span>
                </label><br>
                <select id="picklist_module_name"
                    name="Picklist[module_name]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value="0">Select</option>
                    <?php foreach ($modulenames as $key => $value): ?>
                        <option value="<?= $key ?>"><?= htmlspecialchars($value) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group field-picklist-table_name">
                <label class="control-label" for="picklist-table_name">
                    Select Picklist in <span class="red">*</span>
                </label><br>
                <select id="picklist_table_name"
                    name="Picklist[table_name]"
                    class="DD~M form-control productinput singleselect"
                    aria-invalid="false">
                    <option value="0">Select</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2"></div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-2 col-md-2 col-sm-2"></div>
        <div class="col-lg-8 col-md-8 col-sm-8">
            <div id="tableContainer">
                <table id="dynamicTable" class="table table-bordered">
                    <thead>
                        <tr class="listViewHeaders bgColor">
                            <th id="listheader">Values</th>
                            <th class="text-end">
                                <button type="button" id="exportPicklistBtn"
                                        class="btn btn-success"
                                        data-export-url="<?= \yii\helpers\Url::to(['picklist/exportpicklistvalues']) ?>">
                                    Export </button>

                                <button type="button" class="btn btn-default btn-outline" id="addpicklist">
                                    <i class="fa fa-plus"></i> Add Value
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically -->
                    </tbody>
                </table>
                <div class="footer-controls">
    <div class="left-controls">
        <div id="valueShow">
            Show:
            <select id="pageSizeSelect" class="form-select form-select-sm" style="width:auto;display:inline-block">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    <div class="right-controls">
        <div id="paginationContainer">
            <div class="pagination-buttons">
            </div>
        </div>
    </div>
</div>

            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2"></div>
    </div>
    <?php
    // $this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
    $this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    $this->registerJsFile('@web/js/picklist/edit.js', ['depends' => [AdminAsset::class]]);
    ?>

    <?php ActiveForm::end(); ?>

</div>
<!-- added by ptpatel -->
<div class="modal fade" id="picklistModal" tabindex="-1" aria-labelledby="editModalSummeryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <label id="modal_label_name"> </label>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- hidden fields -->
                <input type="hidden" name="_csrf" id="csrfToken" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" id="editRecordId" name="editRecordId">
                <input type="hidden" id="editFieldId" name="editFieldId">
                <input type="hidden" id="picklistmode" name="picklistmode">

                <div class="mb-3" id="parent_grand_dd_div">
                    <div class="form-group field-picklistEditValue">
                        <label class="control-label" for="picklistEditValue" id="parent_grand_dd_lbl">

                        </label><br>
                        <select name='picklist_grand_parent_dd' id="picklist_grand_parent_dd" class="form-control singleselect">
                            <option value="0"> Select </option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="mb-3" id="parent_dd_div">
                    <div class="form-group field-picklistEditValue">
                        <label class="control-label" for="picklistEditValue" id="parent_dd_lbl">

                        </label><br>
                        <!-- <select name='picklist_parent_dd' id="picklist_parent_dd" class="form-control singleselect multySelect">
                            <option value="0"> Select </option>
                        </select> -->
                        <select name='picklist_parent_dd[]' multiple id="picklist_parent_dd" class="form-control multySelect">
                            <option value="0"> Select </option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <!-- text field -->
                <div class="mb-3">
                    <div class="form-group field-picklistEditValue">
                        <label class="control-label" for="picklistEditValue" id="modellbl">
                            <span class="red">*</span>
                        </label><br>
                        <input type="text" id="picklistEditValue" class="form-control" name="picklistEditValue">
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="mb-3" id="picklistExchangeRateValue">
                    <div class="form-group field-picklistEditValue">
                        <label class="control-label" for="picklistEditValue" id="">Exchange Rate
                        </label><br>
                        <input type="text" id="exchange_rate" class="form-control" name="exchange_rate">
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-outline" id="importPicklist">
                    <i class="fa fa-upload"></i> Import (Bulk Upload)
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary savePicklistData">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header">
                    <label id="import_modal_label_name"></label>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <a id="downloadCsvBtn" href="#" class="btn btn-success" target="_blank">
                    <i class="fa fa-download"></i> Download Sample CSV
                </a>
                        <br><br>
                <!-- CSV upload form -->
                <form id="importCsvForm" enctype="multipart/form-data">
                    <input type="hidden" name="csv_upload" value="1">
                    <input type="hidden" name="module_id" id="importModuleId">
                    <input type="hidden" name="table_id" id="importTableId">
                    <input type="hidden" id="importParentIds" name="picklistParentValue">
                    <input type="file" name="csv_file" id="csv_file_input" accept=".csv" required class="form-control mb-3">
                    <button type="submit" class="btn btn-primary">Upload CSV</button>
                </form>
                <div id="importResult"></div>
            </div>
        </div>
    </div>
</div>