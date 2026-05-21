<?php
use yii\helpers\Html;

/** @var string $message */
$this->title = 'Error Occurred';
?>

<div class="custom-alert-overlay" style="position: static; display: flex; background: transparent; min-height: 60vh; align-items: center; justify-content: center;">
    <div class="custom-alert-card" data-type="error" style="width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
        <div class="custom-alert-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div class="custom-alert-content">
            <h3 class="custom-alert-title">System Error</h3>
            <p class="custom-alert-message" style="margin-top: 8px; word-break: break-word;">
                <?= Html::encode($message) ?>
            </p>
        </div>
        <div class="custom-alert-actions" style="margin-top: 24px; display: flex; justify-content: flex-end;">
            <a href="<?= Yii::$app->request->referrer ?: Yii::$app->homeUrl ?>" class="custom-btn-cancel" style="text-decoration: none;">
                Go Back
            </a>
        </div>
    </div>
</div>
