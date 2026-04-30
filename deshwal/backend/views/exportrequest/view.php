<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Exportrequest $model */

$this->title = $model->export_request_id;
$this->params['breadcrumbs'][] = ['label' => 'Exportrequests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="exportrequest-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'export_request_id' => $model->export_request_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'export_request_id' => $model->export_request_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'export_request_id',
            'ownerid',
            'creatorid',
            'modifiedby',
            'createdtime',
            'modifiedtime',
            'export_request_no',
            'from_date',
            'to_date',
            'status',
            'module_name',
            'deleted',
        ],
    ]) ?>

</div>
