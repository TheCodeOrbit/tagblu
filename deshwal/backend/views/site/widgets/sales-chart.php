<!-- views/widgets/sales.php -->
<?php
// Sample data - you can replace this with real data from the controller
$labels = ['Electronics', 'Clothing', 'Groceries', 'Books'];
$data = [300, 500, 100, 150];
$colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];
?>

<canvas id="pieChartWidget" width="400" height="300"></canvas>

<script>
const pieCtx = document.getElementById('pieChartWidget').getContext('2d');

new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($data) ?>,
            backgroundColor: <?= json_encode($colors) ?>,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Sales Distribution by Category'
            },
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
