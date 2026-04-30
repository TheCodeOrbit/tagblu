<?php

use backend\assets\AdminAsset;

AdminAsset::register($this);
$baseUrl = Yii::$app->HomeUrl;
?>

<div class="select-1">
    <div class="welcome-section">
        <h1 class="welcome-title">Welcome Back, <?= Yii::$app->user->identity->first_name ?? 'User' ?>!</h1>
        <p class="welcome-subtitle">Here's a quick look at your business performance today.</p>
    </div>

    <!-- Hero Stats Row -->
    <div class="hero-stats-grid">

        <!-- Opportunities Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">My Opportunities</span>
                <span class="stat-value"><?= number_format((float)$this->context->getWidgetData('my_opportunities')) ?></span>
                <div class="stat-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Live
                </div>
            </div>
        </div>

        <!-- Tasks Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22h6a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v10"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10.4 12.6a2 2 0 1 1 3 3L8 21l-4 1 1-4Z"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Today's Tasks</span>
                <span class="stat-value"><?= number_format((float)$this->context->getWidgetData('my_today_tasks')) ?></span>
                <div class="stat-trend active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Active
                </div>
            </div>
        </div>

        <!-- Opportunity Value Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap emerald">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Oppr. Value</span>
                <span class="stat-value">₹<?= number_format((float)$this->context->getWidgetData('my_opportunities_amount'), 2) ?></span>
                <div class="stat-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Estimated
                </div>
            </div>
        </div>

        <!-- Sourcing Deals Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Sourcing Deals</span>
                <span class="stat-value"><?= number_format((float)$this->context->getWidgetData('my_sourcingdeal')) ?></span>
                <div class="stat-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Total
                </div>
            </div>
        </div>

    </div>
</div>