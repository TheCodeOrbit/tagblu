<?php
use yii\helpers\Html;

/** @var string $message */
$this->title = 'Error Occurred';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-exclamation-triangle-fill"></i> Error
                    </h4>
                </div>

                <div class="card-body">
                    <p class="lead">
                        <h5><?= Html::encode($message) ?></h5>
                    </p>
                </div>

                <div class="card-footer text-end">
                    <a href="<?= Yii::$app->request->referrer ?: Yii::$app->homeUrl ?>" class="btn btn-outline-danger">
                        &laquo; Go Back
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
