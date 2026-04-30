<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\WorkflowTemplate $model */
$baseUrl = Url::base();
$this->title = 'Workflow Rule';
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
                 <img src="<?= $baseUrl; ?>/thememain/img/module-icon/workflowrule.png" class=" head-img">
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
                            'moduleList' => $moduleList,
                            'templateList' => $templateList,
                            'templatemodel' =>$templatemodel,
                        ]) ?>
                </div>
            </div>
    </div>
</div>
<?php $this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/js/workflowrule/edit.js', ['depends' => [AdminAsset::class]]);
?>