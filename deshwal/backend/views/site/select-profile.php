<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<link rel="stylesheet" href="<?= Yii::getAlias('@web') ?>/thememain/fontawesome/css/all.css">
<style>
    .profile-container {
        max-width: 650px;
        margin: 60px auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .profile-heading {
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    .profile-subtext {
        font-size: 14px;
        color: #777;
        margin-bottom: 30px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .profile-card {
        background: rgba(237, 233, 233, 0.6);
        border-radius: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: 0.2s;
        width: 180px;          
        height: 60px;          
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 10px;
    }

    .profile-card:hover {
        border-color: #007bff;
        background: rgba(238, 246, 255, 0.6);
    }

    .profile-title {
        font-size: 14px;
        font-weight: 600;
        max-width: 100%;
        /* white-space: nowrap; */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-meta {
        font-size: 13px;
        color: #555;
    }

    .profile-container {
        max-width: 650px;
        margin: 60px auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        text-align: center;
        position: relative;
    }

    .logout-link {
        position: absolute;
        top: 10px;
        right: 15px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 4px;
        background: transparent;
        color: var(--color-primary) !important;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .logout-link:hover {
        background: #f1f1f1;
    }

    .logout-icon {
        font-size: 18px;
    }

    .logout-text {
        line-height: 1;
    }

    .logout-icon {
        font-size: 18px;
        color: var(--color-primary) !important;
    }
</style>

<?php //print_r($profiles);die;
?>
<div class="profile-container">
    <?php if (!empty($siteSetting->logo_path)): ?>
        <div class="Logo-1">
            <?= Html::img(
                Yii::getAlias('@web') . $siteSetting->logo_path,
                ['alt' => $siteSetting->company, 'style' => '', "class" => "img-lg-dp"]
            ) ?>
        </div>
        <br>
    <?php endif; ?>
    <a href="<?= \yii\helpers\Url::to(['site/logout']) ?>"
        class="logout-link"
        data-method="post"
        title="Logout">
        <i class="fa-solid fa-sign-out-alt logout-icon"></i>
        <span class="logout-text">Logout</span>
    </a>
    <div class="profile-heading">Select Your Active Role</div>
    <div style="margin: 10px 0; font-size: 16px; font-weight: 500;">
        <?php if (!empty($user['first_name'])): ?>
            <div style="margin: 10px 0; font-size: 16px; font-weight: 500;">
                <strong>Hi <?= \yii\helpers\Html::encode($user['first_name'] . ' ' . ($user['last_name'] ?? '')) ?></strong>
            </div>
        <?php endif; ?>
    </div>
    <div class="profile-subtext">
        You have multiple roles. Choose the one you want to use for this session.
    </div>

    <div class="profile-grid">

        <?php foreach ($profiles as $val => $p):
            //print_r($p);die;
        ?>
            <form method="post" action="<?= Url::to(['site/set-profile']) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                <!-- <input type="hidden" name="profile_id" value="<= $p->id ?>"> -->
                <input type="hidden" name="profile_id" value="<?= $p['roleid'] ?>">

                <button type="submit" class="profile-card" title="<?= Html::encode($p['rolename'] ?? $p['rolename']) ?>">
                    <!-- <div class="profile-title">< Html::encode($p->profile_name ?? $p->id) ?></div>
                    <div class="profile-meta">Role: < Html::encode($p->role_code ?? '-') ?></div> -->

                    <div class="profile-title"><?= Html::encode($p['rolename'] ?? $p['rolename']) ?></div>
                    <!-- <div class="profile-meta">Role: <= Html::encode($p['rolename'] ?? '-') ?></div> -->
                </button>
            </form>
        <?php endforeach; ?>

    </div>

</div>
