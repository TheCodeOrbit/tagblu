<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Search Results';
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
            <a class="link-body-emphasis text-decoration-none gray-shade-2-breadcrum"><?php echo $this->title; ?></a>
        </li>
    </ol>
</nav>
<?php if (!empty($pickupRequestData)) { ?>
<h4>Pickup Request</h4>
    <?php if($error){ ?>
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    <?php } ?>
    <div class="table-container mt-1">
        <div class="table-responsive">
            <table class="table table-hover custom-table-border">
                <thead>
                    <tr>
                        <th>Pickup Request ID </th>
                        <th>Status </th>
                        <th>Location </th>
                        <th>SPOC Name</th>
                        <th>SPOC Number</th>
                        <th>SPOC Email</th>
                        <th>Escalation Name</th>
                        <th>Escalation Number</th>
                        <th>Preferred Pickup Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(empty($pickupRequestData)){ ?>
                        <tr>
                            <td colspan="9" class="text-center"><p class="alert alert-danger" role="alert">No Data Found</p></td>
                        </tr>
                    <?php }else{
                        foreach($pickupRequestData as $prd){ ?>
                            <tr>
                                <td>
                                    <?php echo Html::a($prd["pickup_request"]??"View Details", Url::to(['pickuprequest/view', 'pickup_request_id' => $prd["pickup_request_id"]]), ['class' => 'btn btn-info btn-sm']); ?>
                                </td>
                                <td><?php echo $prd["status_value"]??"";?></td>
                                <td><?php echo $prd["location"]??"";?></td>
                                <td><?php echo $prd["spoc_name"]??"";?></td>
                                <td><?php echo $prd["spoc_number"]??"";?></td>
                                <td><?php echo $prd["spoc_email"]??"";?></td>
                                <td><?php echo $prd["escalation_name"]??"";?></td>
                                <td><?php echo $prd["escalation_number"]??"";?></td>
                                <td><?php echo $prd["preferred_pickup_date"]??"";?></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- Custom Pagination Links -->
<!-- start of pagination  -->
<?php
if(isset($pagination)){
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
<?php } ?>
<?php } ?>

<?php if (!empty($dataWipingResults)) { ?>
<h4>Data Wiping Details</h4>
<div class="table-container mt-2">
    <div class="table-responsive">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th>Laptop Serial No</th>
                    <th>Reference No</th>
                    <th>Lot Number</th>
                    <th>Lot Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($dataWipingResults)) {
                    foreach ($dataWipingResults as $wip) {
                        echo "<tr>";
                        echo "<td>" . Html::encode($wip['laptop_serial_no']??'') . "</td>";
                        echo "<td>" . Html::a(Html::encode($wip['req_reference_no']),
                            ['/site/datawiping', 'id' => $wip['req_reference_no']],
                            ['target' => '_blank', 'class' => 'btn btn-info btn-sm']
                        ) . "</td>";
                        echo "<td>" . Html::encode($wip['lot_no']??'') . "</td>";
                        echo "<td>" . Html::encode($wip['status_name']??'') . "</td>";
                        echo "</tr>";
                    }
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            <p class="alert alert-danger" role="alert">No Data Found</p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Data Wiping Pagination -->
<?php if(isset($paginationDataWiping) && $paginationDataWiping['pageCount'] > 1): ?>
    <div class="d-flex justify-content-end align-items-center mb-2">
        <span>
            <button type="button" class="btn btn-info btn-sm" style="cursor:default">
                Total Number of Records
                <span class="badge text-bg-secondary"><?= $paginationDataWiping['totalCount']; ?></span>
            </button>
        </span>
        <nav class="ms-2">
            <ul class="pagination pagination-sm mb-0">
                <?php
                $curPageDW = $paginationDataWiping['currentPage'];
                $pageCountDW = $paginationDataWiping['pageCount'];
                $queryParams = Yii::$app->request->getQueryParams();
                unset($queryParams['dataWipingPage']);
                $baseUrl = Yii::$app->request->url;
                $baseUrl = strtok($baseUrl, '?');

                $prevPage = max(1, $curPageDW - 1);
                $prevDisabled = ($curPageDW <= 1) ? "disabled" : "";
                $queryParams['dataWipingPage'] = $prevPage;
                echo '<li class="page-item '.$prevDisabled.'">';
                echo '<a class="page-link" href="'.$baseUrl.'?'.http_build_query($queryParams).'">Previous</a></li>';

                echo '<li class="page-item active"><span class="page-link">'.$curPageDW.'</span></li>';

                $nextPage = min($pageCountDW, $curPageDW + 1);
                $nextDisabled = ($curPageDW >= $pageCountDW) ? "disabled" : "";
                $queryParams['dataWipingPage'] = $nextPage;
                echo '<li class="page-item '.$nextDisabled.'">';
                echo '<a class="page-link" href="'.$baseUrl.'?'.http_build_query($queryParams).'">Next</a></li>';
                ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>
<?php } ?>

<?php if (empty($pickupRequestData) && empty($dataWipingResults)) : ?>
    <div class="alert alert-danger mt-3" role="alert">
        No Result Found
    </div>
<?php endif; ?>

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

