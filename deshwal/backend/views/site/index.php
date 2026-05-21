<?php

use backend\assets\AdminAsset;
use yii\web\JqueryAsset;

AdminAsset::register($this);
$baseUrl = rtrim(Yii::$app->request->baseUrl, '/') . '/';

// Load ApexCharts for interactive analytics
$this->registerJsFile($baseUrl . 'thememain/js/apexcharts.js', [
    'depends' => [JqueryAsset::class],
]);
?>

<div class="select-1 dashboard-premium-wrap">

    <!-- ═══════════════════════════════════════════════════
         SECTION 1: Welcome Glass Panel + Quick Actions
         ═══════════════════════════════════════════════════ -->
    <div class="welcome-glass-panel">
        <div class="welcome-content">
            <div class="welcome-text">
                <span class="welcome-greeting">Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?>,</span>
                <h1 class="welcome-name"><?= Yii::$app->user->identity->first_name ?? 'User' ?></h1>
                <p class="welcome-subtitle">Here's what's happening with your business today.</p>
            </div>
            <div class="quick-actions">
                <a href="<?= $baseUrl ?>leads/create" class="quick-action-btn">
                    <div class="qa-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                    </div>
                    <span>Add Lead</span>
                </a>
                <a href="<?= $baseUrl ?>tasks/create" class="quick-action-btn">
                    <div class="qa-icon amber-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </div>
                    <span>Create Task</span>
                </a>
                <a href="<?= $baseUrl ?>opportunity/create" class="quick-action-btn">
                    <div class="qa-icon emerald-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    </div>
                    <span>New Opportunity</span>
                </a>
                <a href="<?= $baseUrl ?>sourcingdeal/create" class="quick-action-btn">
                    <div class="qa-icon purple-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                    </div>
                    <span>Sourcing Deal</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 1.5: Global Period Filter
         ═══════════════════════════════════════════════════ -->
    <div class="dashboard-filter-bar">
        <div class="period-picker-wrap" id="globalPeriodPicker">
            <button class="period-btn" data-period="today">Today</button>
            <button class="period-btn active" data-period="this_week">Week</button>
            <button class="period-btn" data-period="this_month">Month</button>
            <button class="period-btn" data-period="this_quarter">Quarter</button>
            <button class="period-btn" data-period="this_year">Year</button>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 2: Hero KPI Stats Row
         ═══════════════════════════════════════════════════ -->
    <div class="hero-stats-grid">
        <!-- Opportunities Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">My Opportunities</span>
                <span class="stat-value" id="stat_opportunities" data-value="<?= (float)$this->context->getWidgetData('my_opportunities') ?>">0</span>
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
                <span class="stat-value" id="stat_tasks" data-value="<?= (float)$this->context->getWidgetData('my_today_tasks') ?>">0</span>
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
                <span class="stat-value" id="stat_opp_value" data-value="<?= (float)$this->context->getWidgetData('my_opportunities_amount') ?>" data-prefix="₹">₹0</span>
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
                <span class="stat-value" id="stat_sourcing" data-value="<?= (float)$this->context->getWidgetData('my_sourcingdeal') ?>">0</span>
                <div class="stat-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Total
                </div>
            </div>
        </div>

        <!-- My Leads Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">My Leads</span>
                <span class="stat-value" id="stat_leads" data-value="<?= (float)$this->context->getWidgetData('my_leads') ?>">0</span>
                <div class="stat-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Active
                </div>
            </div>
        </div>

        <!-- Today's Calls Card -->
        <div class="stat-card-glass">
            <div class="stat-icon-wrap" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Today's Calls</span>
                <span class="stat-value" id="stat_calls" data-value="<?= (float)$this->context->getWidgetData('my_today_calls') ?>">0</span>
                <div class="stat-trend active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Scheduled
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 3: Interactive Analytics Row
         ═══════════════════════════════════════════════════ -->
    <div class="analytics-row">
        <!-- Target vs Achievement Chart -->
        <div class="analytics-card-glass wide">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Sales Performance</h3>
                    <span class="analytics-badge">Monthly Target vs Achievement</span>
                </div>
                <div class="analytics-actions">
                    <button class="chart-action-btn focus-mode-toggle" title="Focus Mode">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                    <div class="analytics-period">
                        <span class="period-dot"></span> FY <?= date('Y') ?>–<?= date('Y') + 1 ?>
                    </div>
                </div>
            </div>
            <div class="analytics-card-body">
                <div id="targetAchievementChart" style="width: 100%; min-height: 320px;"></div>
            </div>
        </div>

        <!-- Sales Pipeline Funnel -->
        <div class="analytics-card-glass">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Sales Pipeline</h3>
                    <span class="analytics-badge" style="color: #ec4899; background: rgba(236, 72, 153, 0.1);">Conversion Funnel</span>
                </div>
                <div class="analytics-period">
                    <span class="period-dot" style="background: #ec4899;"></span> Active
                </div>
            </div>
            <div class="analytics-card-body">
                <div id="salesFunnelChart" style="width: 100%; min-height: 320px; display: flex; justify-content: center; align-items: center;"></div>
            </div>
        </div>

        <!-- Engagement Activity (Calls vs Meetings) -->
        <div class="analytics-card-glass wide">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Engagement Activity</h3>
                    <span class="analytics-badge">Calls vs Meetings</span>
                </div>
            </div>
            <div class="analytics-card-body">
                <div id="engagementChart" style="width: 100%; min-height: 320px;"></div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 3.5: Advanced Analytics Row
         ═══════════════════════════════════════════════════ -->
    <div class="analytics-row mt-4">
        <!-- Revenue Forecast Chart -->
        <div class="analytics-card-glass wide">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Revenue Forecast</h3>
                    <span class="analytics-badge" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">Won vs Projected</span>
                </div>
                <div class="analytics-actions">
                    <button class="chart-action-btn focus-mode-toggle" title="Focus Mode" data-target="revenueForecastChart">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                </div>
            </div>
            <div class="analytics-card-body">
                <div id="revenueForecastChart" style="width: 100%; min-height: 320px;"></div>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="analytics-card-glass">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Lead Distribution</h3>
                    <span class="analytics-badge">Status Breakdown</span>
                </div>
                <div class="analytics-actions">
                    <button class="chart-action-btn focus-mode-toggle" title="Focus Mode" data-target="statusDistributionChart">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                </div>
            </div>
            <div class="analytics-card-body">
                <div id="statusDistributionChart" style="width: 100%; min-height: 320px;"></div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 3.8: Business Activity Pulse (Heatmap)
         ═══════════════════════════════════════════════════ -->
    <div class="analytics-row mt-4">
        <div class="analytics-card-glass full-width">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Business Activity Pulse</h3>
                    <span class="analytics-badge" style="color: #6366f1; background: rgba(99, 102, 241, 0.1);">Activity Heatmap (Last 30 Days)</span>
                </div>
                <div class="analytics-actions">
                    <button class="chart-action-btn focus-mode-toggle" title="Focus Mode">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                    </button>
                </div>
            </div>
            <div class="analytics-card-body">
                <div id="activityHeatmapChart" style="width: 100%; min-height: 250px;"></div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 4: Recent Activity / Pending Approvals
         ═══════════════════════════════════════════════════ -->
    <div class="dashboard-table-section" style="margin-top: 28px;">
        <div class="analytics-card-glass">
            <div class="analytics-card-header">
                <div class="analytics-title-group">
                    <h3 class="analytics-title">Pending Approvals</h3>
                    <span class="analytics-badge" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);">Action Required</span>
                </div>
                <div class="analytics-period">
                    <a href="<?= $baseUrl ?>payments/list" style="color: #6366f1; font-weight: 600; text-decoration: none; font-size: 13px;">View All →</a>
                </div>
            </div>
            
            <?php
            // Fetch the 5 most recent pending approvals
            $pendingApprovals = (new \yii\db\Query())
                ->select([
                    'payments.payments_id', 
                    'payments.payment_no', 
                    'payments.account_name', 
                    'payments.total_invoice_amount', 
                    'payments.modifiedtime', 
                    'payments.stage'
                ])
                ->from('payments')
                ->where(['IN', 'payments.stage', [2, 3]]) // First and Second Approval Pending
                ->andWhere(['payments.deleted' => 0])
                ->orderBy(['payments.modifiedtime' => SORT_DESC])
                ->limit(5)
                ->all();
            ?>

            <div class="glass-table-wrapper">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Payment No.</th>
                            <th>Account Name</th>
                            <th>Amount</th>
                            <th>Stage</th>
                            <th>Last Updated</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingApprovals)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    No pending approvals found. Great job!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingApprovals as $approval): ?>
                                <?php 
                                    $stageName = ($approval['stage'] == 2) ? 'First Approval Pending' : 'Second Approval Pending';
                                    $stageColor = ($approval['stage'] == 2) ? '#f59e0b' : '#ec4899';
                                    $stageBg = ($approval['stage'] == 2) ? 'rgba(245, 158, 11, 0.1)' : 'rgba(236, 72, 153, 0.1)';
                                ?>
                                <tr>
                                    <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($approval['payment_no'] ?? 'N/A') ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="avatar-circle" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                                <?= strtoupper(substr(trim($approval['account_name'] ?? 'U'), 0, 1)) ?>
                                            </div>
                                            <span style="font-weight: 500; color: #334155;"><?= htmlspecialchars($approval['account_name'] ?? 'Unknown Account') ?></span>
                                        </div>
                                    </td>
                                    <td style="font-weight: 700; font-family: 'Outfit', sans-serif;">
                                        ₹<?= number_format((float)($approval['total_invoice_amount'] ?? 0), 2) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge" style="color: <?= $stageColor ?>; background: <?= $stageBg ?>; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                            <?= $stageName ?>
                                        </span>
                                    </td>
                                    <td style="color: #64748b; font-size: 13px;">
                                        <?= !empty($approval['modifiedtime']) ? date('d M Y, h:i A', strtotime($approval['modifiedtime'])) : 'N/A' ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="<?= $baseUrl ?>payments/view?id=<?= $approval['payments_id'] ?>" class="btn-action-view">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php
// Expose baseUrl for the external JS file
$this->registerJs("window.dashboardBaseUrl = '{$baseUrl}';", \yii\web\View::POS_HEAD);

// Register the new dashboard JS file
$this->registerJsFile($baseUrl . 'thememain/js/dashboard-premium.js', [
    'depends' => [\yii\web\JqueryAsset::class, \backend\assets\AdminAsset::class],
]);
?>