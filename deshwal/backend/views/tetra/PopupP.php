<?php

$totalCount = isset($totalCount) ? (int)$totalCount : count($allProducts);
$page       = isset($page) ? (int)$page : 1;
$perPage    = isset($perPage) ? (int)$perPage : 20;

$modalData = [
    'allProducts'   => $allProducts,
    'categories'    => $categories,
    'subcategories' => $subcategories,
]; 
?>
<textarea id="modalProductData" type="hidden" style="display:none;">
<?= htmlspecialchars(json_encode($modalData), ENT_QUOTES, 'UTF-8') ?>
</textarea>

<div class="modal fade" id="productSearchModalSO" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Product Search</h5>
                <button type="button" class="close btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form id="product-search-form" onsubmit="return false;">
                    <div class="row mb-2">
                        <div class="col">
                            <input type="text" id="search-lot-no" placeholder="Lot Number" class="form-control">
                        </div>
                        <div class="col">
                            <input type="text" id="search-product-name" placeholder="Product Name" class="form-control">
                        </div>
                        <div class="col">
                            <select id="search-category" class="form-control">
                                <option value="">Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['prod_category_id'] ?>">
                                        <?= htmlspecialchars($cat['prod_category_value'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <select id="search-sub-category" class="form-control">
                                <option value="">Sub-Category</option>
                                <?php foreach ($subcategories as $sub): ?>
                                    <option value="<?= (int)$sub['sub_catagory_id'] ?>" data-cat="<?= (int)$sub['prod_catagory_id'] ?>">
                                        <?= htmlspecialchars($sub['sub_catagory_value'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" id="search-tag-no" placeholder="Tag Number" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex mb-2">
                        <button type="button" class="btn btn-sm btn-warning" id="add-by-tag-btn">
                            Add Single by Tag Number
                        </button>
                        <button type="button" class="btn btn-sm btn-primary ml-2" id="search-btn">
                            Search
                        </button>
                    </div>
                </form>

                

                <div id="searchResults">
                    <?php if (!empty($allProducts)): ?>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="so-check-all"></th>
                                <th>Lot Number</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Tag Number</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($allProducts as $row): ?>
                                <?php $raw = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                <tr>
                                    <td><input type="checkbox" class="product-select" data-raw='<?= $raw ?>'></td>
                                    <td><?= htmlspecialchars($row['lot_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['product_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['prod_category_value'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['sub_catagory_value'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['tag_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= (int)($row['qty'] ?? 1) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div>No results found</div>
                    <?php endif; ?>
                </div>
                <div class="d-flex mb-2 align-items-center">
                    <label for="per-page" class="mb-0 mr-1">Show</label>
                    <select id="per-page" class="form-control d-inline-block" style="width:auto;">
                        <option value="20"  <?= $perPage == 20  ? 'selected' : '' ?>>20</option>
                        <option value="50"  <?= $perPage == 50  ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    <small id="page-info" class="ml-2">
                        <?php
                        if ($totalCount > 0) {
                            $start = ($page - 1) * $perPage + 1;
                            $end   = min($start + count($allProducts) - 1, $totalCount);
                            echo "Showing {$start}-{$end} of {$totalCount} products";
                        } else {
                            echo "No products found";
                        }
                        ?>
                    </small>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <nav aria-label="Product pagination">
                        <ul class="pagination justify-content-end" id="search-pagination">
                            <?php
                            $pageCount = $perPage > 0 ? (int)ceil($totalCount / $perPage) : 1;
                            if ($pageCount > 1):
                                $maxButtons = 5;
                                $startPage  = max(1, $page - (int)floor($maxButtons / 2));
                                $endPage    = min($pageCount, $startPage + $maxButtons - 1);
                                if ($endPage - $startPage < $maxButtons - 1) {
                                    $startPage = max(1, $endPage - $maxButtons + 1);
                                }
                                ?>
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link page-link-popup" href="#" data-page="1">«</a>
                                </li>
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link page-link-popup" href="#" data-page="<?= max(1, $page - 1) ?>">‹</a>
                                </li>
                                <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
                                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                                        <a class="page-link page-link-popup" href="#" data-page="<?= $p ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $pageCount ? 'disabled' : '' ?>">
                                    <a class="page-link page-link-popup" href="#" data-page="<?= min($pageCount, $page + 1) ?>">›</a>
                                </li>
                                <li class="page-item <?= $page >= $pageCount ? 'disabled' : '' ?>">
                                    <a class="page-link page-link-popup" href="#" data-page="<?= $pageCount ?>">»</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="select-all-btnSO" class="btn btn-sm btn-info">Select All Products</button>
                <button type="button" id="append-selected-btnSO" class="btn btn-success">Done</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
