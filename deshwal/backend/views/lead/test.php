<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use backend\assets\AdminAsset;


// AdminAsset::register($this);
$baseUrl = Yii::$app->HomeUrl;
?>

<div class="page-content">
  <div class="records table-responsive">
    <div class="record-header">
      <div class="add">
        <img src="
          <?= $baseUrl;?>/thememain/img/lead icon.svg">
        <select name="" id="">
          <option value="">All Open Lead</option>
        </select>
      </div>
      <div class="browse">
        <img src="
            <?= $baseUrl;?>/thememain/img/flowbite-refresh-outline.svg">
        <button class="btn-1">
          <img src="
                <?= $baseUrl;?>/thememain/img/down.png" style="width:32px;">Import </button>
        <button class="btn" style="background: none;border: 1px solid var(--color-primary) !important; color: #585858;font-size: 12px;">
          <img src="
                  <?= $baseUrl;?>/thememain/img/List-view.png" style="width: 37px;"> List view </button>
        <img src="
                  <?= $baseUrl;?>/thememain/img/typcn-filter.svg">
        <img src="
                    <?= $baseUrl;?>/thememain/img/fluent-column-triple-edit-24-regular.svg">
        <button> + Add</button>
      </div>
    </div>
  </div>
</div>
<div class="select-1">
  <div class="container-d">
    <div class="col-md-12">
      section 1
     
    </div>
    
  </div>
  <div class="container-d">
    <div class="col-md-12">
      section 2
     
    </div>
    
  </div>
  <div class="container-d">
    <div class="col-md-12">
      section 3
     
    </div>

    <!----------start to code zitendra time duretion---------->
   
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('.timepicker').timepicker({
            showMeridian: false,
            showInputs: true
        });
    });
</script>
<input class="timepicker" type="text">

     <!----------end to code zitendra---------->
  </div>
</div>


