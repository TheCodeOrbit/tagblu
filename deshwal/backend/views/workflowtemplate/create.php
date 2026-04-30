<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\WorkflowTemplate $model */
$baseUrl = Url::base();
$this->title = 'Workflow Template';

//  $this->registerJsFile('@web/js/ckeditor/ckeditor.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile(
//     'https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js',
//     ['position' => \yii\web\View::POS_HEAD]
// );
// $this->registerJsFile('@web/ckeditor/ckeditor/ckeditor.js', [
//     'position' => \yii\web\View::POS_HEAD,
// ]);
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
                 <img src="<?= $baseUrl; ?>/thememain/img/module-icon/workflowtemplate.png" class=" head-img">
                <span class="sm-modname"><?= Html::encode($this->title) ?></span>
            </div>

            
        </div>

        <div class="accordion-item row titlerow">
            <div class="accordion-header col-12 blocktitle2743">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2743">
                    <strong><?php echo $this->title; ?> </strong>
                </button>
            </div>

            <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion">
                <div class="accordion-body">
                    <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                </div>
            </div>
    </div>
</div>
