<?php

use app\models\Exportrequest;
use backend\assets\AdminAsset;

AdminAsset::register($this);
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$baseUrl = Url::base();

$this->title = 'Exportrequests';

// $this->registerCssFile('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]); ?>
<!-- <div class="exportrequest-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Exportrequest', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?php //echo  GridView::widget([
        // 'dataProvider' => $dataProvider,
        // 'columns' => [
        //     ['class' => 'yii\grid\SerialColumn'],

        //     'export_request_id',
        //     'ownerid',
        //     'creatorid',
        //     'modifiedby',
        //     'createdtime',
            //'modifiedtime',
            //'export_request_no',
            //'from_date',
            //'to_date',
            //'status',
            //'module_name',
            //'deleted',
    //         [
    //             'class' => ActionColumn::className(),
    //             'urlCreator' => function ($action, Exportrequest $model, $key, $index, $column) {
    //                 return Url::toRoute([$action, 'export_request_id' => $model->export_request_id]);
    //              }
    //         ],
    //     ],
    // ]); ?>


</div> -->
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
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/exportrequest.png" class=" head-img">
        <span class="sm-modname">Export All Request</span>
        <br>
      </div>

      <div class="browse">
        <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
          id="refresh-icon"
          alt="Refresh"
          title="Refresh Page">

            
          <a href="<?php echo Yii::$app->urlManager->createUrl('exportrequest/create'); ?>" class="add-lead-btn2"><button>+ Add</button></a>

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
                    <th>Export Request No</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Module Name</th>
                    <th>Links</th>
                    <!-- <th>Action</th> -->
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
$this->registerJsFile('@web/thememain/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/js/exportrequest/edit.js', ['depends' => [AdminAsset::class]]); ?>
