<?php
use backend\components\SvgRenderHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Settings';
$baseUrl = Url::base();

$this->registerCss(<<<CSS
.settings-root {
    padding: 24px 32px 32px;
}

.settings-page-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
}

.settings-page-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e5f0ff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.settings-page-header-icon img {
    max-width: 20px;
    max-height: 20px;
}

.settings-page-header-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.settings-page-header-subtitle {
    font-size: 13px;
    color: #6b7280;
    margin-top: 2px;
}

.settings-content {
    margin-top: 14px;
}

.settings-theme-wrapper {
    max-width: 860px;
    margin: 0 auto;
}

.settings-theme-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}

.settings-theme-card-header {
    padding: 14px 20px;
    border-bottom: 1px solid #edf2f7;
}

.settings-theme-card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.settings-theme-card-body {
    padding: 18px 20px 22px;
}

.settings-theme-description {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 18px;
}

.settings-theme-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
}

.settings-theme-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    min-width: 70px;
}

.settings-theme-select {
    min-width: 220px;
    max-width: 320px;
}

.settings-theme-apply {
    padding: 6px 18px;
    font-size: 14px;
    border-radius: 999px;
    background: var(--color-primary) !important;
    color:white;
}

CSS);
?>
 <div class="settings-root">

    <div class="settings-page-header">
        <div class="settings-page-header-icon icons-coll">
            <?= SvgRenderHelper::renderIcon('settings.svg', true); ?>
        </div>

        <div class="settings-page-header-content">
            <h1 class="settings-page-header-title">
                <?= Html::encode($this->title) ?>
            </h1>
            <div class="settings-page-header-subtitle">
                Manage your preferences for the application.
            </div>
        </div>
    </div>

    <div class="settings-content">
        <div class="settings-theme-wrapper">
            <div class="settings-theme-card">
                <div class="settings-theme-card-header">
                    <h5>Theme</h5>
                </div>

                <div class="settings-theme-card-body">
                    <div class="settings-theme-description">
                        Choose which theme you want to use in the application.
                    </div>

                    <form action="<?= Url::to(['setting/change-theme']) ?>"
                          method="post"
                          id="themeForm">

                        <?= Html::hiddenInput(
                            Yii::$app->request->csrfParam,
                            Yii::$app->request->getCsrfToken()
                        ) ?>

                        <div class="settings-theme-row">
                            <div class="settings-theme-label">Theme</div>

                            <select name="id"
                                    id="themeSelect"
                                    class="form-select form-select-sm settings-theme-select">
                                <?php foreach ($themes as $name): ?>
                                    <option value="<?= (int)$name['id'] ?>"
                                        <?= Yii::$app->session->get('_theme_id') == (int)$name['id'] ? 'selected' : '' ?>>
                                        <?= Html::encode($name['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit"
                                    id="themeApplyBtn"
                                    class="btn settings-theme-apply">
                                Apply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form   = document.getElementById('themeForm');
    var select = document.getElementById('themeSelect');
    var apply  = document.getElementById('themeApplyBtn');

    if (!form || !select || !apply) return;
 
    apply.addEventListener('click', function (e) {
        e.preventDefault();
        if (!select.value) return;

        if (HTMLFormElement.prototype.submit) {
            HTMLFormElement.prototype.submit.call(form);
        } else if (form.submit) {
            form.submit();
        }
    });
});
</script>
