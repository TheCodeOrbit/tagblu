<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= Url::to('@web/themes/images/favicon.ico') ?>">

    <title><?= Html::encode($this->title) ?> | Minia - Admin & Dashboard Template</title>

    <!-- Include additional CSS files -->
    <?php $this->head() ?>
    <?= $this->render('head-css') ?>  <!-- This is equivalent to @include('layouts.head-css') -->
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

    <!-- JAVASCRIPT -->
    <?= $this->render('vendor-scripts') ?>  <!-- This is equivalent to @include('layouts.vendor-scripts') -->

    <?php $this->endBody() ?>
</body>

</html>
