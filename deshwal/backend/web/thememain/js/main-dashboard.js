let grid;
let widgetCount = 1;
var newURL = window.location.href;

$(function () {
  grid = GridStack.init({
    column: 12, // total columns per row
    // cellHeight: 'auto',
    cellHeight: '40',
    disableResize: true,
    disableDrag: false,
    float: true, // prevents overriding
    margin: 10
  });
});
function buildUrl(path) {
    let baseURL = window.location.origin + window.location.pathname;

    // Ensure base URL ends with "/"
    if (!baseURL.endsWith('/')) {
        baseURL += '/';
    }

    // Remove leading slash from path if exists
    if (path.startsWith('/')) {
        path = path.substring(1);
    }

    return baseURL + path;
}
document.addEventListener('DOMContentLoaded', () => {
  console.log("newURL"+newURL);
  
  startLoading();
  let url = 'site/dispalywidgets';
  url = buildUrl(url);
  console.log("url"+url);
  $.ajax({
    url: url,
    type: 'GET',
    data: {},
    success: function (html) {
      const temp = document.createElement('div');
      temp.innerHTML = html;
      temp.querySelectorAll('.grid-stack-item').forEach(function (widgetEl) {
        if ($(widgetEl).find('[data-widget-type]').data('widget-type') == 1)
          addSummeryORGraphwidget($(widgetEl).find('[data-widget-url]').data('widget-url'));
        grid.addWidget(widgetEl);
      });
    },
    error: function (xhr, status, error) {
      console.error("Error loading widget:", error);
    }
  });
  stopLoading();
});
$(document).on("change", "#widgetdd", function () {
    addWidgetFromUrl(this);
});
window.addWidgetFromUrl = function (selectElement) {
  console.log("onchange call");
  const selectedOption = selectElement.options[selectElement.selectedIndex];
  // Get values from option
  const widgetUrl = selectedOption.value;
  const position = selectedOption.dataset.position;
  const title = selectedOption.dataset.title;
  const widgetName = selectedOption.dataset.name;
  const widgetId = selectedOption.dataset.id;
  const widgetType = selectedOption.dataset.type;
  const modulename = selectedOption.dataset.modulename;
  const filterid = selectedOption.dataset.filterid;
   var getwidgeturl = buildUrl('site/getwidgets');
  $.ajax({
    url: getwidgeturl,
    type: 'GET',
    data: {
      widgeturl: widgetUrl,
      position: position,
      title: title,
      name: widgetName,
      widgetId: widgetId,
      widgetType: widgetType,
      modulename: modulename,
      filterid: filterid,
    },
    success: function (html) {
      console.log(html);
      // if widget is selected it should not show in dd
      $('#widgetdd option[value="' + widgetUrl + '"]').remove();
      $('#widgetdd').val('');
      if (widgetType == 1)
        addSummeryORGraphwidget(widgetUrl, modulename);
      grid.addWidget(html, { autoPosition: true });
    },
    error: function (xhr, status, error) {
      console.error("Error loading widget:", error);
    }
  });
};
function addSummeryORGraphwidget(widgetUrl, modulename) {
  console.log("from addSummeryORGraphwidget-->" + widgetUrl);
  let csrfToken = $("#csrfToken").val();
  const data = {
    _csrf: csrfToken,
    widgetUrl: widgetUrl,
    modulename: modulename,
  } 
  var charturl = buildUrl('site/getchartdata');
  $.ajax({
    url: charturl,
    type: 'GET',
    data: data,
    success: function (data) {
      console.log(data);
      if (widgetUrl === 'target-achivment.php') {
        initApexTargetvsachivementChart(data);
      }
      else if (widgetUrl === 'deal_won_in_last_7_days_amount.php') {
        initDealwoninlast7daysamount(data);
      } else if (widgetUrl === 'total_ac.php') {
        initAccountPieChart();
      }
      else if (widgetUrl === 'count_total_rc_acc_vs_non_rc_acc.php') {
        rcAccountChart(data);
      }
      else if (widgetUrl === 'payment_approval_pending_second_stage.php') {
        secondApprovalPendingClientWiseChart(data);
      }
      else if (widgetUrl === 'lot_pending_for_segregation.php') {
        pendingforsegragation(data);
      }
      else if (widgetUrl === 'lot_pending_for_tagging.php') {
        pendingfortagging(data);
      }
      else if (widgetUrl === 'lot_pending_for_cleaning.php') {
        pendingforcleaning(data);
      }
      else if (widgetUrl === 'lot_pending_for_iqc.php') {
        pendingforiqc(data);
      }
      else if (widgetUrl === 'lot_pending_for_sticker_removal.php') {
        pendingforstickerremoval(data);
      }
      else if (widgetUrl === 'ldt_count_after_iqc_completed_ageingwise.php') {
        ldtcountafteriqccompletedageingwise(data);
      }

      // stopLoading();
    },
    error: function (xhr, status, error) {
      console.error("Error loading widget:", error);
    }
  });
}
//footer button functionality
$(document).on('click', '.close-widget', function () {
  const widget = $(this).closest('.grid-stack-item')[0];
  let widgetId = widget.getAttribute('data-gs-id').split("-")[1];
  console.log("widget close-widget");
  updateWidgetView(widgetId, 1);
  grid.removeWidget(widget);
  refreshWidgetDropdowanoptions();
});
$(document).on('click', '.refresh-widget', function () {
  const widget = $(this).closest('.grid-stack-item')[0];
  let widgetId = widget.getAttribute('data-gs-id').split("-")[1];
  if (widgetId) {
    // Optional: update content inside the widget
    const contents = widget.querySelectorAll('.grid-stack-item-content');
    if (contents.length > 1) {
      refreshWidget(widgetId, widget);
    }
  }
  // grid.removeWidget(widget);
});

