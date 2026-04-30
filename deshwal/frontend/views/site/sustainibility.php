<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
use frontend\assets\AppAsset;
AppAsset::register($this);
/** @var yii\web\View $this */
$this->title = 'Sustainability Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// $this->registerJsFile('https://cdn.jsdelivr.net/npm/apexcharts', ['depends' => [AppAsset::class]]);

$this->registerCssFile('@web/css/sustain.css',['depends' => [AppAsset::class]]);
$this->registerJsFile('@web/js/apexcharts.min.js', ['depends' => [AppAsset::class]]);
$this->registerJsFile('@web/js/sustain.js', ['depends' => [AppAsset::class]]);

// Pass PHP arrays to JavaScript
$chartDataJs = <<<JS
window.RECYCLE = {$this->render('_json', ['data' => $recycle])};
window.RESALE = {$this->render('_json', ['data' => $resale])};
window.RECYCLE_COMPONENTS = {$this->render('_json', ['data' => $recycleComponents])};
window.RESALE_COMPONENTS = {$this->render('_json', ['data' => $resaleComponents])};
window.WASTE_SEGMENTS = {$this->render('_json', ['data' => $wasteSegments])};
window.IMPACT_SAVINGS = {$this->render('_json', ['data' => $impactSavings])};
window.LANDFILL_PIE = {$this->render('_json', ['data' => $landfillPie])};
JS;

$this->registerJs($chartDataJs, yii\web\View::POS_HEAD);
?>

<div class="container py-4">
  <h2 class="dashboard-title mb-4">Sustainability Dashboard</h2>

<div class="row g-4 mb-4">

  <!-- ♻️ Recycle Card -->
  <div class="col-lg-6">
    <div class="card shadow-sm rounded-4 p-3">
      <!-- <h5 class="fw-bold mb-3">♻️ Recycle</h5> -->
      <h5 class="mb-3">♻️ Recycle</h5>
      <div class="row g-3">
        <div class="col-4">
          <div class="metric-tile bg-primary text-white">Weight (MT)<br><strong>0.475</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-danger text-white">CO2 Emission Saved (MT)<br><strong>0.95</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-success text-white">Energy (KL)<br><strong>0.35</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-info text-white">Water (KL)<br><strong>451.25</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-warning text-white">Raw Materials (KL)<br><strong>332.5</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-teal text-white">Trees Saved<br><strong>43</strong></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔧 Refurbished Card -->
  <div class="col-lg-6">
    <div class="card shadow-sm rounded-4 p-3">
      <!-- <h5 class="fw-bold mb-3">🔧 Refurbished</h5> -->
      <h5 class="mb-3">🔧 Refurbished</h5>
      <div class="row g-3">
        <div class="col-4">
          <div class="metric-tile bg-primary text-white">Weight (MT)<br><strong>0.285</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-danger text-white">CO2 Emission Saved (MT)<br><strong>38.475</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-success text-white">Energy (KL)<br><strong>2.67</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-info text-white">Water (KL)<br><strong>2565</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-warning text-white">Raw Materials (KL)<br><strong>256.5</strong></div>
        </div>
        <div class="col-4">
          <div class="metric-tile bg-teal text-white">Trees Saved<br><strong>1909</strong></div>
        </div>
      </div>
    </div>
  </div>

</div>

  <!-- <div class="row section">
    <div class="col-md-6">
      <h5>Recycle</h5>
      <div class="row">
        <div class="col-4"><div class="card-metric blue">Weight (MT)<br><strong><?= Html::encode($recycle['weight']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric red">CO2 Emission Saved (MT)<br><strong><?= Html::encode($recycle['co2']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric green">Energy (KL)<br><strong><?= Html::encode($recycle['energy']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric cyan">Water (KL)<br><strong><?= Html::encode($recycle['water']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric orange">Raw Materials (KL)<br><strong><?= Html::encode($recycle['rawmaterail']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric teal">Trees Saved<br><strong><?= Html::encode($recycle['trees']) ?></strong></div></div>
      </div>
    </div>
    <div class="col-md-6">
      <h5>Refurbished</h5>
      <div class="row">
        <div class="col-4"><div class="card-metric blue">Weight (MT)<br><strong><?= Html::encode($resale['weight']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric red">CO2 Emission Saved (MT)<br><strong><?= Html::encode($resale['co2']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric green">Energy (KL)<br><strong><?= Html::encode($resale['energy']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric cyan">Water (KL)<br><strong><?= Html::encode($resale['water']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric orange">Raw Materials (KL)<br><strong><?= Html::encode($resale['rawmaterail']) ?></strong></div></div>
        <div class="col-4"><div class="card-metric teal">Trees Saved<br><strong><?= Html::encode($resale['trees']) ?></strong></div></div>
      </div>
    </div>
  </div> -->

  <!-- Chart containers -->
  <!-- <div class="section">
    <div class="chart-card">Total Resale and Recycle by Waste segments</div>
    <div class="chart-wrapper"><div id="wasteChart"></div></div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="section">
        <div class="chart-card">Recycle Components (MT)</div>
        <div class="chart-wrapper"><div id="recycleComponentsChart"></div></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="section">
        <div class="chart-card">Resale Components (MT)</div>
        <div class="chart-wrapper"><div id="resaleComponentsChart"></div></div>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="chart-card">Total Environmental Impact Saving</div>
    <div class="chart-wrapper"><div id="envImpactChart"></div></div>
  </div>

  <div class="section">
    <h4 class="fw-bold">LANDFILL IMPACT</h4>
    <div class="chart-card">Landfill Impact - Figures in Thousand</div>
    <div class="chart-wrapper"><div id="landfillPieChart"></div></div>
  </div>
</div> -->
