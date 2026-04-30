<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Certificate Generarted';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

function statusClass($status){
    if(empty($status)) return "status in-progress";
    if($status == 'Certificate Generated') return "status done";
    // if($status == 'Pending') return "status text-danger bg-danger-subtle";
    return "status done";
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
            <a class="link-body-emphasis text-decoration-none gray-shade-2-breadcrum">Certificate Generarted</a>
        </li>
    </ol>
</nav>
 <h4>Overview</h4>
    <div class="overview">
        <!-- <div class="overview-card active-card">
            <p class="dasbhoard-header">Total Orders</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">4</p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div> -->
        <div class="overview-card completed-card">
            <p>Certificate Generated</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">
                    <?php 
                        if(isset($completed_count)){
                            echo $completed_count;
                        }else {
                            echo "0";
                        }
                    ?>
                </p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div>
        <!-- <div class="overview-card pending-card">
            <p>Pending</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">2</p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div> -->
        <div class="overview-card processed-card">
            <p>Pending</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">
                    <?php 
                        if(isset($pending_count)){
                            echo $pending_count;
                        }else {
                            echo "0";
                        }
                    ?>
                </p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
<h4>Certificate Generarted</h4>
    <div class="table-container">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th>Req Reference Number</th>
                    <!-- <th>Lot Number</th> -->
                    <th>Total Asset</th>
                    <th>Total Weight</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $pdata){ 
                    $status_class = statusClass($pdata["green_certificate"]);
                ?>
                <tr>
                    <td><?php echo $pdata["req_reference_no"]??"";?></td>
                    <!-- <td><?php echo $pdata["lot_no"]??"";?></td> -->
                    <td><?php echo $pdata["total_assets"]??"";?></td>
                    <td><?php echo $pdata["total_weight"]??"";?></td>
                    <td><span class="<?php echo $status_class;?>">
                        <?php 
                        if(empty($pdata["green_certificate"])){
                            echo "Pending";
                        }else{
                            if(isset($pdata["link"]) && !empty($pdata["link"])){ ?>
                                <a href="<?php echo $pdata["link"];?>" target="_blank" class="text-reset text-decoration-none">Certificate Generated</a>
                            <?php }
                        }
                        ?>
                        </span>
                    </td>
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

