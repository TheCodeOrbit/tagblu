<?php

use backend\assets\AdminAsset;

$baseUrl = Yii::$app->HomeUrl;

/** @var int $subcategory */
/** @var string $ModuleName */
$this->registerCss("
    .ag-paging-row-summary-panel {
        display: none !important;
    }
    .ag-paging-description > #ag-30-start-page,
    .ag-paging-description > #ag-30-of-page,
    .ag-paging-description > #ag-30-of-page-number {
        display: none !important;
    }
");
?>

<script type="text/javascript" src="<?= $baseUrl; ?>thememain/jquery/jquery.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/ag-grid-community.min.js"></script>

<?php
$scriptPath = $baseUrl . "js/$ModuleName/detailedit.js";
?>
<script type="text/javascript" src="<?= $scriptPath ?>"></script>

<script type="text/javascript" src="<?= $baseUrl; ?>js/inventoryageing/detailedit.js"></script>
<div class="toolbar">
  <h5 class="reporttitle"><?= $TabLabel; ?> Report > Subcategory : <?= $subcategory_value; ?></h5>
  <input type="hidden" value="<?= $subcategory;?>" name="subcategory_id" id="subcategory_id">
  <div class="d-flex align-items-center gap-2">
    <div class="dropdown  mb-3">
      <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="exportAllButton" data-bs-toggle="dropdown" aria-expanded="false">
        Export All
      </button>
      <ul class="dropdown-menu" aria-labelledby="exportAllButton">
        <li><a class="dropdown-item" href="#" id="detailexportExcel">Export as Excel</a></li>
        <li><a class="dropdown-item" href="#" id="detailexportPDF">Export as PDF</a></li>
      </ul>
    </div>

    <button class="btn btn-sm btn-secondary mb-3" id="backToGrid">← Back</button>
  </div>
</div>
</div>

<div class="table-list">
  <div class="ag-theme-alpine">
    <div id="reportdetailsGrid" class="ag-theme-alpine" style="height: 500px;"></div>
  </div>
</div>