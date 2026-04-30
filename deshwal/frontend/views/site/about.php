<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use frontend\assets\AppAsset;
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

?>
 <h4>Overview_Payment</h4>
        <div class="overview-payment">
            <div class="overview-card">
                <h2>15</h2>
                <p>Task Orders</p>
            </div>
            <div class="overview-card">
                <h2>10</h2>
                <p>Active Orders</p>
            </div>
            <div class="overview-card">
                <h2>3</h2>
                <p>Task in Progress</p>
            </div>
            <div class="overview-card">
                <h2>2</h2>
                <p>Task Suspended</p>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox"><input type="checkbox"></th>
                        <th>PO Number</th>
                        <th>Invoice Number</th>
                        <th>Location</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="checkbox"><input type="checkbox"></td>
                        <td>DWMPL-001</td>
                        <td>INV-01</td>
                        <td>Delhi</td>
                        <td>20,000</td>
                        <td><span class="status in-progress">Payment In Progress</span></td>
                    </tr>
                    <tr>
                        <td class="checkbox"><input type="checkbox"></td>
                        <td>DWMPL-002</td>
                        <td>INV-02</td>
                        <td>Mumbai</td>
                        <td>50,000</td>
                        <td><span class="status done">Payment Done</span></td>
                    </tr>
                    <tr>
                        <td class="checkbox"><input type="checkbox"></td>
                        <td>DWMPL-003</td>
                        <td>INV-03</td>
                        <td>Uttarakhand</td>
                        <td>30,000</td>
                        <td><span class="status in-progress">Payment In Progress</span></td>
                    </tr>
                    <tr>
                        <td class="checkbox"><input type="checkbox"></td>
                        <td>DWMPL-003</td>
                        <td>INV-03</td>
                        <td>Haryana</td>
                        <td>30,000</td>
                        <td><span class="status done">Payment Done</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
<?php
// Register the external JS file
$this->registerJsFile('@web/js/about/edit.js', ['depends' => [AppAsset::class]]);
// Register your jQuery code to display an alert
$this->registerJs('
    $(document).ready(function() {
        console.log("This is a jQuery alert!");
    });
');
?>