function refreshWidgetDropdowanoptions() {

  let csrfToken = $("#csrfToken").val();
  startLoading();
  var refreshurl = buildUrl('site/refreshwidgetdropdowan');
  $.ajax({
    url: refreshurl,
    type: 'GET',
    data: {
      _csrf: csrfToken,
    },
    success: function (widgets) {
      if (widgets) {
        const $dropdown = $("#widgetdd");
        $dropdown.empty(); // Clear old options
        $dropdown.append('<option value="">-- Select Widget --</option>');
        if (widgets && Array.isArray(widgets)) {
          widgets.forEach(function (wd) {
            const option = $('<option>')
              .val(wd.widgeturl)
              .text(wd.title)
              .attr('data-position', wd.position)
              .attr('data-title', wd.title)
              .attr('data-name', wd.name)
              .attr('data-id', wd.id)
              .attr('data-type', wd.type)
              .attr('data-modulename', wd.modulename)
              .attr('data-filterid', wd.filter_id);
            $dropdown.append(option);
          });
        } else {
          console.warn("Invalid widgets format");
        }
        stopLoading();
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading widget:", error);
    }
  });
}

function updateWidgetView(widgetId, view) {
  // alert("updatewidgetview");

  csrfToken = $("#csrfToken").val();
  startLoading();
  var updateurl = buildUrl('site/updatewidgetsview');
  $.ajax({
    url: updateurl,
    type: 'GET',
    data: {
      widgetid: widgetId,
      view: view,
      _csrf: csrfToken,
    },
    success: function (html) {
      // widget_dd
      if (html)
        stopLoading();

    },
    error: function (xhr, status, error) {
      console.error("Error loading widget:", error);
    }
  });
}
//footer button functions end here
function refreshWidget(widgetId, widget) {
  const $widgetBox = $(widget);
  console.log(widget);
  var getwidgeturl = buildUrl('site/getwidgets');
  startLoading();
  $.ajax({
    url: getwidgeturl,
    type: 'GET',
    data: {
      refresh_widgetId: widgetId,
    },
    success: function (html) {
      console.log(html);
      $widgetBox.find('.grid-stack-item-content').html(html);
      let widgetUrl = $widgetBox.find('.grid-stack-item-content  [data-widget-url]').data('widget-url');
      addSummeryORGraphwidget(widgetUrl);
      stopLoading();
    },
    error: function (xhr, status, error) {
      console.error("Error loading widget:", error);
      $widgetBox.find('.grid-stack-item-content').html('<div class="text-danger">Failed to refresh</div>');
    }
  });
}
//sales dashboard charts
function initDealwoninlast7daysamount(data) {
  console.log("initDealwoninlast7daysamount" + data);
  var options = {
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    series: [
      {
        name: 'Target',
        // data: [37, 44, 28, 32, 36, 41, 27, 18]
        data: data.targetData
      },
      {
        name: 'Achievement',
        // data: [22, 31, 13, 28, 22, 30, 19, 12]
        data: data.achievementData
      }
    ],
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
      labels: {
        style: {
          fontSize: '12px'
        }
      },
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      }
    },
    yaxis: {
      min: 0,
      tickAmount: 5,
      labels: {
        style: {
          fontSize: '12px'
        }
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '40%',
        borderRadius: 0
      }
    },
    fill: {
      opacity: 1,
      colors: ['#0000A0', '#75A3EB']
    },
    colors: ['#0000A0', '#75A3EB'],
    dataLabels: {
      enabled: false
    },
    legend: {
      position: 'top',
      horizontalAlign: 'right', // Optional: 'left' or 'right'
      markers: {
        radius: 12,
        width: 10,
        height: 10
      },
      itemMargin: {
        horizontal: 10,
        vertical: 10
      }
    },
    tooltip: {
      theme: 'dark'
    },
    grid: {
      borderColor: '#eee',
      strokeDashArray: 2,
      xaxis: {
        lines: {
          show: false
        }
      }
    }
  };

  const chart = new ApexCharts(document.querySelector("#dealWonChart"), options);
  chart.render();
}

