<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Payment';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$static_logic = false;
function statusClass($status){
    if(empty($status)) return "status";
    if($status == 'Payment Done') return "status done";
    if($status == 'Payment Received') return "status done";
    if($status == 'Payment Pending') return "status text-danger bg-danger-subtle";
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
            <a class="link-body-emphasis text-decoration-none gray-shade-2-breadcrum">Payment</a>
        </li>
    </ol>
</nav>
 <h4>Payment Overview</h4>
    <?php if($static_logic){ ?>
        <div class="overview">
            <div class="overview-card active-card">
                <p class="dasbhoard-header">Payment Done</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure">4</p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="overview-card completed-card">
                <p>Payment Pending</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure">2</p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="overview-card pending-card">
                <p>Payment In Progress</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure">1</p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="overview-card processed-card">
                <p>Payment Received</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure">1</p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php } else{ ?> 
        <div class="overview">
            <div class="overview-card active-card">
                <p class="dasbhoard-header">Approval Pending</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure"><?php echo $pending_count??"0";?></p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="overview-card completed-card">
                <p>Payment Approved</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure"><?php echo $approved_count??"0";?></p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="overview-card pending-card">
                <p>Payment Transferred</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure"><?php echo $transferred_count??"0";?></p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="overview-card processed-card">
                <p>Partial Payment Done</p>
                <div class="d-flex align-items-baseline">
                    <p class="dashboard-figure"><?php echo $partial_payment_count??"0";?></p>
                    <div class="custom-green-text">
                        <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<h4>Payment</h4>
    <div class="table-container">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th>Req Reference Number</th>
                    <th>PO Number</th>
                    <th>Invoice Number</th>
                    <th>Location</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($paymentData as $pdata){ 
                    $status_class = statusClass($pdata["status"]);
                ?>
                <tr>
                    <?php if($static_logic){ ?>
                        <td><?php echo $pdata["ref_no"]??"";?></td>
                        <td><?php echo $pdata["po_no"]??"";?></td>
                        <td><?php echo $pdata["invoice"]??"";?></td>
                        <td><?php echo $pdata["location"]??"";?></td>
                        <td><?php echo $pdata["amount"]??"";?></td>
                        <td><span class="<?php echo $status_class;?>"><?php echo $pdata["status"]??"";?></span></td>
                    <?php }else{ ?>
                        <td><?php echo $pdata["req_reference_no"]??"";?></td>
                        <td><?php echo $pdata["po_number"]??"";?></td>
                        <td><?php echo $pdata["invoice_number"]??"";?></td>
                        <td><?php echo $pdata["location"]??"";?></td>
                        <td><?php echo $pdata["amount"]??"";?></td>
                        <td><span class="<?php echo $status_class;?>"><?php echo $pdata["status_name"]??"";?></span></td>
                    <?php } ?>
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

