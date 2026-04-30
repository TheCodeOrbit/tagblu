<?php

use yii\helpers\Url;
use yii\helpers\Html;


use backend\assets\AppAsset;

AppAsset::register($this);


$this->title = Yii::t('app', 'Employees');

// Corrected variable assignment
$li_1 = ' Employees';
$title = ' Employees';

?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= Html::encode($this->title) ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><?= Html::a(Html::encode('Employees'), 'javascript: void(0);') ?></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Display a button for creating a new employee -->
    <p>
        <?= Html::a('Create Employee', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <!-- Table for displaying employee list -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>first Name </th>
                <th>Last Name </th>
                <th>Email</th>
                <th>Number</th>
                <th>Status</th>
                <th>Date Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($employees)): ?>
                <?php for ($i = 0; $i < count($employees); $i++): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= Html::encode($employees[$i]->first_name) ?></td>
                        <td><?= Html::encode($employees[$i]->last_name) ?></td>
                        <td><?= Html::encode($employees[$i]->email) ?></td>
                        <td><?= Html::encode($employees[$i]->phone_number) ?></td>
                        <td><?= Html::encode($employees[$i]->status) ?></td>
                        <td><?= Html::encode($employees[$i]->date_time) ?></td>
                        <td>
                            <?= Html::a('View', ['view', 'id' => $employees[$i]->id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Update', ['update', 'id' => $employees[$i]->id], ['class' => 'btn btn-warning']) ?>
                            <?= Html::a('Delete', ['delete', 'id' => $employees[$i]->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                    </tr>
                <?php endfor; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No employees found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>



<?php
// Register your jQuery code using registerJs()
$this->registerJs('
    alert("This is a jQuery alert!");
    $(document).ready(function() {
        console.log("This is a jQuery alert!");
    });
', \yii\web\View::POS_READY);
?>
