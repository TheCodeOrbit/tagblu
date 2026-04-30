<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu"><?= Yii::t('app', 'Menu') ?></li>

                <li>
                    <a href="<?= Url::to(['dashboard/index']) ?>">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="<?= Url::to(['uiinputs/index']) ?>">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">UII_inputs</span>
                    </a>
                </li>

                <li>
                    <a href="<?= Url::to(['table/index']) ?>">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Table</span>
                    </a>
                </li>

                <li>
                    <a href="<?= Url::to(['task/index']) ?>">
                        <i data-feather="grid"></i>
                        <span data-key="t-dashboard"><?= Yii::t('app', 'Task') ?></span>
                    </a>
                </li>

                <li>
                    <a href="<?= Url::to(['employee/index']) ?>">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard"><?= Yii::t('app', 'Employee') ?></span>
                    </a>
                </li>


                <li>
                    <a href="javascript:void(0);" class="has-arrow" data-toggle="collapse" data-target="#appsSubMenu">
                        <i data-feather="grid"></i> <span>App</span>
                    </a>
                    <ul class="collapse sub-menu" id="appsSubMenu">
                        <li><a href="apps-calendar">Calendar</a></li>
                        <li><a href="apps-chat">Chat</a></li>

                        <li>
                            <a href="javascript:void(0);" class="has-arrow" data-toggle="collapse" data-target="#emailSubMenu">Email</a>
                            <ul class="collapse sub-menu" id="emailSubMenu">
                                <li><a href="apps-email-inbox">Inbox</a></li>
                                <li><a href="apps-email-read">Read Email</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="has-arrow" data-toggle="collapse" data-target="#invoicesSubMenu">Invoices</a>
                            <ul class="collapse sub-menu" id="invoicesSubMenu">
                                <li><a href="apps-invoices-list">Invoice List</a></li>
                                <li><a href="apps-invoices-detail">Invoice Detail</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="has-arrow" data-toggle="collapse" data-target="#contactsSubMenu">Contacts</a>
                            <ul class="collapse sub-menu" id="contactsSubMenu">
                                <li><a href="apps-contacts-grid">User Grid</a></li>
                                <li><a href="apps-contacts-list">User List</a></li>
                                <li><a href="apps-contacts-profile">Profile</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="users"></i>
                        <span data-key="t-authentication"><?= Yii::t('app', 'Authentication') ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?= Url::to(['auth/login']) ?>" data-key="t-login"><?= Yii::t('app', 'Login') ?></a></li>
                        <li><a href="<?= Url::to(['auth/register']) ?>" data-key="t-register"><?= Yii::t('app', 'Register') ?></a></li>
                        <li><a href="<?= Url::to(['auth/recoverpw']) ?>" data-key="t-recover-password"><?= Yii::t('app', 'Recover_Password') ?></a></li>
                        <li><a href="<?= Url::to(['auth/lock-screen']) ?>" data-key="t-lock-screen"><?= Yii::t('app', 'Lock_Screen') ?></a></li>
                        <li><a href="<?= Url::to(['auth/logout']) ?>" data-key="t-logout"><?= Yii::t('app', 'Logout') ?></a></li>
                        <li><a href="<?= Url::to(['auth/confirm-mail']) ?>" data-key="t-confirm-mail"><?= Yii::t('app', 'Confirm_Mail') ?></a></li>
                        <li><a href="<?= Url::to(['auth/email-verification']) ?>" data-key="t-email-verification"><?= Yii::t('app', 'Email_Verification') ?></a></li>
                        <li><a href="<?= Url::to(['auth/two-step-verification']) ?>" data-key="t-two-step-verification"><?= Yii::t('app', 'Two_Step_Verification') ?></a></li>
                    </ul>
                </li>

            </ul>


        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->