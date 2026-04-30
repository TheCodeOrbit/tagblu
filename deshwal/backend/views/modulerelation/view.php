<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\ModuleRelation $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Module Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="module-relation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'source_module',
            'related_module',
            'related_table',
            'related_tablekeyid',
            'related_fieldname',
            'related_recordfieldnme',
            'relation_with_account',
            'sequence',
            'deleted',
            'actions',
            'related_columns',
        ],
    ]) ?>

</div>
