<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'GRN';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

function statusClass($pickup_status){
    if(empty($pickup_status)) return "status";
    if($pickup_status == 3) return "status done";
    if($pickup_status == 4) return "status text-danger bg-danger-subtle";
    return "status in-progress";
}
?>
<!-- <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) 
?> -->
<nav aria-label="breadcrumb" class="mt-2">
    <ol class="breadcrumb rounded-3">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis" href="index">
                <i class="fa-solid fa-bars gray-shade-1-breadcrum"></i>
                <span class="visually-hidden">Home</span>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a class="text-decoration-none text-dark-emphasis gray-shade-1-breadcrum" href="index">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a class="link-body-emphasis text-decoration-none gray-shade-2-breadcrum">GRN</a>
        </li>
    </ol>
</nav>
 <h4>Overview</h4>
    <div class="overview">
        <div class="overview-card active-card">
            <p class="dasbhoard-header">Total GRNs</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure"><?= $total_grn??0;?></p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div>
        <!-- <div class="overview-card completed-card">
            <p>Purchase Order Approved</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure"><?= $purchase_order_approved??0;?></p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div> -->
        <div class="overview-card pending-card">
            <p>Pending Invoices</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">0</p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div>
        <div class="overview-card processed-card">
            <p>Total Assets Processed</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure"><?= $total_assets_processed??0;?></p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
<h4>GRN</h4>
    <div class="table-container">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th class="checkbox"><input type="checkbox"></th>
                    <th>GRN ID</th>
                    <th>Purchase Order ID </th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Date Material Received</th>
                    <th>Total PO Quantity</th>
                    <th>Total Physical Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($grnData as $pdata){ 
                    //$status_class = statusClass($pdata["stage"]);
                ?>
                <tr>
                    <td class="checkbox"><input type="checkbox"></td>
                    <td><?php echo $pdata["grn_no"]??"";?></td>
                    <td><?php echo $pdata["purchase_order"]??"";?></td>
                    <td><?php echo $pdata["invoice_number"]??"";?></td>
                    <td><?php echo $pdata["invoice_date"]??"";?></td>
                    <td><?php echo $pdata["date_material_received"]??"";?></td>
                    <td><?php echo $pdata["total_po_quantity"]??"";?></td>
                    <td><?php echo $pdata["total_quantity"]??"";?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

<!-- Custom Pagination Links -->
<!-- start of pagination  -->
<?php

$page = $pagination["page"]??1;
$total_records = $pagination["totalCount"];
$size = $pagination["defaultPageSize"]??1;
$previous_page = $page - 1;
$next_page = $page + 1;
$adjacents = "2";

if (!$total_records) $total_records = 0;
$total_no_of_pages = ceil($total_records / $size);
$second_last = $total_no_of_pages - 1; // total page minus 1

$query_string_part = "";
$query_string = $_SERVER['QUERY_STRING'];
parse_str($query_string, $querystr_in_arr);
if (isset($querystr_in_arr["page"]))
    unset($querystr_in_arr["page"]);
if (count($querystr_in_arr) > 0)
    $query_string_part = "&" . http_build_query($querystr_in_arr);
?>
<div class="d-flex justify-content-end align-items-center">
    <div>
        <button type="button" class="btn btn-info btn-sm" style="cursor:default">
            Total Number of Records <span class="badge text-bg-secondary"><?php echo $total_records; ?></span>
        </button>
    </div>
    <nav class="mt-3 ps-2">
        <ul class="pagination pagination-sm justify-content-end">
            <li class="<?php echo $page <= 1 ? "disabled page-item" : "page-item"; ?>">
                <a class="page-link" <?php if ($page > 1) {
                                            echo "href='?page=$previous_page.$query_string_part'";
                                        } ?>>Previous</a>
            </li>
            <?php
            if ($total_no_of_pages <= 10) {
                for ($counter = 1; $counter <= $total_no_of_pages; $counter++) {
                    if ($counter == $page) {
                        echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
                    } else {
                        echo "<li class='page-item'><a class='page-link' href='?page=$counter.$query_string_part'>$counter</a></li>";
                    }
                }
            } elseif ($total_no_of_pages > 10) {
                if ($page <= 4) {
                    for ($counter = 1; $counter < 8; $counter++) {
                        if ($counter == $page) {
                            echo "<li class='active page-item'><a class='page-link'>$counter</a></li>";
                        } else {
                            echo "<li class='page-item'><a class='page-link' href='?page=$counter.$query_string_part'>$counter</a></li>";
                        }
                    }
                    echo "<li class='page-item'><a class='page-link'>...</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='?page=$second_last.$query_string_part'>$second_last</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='?page=$total_no_of_pages.$query_string_part'>$total_no_of_pages</a></li>";
                } elseif ($page > 4 && $page < $total_no_of_pages - 4) {
                    echo "<li class='page-item'><a class='page-link' href='?page=1'>1</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='?page=2'>2</a></li>";
                    echo "<li class='page-item'><a class='page-link'>...</a></li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) {
                            echo "<li class='active page-item'><a class='page-link'>$counter</a></li>";
                        } else {
                            echo "<li class='page-item'><a class='page-link' href='?page=$counter.$query_string_part'>$counter</a></li>";
                        }
                    }
                    echo "<li class='page-item'><a class='page-link'>...</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='?page=$second_last.$query_string_part'>$second_last</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='?page=$total_no_of_pages.$query_string_part'>$total_no_of_pages</a></li>";
                } else {
                    echo "<li class='page-item'><a class='page-link' href='?page=1'>1</a></li>";
                    echo "<li class='page-item'><a class='page-link' href='?page=2'>2</a></li>";
                    echo "<li class='page-item'><a class='page-link' >...</a></li>";

                    for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
                        if ($counter == $page) {
                            echo "<li class='active page-item'><a class='page-link'>$counter</a></li>";
                        } else {
                            echo "<li class='page-item'><a class='page-link' href='?page=$counter.$query_string_part'>$counter</a></li>";
                        }
                    }
                }
            }
            ?>

            <li class="<?php echo $page >= $total_no_of_pages ? "disabled page-item" : "page-item"; ?>">
                <a class='page-link' <?php if ($page < $total_no_of_pages) {
                                            echo "href='?page=$next_page.$query_string_part'";
                                        } ?>>Next</a>
            </li>
            <?php if ($page < $total_no_of_pages) {
                echo "<li class='page-item'><a class='page-link' href='?page=$total_no_of_pages.$query_string_part'>Last &rsaquo;&rsaquo;</a></li>";
            } ?>
        </ul>
    </nav>
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

