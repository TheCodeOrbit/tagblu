<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Exportrequest $model */
$baseUrl = Url::base();
$this->title = 'Create Export All Request';
?>
<style>
  .head-img {
    position: relative;
    top: 0px !important;
    padding: 10px;
    width: 55px;
  }
  </style>
<div class="container-d">
        <div class="row">
            <div class="col-6">
                 <img src="<?= $baseUrl; ?>/thememain/img/module-icon/exportrequest.png" class=" head-img">
                <span class="sm-modname"><?= Html::encode($this->title) ?></span>
            </div>

            
        </div>

        <div class="accordion-item row titlerow">
            <div class="accordion-header col-12 blocktitle2743">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2743">
                    <strong>EXPORT REQUEST DETAILS</strong>
                </button>
            </div>

            <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion">
                <div class="accordion-body">
                    <?= $this->render('_form', [
                            'model' => $model,
                            'modulenames'=>$modulenames,
                            'owners' =>$owners   
                        ]) ?>
                </div>
            </div>
    </div>
</div>
