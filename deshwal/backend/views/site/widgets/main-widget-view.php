
<?php
$baseUrl = Yii::$app->HomeUrl;
$position = $position ?? '3,3'; // default 3x3 if not set
// echo "<pre>";print_r($widgetData);
// Parse dynamic width & height
list($gridW, $gridH) = explode(',', $position);

// Set footer alignment
$cls = ($gridW == '1') ? 'text-left' : 'text-center';
$height = ($gridW == '2') ? '200px !important' : '400px !important'; // adjust height based on grid width
?>

<div class="grid-stack-item" data-gs-id="widget-<?= $widgetId;?>" gs-w="<?= (int)$gridW; ?>" gs-h="<?= (int)$gridH; ?>" >
    <div class="grid-stack-item-content widget-box position-relative">
        <div class="chart-card" style="<?= $height; ?>">
            <div class="chart-card-body">
                <div class="chart-header <?= $cls; ?>"><?= $title; ?></div>
                <div class="grid-stack-item-content" style="flex: 1;" data-widget-url="<?= $widgeturl ?>" data-widget-type="<?= $widgetType ?>">
                    <?php if($widgetType == 2){
                        include('common_count_widget.php');
                    }
                    else{
                        include($widgeturl);
                    } ?>
                </div>
            </div>
            <div class="chart-footer d-flex justify-content-end align-items-center px-2 py-1">
                <!-- <img src="<?= $baseUrl; ?>/images/img_weuisettingfilled.svg" alt="Settings" class="card-icon" /> -->
                <!-- <img src="<?= $baseUrl; ?>/images/img_gameiconsexpand.svg" alt="Expand" class="card-icon" /> -->
                <!-- <img src="<?= $baseUrl; ?>/images/img_zondiconsrefresh.svg" alt="Refresh" class="refresh-widget card-icon" /> -->
                <img src="<?= $baseUrl; ?>/images/img_pajamasclosexs.svg" alt="Close" class="close-widget card-icon" />
            </div>
        </div>
    </div>
</div>
