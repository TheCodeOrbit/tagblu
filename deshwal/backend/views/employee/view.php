<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */

$this->title = $model->first_name . ' ' . $model->last_name;
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this employee?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <td><?= Html::encode($model->id) ?></td>
    </tr>
    <tr>
        <th>First Name</th>
        <td><?= Html::encode($model->first_name) ?></td>
    </tr>
    <tr>
        <th>Last Name</th>
        <td><?= Html::encode($model->last_name) ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= Html::encode($model->email) ?></td>
    </tr>
    <tr>
        <th>Phone Number</th>
        <td><?= Html::encode($model->phone_number) ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?= Html::encode($model->status) ?></td>
    </tr>
</table>
