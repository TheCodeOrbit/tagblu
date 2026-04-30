<?php

use backend\components\SvgRenderHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use app\models\ListHire;
use app\models\Reference;
use yii\widgets\ActiveForm;
use backend\models\AccessCheck;

AdminAsset::register($this);
// $this->title = Yii::t('app', $TabLabel . " Detail");

$url = Url::to(['Edit']);
$urlApprove = Url::to(['approvelead']);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$baseUrl = Yii::$app->HomeUrl;
$ModuleName = 'ghkjg';
$module = strtolower($ModuleName);
if (!isset($showblocks))
    $showblocks = array();
// echo "<pre>";
// print_r($Record);

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself
$siteDir = Yii::$app->params["dirName"];
$ModuleName = $ActionList["ModuleName"];

$ActionName = $ActionList["ActionName"];
$ModuleLabel = $ActionList["ModuleLabel"];
$_SESSION["countpro"] = "";
$_SESSION["taxcounterr"] = "";
$sesionid = isset($_SESSION[$siteDir . "_id"])
    ? $_SESSION[$siteDir . "_id"]
    : "deshwal";

$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/edit.js";
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';


$this->title = Yii::t('app', " $ActionName " . $TabLabel);


function convertToUcfirstOrPascalCase($string)
{
    // Check if the string contains underscores
    if (strpos($string, '_') !== false) {
        // Convert to PascalCase by splitting, capitalizing each part, and joining
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    } else {
        // Capitalize the first letter of the string
        return ucfirst($string);
    }
}
$cancelurl = '';
if ($action_name === 'create') {
    if ($TabId == 67 || $TabId == 68 || $TabId == 69) // 67 segregation, 68 tagging
    {
        $cancelurl = $baseUrl . $ModuleName . "/dashboard";
    } else {
        $cancelurl = $baseUrl . $ModuleName . "/list";
    }
    if (isset($sourceid) && isset($sourcemodule))
        $cancelurl .= "?sourcemodule=" . $sourcemodule . "&sourceid=" . $sourceid;
} else {
    $cancelurl = $baseUrl . $ModuleName . "/detail?Record=" . $RecordId;
    if (isset($sourceid) && isset($sourcemodule))
        $cancelurl .= "&sourcemodule=" . $sourcemodule . "&sourceid=" . $sourceid;
}
$this->registerJsFile(Url::to($baseUrl . "thememain/js/select2.min.js"), ['depends' => [yii\web\JqueryAsset::class]]);
$this->registerJsFile(Url::to($baseUrl . "thememain/js/tetra/single-dd.js"), ['depends' => [yii\web\JqueryAsset::class]]);
$this->registerJsFile(Url::to($baseUrl . "thememain/js/tetra/multilist-dd.js"), ['depends' => [yii\web\JqueryAsset::class]]);
$this->registerCssFile($baseUrl . '/thememain/css/multiple.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile($baseUrl . '/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile($baseUrl . '/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);
$this->registerJsFile(Url::to($scriptPath), ['depends' => [yii\web\JqueryAsset::class]]);
if ($ModuleName === 'datawiping') {
    $this->registerJsFile(Url::to($baseUrl . "thememain/js/tetra/papaparse.min.js"), ['depends' => [yii\web\JqueryAsset::class]]);
}

?>


<!-- <link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/multiple.css"> -->
<div class="select-1">
    <input type="hidden" value="<?= $ModuleName; ?>" id="module">
    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
    <input type="hidden" class="srctabid" value="<?= $TabId;  ?>">
    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
    <input type="hidden" value="<?php echo $RecordId; ?>" id="record" name="record" />
    <div class="container-d">
        <div class="row">
            <div class="col-6"
                style="display:flex; flex-direction:row; align-items:center; gap:10px;">

                <div class="icons-coll head-img-create" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <?= SvgRenderHelper::renderIcon($ModuleName . '.svg', true); ?>
                </div>

                <div style="display:flex; flex-direction:column; align-items:flex-start; justify-content:center;">
                    <span class="sm-modname"
                        style="display:block; line-height:1.2;">
                        <?php
                        if ($ActionName == "Create") {
                            echo "Add";
                        } else {
                            echo $ActionName;
                        }
                        ?>
                    </span>
                    <span class="fullname"
                        style="display:block; line-height:1.2;">
                        <?= $TabLabel; ?>
                    </span>
                </div>

            </div>

            <div class="col-6 show-required">
                <!-- <div class="toggle-switch" onclick="toggleRequiredFields2()"></div> -->
                <div class="toggle-switch" id="toggle-switch2"></div>
                Show Required & Important Fields
            </div>

        </div>

        <div id="error-div"></div>

        <?php $form = ActiveForm::begin([
            "id" => "pristine-valid-example",
            'options' => [
                'enctype' => 'multipart/form-data', // Required for file uploads
            ],
        ]); ?>


        <input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode" />
        <input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />

        <?php
        $c = 1;

        foreach ($ColumnList->blocks as $block) {
            $notempty = array();
            // check if any one field is visible
            if ($block->blocklabel === "SYSTEM GENERATED") {
                $notempty[$block->blockid] = 1;
            } else {
                if (!empty($showblocks)) {
                    if (!in_array($block->blockid, $showblocks)) {
                        continue;
                    } else {
                        echo "<input type='hidden' value = '" . base64_encode($RecordId) . "' name = 'inspectionitems'>";
                    }
                }
                foreach ($block->$relationName as $field) {

                    $notempty[$block->blockid] = 0;
                    $fieldid = $field->fieldid;
                    if ($hasadminpower == 1) {
                        $visible = 0;
                        $readonly = 0;
                        $notempty[$block->blockid] = 1;
                        break;
                    } else {
                        //now check if this field is allowed to edit ,readonly etc
                        $model = new AccessCheck();
                        $permission = $model->fieldacces($uid, $fieldid);
                        //print_r($permission);die;
                        if (is_array($permission)) {
                            $visible = $permission['visible'];
                            $readonly = $permission['readonly'];
                            if ($visible == 0) {
                                $notempty[$block->blockid] = 1;
                                break;
                            }
                        }
                    }
                }
            }
            // if($block->blocklabel == 'KYC INFORMATION')
            // echo $notempty[$block->blockid] ;die;

            if ((!empty($block->$relationName) || $block->blocklabel === "SYSTEM GENERATED" || $block->blockid == 150) && $block->display_status == 1) {

                if ($block->blockid == 150)
                    $notempty[$block->blockid] = 1;
                if ((isset($notempty[$block->blockid]) && $notempty[$block->blockid] == 1)) {
                    // print_r($block->$relationName);
                    if ($block->blocklabel === "SYSTEM GENERATED")
                        $cls = "tr-hidden";
                    else
                        $cls = "";

        ?>

                    <div
                        class="accordion-item row titlerow <?= $cls; ?> <?= $notempty[$block->blockid] ?> row<?= $block->blockid ?>">
                        <!-- code added by ptpatel on date 08-04-25 -->
                        <!-- if role is finance manager then hide OEM Manager Block -->

                        <?php if (!($block->blockid == 150 && $roleId == "H19")) { //H19 = Deshwal Finance manager
                        ?>
                            <div class="accordion-header col-12 <?= 'blocktitle' . $block->blockid ?>">
                                <!-- <div class="title-tab">
                                    <label class="title-info"><?= $block->blocklabel ?></label>
                                </div> -->
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?= $block->blockid ?>">
                                    <strong><?= $block->blocklabel ?></strong>
                                </button>
                            </div>

                        <?php } ?>
                        <div id="collapse<?= $block->blockid ?>" class="accordion-collapse collapse show"
                            data-bs-parent="#simpleAccordion">
                            <div class="accordion-body">
                                <!-- end code added by ptpatel on date 08-04-25 -->
                                <?php if ($block->blocktype == "Multiple") {
                                    //echo "Two column";
                                    require "Multirecord.php";
                                    unset($cnt_rows);
                                    // echo "</div>";
                                } else {
                                    require "SingleTwoCol.php";
                                    // echo "</div>";

                                ?>

                                <?php
                                }
                                if ($block->blocklabel !== "SYSTEM GENERATED") {
                                ?>
                                    <!-- closse title div -->

                            </div>
                        <?php
                                }
                        ?>
                        </div>
                    </div>

        <?php }
            }
        }

        //endforeach;
        ?>
        <!-- //code added by ptpatel -->
        <?php if ($TabId == 69 || $TabId == 70) {
        ?>
            <!-- code added by ptpatel on date 12-12-2025 -->
            <div class="mb-3">
                <a class="btn btn-secondary" id="btnBulkStatusUpload">
                    Bulk Status Update
                </a>
            </div>
            <!-- code added by ptpatel on date 12-12-2025 -->
            <div class="table-container table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tag No</th>
                            <th>Bin No.</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id='inventory_tbl'></tbody>
                </table>
            </div>

        <?php
        } ?>
        <!-- end code added by ptpatel -->
        <!-- end accordion -->
        <div class="row mt-2">
            <div class="col-12">
                <?php
                // in segregation module some records save as a draft 
                if ($TabId == '67') //67 is segregation module
                {
                ?>
                    <?= Html::Button("Save as Draft", [
                        "class" => "btn btn-primary savedraftbutton savebutton",
                    ]) ?>
                <?php
                }
                ?>
                <?php if (!in_array($TabId, [69, 70])) { ?>
                    <?= Html::Button("Save", [
                        "class" => "btn btn-primary savebutton",
                    ]) ?>
                    <a href="<?= $cancelurl; ?>"><?= Html::Button("Cancel", [
                                                        "class" => "btn mod-close btn-secondary",
                                                        "name" => "btncancel",
                                                    ]) ?></a>
                <?php } ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>


    </div>
</div>

<!-- <script type="text/javascript" src="< $baseUrl;?>theme/libs/pristinejs/pristinejs.min.js"></script> -->
<!-- <script type="text/javascript" src="< $baseUrl;?>theme/js/pages/form-validation.init.js"></script> -->

<?php
$this->registerJsFile('@web/thememain/js/tetra/validator.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/validatorcalling.js', ['depends' => [AdminAsset::class]]);
?>
<?php
// Register your jQuery code using registerJs()
$this->registerJs('
    // alert("This is a jQuery alert!");
    $(document).ready(function() {
    
       
    });
', \yii\web\View::POS_READY);
?>
<!-- <script src="<baseUrl; ?>thememain/js/tetra/validator.js"></script>  -->
<script>

</script>

<!-- this model added by ptpatel on date 12-12-2025 -->
<?php if ($TabId == 69 || $TabId == 70) { ?>
    <div class="modal fade" id="bulkStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Bulk Status Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="bulkStatusForm">
                        <label><b>Select CSV File</b></label>
                        <input type="file" id="bulkStatusFile" accept=".csv" class="form-control">

                        <br>
                        <?php $samplelink = Yii::$app->urlManager->baseUrl . '/thememain/samples/bulkupdate_stickerremoval_cleaning_status.csv'; ?>
                        <a href="<?= $samplelink; ?>" download>Download Sample CSV</a>

                        <br><br>
                    </div>
                    <div id="bulkStatusMessage" class="alert d-none"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btnSubmitBulkStatus">Submit</button>
                </div>

            </div>
        </div>
    </div>
<?php } ?>
<div class="modal fade" id="importErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Import Aborted</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="errRowWrapper"><b>Row: </b><span id="errRow"></span></div>
                <div id="errFieldWrapper"><b>Field: </b><span id="errField"></span></div>
                <div id="errValueWrapper"><b>Value: </b><span id="errValue"></span></div>
                <p><b>Reason: </b> <span id="errReason"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- this model added by ptpatel on date 12-12-2025 -->