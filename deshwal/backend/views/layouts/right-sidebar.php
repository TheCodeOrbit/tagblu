<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title d-flex align-items-center bg-dark p-3">

            <h5 class="m-0 me-2 text-white"><?= Yii::t('app', 'Theme Customizer') ?></h5>

            <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
        </div>

        <hr class="m-0" />

        <div class="p-4">
            <h6 class="mb-3"><?= Yii::t('app', 'Layout') ?></h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout" id="layout-vertical" value="vertical">
                <label class="form-check-label" for="layout-vertical"><?= Yii::t('app', 'Vertical') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout" id="layout-horizontal" value="horizontal">
                <label class="form-check-label" for="layout-horizontal"><?= Yii::t('app', 'Horizontal') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2"><?= Yii::t('app', 'Layout Mode') ?></h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-light" value="light">
                <label class="form-check-label" for="layout-mode-light"><?= Yii::t('app', 'Light') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-dark" value="dark">
                <label class="form-check-label" for="layout-mode-dark"><?= Yii::t('app', 'Dark') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2"><?= Yii::t('app', 'Layout Width') ?></h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-width" id="layout-width-fluid" value="fluid"
                    onchange="document.body.setAttribute('data-layout-size', 'fluid')">
                <label class="form-check-label" for="layout-width-fluid"><?= Yii::t('app', 'Fluid') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-width" id="layout-width-boxed" value="boxed"
                    onchange="document.body.setAttribute('data-layout-size', 'boxed')">
                <label class="form-check-label" for="layout-width-boxed"><?= Yii::t('app', 'Boxed') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2"><?= Yii::t('app', 'Layout Position') ?></h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-position" id="layout-position-fixed"
                    value="fixed" onchange="document.body.setAttribute('data-layout-scrollable', 'false')">
                <label class="form-check-label" for="layout-position-fixed"><?= Yii::t('app', 'Fixed') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-position" id="layout-position-scrollable"
                    value="scrollable" onchange="document.body.setAttribute('data-layout-scrollable', 'true')">
                <label class="form-check-label" for="layout-position-scrollable"><?= Yii::t('app', 'Scrollable') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2"><?= Yii::t('app', 'Topbar Color') ?></h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="topbar-color" id="topbar-color-light" value="light"
                    onchange="document.body.setAttribute('data-topbar', 'light')">
                <label class="form-check-label" for="topbar-color-light"><?= Yii::t('app', 'Light') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="topbar-color" id="topbar-color-dark" value="dark"
                    onchange="document.body.setAttribute('data-topbar', 'dark')">
                <label class="form-check-label" for="topbar-color-dark"><?= Yii::t('app', 'Dark') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2 sidebar-setting"><?= Yii::t('app', 'Sidebar Size') ?></h6>

            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-default" value="default"
                    onchange="document.body.setAttribute('data-sidebar-size', 'lg')">
                <label class="form-check-label" for="sidebar-size-default"><?= Yii::t('app', 'Default') ?></label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-compact" value="compact"
                    onchange="document.body.setAttribute('data-sidebar-size', 'md')">
                <label class="form-check-label" for="sidebar-size-compact"><?= Yii::t('app', 'Compact') ?></label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-size" id="sidebar-size-small" value="small"
                    onchange="document.body.setAttribute('data-sidebar-size', 'sm')">
                <label class="form-check-label" for="sidebar-size-small"><?= Yii::t('app', 'Small (Icon View)') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2 sidebar-setting"><?= Yii::t('app', 'Sidebar Color') ?></h6>

            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-light" value="light"
                    onchange="document.body.setAttribute('data-sidebar', 'light')">
                <label class="form-check-label" for="sidebar-color-light"><?= Yii::t('app', 'Light') ?></label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-dark" value="dark"
                    onchange="document.body.setAttribute('data-sidebar', 'dark')">
                <label class="form-check-label" for="sidebar-color-dark"><?= Yii::t('app', 'Dark') ?></label>
            </div>
            <div class="form-check sidebar-setting">
                <input class="form-check-input" type="radio" name="sidebar-color" id="sidebar-color-brand" value="brand"
                    onchange="document.body.setAttribute('data-sidebar', 'brand')">
                <label class="form-check-label" for="sidebar-color-brand"><?= Yii::t('app', 'Brand') ?></label>
            </div>

            <h6 class="mt-4 mb-3 pt-2"><?= Yii::t('app', 'Direction') ?></h6>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-direction" id="layout-direction-ltr" value="ltr">
                <label class="form-check-label" for="layout-direction-ltr"><?= Yii::t('app', 'LTR') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="layout-direction" id="layout-direction-rtl" value="rtl">
                <label class="form-check-label" for="layout-direction-rtl"><?= Yii::t('app', 'RTL') ?></label>
            </div>
        </div>
    </div>
</div>
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>
