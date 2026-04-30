<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$user_id = Yii::$app->user->id;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <input type="hidden" id="user_id" name="user_id" value='<?php echo $user_id ?>'>
    
    <div class="element-lead-list-view">
        <div class="div">
            <div class="overlap">
                <div class="overlap-group">
                    <div class="rectangle"></div>

                    <!--sidebar -->
                    <div class="group">
                        <div class="overlap-2">
                            <div class="rectangle-2"></div>

                            <div class="ic-sharp-dashboard">
                                <div class="div-wrapper">
                                    <div class="frame">
                                        <div class="overlap-group-wrapper">
                                            <div class="overlap-group-2">
                                                <div class="text-wrapper">Home</div>
                                                <img
                                                    class="streamline-home"
                                                    src="https://c.animaapp.com/4Te5O9cu/img/streamline-home-4.svg" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="frame-wrapper">
                                <div class="div-wrapper">
                                    <div class="group-2">
                                        <div class="overlap-group-3">
                                            <div class="text-wrapper-2">Contacts</div>
                                            <img class="img" src="https://c.animaapp.com/4Te5O9cu/img/mdi-account-group.svg" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="group-wrapper">
                                <div class="group-3">
                                    <div class="overlap-group-4">
                                        <div class="text-wrapper-3">Accounts</div>
                                        <img class="img" src="https://c.animaapp.com/4Te5O9cu/img/clarity-building-line.svg" />
                                    </div>
                                </div>
                            </div>
                            <div class="frame-2">
                                <div class="overlap-wrapper">
                                    <div class="overlap-3">
                                        <div class="group-4">
                                            <div class="overlap-group-5">
                                                <div class="text-wrapper-4">Sales</div>
                                                <img
                                                    class="ph-chart-line-up"
                                                    src="https://c.animaapp.com/4Te5O9cu/img/ph-chart-line-up-fill.svg" />
                                            </div>
                                        </div>
                                        <div class="rectangle-3"></div>
                                        <div class="rectangle-4"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="frame-3">
                                <div class="group-5">
                                    <div class="overlap-group-6">
                                        <div class="text-wrapper-5">Service</div>
                                        <img class="ooui-heart" src="https://c.animaapp.com/4Te5O9cu/img/ooui-heart.svg" />
                                    </div>
                                </div>
                            </div>
                            <div class="frame-4">
                                <div class="group-6">
                                    <div class="overlap-group-7">
                                        <div class="text-wrapper">Outreach</div>
                                        <img class="img" src="https://c.animaapp.com/4Te5O9cu/img/uiw-mail-o.svg" />
                                    </div>
                                </div>
                            </div>
                            <div class="frame-5">
                                <div class="group-7">
                                    <div class="group-8">
                                        <div class="overlap-group-8">
                                            <div class="text-wrapper-3">Commerce</div>
                                            <img
                                                class="img-2"
                                                src="https://c.animaapp.com/4Te5O9cu/img/ant-design-shopping-cart-outlined.svg" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="frame-6">
                                <div class="group-9">
                                    <div class="overlap-group-9">
                                        <div class="text-wrapper-5">Your Acco..</div>
                                        <img class="img-2" src="https://c.animaapp.com/4Te5O9cu/img/mdi-office-building-outline.svg" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--sidebar end -->

                       <!--  <div class="component">
                            <div class="text-wrapper-6">Accounts</div>
                            <img class="ep-arrow-down" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg" />
                            <img class="vector" src="https://c.animaapp.com/4Te5O9cu/img/vector-93-1.svg" />
                        </div> -->
                        <?php
                        //get all menus dynamic
                        ?>
                        <div class="component">
                            <div class="text-wrapper-6">Accounts</div>
                            <img class="ep-arrow-down" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg" />
                            <img class="vector" src="https://c.animaapp.com/4Te5O9cu/img/vector-93-1.svg" />
                        </div>
                       
                        <div class="component-2">
                            <div class="text-wrapper-7">Leads</div>
                            <img class="ep-arrow-down-2" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-19.svg" />
                            <img class="vector" src="https://c.animaapp.com/4Te5O9cu/img/vector-93-1.svg" />
                        </div>
                        <?php
                        ?>
                        


                    </div>
                   <div class="main-container" style="position: relative;height: 100vh;width: 100%;">

                    <?= $content ?>
                    </div>


                </div>




                <!-- top bar -->
               <!--  <div class="group-20">
                    <img class="group-21" src="https://c.animaapp.com/4Te5O9cu/img/group-130@2x.png" />
                    <img class="material-symbols" src="https://c.animaapp.com/4Te5O9cu/img/material-symbols-menu.svg" />
                    <img class="clarity-notification" src="https://c.animaapp.com/4Te5O9cu/img/clarity-notification-solid.svg" />
                    <img class="gg-profile" src="https://c.animaapp.com/4Te5O9cu/img/gg-profile.svg" />
                    <img class="ph-plus-fill" src="https://c.animaapp.com/4Te5O9cu/img/ph-plus-fill.svg" />
                    <div class="group-22">
                        <div class="overlap-group-15">
                            <img class="iconamoon-search" src="https://c.animaapp.com/4Te5O9cu/img/iconamoon-search-thin.svg" />
                            <div class="text-wrapper-128">Search</div>
                            <img class="ic-round-arrow-left-66" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-2.svg" />
                            <img class="vector-68" src="https://c.animaapp.com/4Te5O9cu/img/vector-73.svg" />
                        </div>
                    </div>
                    <img class="mingcute" src="https://c.animaapp.com/4Te5O9cu/img/mingcute-announcement-fill.svg" />
                </div> -->
                <div class="d-header-menu">
                    <div class="d-header-flex1">
                        <img class="material-symbols" src="https://c.animaapp.com/4Te5O9cu/img/material-symbols-menu.svg" />
                    </div>
                    <div class="d-header-flex2">
                     <img class="group-21" src="https://c.animaapp.com/4Te5O9cu/img/group-130@2x.png" />
                    </div>
                    <div class="d-header-flex3">
                     <div class="group-22">
                        <div class="overlap-group-15">
                            <img class="iconamoon-search" src="https://c.animaapp.com/4Te5O9cu/img/iconamoon-search-thin.svg">
                            <div class="text-wrapper-128">Search</div>
                            <img class="ic-round-arrow-left-66" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-2.svg">
                            <img class="vector-68" src="https://c.animaapp.com/4Te5O9cu/img/vector-73.svg">
                        </div>
                    </div>
                    </div>
                    <div class="d-header-flex4">
                        <img class="ph-plus-fill" src="https://c.animaapp.com/4Te5O9cu/img/ph-plus-fill.svg">

                    </div>
                     <div class="d-header-flex5">
                        <img class="mingcute" src="https://c.animaapp.com/4Te5O9cu/img/mingcute-announcement-fill.svg">
                    </div>
                     <div class="d-header-flex6">
                        <img class="clarity-notification" src="https://c.animaapp.com/4Te5O9cu/img/clarity-notification-solid.svg">
                    </div>
                     <div class="d-header-flex7">
                        <img class="gg-profile" src="https://c.animaapp.com/4Te5O9cu/img/gg-profile.svg">
                    </div>
                </div>
                <!-- top bar end-->

                <!-- pagination -->
              

            </div>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>



</html>
<?php $this->endPage();
die;?>
