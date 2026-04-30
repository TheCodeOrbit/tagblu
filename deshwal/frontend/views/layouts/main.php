<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
$baseUrl = Yii::$app->HomeUrl;
$controller = Yii::$app->controller->id;       // Controller ID
$action = Yii::$app->controller->action->id;  // Action ID
$vendor_name = $_SESSION["vendor_name"]??"";
$is_admin = $_SESSION["is_admin"]??0;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="header">
    <div id="loading-overlay" class="">
        <div class="loading-spinner"></div>
    </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
			 <div class="user">
            <img src="<?= $baseUrl; ?>images/logo.png" alt="User">
        </div>
        </div>
       
        <div class="icons">
            <form class="me-2" id="search-form" action="<?= Url::to(['/site/search'], true) ?>" method="get">
                <div class="input-group">
                    <?php $search_value = Yii::$app->request->get('query')??""; ?>
                    <input required type="text" class="form-control" placeholder="Search" name="query" id="search-input" value="<?php echo $search_value;?>">
                    <button class="btn btn-outline-secondary searchbtn" type="submit" id="search-input">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <!-- <input type="text" placeholder="Search" name="query" id="search-input">
                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button> -->
            </form>
            <!-- <img src="<?= $baseUrl; ?>images/simple-icons_dell.png" alt="Bell Icon" class="bell-icon"> -->
            <!-- <img src="<?= $baseUrl; ?>images/ant-design_reload-time-outline.png" alt="Bell Icon" class="bell-icon">
            <img src="<?= $baseUrl; ?>images/clarity_notification-line.png" alt="Bell Icon" class="bell-icon">
            <img src="<?= $baseUrl; ?>images/Group-908.png" alt="Bell Icon" class="bell-icon"> -->
            <div title="Profile" class="d-flex flex-column justify-content-center align-items-center">
                <img class="profile-img-div" src="<?= $baseUrl; ?>images/no-image.png" width="25" height="25" alt="User Image">
                <div class="user-name"><?php echo $_SESSION["loggedin_user_name"]??"";?><?php echo $_SESSION["user_roles"]?" (".$_SESSION["user_roles"].")":""?><?php echo $is_admin?"|Admin":"";?></div>
            </div>
        </div>
    </div>

    <div class="sidebar">
        <p class="company-name"><?php echo $vendor_name??"";?></p>
        <hr>
        <?php 
         echo Html::a('Pickup Request', ['pickuprequest/index'], ['class' => ($controller=="pickuprequest")?"active":""]);
           echo Html::a('Purchase Order', ['site/purchaseorder'], ['class' => $action=="purchaseorder"?"active":""]);
        echo Html::a('Payment', ['site/index'], ['class' => ($action=="index" && $controller=="site")?"active":""]);
        //echo Html::a('Pickup', ['site/pickup'], ['class' => $action=="pickup"?"active":""]);
       
      
        echo Html::a('GRN', ['site/grn'], ['class' => $action=="grn"?"active":""]);
        ?>
        <!-- Services Menu (Bootstrap Collapse) -->
        <?php 
            // Check if any submenu item is active
            $isServicesActive = in_array($action, ['servicesdashboard','weighing', 'drilling', 'degaussing', 'shredding','datawiping']) ? "show" : "";
        ?>
        <a class="d-block" data-bs-toggle="collapse" href="#servicesMenu" role="button" aria-expanded="<?= $isServicesActive ? 'true' : 'false' ?>" aria-controls="servicesMenu">
            Data Sanitization &#9662;
        </a>
        <div class="collapse <?= $isServicesActive ?>" id="servicesMenu">
            <?php 
                echo Html::a('Dashboard', ['site/servicesdashboard'], ['class' => $action == "servicesdashboard" ? "active d-block ms-5" : "d-block ms-5"]);
                echo Html::a('Drilling', ['site/drilling'], ['class' => $action == "drilling" ? "active d-block ms-5" : "d-block ms-5"]);
                echo Html::a('Degaussing', ['site/degaussing'], ['class' => $action == "degaussing" ? "active d-block ms-5" : "d-block ms-5"]);
                echo Html::a('Shredding', ['site/shredding'], ['class' => $action == "shredding" ? "active d-block ms-5" : "d-block ms-5"]);
                echo Html::a('Data Wiping', ['site/datawiping'], ['class' => $action=="datawiping"?"active d-block ms-5":"d-block ms-5"]);
                echo Html::a('Weighing', ['site/weighing'], ['class' => $action == "weighing" ? "active d-block ms-5" : "d-block ms-5"]);
            ?>
        </div>
        <!-- <a href="#">Weighing</a>
        <a href="#">Drilling</a>
        <a href="#">Degaussing</a>
        <a href="#">Shredding</a> -->
        <?php 
        
        echo Html::a('Certificate Generated', ['site/certificate'], ['class' => $action=="certificate"?"active":""]);
        ?>
         <?php 
        
        echo Html::a('Sustainibility', ['site/sustainibility'], ['class' => $action=="sustainibility"?"active":""]);
        ?>
         <?php 
        
        echo Html::a('Escalation Matrix', ['site/escalation'], ['class' => $action=="escalation"?"active":""]);
        ?>
        <?php if($is_admin){ ?>
            <a href="#">Escalation Matrix</a>
        <?php } ?>
        <div class="bottom-links">
            <?php if($is_admin){ ?>
                <a href="#">Settings</a>
            <?php } ?>
            <?php
            echo Html::a('Reset Password', ['/site/changepassword'], ['class' => $action=="changepassword"?"active":""]);
             echo Html::beginForm(['/site/logout'], 'post', ['id' => 'logout-form'])
             . ' <a class="logout" style="cursor:pointer">Logout</a>'           
             . Html::endForm();
             ?>
        </div>
    </div>

    <div class="dashboard-content">
       
        <?= $content ?>
    </div>
        
<?php
$this->registerJsFile('@web/js/main/edit.js', ['depends' => [AppAsset::class]]);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
