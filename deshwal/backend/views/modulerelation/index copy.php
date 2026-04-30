<?php

use backend\models\ModuleRelation;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\ModuleRelationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Module Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-relation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Module Relation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'source_module',
            'related_module',
            'related_table',
            'related_tablekeyid',
            //'related_fieldname',
            //'related_recordfieldnme',
            //'relation_with_account',
            //'sequence',
            //'deleted',
            //'actions',
            //'related_columns',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ModuleRelation $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
