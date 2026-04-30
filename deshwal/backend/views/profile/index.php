<?php
use backend\assets\AdminAsset;

AdminAsset::register($this);
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$baseUrl = Url::base();

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'LIST PROFILES';
// $this->params['breadcrumbs'][] = $this->title;
// $this->registerCssFile('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]); 
?>

<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add">
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/profile.png" class=" head-img">
        <span class="sm-modname">Profile</span>
        <br>

      

      </div>

      <div class="browse">
        <img src="<?= $baseUrl; ?>/thememain/img/flowbite-refresh-outline.svg"
          id="refresh-icon"
          alt="Refresh"
          title="Refresh Page">

        <script>
          // JavaScript to reload the page when the image is clicked
          document.getElementById("refresh-icon").addEventListener("click", function() {
            location.reload(); // Reload the current page
          });
        </script>
  
       
          <a href="<?php echo Yii::$app->urlManager->createUrl('profile/create'); ?>" class="add-lead-btn2"><button>+ Add</button></a>

      </div>
    </div>
  </div>
</div>
<div class="select-1">
  <div class="container-d">
    <div class="col-lg-12">
      <div class="card">
        <div class="row">

          <div class="col-md-6 text-left">
            <h3> <?= $this->title ?> </h3>
          </div>
         

        </div>
        <div class="row">
          <div class="col-md-12 " style="">
            <div class="table-responsive">
              <table id="dtrecord" class="table table-striped table-bordered" width="100%" cellspacing="0"
                style="text-align: left !important">

                <thead>
                  <tr>
                    <th>Profile Id</th>
                    <th>Profile Name</th>
                    <th>Description</th>
                    <!-- <th>Enabled</th> -->
                    <th>Action</th>
                  </tr>
                </thead>
              </table>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/js/profile/edit.js', ['depends' => [AdminAsset::class]]); ?>