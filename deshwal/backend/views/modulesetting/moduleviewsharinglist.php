<?php
use backend\assets\AdminAsset;
use backend\components\SvgRenderHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\data\Pagination $pagination */
/** @var int $pageSize */
/** @var int $totalCount */
/** @var string $q */

$this->title   = 'Module View Sharing';
$baseUrl       = Url::base();
$csrfToken     = Yii::$app->request->csrfToken;
$csrfParam     = Yii::$app->request->csrfParam;
$logo = 'modulesetting';
$this->registerCssFile('@web/thememain/css/flatpickr.min.css');
$this->registerCssFile('@web/thememain/css/select2.min.css',   ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/multilist-dd.css',  ['depends' => [AdminAsset::class]]);
?>

<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add" style="
    padding: 12px;
    margin-top: 10px;
">
         <span class="icons-coll " >
            <?= SvgRenderHelper::renderIcon($logo.'.svg',true); ?>
        </span>
        <span class="sm-modname"><strong><?= $this->title; ?></strong></span>
        <br>
      </div>

      
    </div>
  </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                    <input type="text"
                           id="moduleViewSearch"
                           class="form-control"
                           placeholder="Search by title, module, permissions, description..."
                           value="<?= Html::encode($q) ?>">
                </div>
            </div>

            

            <div class="col-6 text-md-end">
                <?= Html::a(
                    'Add <i class="fa fa-plus"></i>',
                    ['modulesetting/detailedit-create'],
                    [
                        'class'     => 'btn btn-sm btn-outline-primary',
                        'title'     => 'Add Module View Sharing',
                        'data-pjax' => '0',
                    ]
                ) ?>
            </div>
        </div>

        <div class="table-responsive">
            <table id="moduleViewTable" class="table table-hover align-middle table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Title</th>
                        <th>Module Name</th>
                        <th style="width: 90px;">Edit</th>
                        <th style="width: 90px;">View</th>
                        <th style="width: 90px;">Admin</th>
                        <th style="width: 120px;">Superadmin</th>
                        <th>Description</th>
                        <th style="width: 110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($rows)): ?>
                    <?php
                    $start = $pagination->offset + 1;
                    foreach ($rows as $index => $row):
                        $serial = $start + $index;
                    ?>
                        <tr>
                            <td><?= $serial ?></td>
                            <td>
                                <div class="text-truncate-title" style="max-height: 40px;overflow-y: auto;white-space: normal;">
                                    <?= Html::encode($row['title']) ?>
                                </div>
                            </td>

                            <td><?= Html::encode($row['module_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= $row['edit_allow'] ? 'success' : 'secondary' ?>">
                                    <?= $row['edit_allow'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $row['view_allow'] ? 'success' : 'secondary' ?>">
                                    <?= $row['view_allow'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $row['admin_allow'] ? 'success' : 'secondary' ?>">
                                    <?= $row['admin_allow'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $row['superadmin_allow'] ? 'success' : 'secondary' ?>">
                                    <?= $row['superadmin_allow'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate-desc" style="max-height: 40px;overflow-y: auto;white-space: normal;">
                                    <?= Html::encode($row['description']) ?>
                                </div>
                            </td>
                            <td class="d-flex">
                                <?= Html::a(
                                    '<i class="fa fa-eye"></i>',
                                    ['modulesetting/detailedit-update', 'id' => $row['des_id'], 'mode' => 'view'],
                                    [
                                        'class'     => 'btn btn-sm btn-outline-secondary me-1',
                                        'title'     => 'View',
                                        'data-pjax' => '0',
                                    ]
                                ) ?>
                                <?= Html::a(
                                    '<i class="fa fa-edit"></i>',
                                    ['modulesetting/detailedit-update', 'id' => $row['des_id'], 'mode' => 'edit'],
                                    [
                                        'class'     => 'btn btn-sm btn-outline-primary',
                                        'title'     => 'Edit',
                                        'data-pjax' => '0',
                                    ]
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">No records found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
            <div class="mb-2 mb-md-0">
                <?php
                $from = $totalCount ? $pagination->offset + 1 : 0;
                $to   = $pagination->offset + count($rows);
                ?>
                <small class="text-muted">
                    Showing <?= $from ?>–<?= $to ?> of <?= $totalCount ?> records
                </small>
            </div>
            <div class="col-md-4 mb-2 mb-md-0 text-md-center">
                <div class="d-flex align-items-center justify-content-md-center">
                    <label for="pageSize" class="me-2 mb-0">Rows per page:</label>
                    <select id="pageSize" class="form-select form-select-sm" style="max-width: 120px;">
                        <?php foreach ([10, 25, 50, 100] as $size): ?>
                            <option value="<?= $size ?>" <?= $pageSize == $size ? 'selected' : '' ?>>
                                <?= $size ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <?php
                    $page      = $pagination->page;      // zero-based
                    $pageCount = $pagination->pageCount;
                    $perPage   = $pagination->getPageSize();

                    $createUrl = function($p) use ($pagination, $perPage, $q) {
                        $url = $pagination->createUrl($p, $perPage);
                        $sep = strpos($url, '?') === false ? '?' : '&';
                        if ($q !== '') {
                            $url .= $sep . 'q=' . urlencode($q);
                            $sep  = '&';
                        }
                        if (strpos($url, 'per-page=') === false) {
                            $url .= $sep . 'per-page=' . $perPage;
                        }
                        return $url;
                    };
                    ?>

                    <li class="page-item <?= $page <= 0 ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= $page <= 0 ? 'javascript:void(0);' : $createUrl(0) ?>"
                           aria-label="First">
                            &laquo;&laquo;
                        </a>
                    </li>

                    <li class="page-item <?= $page <= 0 ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= $page <= 0 ? 'javascript:void(0);' : $createUrl($page - 1) ?>"
                           aria-label="Previous">
                            &laquo;
                        </a>
                    </li>

                    <?php
                    $window = 2;
                    $startP = max(0, $page - $window);
                    $endP   = min($pageCount - 1, $page + $window);

                    for ($p = $startP; $p <= $endP; $p++):
                        $isActive = $p == $page;
                    ?>
                        <li class="page-item <?= $isActive ? 'active' : '' ?>">
                            <?php if ($isActive): ?>
                                <span class="page-link"><?= $p + 1 ?></span>
                            <?php else: ?>
                                <a class="page-link" href="<?= $createUrl($p) ?>"><?= $p + 1 ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page >= $pageCount - 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= $page >= $pageCount - 1 ? 'javascript:void(0);' : $createUrl($page + 1) ?>"
                           aria-label="Next">
                            &raquo;
                        </a>
                    </li>

                    <li class="page-item <?= $page >= $pageCount - 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= $page >= $pageCount - 1 ? 'javascript:void(0);' : $createUrl($pageCount - 1) ?>"
                           aria-label="Last">
                            &raquo;&raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

    </div>
</div>

<div class="modal fade custom-modal" id="detailedit-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h4 class="modal-title" id="detailedit-modal-title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body custom-modal-body" id="detailedit-modal-body"></div>
            <div class="modal-footer custom-modal-footer"></div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(document).on('input', '#moduleViewSearch', function() {
    var q = $.trim($(this).val());
    var url = new URL(window.location.href);
    if (q) {
        url.searchParams.set('q', q);
    } else {
        url.searchParams.delete('q');
    }
    clearTimeout(window._moduleViewSearchTimer);
    window._moduleViewSearchTimer = setTimeout(function() {
        window.location.href = url.toString();
    }, 400);
});

$(document).on('change', '#pageSize', function() {
    var perPage = $(this).val();
    var url = new URL(window.location.href);
    url.searchParams.set('per-page', perPage);
    url.searchParams.set('page', 0);
    window.location.href = url.toString();
});

$(document).off('click', '.pagination a.page-link');

$(document).on('click', '.pagination a.page-link', function (e) {
    var href = $(this).attr('href');
    if (!href || href === 'javascript:void(0);') {
        e.preventDefault();
        return false;
    }
    window.location.href = href;
});

JS;

$this->registerJs($js);
?>
