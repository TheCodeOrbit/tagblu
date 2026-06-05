(function ($) {
  'use strict';

  var baseUrl = window.vmDashboardBaseUrl || '/';
  var API_URL = baseUrl + 'site/getdashboarddata';
  var currentPeriod = 'this_week';

  console.log('VM Dashboard baseUrl:', baseUrl, 'API_URL:', API_URL);

  var PERIOD_LABELS = {
    today: 'Today',
    this_week: 'This Week',
    this_month: 'This Month',
    this_quarter: 'This Quarter',
    this_year: 'This Year'
  };

  var numberFormatter = new Intl.NumberFormat('en-IN');

  function formatCurrency(val) {
    return '\u20B9' + numberFormatter.format(Math.round(val));
  }

  function updateActivePeriodLabel() {
    $('.vm-active-period-label').text(PERIOD_LABELS[currentPeriod] || 'This Week');
  }

  function fetchData(period) {
    currentPeriod = period || currentPeriod;
    updateActivePeriodLabel();

    $.ajax({
      url: API_URL,
      type: 'GET',
      data: { period: currentPeriod },
      dataType: 'json',
      beforeSend: function () { },
      success: function (res) {
        if (res.error) return;
        updateCards(res.cards);
        updatePipeline(res.pipeline);
        updateActivity(res.activity);
        updateInsights(res.insights);
        renderOpportunityTrendChart(res.won_trend_chart);
        renderWonAmountTrendChart(res.won_trend_chart, res.cards);
        renderOppDistributionChart(res.pipeline);
        updateTeamsLeaderboard(res);
        updateLeaderboards(res);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error('VM Dashboard API error:', textStatus, errorThrown, jqXHR.responseText);
      }
    });
  }

  function updateCards(cards) {
    if (!cards) return;

    var opps = cards.opportunities || {};
    $('#vm_total_isrs').text(numberFormatter.format(opps.value || 0));
    applyTrend('#vm_isrs_trend', opps.trend, opps.trend_value);

    var oppsAmt = cards.opportunities_amount || {};
    $('#vm_total_opps').text(numberFormatter.format(oppsAmt.value || 0));
    applyTrend('#vm_opps_trend', oppsAmt.trend, oppsAmt.trend_value);

    var won = cards.won_opportunities || {};
    $('#vm_won_opps').text(numberFormatter.format(won.value || 0));
    applyTrend('#vm_won_trend', won.trend, won.trend_value);

    var open = cards.open_opportunities || {};
    $('#vm_open_opps').text(numberFormatter.format(open.value || 0));
    applyTrend('#vm_open_trend', open.trend, open.trend_value);

    var wonAmt = cards.won_opportunities_amount || {};
    $('#vm_won_amount').text(formatCurrency(wonAmt.value || 0));
    applyTrend('#vm_won_amount_trend', wonAmt.trend, wonAmt.trend_value);
  }

  function applyTrend(containerId, direction, value) {
    var $el = $(containerId);
    $el.removeClass('trend-up trend-down');
    if (direction === 'up') {
      $el.addClass('trend-up');
      $el.find('.vm-trend-text').text(numberFormatter.format(value) + '% vs last period');
    } else if (direction === 'down') {
      $el.addClass('trend-down');
      $el.find('.vm-trend-text').text(numberFormatter.format(value) + '% vs last period');
    } else {
      $el.find('.vm-trend-text').text('--% vs last period');
    }
  }

  function updatePipeline(pipeline) {
    if (!pipeline || !pipeline.stages) return;

    var total = pipeline.total || 0;
    $('#vm_pipeline_total').text(total);

    var stageMap = {
      'Prospect': { dot: 'blue', bar: 'blue', el: 1 },
      'Screening': { dot: 'light-blue', bar: 'light-blue', el: 2 },
      'Qualified': { dot: 'green', bar: 'green', el: 3 },
      'Submit for Pricing': { dot: 'yellow', bar: 'yellow', el: 4 },
      'Purchase Price Received': { dot: 'purple', bar: 'purple', el: 5 },
      'Quote Approved': { dot: 'teal', bar: 'teal', el: 10 },
      'Closed Won': { dot: 'emerald', bar: 'emerald', el: 8 },
      'Closed Lost': { dot: 'red', bar: 'red', el: 9 }
    };

    $.each(stageMap, function (label, info) {
      var data = pipeline.stages[label];
      var count = data ? data.count : 0;
      var pct = data ? data.percentage : 0;
      $('#vm_bar_' + info.el).css('width', pct + '%');
      $('#vm_count_' + info.el).text(count + ' (' + pct + '%)');
    });
  }

  function updateActivity(activity) {
    if (!activity) return;
    $('#vm_snap_calls').text(numberFormatter.format(activity.calls || 0));
    $('#vm_snap_meetings').text(numberFormatter.format(activity.meetings || 0));
    $('#vm_snap_quotes').text(numberFormatter.format(activity.approved_quotes || 0));
  }

  function updateInsights(insights) {
    if (!insights) return;
    $('#vm_insight_stuck').text(insights.stuck_count || 0);
    $('#vm_insight_highval').text(insights.high_value_count || 0);
    if (insights.rate_change !== undefined) {
      var sign = insights.rate_change >= 0 ? '+' : '';
      $('#vm_insight_conversion').text('Overall win rate improved by ' + sign + insights.rate_change + '% compared to last month.');
    }
  }

  var oppsTrendChart = null;
  function renderOpportunityTrendChart(chartData) {
    if (!chartData) return;
    var el = document.querySelector('#vmOppsTrendChart');
    if (!el) return;
    if (oppsTrendChart) { oppsTrendChart.destroy(); oppsTrendChart = null; }
    var options = {
      chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      series: [{ name: 'Won Opportunities', data: chartData.series || [] }],
      xaxis: { categories: chartData.categories || [], labels: { style: { fontSize: '11px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
      yaxis: { labels: { style: { fontSize: '11px', colors: '#94a3b8' } }, min: 0 },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 3, colors: ['#6366f1'] },
      fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.05, stops: [0, 100] } },
      colors: ['#6366f1'],
      grid: { borderColor: 'rgba(0,0,0,0.05)', strokeDashArray: 3 },
      tooltip: { theme: 'light' }
    };
    oppsTrendChart = new ApexCharts(el, options);
    oppsTrendChart.render();
  }

  var wonAmountChart = null;
  function renderWonAmountTrendChart(trendData, cards) {
    var el = document.querySelector('#vmWonAmountTrendChart');
    if (!el) return;
    if (wonAmountChart) { wonAmountChart.destroy(); wonAmountChart = null; }
    var wonAmt = cards && cards.won_opportunities_amount ? cards.won_opportunities_amount.value : 0;
    var seriesData = trendData && trendData.series ? trendData.series.slice() : [];
    if (seriesData.length > 0) {
      seriesData = seriesData.map(function () { return Math.round(wonAmt / (seriesData.length || 1)); });
    }
    var options = {
      chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
      series: [{ name: 'Won Amount', data: seriesData }],
      xaxis: { categories: trendData && trendData.categories ? trendData.categories : [], labels: { style: { fontSize: '11px', colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
      yaxis: { labels: { style: { fontSize: '11px', colors: '#94a3b8' }, formatter: function (v) { return '\u20B9' + numberFormatter.format(v); } }, min: 0 },
      dataLabels: { enabled: false },
      plotOptions: { bar: { columnWidth: '50%', borderRadius: 6, distributed: false } },
      colors: ['#10b981'],
      grid: { borderColor: 'rgba(0,0,0,0.05)', strokeDashArray: 3 },
      tooltip: { theme: 'light', y: { formatter: function (v) { return '\u20B9' + numberFormatter.format(v); } } }
    };
    wonAmountChart = new ApexCharts(el, options);
    wonAmountChart.render();
  }

  var distChart = null;
  function renderOppDistributionChart(pipeline) {
    var el = document.querySelector('#vmOppDistributionChart');
    if (!el) return;
    if (distChart) { distChart.destroy(); distChart = null; }
    var labels = [], series = [], colors = [];
    var colorMap = {
      'Prospect': '#2563eb', 'Screening': '#60a5fa', 'Qualified': '#10b981',
      'Submit for Pricing': '#f59e0b', 'Purchase Price Received': '#c084fc',
      'Quote Approved': '#14b8a6', 'Closed Won': '#34d399', 'Closed Lost': '#f87171'
    };
    if (pipeline && pipeline.stages) {
      $.each(pipeline.stages, function (label, data) {
        if (data.count > 0) {
          labels.push(label);
          series.push(data.count);
          colors.push(colorMap[label] || '#6366f1');
        }
      });
    }
    if (series.length === 0) { series.push(1); labels.push('No Data'); colors.push('#e2e8f0'); }
    var options = {
      chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
      series: series,
      labels: labels,
      colors: colors,
      legend: { position: 'bottom', fontSize: '12px', itemMargin: { horizontal: 10, vertical: 4 } },
      dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 600, colors: ['#fff'] }, formatter: function (val) { return Math.round(val) + '%'; } },
      plotOptions: { pie: { donut: { size: '55%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, formatter: function (w) { return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0); } } } } } },
      tooltip: { theme: 'light' }
    };
    distChart = new ApexCharts(el, options);
    distChart.render();
  }

  function updateTeamsLeaderboard(res) {
    var teams = res && res.teams;
    var $tbody = $('#vm_teams_tbody');
    if (!$tbody.length) return;
    if (teams && teams.length > 0) {
      var html = '';
      $.each(teams, function (i, row) {
        html += '<tr>';
        html += '<td><div class="vm-isr-name"><span class="vm-isr-avatar av-' + ((i % 5) + 1) + '">' + (row.name ? row.name.charAt(0) : '?') + '</span>' + (row.name || '--') + '</div></td>';
        html += '<td>' + numberFormatter.format(row.opportunities || 0) + '</td>';
        html += '<td>' + numberFormatter.format(row.won || 0) + '</td>';
        html += '<td>' + formatCurrency(row.won_amount || 0) + '</td>';
        html += '<td><div class="vm-win-rate-bar"><span class="rate-val">' + (row.win_rate || 0) + '%</span><div class="vm-mini-bar-wrap"><div class="vm-mini-bar-fill" style="width:' + (row.win_rate || 0) + '%"></div></div></div></td>';
        html += '</tr>';
      });
      $tbody.html(html);
    }
  }

  function updateLeaderboards(res) {
    var lb = res && res.leaderboards;
    if (!lb) return;
    renderLeaderboardTable('#vm_lb_opps', lb.by_opportunities, 'opp_count', 'vs_last_week');
    renderLeaderboardTable('#vm_lb_won_amt', lb.by_won_amount, 'won_amount', 'vs_last_week', true);
    renderLeaderboardTable('#vm_lb_calls', lb.by_calls, 'call_count', 'vs_last_week');
    renderLeaderboardTable('#vm_lb_meetings', lb.by_meetings, 'meet_count', 'vs_last_week');
  }

  function renderLeaderboardTable(tbodyId, data, valueKey, trendKey, isCurrency) {
    var $tbody = $(tbodyId);
    if (!$tbody.length) return;
    if (!data || data.length === 0) {
      $tbody.html('<tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:24px;">No data</td></tr>');
      return;
    }
    var html = '';
    $.each(data, function (i, row) {
      var val = isCurrency ? formatCurrency(row[valueKey] || 0) : numberFormatter.format(row[valueKey] || 0);
      var trend = row[trendKey] || 0;
      var trendClass = trend >= 0 ? 'up' : 'down';
      var trendSign = trend >= 0 ? '+' : '';
      html += '<tr>';
      html += '<td><div class="vm-isr-name"><span class="vm-isr-avatar av-' + ((i % 5) + 1) + '">' + (row.name ? row.name.charAt(0) : '?') + '</span>' + (row.name || '--') + '</div></td>';
      html += '<td>' + val + '</td>';
      html += '<td><span class="vm-micro-trend ' + trendClass + '">' + trendSign + trend + '%</span></td>';
      html += '</tr>';
    });
    $tbody.html(html);
  }

  $(document).on('click', '.vm-period-btn', function () {
    var $btn = $(this);
    var period = $btn.data('period');
    if (!period || period === currentPeriod) return;
    $('.vm-period-btn.active').removeClass('active');
    $btn.addClass('active');
    fetchData(period);
  });

  $(function () {
    fetchData('this_week');
  });

})(jQuery);
