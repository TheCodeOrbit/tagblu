<?php

use backend\assets\AdminAsset;
use yii\web\JqueryAsset;

AdminAsset::register($this);
$baseUrl = rtrim(Yii::$app->request->baseUrl, '/') . '/';

// Register CSS
$this->registerCssFile($baseUrl . 'thememain/css/vm-dashboard.css', [
    'depends' => [\backend\assets\AdminAsset::class],
]);

// Register ApexCharts JS
$this->registerJsFile($baseUrl . 'thememain/js/apexcharts.js', [
    'depends' => [JqueryAsset::class],
]);

$userName = Yii::$app->user->identity->first_name ?? 'Manager';
?>

<div class="vm-dashboard-wrap">

    <!-- ═══════════════════════════════════════════════════
         SECTION 1: Welcome Header + Period Picker
         ═══════════════════════════════════════════════════ -->
    <div class="vm-welcome-panel">
        <div class="vm-welcome-content">
            <div class="vm-welcome-text">
                <span class="vm-greeting">Welcome back,</span>
                <h1><?= $userName ?> 👋</h1>
                <p class="vm-subtitle">Here's the overall performance snapshot for your vertical and teams.</p>
            </div>

            <div class="vm-period-picker" id="vmPeriodPicker">
                <button class="vm-period-btn" data-period="today">Today</button>
                <button class="vm-period-btn active" data-period="this_week">This Week</button>
                <button class="vm-period-btn" data-period="this_month">This Month</button>
                <button class="vm-period-btn" data-period="this_quarter">This Quarter</button>
                <button class="vm-period-btn" data-period="this_year">This Year</button>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 2: 5 Hero KPI Cards
         ═══════════════════════════════════════════════════ -->
    <div class="vm-hero-grid">
        <!-- Card 1: Total ISRs -->
        <div class="vm-stat-card card-blue">
            <div class="vm-stat-icon icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="vm-stat-info">
                <span class="vm-stat-label">Total ISRs</span>
                <span class="vm-stat-value" id="vm_total_isrs">0</span>
                <div class="vm-stat-trend" id="vm_isrs_trend">
                    <span class="vm-trend-arrow"></span>
                    <span class="vm-trend-text">--% vs last period</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Opportunities -->
        <div class="vm-stat-card card-green">
            <div class="vm-stat-icon icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <div class="vm-stat-info">
                <span class="vm-stat-label">Total Opportunities</span>
                <span class="vm-stat-value" id="vm_total_opps">0</span>
                <div class="vm-stat-trend" id="vm_opps_trend">
                    <span class="vm-trend-arrow"></span>
                    <span class="vm-trend-text">--% vs last period</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Won Opportunities -->
        <div class="vm-stat-card card-purple">
            <div class="vm-stat-icon icon-purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34"/><path d="M12 2a6 6 0 0 1 6 6v5a6 6 0 0 1-12 0V8a6 6 0 0 1 6-6z"/></svg>
            </div>
            <div class="vm-stat-info">
                <span class="vm-stat-label">Won Opportunities</span>
                <span class="vm-stat-value" id="vm_won_opps">0</span>
                <div class="vm-stat-trend" id="vm_won_trend">
                    <span class="vm-trend-arrow"></span>
                    <span class="vm-trend-text">--% vs last period</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Open Opportunities -->
        <div class="vm-stat-card card-orange">
            <div class="vm-stat-icon icon-orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="vm-stat-info">
                <span class="vm-stat-label">Open Opportunities</span>
                <span class="vm-stat-value" id="vm_open_opps">0</span>
                <div class="vm-stat-trend" id="vm_open_trend">
                    <span class="vm-trend-arrow"></span>
                    <span class="vm-trend-text">--% vs last period</span>
                </div>
            </div>
        </div>

        <!-- Card 5: Total Won Amount -->
        <div class="vm-stat-card card-teal">
            <div class="vm-stat-icon icon-teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="vm-stat-info">
                <span class="vm-stat-label">Total Won Amount</span>
                <span class="vm-stat-value" id="vm_won_amount">₹0</span>
                <div class="vm-stat-trend" id="vm_won_amount_trend">
                    <span class="vm-trend-arrow"></span>
                    <span class="vm-trend-text">--% vs last period</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 3: Pipeline + Activity + Insights/Teams
         ═══════════════════════════════════════════════════ -->
    <div class="vm-mid-row">

        <!-- LEFT: Opportunity Pipeline By Stage -->
        <div class="vm-glass-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Opportunity Pipeline (By Stage)</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body">
                <div class="vm-pipeline-list" id="vm_pipeline_list">
                    <!-- Pipeline rows injected dynamically via JS -->
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot blue"></span><span class="vm-stage-name">Prospect</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill blue" id="vm_bar_1" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_1">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot light-blue"></span><span class="vm-stage-name">Screening</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill light-blue" id="vm_bar_2" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_2">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot green"></span><span class="vm-stage-name">Qualified</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill green" id="vm_bar_3" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_3">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot yellow"></span><span class="vm-stage-name">Submit for Pricing</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill yellow" id="vm_bar_4" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_4">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot purple"></span><span class="vm-stage-name">Purchase Price Received</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill purple" id="vm_bar_5" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_5">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot teal"></span><span class="vm-stage-name">Quote Approved</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill teal" id="vm_bar_10" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_10">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot emerald"></span><span class="vm-stage-name">Closed Won</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill emerald" id="vm_bar_8" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_8">0 (0%)</div></div>
                    <div class="vm-pipeline-item"><div class="vm-stage-label"><span class="vm-stage-dot red"></span><span class="vm-stage-name">Closed Lost</span></div><div class="vm-bar-wrap"><div class="vm-bar-fill red" id="vm_bar_9" style="width:0%"></div></div><div class="vm-stage-count" id="vm_count_9">0 (0%)</div></div>
                </div>
            </div>
            <div class="vm-card-footer">
                <span class="vm-pipeline-total-label">Total Opportunities</span>
                <span class="vm-pipeline-total-val" id="vm_pipeline_total">0</span>
            </div>
        </div>

        <!-- CENTER: Activity Snapshot + Insights -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            <!-- Activity Snapshot -->
            <div class="vm-glass-card" style="flex:1;">
                <div class="vm-card-header">
                    <h3 class="vm-card-title">Activity Snapshot</h3>
                    <span class="vm-card-badge vm-active-period-label">This Week</span>
                </div>
                <div class="vm-card-body">
                    <div class="vm-activity-grid">
                        <!-- Calls -->
                        <div class="vm-activity-item">
                            <div class="vm-activity-icon blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <span class="vm-activity-value" id="vm_snap_calls">0</span>
                            <span class="vm-activity-label">Calls</span>
                            <span class="vm-activity-trend up" id="vm_calls_trend">-- vs last period</span>
                        </div>

                        <!-- Meetings -->
                        <div class="vm-activity-item">
                            <div class="vm-activity-icon green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <span class="vm-activity-value" id="vm_snap_meetings">0</span>
                            <span class="vm-activity-label">Meetings</span>
                            <span class="vm-activity-trend up" id="vm_meetings_trend">-- vs last period</span>
                        </div>

                        <!-- Quotes Sent -->
                        <div class="vm-activity-item">
                            <div class="vm-activity-icon purple">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            </div>
                            <span class="vm-activity-value" id="vm_snap_quotes">0</span>
                            <span class="vm-activity-label">Quotes Sent</span>
                            <span class="vm-activity-trend up" id="vm_quotes_trend">-- vs last period</span>
                        </div>

                        <!-- Approvals -->
                        <div class="vm-activity-item">
                            <div class="vm-activity-icon amber">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <span class="vm-activity-value" id="vm_snap_approvals">0</span>
                            <span class="vm-activity-label">Approvals</span>
                            <span class="vm-activity-trend up" id="vm_approvals_trend">-- vs last period</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insights -->
            <div class="vm-glass-card" style="flex:1;">
                <div class="vm-card-header">
                    <h3 class="vm-card-title">Insights</h3>
                </div>
                <div class="vm-card-body">
                    <div class="vm-insights-list">
                        <div class="vm-insight-row warning">
                            <div class="vm-insight-icon">⚠️</div>
                            <div class="vm-insight-text"><strong id="vm_insight_stuck">0</strong> opportunities are stuck in <strong>Qualified</strong> stage for more than 7 days.</div>
                        </div>
                        <div class="vm-insight-row danger">
                            <div class="vm-insight-icon">🔥</div>
                            <div class="vm-insight-text"><strong id="vm_insight_highval">0</strong> high-value opportunities need your immediate attention.</div>
                        </div>
                        <div class="vm-insight-row info">
                            <div class="vm-insight-icon">📈</div>
                            <div class="vm-insight-text" id="vm_insight_conversion">Overall win rate improved by --% compared to last week.</div>
                        </div>
                        <a href="<?= $baseUrl ?>opportunities/list" class="vm-view-all-link">View All Insights →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Top Performing Teams -->
        <div class="vm-glass-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Top Performing Teams</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body" style="padding: 12px 20px;">
                <table class="vm-teams-table">
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Opportunities</th>
                            <th>Won</th>
                            <th>Won Amount</th>
                            <th>Win Rate</th>
                        </tr>
                    </thead>
                    <tbody id="vm_teams_tbody">
                        <!-- Dynamic rows inserted by JS -->
                        <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:40px;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="vm-card-footer">
                <a href="#" class="vm-view-all-link" style="margin:0;">View All Teams →</a>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 4: Top ISRs Leaderboard (4 cards)
         ═══════════════════════════════════════════════════ -->
    <div class="vm-leaderboard-row">
        <!-- ISRs by Opportunities -->
        <div class="vm-glass-card vm-leaderboard-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Top ISRs by Opportunities</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body">
                <table class="vm-leaderboard-table">
                    <thead><tr><th>ISR</th><th>Opportunities</th><th>vs Last Week</th></tr></thead>
                    <tbody id="vm_lb_opps"></tbody>
                </table>
            </div>
            <div class="vm-card-footer">
                <a href="#" class="vm-view-all-link" style="margin:0;">View All ISRs →</a>
            </div>
        </div>

        <!-- ISRs by Won Amount -->
        <div class="vm-glass-card vm-leaderboard-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Top ISRs by Won Amount</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body">
                <table class="vm-leaderboard-table">
                    <thead><tr><th>ISR</th><th>Won Amount</th><th>vs Last Week</th></tr></thead>
                    <tbody id="vm_lb_won_amt"></tbody>
                </table>
            </div>
            <div class="vm-card-footer">
                <a href="#" class="vm-view-all-link" style="margin:0;">View All ISRs →</a>
            </div>
        </div>

        <!-- ISRs by Calls -->
        <div class="vm-glass-card vm-leaderboard-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Top ISRs by Calls</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body">
                <table class="vm-leaderboard-table">
                    <thead><tr><th>ISR</th><th>Calls</th><th>vs Last Week</th></tr></thead>
                    <tbody id="vm_lb_calls"></tbody>
                </table>
            </div>
            <div class="vm-card-footer">
                <a href="#" class="vm-view-all-link" style="margin:0;">View All ISRs →</a>
            </div>
        </div>

        <!-- ISRs by Meetings -->
        <div class="vm-glass-card vm-leaderboard-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Top ISRs by Meetings</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body">
                <table class="vm-leaderboard-table">
                    <thead><tr><th>ISR</th><th>Meetings</th><th>vs Last Week</th></tr></thead>
                    <tbody id="vm_lb_meetings"></tbody>
                </table>
            </div>
            <div class="vm-card-footer">
                <a href="#" class="vm-view-all-link" style="margin:0;">View All ISRs →</a>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         SECTION 5: Charts Row (3 charts)
         ═══════════════════════════════════════════════════ -->
    <div class="vm-charts-row">
        <!-- Opportunities Trend -->
        <div class="vm-glass-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Opportunities Trend</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body vm-chart-wrap">
                <div id="vmOppsTrendChart"></div>
            </div>
        </div>

        <!-- Won Amount Trend -->
        <div class="vm-glass-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Won Amount Trend</h3>
                <span class="vm-card-badge vm-active-period-label">This Week</span>
            </div>
            <div class="vm-card-body vm-chart-wrap">
                <div id="vmWonAmountTrendChart"></div>
            </div>
        </div>

        <!-- Opportunity Distribution (Donut) -->
        <div class="vm-glass-card">
            <div class="vm-card-header">
                <h3 class="vm-card-title">Opportunity Distribution (By Stage)</h3>
            </div>
            <div class="vm-card-body vm-chart-wrap">
                <div id="vmOppDistributionChart"></div>
            </div>
            <div class="vm-card-footer">
                <a href="#" class="vm-view-all-link" style="margin:0;">View Full Distribution →</a>
            </div>
        </div>
    </div>

</div>

<!-- Expose baseUrl and load JS -->
<?php
$this->registerJs("window.vmDashboardBaseUrl = '{$baseUrl}';", \yii\web\View::POS_HEAD);

$this->registerJsFile($baseUrl . 'thememain/js/vm-dashboard.js', [
    'depends' => [\yii\web\JqueryAsset::class, \backend\assets\AdminAsset::class],
]);
?>
