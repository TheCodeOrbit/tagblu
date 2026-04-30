<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Create Employees');
$li_1 = 'Create Employees';
$title = 'Create Employees';

?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= Html::encode($title) ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><?= Html::a(Html::encode($li_1), 'javascript: void(0);') ?></li>
                    <?php if (isset($title)): ?>
                        <li class="breadcrumb-item active"><?= Html::encode($title) ?></li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="employee-form">

                    <?php $form = ActiveForm::begin([
                        'id' => 'pristine-valid-example', // Add ID for Pristine.js
                        'action' => ['employee/create'], // Specify the action to handle the submission
                        'method' => 'post',  // Method type
                    ]); ?>

                    <!-- CSRF Protection -->
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken); ?>

                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label>First name</label>
                                <input type="text" name="first_name" required data-pristine-required-message="Please Enter a First name"
                                    class="form-control" placeholder="First name" />
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label>Last name</label>
                                <input type="text" name="last_name" required data-pristine-required-message="Please Enter a Last name"
                                    class="form-control" placeholder="Last name" />
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label>Email</label>
                                <input type="email" name="email" required data-pristine-required-message="Please Enter a Email"
                                    class="form-control" placeholder="Enter your Email" />
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label>Phone Number</label>
                                <input type="number" name="phone_number" min="14" required data-pristine-required-message="Please Enter your Phone number"
                                    class="form-control" placeholder="Enter your Phone number" />
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="status" required class="form-control form-select" data-pristine-required-message="Please Select Status">
                                    <option value="">Select Type</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label for="example-datetime-local-input" class="form-label">Date and time</label>
                                <input class="form-control" name="date_time" type="datetime-local" data-pristine-required-message="Please Select Date and Time" id="example-datetime-local-input">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('Submit form', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
                <hr class="my-5">
            </div>
        </div>
    </div>
</div>