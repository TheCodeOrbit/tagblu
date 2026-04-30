<?php

/** @var yii\web\View $this */
$static_logic = false;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Data Wiping';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

function statusClass($status){
    if(empty($status)) return "status";
    if($status == 'Completed' || $status == 5) return "status done";
    if($status == 'Pending' || $status == 4) return "status in-progress";
    return "status text-danger bg-danger-subtle";
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
            <a class="link-body-emphasis text-decoration-none gray-shade-2-breadcrum">Data Wiping</a>
        </li>
    </ol>
</nav>
 <h4>Overview</h4>
    <div class="overview">
        <!-- <div class="overview-card active-card">
            <p class="dasbhoard-header">Total Orders</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">3</p>
                <div class="custom-green-text">
                    <span class="ms-2">+ 0.00%</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>
        </div> -->
        <div class="overview-card completed-card">
            <p>Completed</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">
                    <?php 
                        if($static_logic){
                            echo "1";
                        }else if(isset($completed_count)){
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
        <div class="overview-card pending-card">
            <p>In Progress</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">
                    <?php 
                        if($static_logic){
                            echo "1";
                        }else if(isset($in_process_count)){
                            echo $in_process_count;
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
        <div class="overview-card processed-card">
            <p>Pending</p>
            <div class="d-flex align-items-baseline">
                <p class="dashboard-figure">
                    <?php 
                        if($static_logic){
                            echo "1";
                        }else if(isset($pending_count)){
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

    <?php
if (!empty($search_value)):
?>
    <h4>Search Results (Laptop Serial Number: <?= htmlspecialchars($search_value) ?>)</h4>
    <div class="table-container">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th>Laptop Serial No</th>
                    <th>Ref Number</th>
                    <th>Lot Number</th>
                    <th>Lot Status</th>
                    <!-- <th>Total Asset Received</th>
                    <th>Wiping Done</th>
                    <th>Wiping Pending</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dsData as $row): ?>
                <tr>
                    <td><?= $row['laptop_serial_no'] ?? '' ?></td>
                    <td><?= $row['req_reference_no'] ?? '' ?></td>
                    <td><?= $row['lot_no'] ?? '' ?></td>
                    <td>
                        <span class="<?= statusClass($row['status_name'] ?? '') ?>">
                            <?= $row['status_name'] ?? '' ?>
                        </span>
                    </td>
                    <!-- <td><?php // $row['total_assets'] ?? '' ?></td> -->
                    <!-- <td class="wiping-done-cell"
                        data-datawiping-id="<?php // $row['datawiping_id'] ?? '' ?>"
                        style="cursor:pointer;background:#f6fbff;color:#196cb2;font-weight:bold;">
                        <?php // $row['wiping_done'] ?? ''?>
                        <span style="margin-left:5px;font-size:14px;color:#196cb2;" class="icon">&#128269;</span>
                    </td> -->
                    <!-- <td><?php //$row['wiping_pending'] ?? '' ?></td> -->
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($dsData)): ?>
        <div class="alert alert-warning">No results found for "<?= htmlspecialchars($search_value) ?>"</div>
    <?php endif; ?>

<?php
// Default table when no search_value
else:
?>
    <h4>Data Wiping</h4>
    <div class="table-container">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th>Req Reference Number</th>
                    <th>Lot Number</th>
                    <th>Total Asset Received</th>
                    <th>Wiping Done</th>
                    <th>Wiping Pending</th>
                    <th>Lot Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dsData as $pdata){
                    $status_class = statusClass($pdata["status"]);
                ?>
                <tr>
                <?php if($static_logic){ ?>
                    <td><?= ($pdata["ref_no"] ?? "") ?></td>
                    <td><?= ($pdata["lot_no"] ?? "") ?></td>
                    <td><?= ($pdata["total_assets"] ?? "") ?></td>
                    <td class="wiping-done-cell"
                        data-datawiping-id="<?= ($pdata["datawiping_id"] ?? "") ?>"
                        style="cursor:pointer;background:#f6fbff;color:#196cb2;font-weight:bold;">
                        <?= ($pdata["wiping_done"] ?? "") ?>
                        <span style="margin-left:5px;font-size:14px;color:#196cb2;" class="icon">&#128269;</span>
                    </td>
                    <td><?= ($pdata["wiping_pending"] ?? "") ?></td>
                    <td <?php if($pdata["status"] == "Completed"){ ?> data-bs-toggle="modal" data-bs-target="#datawipingModal" <?php } ?>>
                        <span class="<?= $status_class ?>"><?= ($pdata["status"] ?? "") ?></span>
                    </td>
                <?php }else{ ?>
                    <td><?= ($pdata["req_reference_no"] ?? "") ?></td>
                    <td><?= ($pdata["lot_no"] ?? "") ?></td>
                    <td><?= ($pdata["hdd_count"] ?? "") ?></td>
                    <td class="wiping-done-cell"
                        data-datawiping-id="<?= ($pdata["datawiping_id"] ?? "") ?>"
                        style="cursor:pointer;background:#f6fbff;color:#196cb2;font-weight:bold;">
                        <?= ($pdata["hdd_completed"] ?? "0") ?>
                        <span style="margin-left:5px;font-size:14px;color:#196cb2;" class="icon">&#128269;</span>
                    </td>
                    <td><?= ($pdata["hdd_pending"] ?? "") ?></td>
                    <td data-module="datawiping"
                        <?php if($pdata["status"] == 5){?>
                            data-request-id="<?= ($pdata["datawiping_id"] ?? "") ?>"
                            data-bs-toggle="modal" data-bs-target="#datawipingModal"
                        <?php } ?>>
                        <span class="<?= $status_class ?>"><?= ($pdata["status_name"] ?? "") ?></span>
                    </td>
                <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

    <div class="modal fade" id="wipingDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asset Wiping Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="wipingDetailsTable">
                <thead>
                    <tr>
                    <th>Laptop Serial No</th>
                    <th>HDD/SSD Serial No</th>
                    <th>Software Name</th>
                    <th>Wiping Date</th>
                    <th>Certificate</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>

<!-- Custom Pagination Links -->
<!-- start of pagination  -->
<?php
$static_logic = false;
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
<div class="modal fade" id="datawipingModal" tabindex="-1" aria-labelledby="datawipingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="datawipingLabel">Data Wiping</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table class="table table-bordered text-center">
                        <thead class="align-middle">
                            <tr class="table-info">
                                <th>#</th>
                                <th>Laptop Serial Number</th>
                                <th>Document</th>
                            </tr>
                        </thead>
                        <tbody id="attachment-list">
                            <!-- <tr><td>1</td><td>DYBYRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "DYBYRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>2</td><td>3NJYRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "3NJYRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>3</td><td>1PCZRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "1PCZRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>4</td><td>FKJMRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "FKJMRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>5</td><td>DPB0SQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "DPB0SQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>6</td><td>9KJMRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "9KJMRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>7</td><td>99H2SQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "99H2SQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>8</td><td>HG8YRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "HG8YRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>9</td><td>3MX7SQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "3MX7SQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>10</td><td>DBQLQQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "DBQLQQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>11</td><td>47BGSQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "47BGSQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>12</td><td>FPJMRQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "FPJMRQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>13</td><td>6CV2SQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "6CV2SQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>14</td><td>746JPQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "746JPQ2.pdf"],['target' => '_blank']);?></td></tr>
                            <tr><td>15</td><td>CR58SQ2</td><td><?php echo Html::a('Download', ['site/download','file_name' => "CR58SQ2.pdf"],['target' => '_blank']);?></td></tr> -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer mt-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Register the external JS file
$this->registerJsFile('@web/js/about/edit.js', ['depends' => [AppAsset::class]]);
// Register your jQuery code to display an alert
$this->registerJs('
    $(document).ready(function() {
        console.log("This is a jQuery alert!");

        $(document).on("click", ".wiping-done-cell", function() {
            var datawiping_id = $(this).data("datawiping-id");
            if (!datawiping_id) return;
            var _csrf = $("#csrfToken").val();

            $.ajax({
                type: "POST",
                url: "get-data-wiping-asset-details",
                data: {
                    datawiping_id: datawiping_id,
                    _csrf: _csrf
                },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success" && response.data.length > 0) {
                        var rows = "";
                        $.each(response.data, function(i, item) {
                            var certHtml = "";
                          if (item.certificate) {
                                certHtml = `<a target="_blank" href="downloadfile?fileid=` + item.certificate + `" class="show-cert-image">Download</a>`;
                            } else {
                                certHtml = "";
                            }
                            rows += `<tr>
                                <td>` + (item.laptop_serial_no || "") + `</td>
                                <td>` + (item.hdd_sdd_serial_no || "") + `</td>
                                <td>` + (item.software_name || "") + `</td>
                                <td>` + (item.wiping_date || "") + `</td>
                                <td>` + certHtml + `</td>
                            </tr>`;
                        });
                        $("#wipingDetailsTable tbody").html(rows);
                    } else {
                        $("#wipingDetailsTable tbody").html("<tr><td colspan=\'5\'>No details found.</td></tr>");
                    }
                    $("#wipingDetailsModal").modal("show");
                }
            });
        });
    });
');

?>

