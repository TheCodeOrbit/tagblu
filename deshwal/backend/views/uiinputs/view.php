<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\Uiinputs $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Uiinputs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="uiinputs-view">

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
            'name',
            'hidden_field',
            'password',
            'textarea:ntext',
            'file',
            'checkbox',
            'listbox',
            'dropdown_single',
            'checkboxlist',
            'radio_button',
            'referencetype',
            'DateTimePicker',
            'label',
            'dropdown_multipe',
            'MonthYearPicker',
            'DateTime',
            'date',
            'BatchList',
            'maskingdate',
        ],
    ]) ?>

</div>
