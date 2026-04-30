<?php
error_reporting(-1);
ini_set("display_errors", true);
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use backend\assets\AdminAsset;

AdminAsset::register($this);

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
//registerJsFile($scriptPath, ['depends' => [\yii\web\JqueryAsset::class]]);
//print_r($_SESSION);
// $MineNamee=$_SESSION['cms_mine_name'];
// if($MineNamee=='pekb'){
// $MineName=1;
// }elseif($MineNamee=='talabira'){
// $MineName=2;
// }elseif($MineNamee=='gp3'){
// $MineName=3;
// }elseif($MineNamee=='kurmitar'){
// $MineName=4;
// }else{

// $MineName=5;

// }

//echo "<pre>";print_r($invmngrule);exit;
//print_r($_SESSION);
// echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";die;
// echo $ActionUrl=Yii::$app->createAbsoluteUrl($ModuleName)."/";die;
//echo "<br>ActionUrl=$ActionUrl";
// $this->pageTitle=Yii::$app->name . " - $ModuleName";
//$this->breadcrumbs=array('Customer',);
$fullurl = Yii::$app->request->getUrl();
$baseUrl = Yii::$app->HomeUrl;

//echo $fullurl ; exit ;

?>
<!-- // <script src="<?php
//echo Yii::$app->request->baseUrl;
?>/js/chosen.jquery.min.js"></script> -->

<!-- <script src="<?php
//echo Yii::$app->request->baseUrl;
?>/js/jquery.inputmask.bundle.js"></script> -->
<!-- <script type="text/javascript">var fullurl = "<?= $fullurl ?>";</script> -->
<!-- // <script src="<?php
//echo Yii::$app->request->baseUrl;
?>/js/cookie.js"></script> -->
<!-- <link rel="stylesheet" type="text/css" href="<?php //echo Yii::$app->request->baseUrl; ?>/css/jquery.datetimepicker.css"/> -->

<div class="modal-header">
  <h5 class="modal-title base-color" id="addLeadModalLabel"><img
      src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img-create"><?php if ($ActionName == "Create")
            echo "Add";
          else
            echo $ActionName; ?> <?= $TabLabel ?></h5>
  
  <button type="button" class="btn-close mod-close" aria-label="Close"></button>
