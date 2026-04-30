$(function () {
  // Read chart data from window variables
  const recycle = window.RECYCLE;
  const resale = window.RESALE;
  const recycleComponents = window.RECYCLE_COMPONENTS;
  const resaleComponents = window.RESALE_COMPONENTS;
  const wasteSegments = window.WASTE_SEGMENTS;
  const impactSavings = window.IMPACT_SAVINGS;
  const landfillPie = window.LANDFILL_PIE;

  // Extract values for charts
  const getLabels = (list) => list.map(item => item.label);
  const getValues = (list) => list.map(item => item.value);

  // Chart: Recycle Components
  new ApexCharts(document.querySelector("#recycleComponentsChart"), {
    chart: { type: 'bar', height: 300 },
    plotOptions: {
      bar: { horizontal: true, barHeight: '40%', distributed: true }
    },
    series: [{ data: getValues(recycleComponents) }],
    xaxis: { categories: getLabels(recycleComponents) },
    colors: ['#ffa726', '#f06292', '#4fc3f7', '#90a4ae', '#ba68c8', '#aed581'],
    legend: { show: false }
  }).render();

  // Chart: Resale Components
  new ApexCharts(document.querySelector("#resaleComponentsChart"), {
    chart: { type: 'bar', height: 300 },
    plotOptions: {
      bar: { horizontal: true, barHeight: '40%', distributed: true }
    },
    series: [{ data: getValues(resaleComponents) }],
    xaxis: { categories: getLabels(resaleComponents) },
    colors: ['#f06292', '#ffa726', '#aed581', '#ba68c8', '#4fc3f7'],
    legend: { show: false }
  }).render();

  // Chart: Waste Segments (Recycle vs Resale)
  new ApexCharts(document.querySelector("#wasteChart"), {
    chart: { type: 'bar', height: 350, stacked: true },
    plotOptions: { bar: { horizontal: true, barHeight: '40%' } },
    series: [
      { name: "Recycle", data: wasteSegments.map(item => item.recycle) },
      { name: "Resale", data: wasteSegments.map(item => item.resale) }
    ],
    xaxis: { categories: getLabels(wasteSegments) },
    colors: ['#b0ecb4', '#f66d6d'],
    legend: { position: 'right', offsetY: 20 }
  }).render();

  // Chart: Total Environmental Impact Saving
  new ApexCharts(document.querySelector("#envImpactChart"), {
    chart: { type: 'bar', height: 400 },
    plotOptions: { bar: { horizontal: true, barHeight: '40%' } },
    series: [{ name: 'Impact', data: getValues(impactSavings) }],
    xaxis: { categories: getLabels(impactSavings) },
    colors: ['#6f42c1']
  }).render();

  // Chart: Landfill Pie
  new ApexCharts(document.querySelector("#landfillPieChart"), {
    chart: { type: 'pie', height: 300 },
    series: getValues(landfillPie),
    labels: getLabels(landfillPie),
    colors: ['#28a745', '#dc3545']
  }).render();
});
