<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Pickup Request';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

?>
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
            <a class="link-body-emphasis text-decoration-none gray-shade-2-breadcrum">Pickup Request</a>
        </li>
    </ol>
</nav>
<h4 class="mt-3">Overview</h4>
<div class="overview">
    <div class="overview-card active-card">
        <p class="dasbhoard-header">Total Draft</p>
        <div class="d-flex align-items-baseline">
            <p class="dashboard-figure"><?= $total_drafts ?? 0; ?></p>
            <div class="custom-green-text">
                <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
            </div>
        </div>
    </div>
    <div class="overview-card completed-card">
        <p>Pickup Requested</p>
        <div class="d-flex align-items-baseline">
            <p class="dashboard-figure"><?= $pickup_requested ?? 0; ?></p>
            <div class="custom-green-text">
                <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
            </div>
        </div>
    </div>
    <div class="overview-card pending-card">
        <p>Pickup Created</p>
        <div class="d-flex align-items-baseline">
            <p class="dashboard-figure"><?= $totalPickcreatedCount ?? 0; ?></p>
            <div class="custom-green-text">
                <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
            </div>
        </div>
    </div>
    <div class="overview-card processed-card">
        <p>Total Assets Processed</p>
        <div class="d-flex align-items-baseline">
            <p class="dashboard-figure"><?= $total_assets_processed ?? 0; ?></p>
            <div class="custom-green-text">
                <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
            </div>
        </div>
    </div>
</div>
<h4>Pickup Request</h4>
<div class="d-flex justify-content-end mb-1">
    <?php echo Html::a('Add Pickup Request', ['pickuprequest/create'], ['class' => "btn btn-primary btn-sm"]); ?>
</div>
<div class="top-scroll">
    <div></div>
</div>
<div class="table-container mt-1">
    <div class="table-responsive">
    <table class="table table-hover custom-table-border"  >
        <thead>
            <tr>
                <th>Pickup Request ID </th>
                <th>Status </th>
                <th>Sourcing Deal No</th>
                <th>Pickup No</th>
                <th>Location </th>
                <!-- hide 2.	Please HIDE the below fields in Pickup Requested Form:-
a.	SPOC Name
b.	SPOC number
c.	SPOC Email
d.	Escalation Name
e.	Escalation Number
as per 24 june 2025 client mail -->
                <!-- <th>SPOC Name</th>
                        <th>SPOC Number</th>
                        <th>SPOC Email</th>
                        <th>Escalation Name</th>
                        <th>Escalation Number</th> -->
                <!-- <th>Preferred Pickup Date</th> -->
                <!-- <th>Sourcing Deal Stage</th> -->
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pickupRequestData as $prd) {
                if ($prd['status_value'] != '1')
                    $prd['status_value'] = $prd["overall_status"];
                ?>
                <tr>
                    <td>
                        <?php
                        echo !empty($prd["pickup_request"])
                            ? Html::a(
                                $prd["pickup_request"],
                                Url::to(['pickuprequest/view', 'pickup_request_id' => $prd["pickup_request_id"]]),
                                ['class' => 'btn btn-info btn-sm']
                            )
                            : "";
                        ?>
                    </td>
                    <td><?php echo $prd["status_value"] ?? ""; ?></td>
                    <td><?php echo $prd["sourcingdeal_no"] ?? ""; ?></td>
                    <td><?php echo $prd["pickup_no"] ?? ""; ?></td>
                    <td><?php echo $prd['pickup_location'];//$prd["location"] ?? ""; ?></td>
                    <!-- hide 2.	Please HIDE the below fields in Pickup Requested Form:-
a.	SPOC Name
b.	SPOC number
c.	SPOC Email
d.	Escalation Name
e.	Escalation Number
as per 24 june 2025 client mail -->
                    <!-- <td><?php // echo $prd["spoc_name"]??""; ?></td>
                            <td><?php //echo $prd["spoc_number"]??""; ?></td>
                            <td><?php //echo $prd["spoc_email"]??""; ?></td>
                            <td><?php //echo $prd["escalation_name"]??""; ?></td>
                            <td><?php //echo $prd["escalation_number"]??""; ?></td> -->
                    <!-- <td><?php //echo $prd["preferred_pickup_date"] ?? ""; ?></td> -->
                    <!-- <td><?php //echo $prd["sourcingdeal_stage"]??""; ?></td> -->
                    <!-- <td>
                        < !empty($prd["pickup_request_id"])
                            ? Html::a(
                                'View Details',
                                ['pickuprequest/view', 'pickup_request_id' => $prd["pickup_request_id"]],
                                ['class' => 'btn btn-info btn-sm']
                            )
                            : '' ?>

                    </td> -->
                    <td>
                        <?= !empty($prd["pickup_id"])
                            ? Html::a(
                                'View Details',
                                ['pickuprequest/viewpickup', 'pickup_id' => $prd["pickup_id"]],
                                ['class' => 'btn btn-info btn-sm']
                            )
                            : '' ?>

                    </td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
     </div> 
</div>

<!-- Custom Pagination Links -->
<!-- start of pagination  -->
<?php
if (isset($pagination)) {
    $page = $pagination["page"] ?? 1;
    $total_records = $pagination["totalCount"];
    $size = $pagination["defaultPageSize"] ?? 1;
    $previous_page = $page - 1;
    $next_page = $page + 1;
    $adjacents = "2";

    if (!$total_records)
        $total_records = 0;
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
<?php } ?>
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