function initAccountPieChart() {
  const options = {
    chart: {
      type: 'donut',
      height: 300
    },
    series: [100, 10, 60, 30],
    labels: [
      'Total Account',
      'High Business Account',
      'Low Business Account',
      'No Business Accounts (Nos)'
    ],
    colors: ['#0000A0', '#8080ff', '#00bcd4', '#4dd0e1'],
    legend: {
      show: false
    },
    dataLabels: {
      enabled: true,
      style: {
        colors: ['#fff'],
        fontSize: '12px',
        fontWeight: 'bold'
      },
      formatter: function (val, opts) {
        return opts.w.globals.series[opts.seriesIndex]; // show raw value
      }
    },
    tooltip: {
      enabled: true
    },
    plotOptions: {
      pie: {
        donut: {
          size: '35%',
          labels: {
            show: false
          }
        }
      }
    }
  };

  const chart = new ApexCharts(document.querySelector("#accountPieChart"), options);
  chart.render();
}

function initApexTargetvsachivementChart(data) {
  var options = {
    chart: {
      type: 'donut',
      height: 300
    },
    series: [data.target, data.achievement], // Target, Achievement
    labels: ['Target', 'Achievement'],
    colors: ['#0013B6', '#6DD1FF'],
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
        const value = opts.w.globals.series[opts.seriesIndex];
        return value + 'L';
      },
      style: {
        fontSize: '12px',
        fontWeight: 'bold'
      }
    },
    legend: {
      position: 'bottom',
      markers: {
        width: 12,
        height: 12
      }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '35%'
        }
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#targetvsachivement"), options);
  chart.render();
}

