<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Add ');
//Add DataTables CSS CDN
$this->registerCssFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('https://unpkg.com/ag-grid-community/styles/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://unpkg.com/ag-grid-community/styles/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);

$url =Url::to(['create']);
?>