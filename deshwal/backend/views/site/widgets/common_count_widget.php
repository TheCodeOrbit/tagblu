<?php $baseUrl = Yii::$app->HomeUrl; ?>
<div class="dash-count-container">
    <div class="dash-count-txt">
        <a href='<?= $baseUrl . $modulename ?>/list?widgetid=<?= $filterid; ?>'>
            <?= $widgetData ;?>
        </a>
    </div>
    <div class="dash-count-label"><?= $title; ?></div>
</div>