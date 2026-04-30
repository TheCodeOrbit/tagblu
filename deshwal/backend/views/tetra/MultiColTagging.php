<?php

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

$cancelurl = $baseUrl . $ModuleName . "/dashboard";

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
    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
    <input type="hidden" value="<?php echo $RecordId; ?>" id="record" name="record" />
    <input type="hidden" value="<?php echo $taggingdetails; ?>" id="taggingdetails" name="taggingdetails" />
    <div class="container-d">
        <div class="row">
            <div class="col-6">
                <img src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img-create">
                <span class="sm-modname"><?php if ($ActionName == "Create")
                    echo "Bulk Upload";
                else
                    echo $ActionName; ?> <?= $TabLabel; ?></span>
            </div>



        </div>

        <div id="error-div"></div>

        <?php
        // $form = ActiveForm::begin([
        //     "id" => "pristine-valid-example",
        //     'options' => [
        //         'enctype' => 'multipart/form-data', // Required for file uploads
        //     ],
        // ]); 
        ?>


        <input type="hidden" value="<?php echo "bulkuplaodtagging"; ?>" id="mode" name="mode" />
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
        
            if ((!empty($block->$relationName) || $block->blocklabel === "SYSTEM GENERATED" || $block->blocklabel == 'OEM MANAGER DETAILS') && $block->display_status == 1) {

                if ($block->blocklabel == 'OEM MANAGER DETAILS')
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

                        <?php if (!($block->blocklabel == "OEM MANAGER DETAILS" && $roleId == "H19")) { //H19 = Deshwal Finance manager
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
                                    //require "Multirecord.php";
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
        <div id="csvProgress" style="display:none;margin-top:10px;">
            <div class="progress">
                <div id="csvProgressBar" class="progress-bar" style="width:0%;">0%</div>
            </div>
        </div>

        <input type="file" id="uploadCsvInput" name="csv_file" accept=".csv" style="display:none;">
        <button id="uploadCsvBtn" class="btn btn-secondary">Upload CSV</button>



        <button id="downloadCsvBtn" class="btn btn-primary">Download CSV</button>
        <?php 
        $subcategory = explode('_',$taggingdetails)[2];
        $isLaptop = (new \yii\db\Query())
            ->select(['sub_catagory_value'])
            ->from('prod_sub_catagory')
            ->where(['is_active' => 1])
            ->andWhere(['sub_catagory_id' => $subcategory])
            ->scalar();
            if(trim(strtolower($isLaptop)) == "laptop"){
                echo Html::a(
                    'Download Bin List CSV',
                    ['download-bins-csv'],
                    ['class' => 'btn btn-secondary']
                );
            }
        ?>
        
        <div id="csvPreviewContainer" class="" style="display:none;">
            <h5>CSV Preview</h5>
            <div class="row mb-2" id="previewTools" style="display:none;">
                <div class="col-md-3">
                    <input type="text" id="csvSearch" class="form-control" placeholder="Search...">
                </div>

                <div class="col-md-2">
                    <select id="pageSizeSelect" class="form-control">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                </div>
            </div>

            <table id="csvPreviewTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th data-col="id" class="prev-tag-sortable">ID</th>
                        <th data-col="product_name" class="prev-tag-sortable">Product Name</th>
                        <th data-col="category" class="prev-tag-sortable">Category</th>
                        <th data-col="subcategory" class="prev-tag-sortable">Sub Category</th>
                        <th data-col="serial" class="prev-tag-sortable">Serial</th>
                        <th data-col="tag" class="prev-tag-sortable">Tag</th>
                        <th data-col="bin" class="prev-tag-sortable">Bin</th>
                        <th data-col="status" class="prev-tag-sortable">Status</th>
                        <th>Validation</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
            <div id="csvPagination" class="mt-2" style="display:none;">
                <button id="prevPage" class="btn btn-sm btn-secondary">Prev</button>
                <span id="pageInfo" class="mx-2"></span>
                <button id="nextPage" class="btn btn-sm btn-secondary">Next</button>
            </div>

            <br>
            <button id="confirmUpdateBtn" class="btn btn-success" style="display:none;">
                Confirm Update
            </button>
            <a href="dashboard" id="uploadCancel" class="btn btn-secondary" style="display:none;">Cancel</a>

        </div>


        <?php //ActiveForm::end(); ?>


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