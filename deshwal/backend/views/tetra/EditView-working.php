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
      $scriptPath=$baseUrl."js/$ModuleName/edit.js";
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
<!-- <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request
    ->baseUrl; ?>/css/jquery.datetimepicker.css"/> -->

  <div class="modal-header">
        <h5 class="modal-title base-color" id="addLeadModalLabel"><img src="<?=$baseUrl ?>/thememain/img/lead_svgrepo.com.svg" class=" head-img">Add <?= ucfirst($ModuleName) ?></h5>
        <div class="toggle-container">
        <div class="toggle-switch" onclick="toggleRequiredFields()"></div>
        Show Required & Important Fields
        </div>
        <button type="button" class="btn-close" aria-label="Close"></button>
      </div>
    <div class="modal-body">
        <div class="create-form">
  
    <?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>
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
              <input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode"/>
              <!-- <input type="hidden" value="<?php
        //echo $RecordID;
        ?>" id="recordid" name="recordid"/> -->
              <input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module"/>
              <input type="hidden" value="<?php echo $sesionid; ?>" id="sesionid" name="sesionid"/>
              <input type="hidden" value="<?php echo $finaldate; ?>" id="finaldate" name="finaldate"/>
              <input type="hidden" value="<?php echo $finaldateshow; ?>" id="finaldateshow" name="finaldateshow"/>


          <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken;?>">
        <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName;?>">
              <!-- <input type="hidden" value="<?php
        //echo $MineName;
        ?>" id="mine_name" name="EditModel[
        
          <!-- Tabs Section -->


          <div class="col-md-3">
            <div class="left"  style="margin-top: 8px;">
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

                    if (!empty($Block->fields)) { ?>

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
          </div>


          <!-- Tab Content Section -->
           <div class="col-md-8">
            <div class="right">
              <div class="tab-content">
                <?php
                // $c = 1;
                // foreach ($ColumnList->blocks as $BlockKey => $Block) {
                //     if (!empty($Block->fields)) { 
                ?>
               
                      
                    <!--   <div class="tab-pane fade show <?= // $c == 1                          ? "active"                         : "" ?>" id="<?= $Block->blocklabel ?>" role="tabpanel" aria-labelledby="lead-info-tab">
                        <div class="title-tab">
                          <label class="title-info"><?= $Block->blocklabel ?></label>
                        </div> -->
                       <?php
                        // if ($Block->blocktype == "SimpleTwoCol") {
                           
                        //    require "SimpleTwoCol.php";
                        // }
                         ?>
                  
                      <!-- </div> -->


                    <?php 
                  // }
                  // }

                  //endforeach;
                  ?>

                  <?php
                $c = 1;
                foreach ($ColumnList->blocks as $block) {
                    if (!empty($block->fields)) { ?>
               
                      <!-- Lead Information Tab -->
                      <div class="tab-pane fade show <?= $c == 1
                          ? "active"
                          : "" ?>" id="<?= $Block->blocklabel ?>" role="tabpanel" aria-labelledby="lead-info-tab">
                        <div class="title-tab">
                          <label class="title-info"><?= $block->blocklabel ?></label>
                        </div>
                       <?php if ($block->blocktype == "SimpleTwoCol") {
                           //echo "Two column";
                           require "SimpleTwoCol.php";
                        } ?>
                  
                      </div>


                    <?php }
                  }

                  //endforeach;
                  ?>
               
              </div>
            </div>

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
              </div>

<div class="modal fade" id="modalreference" tabindex="-1" role="dialog" aria-labelledby="modalreferencelabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     

     
    </div>
  </div>
</div>

               <?php ActiveForm::end(); ?>
<script type="text/javascript">

   

$(document).on('click', '.mod-close', function() {
  
       $('#add-lead-modal').modal('hide');
    });
      // Toggle the 'active' class on the toggle switch when clicked

$(document).on('click', '.toggle-switch', function() {
  $(this).toggleClass('active');
  toggleRequiredFields();
});

// Function to toggle the visibility of required fields
function toggleRequiredFields() {
  // console.log("toggle");
  const isChecked = $('.toggle-switch').hasClass('active');
    const requiredFields = $('.not-required-field');

   // Show or hide fields based on the toggle state
  requiredFields.each(function() {
     $(this).css('display', isChecked ? 'none' : 'block');
     //alert($(this).isChecked);
  });
}
</script>

<?php
$this->registerJsFile($scriptPath, [
    "depends" => [AdminAsset::class],
]);
$this->registerJsFile("@web/theme/libs/pristinejs/pristinejs.min.js", [
    "depends" => [AdminAsset::class],
]);
$this->registerJsFile("@web/theme/js/pages/form-validation.init.js", [
    "depends" => [AdminAsset::class],
]);
// $this->registerJsFile('@web/theme/js/app.min.js', ['depends' => [AppAsset::class]]);
// ob_flush();

die();
 ?>
