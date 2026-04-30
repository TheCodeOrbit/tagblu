<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\assets\AdminAsset;
AdminAsset::register($this);
$this->registerCssFile('@web/thememain/css/extrabtn.css', ['depends' => [AdminAsset::class]]);
?>

<?php $form = ActiveForm::begin([
    'id' => 'my-form',
    'action' => ['edititems?Record='.$Recordid],
    'method' => 'POST',
    
]); ?>
            <div class="row">
           
 <!-- Hidden input field with a value that will be sent when the form is submitted -->
 <?= Html::hiddenInput('inspectionitems', base64_encode($Recordid)); ?>
 <?= Html::hiddenInput('mode', 'edit'); ?>
 <?= Html::hiddenInput('module', $ModuleName); ?>
                <?php if(isset($AddLaptopDetail) && $AddLaptopDetail === true): ?>
                    <div class="col-lg-3 col-sm-6"><center><button class="inspection-laptop-detail detail-view-btn-gen"  type="submit" name="AddLaptopDetail"><span class="">Add Laptop Inspection</span></center></button></div>
                <?php endif ?>
                <?php if(isset($AddDesktopDetail) && $AddDesktopDetail === true): ?>
                    <div class="col-lg-3 col-sm-6"><center><button class="inspection-desktop-detail detail-view-btn-gen"  type="submit" name="AddDesktopDetail"><span class="">Add Desktop Inspection</span></center></button></div>
                <?php endif ?>
                <?php if(isset($AddTFTDetail) && $AddTFTDetail === true): ?>
                    <div class="col-lg-3 col-sm-6"><center><button class="inspection-tft-detail detail-view-btn-gen"  type="submit" name="AddTFTDetail"><span class="">Add TFT Inspection</span></center></button></div>
                <?php endif ?>
                <?php if(isset($AddGeneralDetail) && $AddGeneralDetail === true): ?>
                    <div class="col-lg-3 col-sm-6"><center><button class="inspection-general-detail detail-view-btn-gen" type="submit" name="AddGeneralDetail"><span class="">Add General Inspection</span></center></button></div>
                <?php endif ?>
          </div>
          
<?php ActiveForm::end(); ?>