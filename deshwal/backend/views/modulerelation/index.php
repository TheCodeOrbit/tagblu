<?php

use backend\assets\AdminAsset;
use backend\components\SvgRenderHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Exportrequest $model */
/** @var yii\widgets\ActiveForm $form */
$baseUrl = Url::base();
// echo $baseUrl;die;
// $this->registerCssFile('@web/thememain/css/bootstrap.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multiple.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css', ['depends' => [AdminAsset::class]]);

$this->title = 'Relation Modules';
$logo = 'modulerelation';
?>
<style>
  .head-img {
    position: relative;
    top: 0px !important;
    padding: 10px;
    width: 55px;
  }
</style>

<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add" style="
    padding: 12px;
    margin-top: 10px;
">
         <span class="icons-coll " >
            <?= SvgRenderHelper::renderIcon($logo.'.svg',true); ?>
        </span>
        <span class="sm-modname"><?= $this->title; ?></span>
        <br>
      </div>

      <div class="browse">
        <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg" 
          id="refresh-icon"
          alt="Refresh"
          title="Refresh Page">


        <!-- <a href="<?php echo Yii::$app->urlManager->createUrl('modulerelation/create'); ?>" class="add-lead-btn2"><button>+ Add</button></a> -->

      </div>
    </div>
  </div>
</div>
<div class="select-1">
  <div class="container-d">
    <div class="accordion-item row titlerow">
            <!-- <div class="accordion-header col-12 blocktitle2743">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2743">
                    <strong><?=  $this->title; ?> </strong>
                </button>
            </div> -->
            <!-- <div id="collapse2743" class="accordion-collapse collapse show" data-bs-parent="#simpleAccordion"> -->
                <!-- <div class="accordion-body"> -->
                  <div class="row mb-2">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-8">
                    <!-- listing -->
                      <!-- <div class="card"> -->
                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group field-picklist-module_name">
                                <label class="control-label" for="picklist-module_name">
                                  Module
                                </label><br>
                                <select id="source_module"
                                  name="Modulerelation[source_module]"
                                  class="form-control productinput singleselect"
                                  aria-invalid="false">
                                  <option value=""> Select </option>
                                  <?php foreach ($tablabels as $value): ?>
                                    <option value="<?= $value->tabid ?>">
                                      <?= htmlspecialchars($value->tablabel) ?>
                                    </option>
                                  <?php endforeach; ?>
                                </select>
                                <div class="help-block"></div>
                              </div>
                            </div>
                        </div>
                        <div class="table-responsive pt-2">
                          <table id="relatedtable" class="table table-striped table-bordered" width="100%" cellspacing="0"
                            style="text-align: left !important">
                            <thead>
                              <tr>
                                <th>Related Module</th>
                                <th>Related Columns</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                          </table>

                        </div>
                      <!-- </div> -->
                      <!-- pagination start -->
                        <!-- <div class="footer-controls">
                            <div class="left-controls">
                                <div id="valueShow">
                                    Show:
                                    <select id="pageSizeSelect" class="form-select form-select-sm" style="width:auto;display:inline-block">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                            </div>
                            <div class="right-controls">
                                <div id="paginationContainer">
                                    <div class="pagination-buttons">
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- pagination end -->
                    </div>
                    <div class="col-lg-2"></div>
                  </div>
                <!-- </div> -->
            <!-- </div> -->
      </div>
  </div>
</div>




<div class="modal fade" id="relatedcolumnModal" tabindex="-1" aria-labelledby="editModalSummeryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <label id="modal_label_name"> </label>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- hidden fields -->
                <input type="hidden" name="_csrf" id="csrfToken" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" id="editRecordId" name="editRecordId">
                <input type="hidden" id="relatedmoduleid" name="relatedmoduleid">
                <input type="hidden" id="sourcemoduleid" name="sourcemoduleid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group field-picklist-table_name">
                      <label class="control-label" for="picklist-table_name">
                        Select Related Column 
                      </label><br>
                      <select id="related_columns" multiple max="3"
                        name="Modulerelation[related_columns][]"
                        class=" form-control productinput multySelect "
                        aria-invalid="false">
                        <option value="">Select Field</option>
                      </select>
                      <div class="help-block"></div>
                    </div>
                    </div>
                  </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary savemodulerelation">Save</button>
            </div>
        </div>
    </div>
</div>
<?php
// $this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/single-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/multilist-dd.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/modulerelation/edit.js', ['depends' => [AdminAsset::class]]);
?>