function rcAccountChart(data) {
  var options = {
    chart: {
      type: 'donut',
      height: 300
    },
    series: [data.rc, data.non_rc],
    labels: ['Total RC Account', 'Non-RC Account Count'],
    colors: ['#4dd0e1', '#00bcd4'],
    legend: {
      position: 'bottom',
      fontSize: '12px',
      markers: {
        width: 10,
        height: 10,
        radius: 50
      },
      itemMargin: {
        vertical: 5
      }
    },
    dataLabels: {
      enabled: true,
      style: {
        fontSize: '12px',
        fontWeight: 'bold'
      },
      formatter: function (val, opts) {
        return opts.w.config.series[opts.seriesIndex];
      }
    },
    tooltip: {
      enabled: true
    },
    plotOptions: {
      pie: {
        donut: {
          size: '35%'
        }
      }
    },
    // title: {
    //   text: 'Total RC Account V/S Non-RC Account Count',
    //   align: 'center',
    //   style: {
    //     fontSize: '16px',
    //     fontWeight: 'bold'
    //   }
    // }
  };

  var chart = new ApexCharts(document.querySelector("#rcAccountChart"), options);
  chart.render();

}
//ens sales dashboard charts
//finance dashboard charts
function secondApprovalPendingClientWiseChart(chartData) {

  console.log("chartData" + chartData);
  const chartEl = document.querySelector("#secondapprovalpendingclientwisechart");
  if (!chartEl) {
    console.error("Chart container not found, retrying...");
    // Retry after short delay
    setTimeout(secondApprovalPendingClientWiseChart, 100);
    return;
  }
  var options = {
    chart: {
      type: 'bar',
      stacked: true,
      height: 400,
      toolbar: {
        show: false
      }
    },
    dataLabels: {
      enabled: false
    },
    series: chartData.series,
    // series: [{
    //   name: '0-2 Days',
    //   data: [2, 2, 2, 2, 2, 1, 2, 2, 2],
    //   color: '#FF9061' // Orange
    // }, {
    //   name: '3+ Days',
    //   data: [0, 1, 3, 1, 0, 0, 2, 1, 0],
    //   color: '#DF4E5C' // Red
    // }],
    xaxis: {
      // categories: [
      //   'First Client', 'VNC INFOTEK', 'Savi Info Solutions',
      //   'Savi Services', 'Andi Group', 'Phoenix Infotech',
      //   'Andi Group', 'Phoenix Infotech', 'Last Client'
      // ],
      categories: chartData.categories,
      labels: {
        rotate: -45
      }
    },
    yaxis: {
      title: {
        text: 'Age'
      }
    },
    legend: {
      position: 'top'
    },
    tooltip: {
      y: {
        formatter: val => `${val} day(s)`
      }
    }
  };

  const chart = new ApexCharts(chartEl, options);
  chart.render();
}
//finanace dashboard charts
//warehouse chart
function pendingforsegragation(data) {
  const options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false // ✅ hides the export/zoom menu
      }
    },
    series: [{
      name: 'Lot Count',
      data: data.values,//[75, 95]
    }],
    xaxis: {
      categories: data.labels,//['0–3', '>3'],
      title: {
        text: 'Days'
      }
    },
    yaxis: {
      // title: {
      //   text: 'Lot'
      // },
      min: 0
    },
    colors: ['#0000A0', '#4dd0e1'],
    legend: {
      show: false
    }
  };

  const chart = new ApexCharts(document.querySelector("#pendingforsegragation"), options);
  chart.render();

}
function pendingfortagging(data) {
  const options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false // ✅ hides the export/zoom menu
      }
    },
    series: [{
      name: 'Lot Count',
      data: data.values,//[75, 95]
    }],
    xaxis: {
      categories: data.labels,//['0–3', '>3'],
      title: {
        text: 'Days'
      }
    },
    yaxis: {
      // title: {
      //   text: 'Lot'
      // },
      min: 0
    },
    colors: ['#0000A0', '#4dd0e1'],
    legend: {
      show: false
    }
  };

  const chart = new ApexCharts(document.querySelector("#pendingfortagging"), options);
  chart.render();

}
function pendingforcleaning(data) {
  const options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false // ✅ hides the export/zoom menu
      }
    },
    series: [{
      name: 'Lot Count',
      data: data.values,//[75, 95]
    }],
    xaxis: {
      categories: data.labels,//['0–3', '>3'],
      title: {
        text: 'Days'
      }
    },
    yaxis: {
      // title: {
      //   text: 'Lot'
      // },
      min: 0
    },
    colors: ['#5c9cff', '#0000A0', '#4dd0e1',],
    legend: {
      show: false
    }
  };

  const chart = new ApexCharts(document.querySelector("#pendingforcleaning"), options);
  chart.render();

}
function pendingforiqc(data) {
  const options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false // ✅ hides the export/zoom menu
      }
    },
    series: [{
      name: 'Lot Count',
      data: data.values,//[75, 95]
    }],
    xaxis: {
      categories: data.labels,//['0–3', '>3'],
      title: {
        text: 'Days'
      }
    },
    yaxis: {
      // title: {
      //   text: 'Lot'
      // },
      min: 0
    },
    colors: ['#5c9cff', '#0000A0', '#4dd0e1',],
    legend: {
      show: false
    }
  };

  const chart = new ApexCharts(document.querySelector("#pendingforiqc"), options);
  chart.render();

}
function pendingforstickerremoval(data) {
  const options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false
      }
    },
    series: [{
      name: 'Lot Count',
      data: data.values,//[75, 95]
    }],
    xaxis: {
      categories: data.labels,//['0–3', '>3'],
      title: {
        text: 'Days'
      }
    },
    yaxis: {
      // title: {
      //   text: 'Lot'
      // },
      min: 0
    },
    colors: ['#5c9cff', '#0000A0', '#4dd0e1',],
    legend: {
      show: false
    }
  };

  const chart = new ApexCharts(document.querySelector("#pendingforstickerremoval"), options);
  chart.render();

}

function ldtcountafteriqccompletedageingwise(data) {
  const options = {
    chart: {
      type: 'bar',
      height: 400,
      toolbar: {
        show: false
      }
    },
    series: [
      {
        name: 'Laptop',
        data: [15, 8, 4]  // replace with your actual data
      },
      {
        name: 'Desktop',
        data: [10, 12, 5]
      },
      {
        name: 'TFT',
        data: [5, 3, 2]
      }
    ],
    colors: ['#333AD1', '#75DAEB', '#36ABFF'],
    xaxis: {
      categories: ['0-3 days', '4-7 days', '>7 days'],
    },
    yaxis: {
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '55%',
        endingShape: 'rounded'
      }
    },
    legend: {
      position: 'top'
    }
  };

  const chart = new ApexCharts(document.querySelector("#ldt_count_after_iqc_completed_ageingwise"), options);
  chart.render();
}

//warehouse chart
