<?php

use backend\assets\AdminAsset;
use common\models\Picklist;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$baseUrl = Url::base();
$this->title = 'Picklists';
$this->registerCssFile('@web/thememain/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]); ?>
<style>
  .head-img {
    position: relative;
    top: 0px !important;
    padding: 10px;
    width: 55px;
  }
  </style>
<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add">
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/picklist.png" class=" head-img">
        <span class="sm-modname">Picklist</span>
        <br>
      </div>

      <div class="browse">
        <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
          id="refresh-icon"
          alt="Refresh"
          title="Refresh Page">

            
          <a href="<?php echo Yii::$app->urlManager->createUrl('picklist/create'); ?>" class="add-lead-btn2"><button>+ Add</button></a>

      </div>
    </div>
  </div>
</div>
<div class="select-1">
  <div class="container-d">
    <div class="col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-md-12 " style="">
            <div class="table-responsive">
              <table id="dtrecord" class="table table-striped table-bordered" width="100%" cellspacing="0"
                style="text-align: left !important">

                <thead>
                  <tr>
                    <th>Target Table</th>
                    <th>Target Field</th>
                    <th>Display Field</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/js/exportrequest/edit.js', ['depends' => [AdminAsset::class]]); ?>

