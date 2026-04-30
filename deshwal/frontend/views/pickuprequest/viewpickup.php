<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->pickup_no." Pickup Detail";
?>

<div class="container">
    <h4 class="mt-2"><?= Html::encode($this->title) ?></h4>
   
    <div class="row">
        <div class="col-12 title-tab mt-2">
            <label class="title-info">Pickup Information</label>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Pickup Request No</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $model["pickup_no"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Pickup Address</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $model["pickup_address"]??"" ?>
                </div>
            </div>
        </div>
     
    
    </div>
</div>