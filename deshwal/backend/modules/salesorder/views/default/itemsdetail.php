<?php


use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Sales Order Items Detail';
$serial   = $filters['serial']   ?? '';
$product  = $filters['product']  ?? '';
$dateFrom = $filters['date_from'] ?? '';
$dateTo   = $filters['date_to']   ?? '';

$totalPages = $pageSize > 0 ? (int)ceil($totalRecords / $pageSize) : 1;
if ($totalPages < 1) {
    $totalPages = 1;
}
?>

<div class="container-fluid mt-3">

<div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Sales Order Items Detail  <?php if (!empty($salesorder_no)): ?>
                (SO No: <?= Html::encode($salesorder_no) ?>)
            <?php endif; ?></h4>

         <a href="<?= Url::to(['default/detail', 'Record' => $salesorder_id]) ?>"
               class="btn btn-sm btn-secondary">
               ← Back to Sales Order
            </a>
    </div>

    <form method="get" class="row g-2 mb-3">
        <input type="hidden" name="salesorder_id" value="<?= (int)$salesorder_id ?>">
       <div class="col-md-3">
                    <label class="form-label">Tag Number</label>
                    <input type="text"
                           name="serial"
                           value="<?= Html::encode($serial) ?>"
                           class="form-control form-control-sm"
                           placeholder="Search by Tag Number">
                </div>
        <!-- <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <input type="text"
                           name="product"
                           value="<?= Html::encode($product) ?>"
                           class="form-control form-control-sm"
                           placeholder="Search by Product Name">
                </div> -->

                <!-- Date range reserved if needed later -->
                <!--
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date"
                           name="date_from"
                           value="<?= Html::encode($dateFrom) ?>"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date"
                           name="date_to"
                           value="<?= Html::encode($dateTo) ?>"
                           class="form-control form-control-sm">
                </div>
                -->

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2">Apply</button>
                </div>
    </form>

   

    <div class="table-responsive">
        <!-- <div class="card-header d-flex justify-content-between align-items-center">
            <span><strong>Total Records: </strong><?= (int)$totalRecords ?></span>
        </div>   -->
            <div class="table-responsive" style="max-height: 70vh; overflow-y:auto; overflow-x:auto;">
                <table class="table table-bordered table-striped table-sm mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tag Number</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>HSN</th>
                        <th>Qty In Stock</th>
                        <th>Qty</th>
                        <th>Purchase Price</th>
                        <th>SP (GST Excl)</th>
                        <th>Selling Price</th>
                        <th>Base Price (Excl)</th>
                        <th>GST %</th>
                        <th>CGST %</th>
                        <th>SGST %</th>
                        <th>IGST %</th>
                        <th>CGST Amt</th>
                        <th>SGST Amt</th>
                        <th>IGST Amt</th>
                        <th>Total Amount</th>
                        <?php if($hasAccess == 1) { ?>
                        <th>Action</th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($rows)) {
                        $startIndex = ($page - 1) * $pageSize;
                        foreach ($rows as $idx => $r) {
                            $index = $startIndex + $idx + 1;

                            $purchasePrice = $r['purchase_price'] ?? null;
                            $spExcl       = $r['selling_price_gst_exclude'] ?? null;
                            $sellingPrice = $r['selling_price'] ?? null;
                            $basePrice    = $r['base_price_gst_exclude'] ?? null;
                            $gstPct       = $r['gst_percentage'] ?? null;
                            $cgstPct      = $r['cgst_percentage'] ?? null;
                            $sgstPct      = $r['sgst_percentage'] ?? null;
                            $igstPct      = $r['igst_percentage'] ?? null;
                            $cgstAmt      = $r['cgst_amount'] ?? null;
                            $sgstAmt      = $r['sgst_amount'] ?? null;
                            $igstAmt      = $r['igst_amount'] ?? null;
                            $totalAmt     = $r['total_amount'] ?? null;
                            ?>
                            <tr>
                                <td><?= $index ?></td>
                                <td><?= Html::encode($r['tag_number']) ?></td>
                                <td><?= Html::encode($r['product_name_text']) ?></td>
                                <td><?= Html::encode($r['category']) ?></td>
                                <td><?= Html::encode($r['sub_category']) ?></td>
                                <td><?= Html::encode($r['hsn_code']) ?></td>
                                <td><?= Html::encode($r['qty_in_stock']) ?></td>
                                <td><?= Html::encode($r['qty']) ?></td>
                                <td><?= $purchasePrice !== null ? number_format($purchasePrice, 2) : '' ?></td>
                                <td><?= $spExcl !== null ? number_format($spExcl, 2) : '' ?></td>
                                <td><?= $sellingPrice !== null ? number_format($sellingPrice, 2) : '' ?></td>
                                <td><?= $basePrice !== null ? number_format($basePrice, 2) : '' ?></td>
                                <td><?= $gstPct !== null ? Html::encode($gstPct) : '' ?></td>
                                <td><?= $cgstPct !== null ? Html::encode($cgstPct) : '' ?></td>
                                <td><?= $sgstPct !== null ? Html::encode($sgstPct) : '' ?></td>
                                <td><?= $igstPct !== null ? Html::encode($igstPct) : '' ?></td>
                                <td><?= $cgstAmt !== null ? number_format($cgstAmt, 2) : '' ?></td>
                                <td><?= $sgstAmt !== null ? number_format($sgstAmt, 2) : '' ?></td>
                                <td><?= $igstAmt !== null ? number_format($igstAmt, 2) : '' ?></td>
                                <td><?= $totalAmt !== null ? number_format($totalAmt, 2) : '' ?></td>
                                <?php if($hasAccess==1) { ?>
                                <td>
                                <button type="button"
                                        class="btn btn-sm btn-primary so-item-edit-btn"
                                        data-id="<?= (int)$r['salesorderitemdetail_id'] ?>">
                                    Edit
                                </button>
                                </td>

                               <?php } ?>
                                <!-- <td>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary so-item-history-btn"
                                            data-item-id="<?= (int)$r['salesorderitemdetail_id'] ?>">
                                        History
                                    </button>
                                </td> -->

                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="20" class="text-center">No records found.</td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        <?php if ($totalRecords > 0): ?>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    Showing
                    <?= (($page - 1) * $pageSize) + 1 ?>
                    to
                    <?= min($page * $pageSize, $totalRecords) ?>
                    of
                    <?= $totalRecords ?>
                    records
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                        $urlParams = [
                            'salesorder/itemslist',
                            'salesorder_id' => $salesorder_id,
                            'serial'        => $serial,
                            'product'       => $product,
                            'date_from'     => $dateFrom,
                            'date_to'       => $dateTo,
                        ];

                        $prevDisabled = $page <= 1 ? ' disabled' : '';
                        $nextDisabled = $page >= $totalPages ? ' disabled' : '';
                        ?>
                        <li class="page-item<?= $prevDisabled ?>">
                            <a class="page-link"
                               href="<?= $page > 1 ? Url::to(array_merge($urlParams, ['page' => 1])) : '#' ?>">
                                First
                            </a>
                        </li>
                        <li class="page-item<?= $prevDisabled ?>">
                            <a class="page-link"
                               href="<?= $page > 1 ? Url::to(array_merge($urlParams, ['page' => $page - 1])) : '#' ?>">
                                Prev
                            </a>
                        </li>

                        <?php
                        $maxButtons = 5;
                        $startPage = max(1, $page - 2);
                        $endPage   = min($totalPages, $startPage + $maxButtons - 1);

                        for ($p = $startPage; $p <= $endPage; $p++): ?>
                            <li class="page-item<?= $p == $page ? ' active' : '' ?>">
                                <a class="page-link"
                                   href="<?= Url::to(array_merge($urlParams, ['page' => $p])) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item<?= $nextDisabled ?>">
                            <a class="page-link"
                               href="<?= $page < $totalPages ? Url::to(array_merge($urlParams, ['page' => $page + 1])) : '#' ?>">
                                Next
                            </a>
                        </li>
                        <li class="page-item<?= $nextDisabled ?>">
                            <a class="page-link"
                               href="<?= $page < $totalPages ? Url::to(array_merge($urlParams, ['page' => $totalPages])) : '#' ?>">
                                Last
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="modal fade" id="soItemEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sales Order Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="soItemEditForm">
                    <input type="hidden" name="id" id="so_item_id">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Product</label>
                            <input type="text" id="so_item_product" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tag Number</label>
                            <input type="text" id="so_item_tag" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Qty In Stock</label>
                            <input type="text" id="so_item_qty_in_stock" class="form-control form-control-sm" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Qty</label>
                            <input type="number" step="0.01" name="qty" id="so_item_qty"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">SP (GST Excl)</label>
                            <input type="number" step="0.01" name="selling_price_gst_exclude"
                                   id="so_item_sp_excl" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Selling Price</label>
                            <input type="number" step="0.01" name="selling_price"
                                   id="so_item_sp" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Base Price (Excl)</label>
                            <input type="text" id="so_item_base" class="form-control form-control-sm" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" id="so_item_category"
                                   class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sub Category</label>
                            <input type="text" name="sub_category" id="so_item_sub_category"
                                   class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">HSN</label>
                            <input type="text" name="hsn_code" id="so_item_hsn"
                                   class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">GST %</label>
                            <input type="number" step="0.01" name="gst_percentage"
                                   id="so_item_gst" class="form-control form-control-sm" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">CGST %</label>
                            <input type="text" id="so_item_cgst_per" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">SGST %</label>
                            <input type="text" id="so_item_sgst_per" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">IGST %</label>
                            <input type="text" id="so_item_igst_per" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">CGST Amt</label>
                            <input type="text" id="so_item_cgst_amt" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">SGST Amt</label>
                            <input type="text" id="so_item_sgst_amt" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">IGST Amt</label>
                            <input type="text" id="so_item_igst_amt" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Amount</label>
                            <input type="text" id="so_item_total" class="form-control form-control-sm" readonly>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="so_item_save_btn">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Item History Modal -->
<div class="modal fade" id="soItemHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="soItemHistoryContent">
                    <p>Loading history...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

 <?php
    $js = <<<JS

    function soNum(v) {
        var n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    // Decide split using bill/ship state codes already on page
    function soCalculateGstSplit(gstPer) {
        var bill = $('#bill_wh_statecode').val();
        var ship = $('#ship_statecode').val();

        gstPer = soNum(gstPer);

        var cgstPer = 0, sgstPer = 0, igstPer = 0;

        if (bill && ship) {
            if (String(bill) === String(ship)) {
                // Same state: IGST only
                igstPer = gstPer;
                cgstPer = 0;
                sgstPer = 0;
            } else {
                // Different: CGST + SGST
                cgstPer = gstPer / 2;
                sgstPer = gstPer / 2;
                igstPer = 0;
            }
        }
        return {cgstPer: cgstPer, sgstPer: sgstPer, igstPer: igstPer};
    }

    function soRecalcPopup() {
        var qty    = soNum($('#so_item_qty').val());
        var spExcl = soNum($('#so_item_sp_excl').val());
        var gstPer = soNum($('#so_item_gst').val());
        var sp     = soNum($('#so_item_sp').val());

        if (sp < spExcl) {
            alert('Selling Price cannot be less than SP (GST Excl).');
            sp = spExcl;
            $('#so_item_sp').val(sp.toFixed(2));
        }

        var base = spExcl * qty;
        var split = soCalculateGstSplit(gstPer);

        var cgstAmt = base * split.cgstPer / 100;
        var sgstAmt = base * split.sgstPer / 100;
        var igstAmt = base * split.igstPer / 100;
        var total   = base + cgstAmt + sgstAmt + igstAmt;

        $('#so_item_base').val(base.toFixed(2));
        $('#so_item_cgst_per').val(split.cgstPer.toFixed(2));
        $('#so_item_sgst_per').val(split.sgstPer.toFixed(2));
        $('#so_item_igst_per').val(split.igstPer.toFixed(2));
        $('#so_item_cgst_amt').val(cgstAmt.toFixed(2));
        $('#so_item_sgst_amt').val(sgstAmt.toFixed(2));
        $('#so_item_igst_amt').val(igstAmt.toFixed(2));
        $('#so_item_total').val(total.toFixed(2));
    }
    function soClampQtyToStock() {
        var maxStock = soNum($('#so_item_qty_in_stock').val());
        var qty      = soNum($('#so_item_qty').val());
        if (qty > maxStock) {
            alert('Qty cannot be more than Qty In Stock. Max allowed is ' + maxStock + '.');
            qty = maxStock;
            $('#so_item_qty').val(qty.toFixed(2));
        }
    }
    $(document).on('change keyup blur', '#so_item_qty', function () {
        soClampQtyToStock();
        soRecalcPopup();
    });
    $(document).on('change keyup blur',
        '#so_item_sp_excl, #so_item_sp, #so_item_gst',
        function () {
            soRecalcPopup();
        }
    );
    $(document).ready(function(){

        $(document).on('click', '.so-item-edit-btn', function(){
            var id = $(this).data('id');
            if (!id) return;

            $.ajax({
                type: 'GET',
                url: 'soitemdetail',
                data: {id: id},
                dataType: 'json',
                success: function(res){
                    if (!res || !res.success) {
                        alert(res && res.message ? res.message : 'Unable to load item.');
                        return;
                    }
                    var d = res.data;

                    $('#so_item_id').val(d.salesorderitemdetail_id);
                    $('#so_item_product').val(d.product_name_text || '');
                    $('#so_item_tag').val(d.tag_number || '');
                    $('#so_item_qty_in_stock').val(d.qty_in_stock || '');

                    $('#so_item_qty').val(d.qty || '');
                    $('#so_item_sp_excl').val(d.selling_price_gst_exclude || '');
                    $('#so_item_sp').val(d.selling_price || '');
                    $('#so_item_base').val(d.base_price_gst_exclude || '');

                    $('#so_item_category').val(d.category || '');
                    $('#so_item_sub_category').val(d.sub_category || '');
                    $('#so_item_hsn').val(d.hsn_code || '');
                    $('#so_item_gst').val(d.gst_percentage || '');

                    soRecalcPopup();

                    $('#soItemEditModal').modal('show');
                },
                error: function(){
                    alert('Server error while loading item.');
                }
            });
        });

        $(document).on('change keyup blur',
            '#so_item_qty, #so_item_sp_excl, #so_item_gst',
            function () {
                soRecalcPopup();
            }
        );

        $(document).on('click', '#so_item_save_btn', function(){
            soClampQtyToStock();
            soRecalcPopup();

            var maxStock = soNum($('#so_item_qty_in_stock').val());
            var qty      = soNum($('#so_item_qty').val());
            if (qty <= 0 || qty > maxStock) {
                alert('Qty must be greater than 0 and not more than Qty In Stock.');
                return;
            }

            var spExcl = soNum($('#so_item_sp_excl').val());
            var sp     = soNum($('#so_item_sp').val());
            if (sp < spExcl) {
                alert('Selling Price cannot be less than SP (GST Excl).');
                return;
            }
            var id = $('#so_item_id').val();
            if (!id) return;

            var payload = {
                id: id,
                qty: $('#so_item_qty').val(),
                selling_price_gst_exclude: $('#so_item_sp_excl').val(),
                selling_price: $('#so_item_sp').val(),
                gst_percentage: $('#so_item_gst').val(),
                category: $('#so_item_category').val(),
                sub_category: $('#so_item_sub_category').val(),
                hsn_code: $('#so_item_hsn').val(),
                _csrf: $('#csrfToken').val() || $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                type: 'POST',
                url: 'soitemupdate',
                data: payload,
                dataType: 'json',
                success: function(res){
                    if (res && res.success) {
                        alert(res.message || 'Item updated successfully.');
                        $('#soItemEditModal').modal('hide');
                        // easiest: reload detail page to reflect changes
                        window.location.reload();
                    } else {
                        alert(res && res.message ? res.message : 'Error while updating item.');
                    }
                },
                error: function(){
                    alert('Server error while updating item.');
                }
            });
        });

    });

    // ====== SALES ORDER ITEM HISTORY (DETAIL PAGE) ======
$(document).on('click', '.so-item-history-btn', function () {
    var itemId = $(this).data('item-id');
    if (!itemId) return;

    $('#soItemHistoryContent').html('<p>Loading history...</p>');

    $.ajax({
        type: 'GET',
        url: 'soitemhistory',
        dataType: 'json',
        data: { id: itemId },
        success: function (res) {
            if (!res || res.success === false) {
                $('#soItemHistoryContent').html(
                    '<div class="alert alert-danger">' +
                    (res && res.message ? res.message : 'Could not load history.') +
                    '</div>'
                );
            } else {
                renderSoItemHistory(res.history || []);
            }
        },
        error: function () {
            $('#soItemHistoryContent').html(
                '<div class="alert alert-danger">Server error while loading history.</div>'
            );
        }
    });

    $('#soItemHistoryModal').modal('show');
});

function renderSoItemHistory(history) {
    if (!history || !history.length) {
        $('#soItemHistoryContent').html(
            '<div class="alert alert-info">No history available for this item.</div>'
        );
        return;
    }

    var html = '';

    history.forEach(function (h, idx) {
        var title = 'Change #' + (idx + 1) +
            ' • ' + (h.changed_at || '') +
            (h.action_type ? ' • ' + h.action_type : '');

        html += '<div class="card mb-3">';
        html += '  <div class="card-header d-flex justify-content-between align-items-center">';
        html += '    <div>' + title + '</div>';
        if (h.remarks) {
            html += '    <div><small>' + escapeHtml(h.remarks) + '</small></div>';
        }
        html += '  </div>';
        html += '  <div class="card-body p-2">';

        var oldRow = (h.old_items && h.old_items.length) ? h.old_items[0] : {};
        var newRow = (h.new_items && h.new_items.length) ? h.new_items[0] : {};

        var fields = [
            { key: 'tag_number', label: 'Tag Number' },
            { key: 'product_name', label: 'Product Id' },
            { key: 'category', label: 'Category' },
            { key: 'sub_category', label: 'Sub Category' },
            { key: 'hsn_code', label: 'HSN' },
            { key: 'qty_in_stock', label: 'Qty In Stock' },
            { key: 'qty', label: 'Qty' },
            { key: 'purchase_price', label: 'Purchase Price' },
            { key: 'selling_price_gst_exclude', label: 'SP (GST Excl)' },
            { key: 'selling_price', label: 'Selling Price' },
            { key: 'base_price_gst_exclude', label: 'Base Price (Excl)' },
            { key: 'gst_percentage', label: 'GST %' },
            { key: 'cgst_percentage', label: 'CGST %' },
            { key: 'sgst_percentage', label: 'SGST %' },
            { key: 'igst_percentage', label: 'IGST %' },
            { key: 'cgst_amount', label: 'CGST Amt' },
            { key: 'sgst_amount', label: 'SGST Amt' },
            { key: 'igst_amount', label: 'IGST Amt' },
            { key: 'total_amount', label: 'Total Amount' }
        ];

        html += '<div class="table-responsive">';
        html += '<table class="table table-sm table-bordered mb-0">';
        html += '<thead><tr>' +
                '<th>Field</th>' +
                '<th>Old Value</th>' +
                '<th>New Value</th>' +
                '</tr></thead><tbody>';

        fields.forEach(function (f) {
            var ov = valueOrEmpty(oldRow[f.key]);
            var nv = valueOrEmpty(newRow[f.key]);
            var changed = (ov !== nv);

            html += '<tr' + (changed ? ' style="background:#fff7e6;"' : '') + '>';
            html += '  <td>' + escapeHtml(f.label) + '</td>';
            html += '  <td>' + escapeHtml(ov) + '</td>';
            html += '  <td>' + escapeHtml(nv) + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>'; // table + responsive
        html += '  </div>';               // card-body
        html += '</div>';                  // card
    });

    $('#soItemHistoryContent').html(html);
}

function valueOrEmpty(v) {
    if (v === null || v === undefined) return '';
    return String(v);
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

JS;
    $this->registerJsFile(
        'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js',
        ['depends' => [\yii\web\JqueryAsset::class]]
    );
    $this->registerJs($js, \yii\web\View::POS_END);

    ?>
