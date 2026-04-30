<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use backend\models\AccessCheck;

AppAsset::register($this);
$model = new AccessCheck();
$id = Yii::$app->user->id;
$moduleName = Yii::$app->controller->module->id;
if($moduleName === "app-backend")
$moduleName = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$access = $model->checkpermission($id,ucfirst($moduleName),$action);
// echo "access = $access";exit;
if($access==0){

Yii::$app->response->redirect(Url::to(['site/InvalidAccessError']))->send();
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= \yii\helpers\Url::to('@web/theme/images/favicon.ico') ?>">
    <script src="https://unpkg.com/feather-icons"></script> 
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>   
    <?php $this->head() ?>
</head>
<body class="pace-done">
    <?php $this->beginBody() ?>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Top bar -->
       
        <?= $this->render('topbar') ?>  <!-- This is equivalent to @include('layouts.topbar') -->

        <!-- Sidebar -->
        <?= $this->render('sidebar') ?>  <!-- This is equivalent to @include('layouts.sidebar') -->

        <!-- Start right Content here -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?= $content ?>  <!-- This is equivalent to @yield('content') -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <!-- Footer -->
            <?= $this->render('footer') ?>  <!-- This is equivalent to @include('layouts.footer') -->
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    <?= $this->render('right-sidebar') ?>  <!-- This is equivalent to @include('layouts.right-sidebar') -->
    <!-- /Right-bar -->
    <script>
      //feather.replace();
    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();die;
