<?php

use app\models\Grn;
use app\models\Leadinformation;
use app\models\Quotes;
use app\models\Sourcingdeal;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use backend\models\AccessCheck;
use backend\modules\segregation\Segregation;
use yii\db\Query;
use yii\data\Pagination;
use yii\widgets\ActiveForm;

AdminAsset::register($this);
$this->title = Yii::t('app', 'Report  ' . $TabLabel);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/reportview.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/dashboard.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/flatpickr.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);

$url = Url::to(['create']);
$uploadUrl = Url::to(['csvupload']);
$baseUrl = Yii::$app->HomeUrl;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself 


?>
<style type="text/css">
    /* Button styling */
    .btn-1 {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Popup styling */

    .custom-header {
        position: relative;
        height: 48px;
        padding: 0 10px;
        background-color: #f4f4f4;
        border-right: 1px solid #ccc;
        text-align: left;
        font-weight: 600;
        vertical-align: middle;
        line-height: 48px;
        white-space: nowrap;
    }

    .ag-row {
        position: absolute;
        display: flex;
        width: 1200px;
        /* total width of all cells */
        box-sizing: border-box;
    }

    .ag-cell {
        position: absolute;
        height: 43px;
        padding: 8px;
        overflow: hidden;
        white-space: nowrap;
        background-color: #fff;
        border-right: 1px solid #ccc;
        display: flex;
        align-items: center;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .ag-cell-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 100%;
    }

    .ag-cell-value {
        display: block;
    }

    .head-img {
        position: relative;
        top: 0px !important;
        padding: 10px;
        width: 55px;
    }
</style>
<link href="<?= $baseUrl; ?>thememain/css/multilist-dd.css" rel="stylesheet">
<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">


<div class="select-1">
    <div class="container-d">
    <?php if ($listpermission == 1 || $hasadminpower == 1) { ?>
        <!-- Filter By Name Modal Structure -->
        <?php 
            // if ($TabId == 80 || $TabId == 77 || $TabId == 123) {
                if(in_array($TabId,[80,77,123])){
             ?>
            <!-- Filter Modal -->
            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Filter Grid</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="filterColumn" class="form-label">Column</label>
                                <select id="filterColumn" class="form-select">
                                    <option value="">-- Select Column --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="filterOperator" class="form-label">Operator</label>
                                <select id="filterOperator" class="form-select">
                                    <!-- <option value="contains">Contains</option>
                                    <option value="equals">Equals</option>
                                    <option value="not_equals">Not Equals</option>
                                    <option value="starts_with">Starts With</option>
                                    <option value="ends_with">Ends With</option> -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="filterValue" class="form-label">Value</label>
                                <input type="text" id="filterValue" class="form-control" placeholder="Enter value">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" id="applyFilter" class="btn btn-primary">Search</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>
        <?php } else if($TabId != 95) { //95 user history ?>

            <div class="filter-bar report-filter card">
                <h5 class="reporttitle mb-1">Filter Options</h5>
                <!-- <form action="" method="post" id="mainForm"> -->
                    <div class="row">
                        <div class="col-lg-12 d-flex align-items-center gap-2">
                            <div class="col-lg-3 mb-3">
                                <label for="acc_dropdown" class="form-label fw-semibold">Account</label>
                                <select name="acc_dropdown" id="acc_dropdown" class="singleselect form-control">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <label for="from_date" class="form-label fw-semibold">From Date</label>
                                <input type="text" id="from_date" name="from_date" placeholder="Select"
                                    class="form-control flatpickr">
                            </div>

                            <div class="col-lg-3 mb-3">
                                <label for="to_date" class="form-label fw-semibold">To Date</label>
                                <input type="text" id="to_date" name="to_date" placeholder="Select"
                                    class="form-control flatpickr">
                            </div>
                            <div class="col-lg-1 pt-2">
                                <button type="button" class="btn btn-success" id="filterRecords" title="Apply Filter">Search</button>
                            </div>
                            <div class="col-lg-1 pt-2">
                                 <button type="button" class="btn btn-danger" id="clearfilter" title="Clear Filter">Cancle</button>
                            </div>
                        </div>
                    </div>
                <!-- </form> -->
            </div>

        <?php } ?>
        <!-- Table -->
        <div class="table-list">
            <div id="" class="ag-theme-alpine">

                <div class="table-container table-responsive ">

                    <div class="table-list" id="gridPanel">
                        <div class="toolbar d-flex align-items-center justify-content-between flex-wrap gap-2" style="padding: 8px 0;">
                            <h5 class="reporttitle mb-0"><?= $TabLabel; ?> Report</h5>

                            <div class="button-group d-flex align-items-center gap-2">

                                <!-- Refresh -->
                                <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
                                    id="refresh-icon" alt="Refresh" class="refresh-icon" title="Refresh Page"
                                    style="cursor:pointer; width:22px;">


                                <?php  if(in_array($TabId,[80,77,123])){ ?>
                                    <!-- Filter Icon -->
                                    <img id="openFilterModal" src="<?= $baseUrl; ?>/thememain/img/typcn-filter.svg"
                                        class="filter-selector-btn" style="cursor:pointer; width:22px;" title="Filter Selector">
                                    <?php if ($TabId == 80 ) { ?>
                                        <a href="<?= Yii::$app->urlManager->createUrl(['modelwiseclubbedinventory/report']) ?>" class="btn btn-primary"> Model wise</a>
                                    <?php } else if ($TabId == 123 ) { ?>
                                        <a href="<?= Yii::$app->urlManager->createUrl(['clubbedinventory/report']) ?>" class="btn btn-primary"> Subcategory wise</a>
                                    <?php } ?>
                                    <!-- Search -->
                                    <input type="text" id="quickSearch" placeholder="Search..." class="quick-search form-control" style="width:160px;">

                                <?php } else if ($TabId != "95") { ?>
                                    <!-- Filter Form (Inline) -->

                                <?php } ?>
                                <?php if($exportpermission) {?>
                                <!-- Export Dropdown -->
                                <div class="dropdown">
                                    <button class="btn-export btn-1 ModuleName_<?= ucfirst($ModuleName); ?>" id="exportAllButton">
                                        Export All ▾
                                    </button>
                                    <div class="dropdown-menu" id="exportDropdown">
                                        <a href="#" id="exportExcel">Export as Excel</a>
                                        <a href="#" id="exportPDF">Export as PDF</a>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if ($TabId == 95) { // user login activity
                        ?>
                            <?php $form = ActiveForm::begin(["id" => "useractivityfilter-form"]); ?>
                            <!-- filter options start from here -->
                            <div class="row toolbar">
                                <div class="col-lg-2 col-md-2">
                                    <input type="text" name="userlogin_from_date" id="userlogin_from_date" placeholder="From date" class="quick-search-textinput flatpickr" />
                                </div>
                                <div class="col-lg-2 col-md-2">
                                    <input type="text" name="userlogin_to_date" id="userlogin_to_date" placeholder="To date" class="quick-search-textinput flatpickr" />
                                </div>
                                <div class="col-lg-2 col-md-2">
                                    <select id="user" name="user" class="quick-search-select singleselect">
                                        <option value="">Select User</option>
                                        <?php if (!empty($users)) {
                                            foreach ($users as $key => $value) {
                                                echo "<option value=" . $key . ">" . $value . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-2">
                                    <select id="activity" name="activity" class="quick-search-select singleselect">
                                        <option value="">Select Activity</option>
                                        <option value="Login">Login</option>
                                        <option value="Logout">Logout</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-2">
                                    <button type="button" id="filteruseractivity" name="filteruseractivity" class="btn btn-primary" value="">Filter</button>
                                </div>
                            </div>
                            <!-- filter options end  here -->
                            <?php ActiveForm::end(); ?>
                        <?php } ?>
                        <div id="reportGrid" class="ag-theme-alpine"></div>

                    </div>

                    <!-- Detail wrapper (initially hidden) -->
                    <div id="detailPanel" style="display:none">
                        <!-- <button class="btn btn-sm btn-secondary mb-3" id="backToGrid">← Back</button> -->
                        <div id="detailContent"><!-- AJAX-loaded HTML will appear here --></div>
                    </div>


                </div>
                <!-- End of Table -->

            </div>
        </div>
    <?php } ?>
        <?php
        $this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);
        $this->registerJsFile('@web/thememain/js/tetra/reportview.js', ['depends' => [AdminAsset::class]]);
        $this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
        ?>


        <?php
        $js = <<<JS
        flatpickr(".flatpickr", {
            dateFormat: "d-m-Y",
            defaultDate: new Date(),
             maxDate: "today",
            onReady: function(selectedDates, dateStr, instance) {
                console.log('Flatpickr type:', typeof flatpickr);
                console.log('From date picker object:', instance);
            }
        });
        // Refresh page on icon click
        $(document).on("click", ".refresh-icon", function() {
            location.reload();
        });
        JS;
        $this->registerJs($js);
        $scriptPath = $baseUrl . "js/$ModuleName/edit.js";
        ?>
        <script type="text/javascript" src="<?= $scriptPath ?>"></script>