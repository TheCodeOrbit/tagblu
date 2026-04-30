<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?= Url::to(['site/index']) ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <?= Html::img('@web/theme/images/logo-sm.svg', ['alt' => '', 'height' => 24]) ?>
                    </span>
                    <span class="logo-lg">
                        <?= Html::img('@web/theme/images/logo-sm.svg', ['alt' => '', 'height' => 24]) ?> <span class="logo-txt">Minia</span>
                    </span>
                </a>

                <a href="<?= Url::to(['site/index']) ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <?= Html::img('@web/theme/images/logo-sm.svg', ['alt' => '', 'height' => 24]) ?>
                    </span>
                    <span class="logo-lg">
                        <?= Html::img('@web/theme/images/logo-sm.svg', ['alt' => '', 'height' => 24]) ?> <span class="logo-txt">Minia</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <!-- App Search-->
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search...">
                    <button class="btn btn-primary" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                </div>
            </form>
        </div>

        <div class="d-flex">

            <!-- Search Icon (Mobile View) -->
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item" id="page-header-search-dropdown" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i data-feather="search" class="icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Search Result">
                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Language Switch -->
            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <?php switch (Yii::$app->session->get('lang')):
                        case 'ru': ?>
                            <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/russia.jpg" alt="Header Language" height="16">
                        <?php break;
                        case 'it': ?>
                            <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/italy.jpg" alt="Header Language" height="16">
                        <?php break;
                        case 'de': ?>
                            <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/germany.jpg" alt="Header Language" height="16">
                        <?php break;
                        case 'es': ?>
                            <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/spain.jpg" alt="Header Language" height="16">
                        <?php break;
                        default: ?>
                            <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/us.jpg" alt="Header Language" height="16">
                    <?php endswitch; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="<?= yii\helpers\Url::to(['site/index', 'lang' => 'en']) ?>" class="dropdown-item notify-item language">
                        <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/us.jpg" alt="English" class="me-1" height="12">
                        <span class="align-middle">English</span>
                    </a>
                    <a href="<?= yii\helpers\Url::to(['site/index', 'lang' => 'es']) ?>" class="dropdown-item notify-item language">
                        <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/spain.jpg" alt="Spanish" class="me-1" height="12">
                        <span class="align-middle">Spanish</span>
                    </a>
                    <a href="<?= yii\helpers\Url::to(['site/index', 'lang' => 'de']) ?>" class="dropdown-item notify-item language">
                        <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/germany.jpg" alt="German" class="me-1" height="12">
                        <span class="align-middle">German</span>
                    </a>
                    <a href="<?= yii\helpers\Url::to(['site/index', 'lang' => 'it']) ?>" class="dropdown-item notify-item language">
                        <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/italy.jpg" alt="Italian" class="me-1" height="12">
                        <span class="align-middle">Italian</span>
                    </a>
                    <a href="<?= yii\helpers\Url::to(['site/index', 'lang' => 'ru']) ?>" class="dropdown-item notify-item language">
                        <img src="<?= Yii::getAlias('@web') ?>/theme/images/flags/russia.jpg" alt="Russian" class="me-1" height="12">
                        <span class="align-middle">Russian</span>
                    </a>
                </div>
            </div>

            <!-- Theme Switcher -->
            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                    <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                </button>
            </div>

            <!-- App Grid -->
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="grid" class="icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <div class="p-2">
                        <div class="row g-0">
                            <div class="col">
                                <a class="dropdown-icon-item" href="#">
                                    <img src="<?= Yii::getAlias('@web') ?>/theme/images/brands/github.png" alt="Github">
                                    <span>GitHub</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="#">
                                    <img src="<?= Yii::getAlias('@web') ?>/theme/images/brands/bitbucket.png" alt="bitbucket">
                                    <span>Bitbucket</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="#">
                                    <img src="<?= Yii::getAlias('@web') ?>/theme/images/brands/dribbble.png" alt="dribbble">
                                    <span>Dribbble</span>
                                </a>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col">
                                <a class="dropdown-icon-item" href="#">
                                    <img src="<?= Yii::getAlias('@web') ?>/theme/images/brands/dropbox.png" alt="dropbox">
                                    <span>Dropbox</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="#">
                                    <img src="<?= Yii::getAlias('@web') ?>/theme/images/brands/mail_chimp.png" alt="mail_chimp">
                                    <span>Mail Chimp</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="#">
                                    <img src="<?= Yii::getAlias('@web') ?>/theme/images/brands/slack.png" alt="slack">
                                    <span>Slack</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           

            <!-- User Profile -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">

                    <img class="rounded-circle header-profile-user" src="<?= Yii::getAlias('@web') ?>/theme/images/users/avatar-3.jpg" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1">Bhavitha</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item"><i class="bx bx-user font-size-16 align-middle me-1"></i> Profile</a>
                    <a class="dropdown-item"><i class="bx bx-wrench font-size-16 align-middle me-1"></i> Settings</a>
                    <a class="dropdown-item"><i class="bx bx-power-off font-size-16 align-middle me-1"></i> Logout</a>
                </div>
            </div>

        </div>



    </div>
</header>