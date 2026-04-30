<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = 'Create Profile';
$this->params['breadcrumbs'][] = ['label' => 'Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Url::base();
?>
<div class="select-1">
    <div class="card">
        <div class="row">
            <div class="col-md-12">
                <div class="row">

                    <div class="col-md-6 text-left">
                        <h3><?= $this->title ?></h3>
                    </div>

                    <div class="col-md-6 text-right">
                        <a href="<?php echo Yii::$app->UrlManager->createUrl('profile/index'); ?>"><img src="<?php echo $baseUrl; ?>/assets/images/back-listview.png" alt="Back" class="back-btn"></a>
                    </div>
                </div>

                <?= $this->render('_form', [
                    'model' => $model,
                    'classadd' => '',
                    'widgets' => $widgets,
                ]) ?>

            </div>
        </div>
    </div>
</div>