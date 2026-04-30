<?php

use backend\models\Uiinputs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\UiinputsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Uiinputs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uiinputs-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Uiinputs', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'hidden_field',
            'password',
            'textarea:ntext',
            //'file',
            //'checkbox',
            //'listbox',
            //'dropdown_single',
            //'checkboxlist',
            //'radio_button',
            //'referencetype',
            //'DateTimePicker',
            //'label',
            //'dropdown_multipe',
            //'MonthYearPicker',
            //'DateTime',
            //'date',
            //'BatchList',
            //'maskingdate',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Uiinputs $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