</div>
<div class="modal-body" id="modalBody">
  <div class="create-form">

    <?php $form = ActiveForm::begin([
      "id" => "pristine-valid-example",
      'options' => [
        'enctype' => 'multipart/form-data', // Required for file uploads
      ],
    ]); ?>
    <div class="row">
      <?php
      $currentdate = date("Y-m-d");
      $finaldate = strtolower(
        date("Y-m-d", strtotime("-1 day", strtotime($currentdate)))
      );

      $currentdate1 = date("d-m-Y");
      $finaldateshow = strtolower(
        date("d-m-Y", strtotime("-1 day", strtotime($currentdate1)))
      );
      ?>
      <!-- <span class="note">Fields with <span class="required star">&nbsp;*&nbsp;</span> are required.</span> -->
      <input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode" />
      <!-- <input type="hidden" value="<?php
      //echo $RecordID;
      ?>" id="recordid" name="recordid"/> -->
      <input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
      <input type="hidden" value="<?php echo $sesionid; ?>" id="sesionid" name="sesionid" />
      <input type="hidden" value="<?php echo $finaldate; ?>" id="finaldate" name="finaldate" />
      <input type="hidden" value="<?php echo $finaldateshow; ?>" id="finaldateshow" name="finaldateshow" />


      <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
      <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
      <!-- <input type="hidden" value="<?php
      //echo $MineName;
      ?>" id="mine_name" name="EditModel[
        
          <!-- Tabs Section -->


      <!-- <div class="col-md-3 fontlist">
            <div class="left">
              <ul class="nav flex-column nav-pills side-popup-add" role="tablist" aria-orientation="vertical">
                <?php
                $c = 1;
                foreach ($ColumnList->blocks as $BlockKey => $Block) {
                  // <!-- UI types
                  // 1 : textField
                  // 2 :hidden
                  // 8 :dropDownList
                  // 12 :referencetype
                  // 4 :TExtarea
                  // 13 :DateTimePicker -->
                
                  if (!empty($Block->$relationName) && $Block->blocklabel != "SYSTEM GENERATED") { ?>

                <li class="nav-item">
                  <a class="nav-link  <?= $c == 1
                    ? "active"
                    : "" ?>" id="<?= $Block->blocklabel ?>-tab" data-toggle="pill" href="#<?= $Block->blocklabel ?>" role="tab"><?= $Block->blocklabel ?></a>
                </li>
                <?php }
                  $c++;
                }
                ?>
              </ul>
            </div>
          </div> -->


      <!-- Tab Content Section -->
      <div class="col-md-12">
        <div class="right">
          <div class="tab-content">

            <?php
            $c = 1;

            foreach ($ColumnList->blocks as $block) {
              if (!empty($block->$relationName)) {

                if ($block->blocklabel === "SYSTEM GENERATED")
                  $cls = "tr-hidden";
                else
                  $cls = "";
                ?>

                <!-- Lead Information Tab -->
                <?php $title_tab_class = $block->blocklabel ? "title-tab" : ""; // 19-12-2024 bharat - if block heading is empty in field table ?>
                <div class="tab-pane fade show <?= $c == 1
                  ? "active"
                  : "" ?> <?= $cls ?>" id="<?= $block->blocklabel ?>" role="tabpanel" aria-labelledby="lead-info-tab">
                  <div class="<?= $title_tab_class ?>">
                    <label class="title-info"><?= $block->blocklabel ?></label>
                  </div>
                  <?php 
                    require "SimpleTwoColContactrole.php";
                   ?>

                </div>
                <!--end tab pane  -->

              <?php }
            }

            //endforeach;
            ?>

          </div>
          <!-- end tab content -->
        </div>
        <!-- end  right section -->

      </div>
    </div>

  </div>

</div>

<div class="modal-footer">
  <?= Html::Button("Save", [
    "class" => "btn btn-primary savebutton",
  ]) ?>
  <?= Html::Button("Cancel", [
    "class" => "btn mod-close btn-secondary",
    "name" => "btncancel",
  ]) ?>
  <button type="button" id="backToTop" class="btn btn-primary" style="display: none;">Back to Top</button>
</div>


<?php ActiveForm::end(); ?>
<script type="text/javascript">



</script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/select2.min.css">
<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/multilist-dd.css">
<script type="text/javascript" src="<?= $scriptPath ?>"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/libs/pristinejs/pristinejs.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/js/pages/form-validation.init.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/editview.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/single-dd.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script>

<!-- for deepika validator start -->


<!-- <script type="text/javascript" src="<?= $scriptPath ?>"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/libs/jquery/jquery.min.js"></script> -->
<!-- Link to your validator.js file -->
  <script src="<?= $baseUrl; ?>thememain/js/tetra/validator.js"></script>  

<!-- Your custom script (optional) -->
    <script>
 
        $(document).ready(function() {
          
          const validator = new Validator();

$(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave").on("change", function () { //alert('dsfs');
    if ($(this).is(":visible") || $(this).hasClass("leave")) {
        validator.validateField($(this));
    }
});

$(".savebutton").on("click", function (e) {
    let isValid = true;

    $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave").each(function () {
        if ($(this).is(":visible") || $(this).hasClass("leave")) {
            if (!validator.validateField($(this))) {
                isValid = false;
            }
        }
    });

    if (!isValid) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $(".help-block:visible:first").offset().top
        }, 500);
    } else {
        $("#pristine-valid-example").submit();
    }
});
            // Make sure the Validator object exists and validateField is a function
            console.log(window.Validator); // This should display the Validator object in console
              window.Validator.validateField($('leadinformation[firstname]')); // Replace with an actual field

            $(".form-control").on("blur change", function() {
                // Check if Validator.validateField is available and call it
                if (typeof window.Validator.validateField === "function") {
                    window.Validator.validateField($(this));
                } else {
                    console.error("Validator.validateField is not a function.");
                }
            });
        });
    </script>  


<!-- end deepika validator -->
<?php

// $scriptPath=$baseUrl."js/$ModuleName/Edit.js";
// $this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile($scriptPath, ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/libs/theme/js/pages/form-validation.init.js', ['depends' => [AdminAsset::class]]);

// $this->registerJsFile($scriptPath, [
//     "depends" => [AdminAsset::class],
// ]);
// $this->registerJsFile("@web/theme/libs/pristinejs/pristinejs.min.js", [
//     "depends" => [AdminAsset::class],
// ]);
// $this->registerJsFile("@web/theme/js/pages/form-validation.init.js", [
//     "depends" => [AdminAsset::class],
// ]);
// $this->registerJsFile('@web/theme/js/app.min.js', ['depends' => [AppAsset::class]]);
// ob_flush();

die();
?>