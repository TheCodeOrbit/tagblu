<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Modules Logs';

$currentType   = $filters['type']   ?? '';
$currentAction = $filters['action'] ?? '';
$currentSearch = $filters['search'] ?? '';

$baseUrl = Url::to(['modulesetting/logs']);
$queryBase = [
    'type'   => $currentType,
    'action' => $currentAction,
    'search' => $currentSearch,
];
?>
<?php
$this->registerCss(<<<CSS
.simple-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}
.simple-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
}
.simple-modal-dialog {
    position: relative;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 0 15px rgba(0,0,0,.4);
}
.simple-modal-header {
    padding: 8px 12px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.simple-modal-body {
    padding: 12px;
}
.simple-modal-close {
    border: 0;
    background: transparent;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
}
CSS);
?>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h1><?= Html::encode($this->title) ?></h1>

                <div class="d-flex gap-2">
                    <select id="log-type-filter" class="form-select" style="min-width: 180px;">
                        <option value="">All Log Types</option>
                        <?php foreach ($logTypes as $key => $label): ?>
                            <option value="<?= Html::encode($key) ?>"
                                <?= $currentType === (string)$key ? 'selected' : '' ?>>
                                <?= Html::encode($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="action-filter" class="form-select" style="min-width: 140px;">
                        <option value="">All Actions</option>
                        <?php foreach ($actions as $key => $label): ?>
                            <option value="<?= Html::encode($key) ?>"
                                <?= $currentAction === (string)$key ? 'selected' : '' ?>>
                                <?= Html::encode($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text"
                           id="search-logs"
                           class="form-control search-input"
                           placeholder="Search logs..."
                           value="<?= Html::encode($currentSearch) ?>">

                    <button id="refresh-logs" class="btn btn-primary">Refresh</button>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:120px; white-space:nowrap;">Type</th>
                            <th style="width:100px; white-space:nowrap;">Action</th>
                            <th>Record ID</th>
                            <th>Tab</th>
                            <th style="width:120px;">User</th>
                            <th style="width:160px;">Date</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $startIndex = ($page - 1) * $pageSize + 1;
                        foreach ($rows as $idx => $r):
                            $index = $startIndex + $idx;

                            switch ($r['log_type']) {
                                case 'parenttab': $typeBadge = 'bg-purple';  break;
                                case 'tab':       $typeBadge = 'bg-primary'; break;
                                case 'block':     $typeBadge = 'bg-info';    break;
                                case 'field':     $typeBadge = 'bg-warning'; break;
                                case 'sequence':  $typeBadge = 'bg-success'; break;
                                default:          $typeBadge = 'bg-secondary';
                            }
                            $typeLabel = $logTypes[$r['log_type']] ?? ucfirst($r['log_type'] ?? '');

                            switch ($r['action']) {
                                case 'add':      $actBadge = 'bg-success'; break;
                                case 'update':   $actBadge = 'bg-warning'; break;
                                case 'delete':   $actBadge = 'bg-danger';  break;
                                case 'sequence': $actBadge = 'bg-info';    break;
                                default:         $actBadge = 'bg-secondary';
                            }
                            $actionLabel = $actions[$r['action']] ?? ucfirst($r['action'] ?? '');

                            $tabLabel = '-';
                            if (!empty($r['tab_id'])) {
                                $tabLabel = (new \yii\db\Query())
                                    ->select('tablabel')
                                    ->from('tab')
                                    ->where(['tabid' => $r['tab_id']])
                                    ->scalar() ?: $r['tab_id'];
                            } elseif (!empty($r['parenttab_id'])) {
                                $tabLabel = 'Parent Tab';
                            }
                        ?>
                            <tr data-id="<?= (int)$r['id'] ?>">
                                <td><?= $index ?></td>
                                <td>
                                    <span class="badge <?= $typeBadge ?>">
                                        <?= Html::encode($typeLabel) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $actBadge ?>">
                                        <?= Html::encode($actionLabel) ?>
                                    </span>
                                </td>
                                <td><?= Html::encode($r['record_id']) ?></td>
                                <td><?= Html::encode($tabLabel) ?></td>
                                <td><?= Html::encode($r['username']) ?></td>
                                <td><?= Yii::$app->formatter->asDatetime($r['created_at']) ?></td>
                                <td>
                                    <?= Html::button('<i class="fa fa-eye"></i>', [
                                        'class' => 'btn btn-sm btn-primary view-log-details',
                                        'title' => 'View',
                                        'data-id' => $r['id'],
                                        'encode' => false,
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No logs found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- assetdetails-like pagination -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing
                        <?= $totalRecords ? (($page - 1) * $pageSize + 1) : 0 ?>
                        –
                        <?= min($page * $pageSize, $totalRecords) ?>
                        of <?= $totalRecords ?>
                    </div>
                    <nav aria-label="Logs pages">
                        <ul class="pagination mb-0">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link"
                                   href="<?= $page > 1
                                       ? Html::encode($baseUrl . '?' . http_build_query(array_merge($queryBase, ['page' => $page - 1])))
                                       : '#' ?>">
                                    Prev
                                </a>
                            </li>
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                                    <a class="page-link"
                                       href="<?= Html::encode($baseUrl . '?' . http_build_query(array_merge($queryBase, ['page' => $p]))) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link"
                                   href="<?= $page < $totalPages
                                       ? Html::encode($baseUrl . '?' . http_build_query(array_merge($queryBase, ['page' => $page + 1])))
                                       : '#' ?>">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="logDetailsModal" class="simple-modal" style="display:none;">
    <div class="simple-modal-backdrop"></div>
    <div class="simple-modal-dialog" style="max-width: 900px; width: 95%;">
        <div class="simple-modal-header">
            <span id="modalTitle">Log Details</span>
            <button type="button" class="simple-modal-close" id="closeDetailsModal">&times;</button>
        </div>
        <div class="simple-modal-body" id="logDetailsContent"
             style="max-height: 70vh; overflow-y: auto;">
            Loading...
        </div>
    </div>
</div>

<?php
$detailsUrl = Url::to(['modulesetting/log-details']);
$js = <<<JS
function reloadLogs() {
    var type   = $('#log-type-filter').val();
    var action = $('#action-filter').val();
    var search = $('#search-logs').val();

    var params = $.param({type: type, action: action, search: search});
    window.location.href = window.location.pathname + (params ? '?' + params : '');
}

$('#log-type-filter, #action-filter').on('change', reloadLogs);
$('#search-logs').on('keyup', function (e) {
    if (e.keyCode === 13) {
        reloadLogs();
    }
});
$('#refresh-logs').on('click', reloadLogs);

$(document).on('click', '.view-log-details', function () {
    var id = $(this).data('id');
    $('#modalTitle').text('Log Details');
    $('#logDetailsContent').html('Loading...');
    $('#logDetailsModal').css('display', 'flex');

    $.get('{$detailsUrl}', {id: id}, function (html) {
        $('#logDetailsContent').html(html);
    });
});

$(document).on('click', '#closeDetailsModal, #logDetailsModal .simple-modal-backdrop', function () {
    $('#logDetailsModal').hide();
});
JS;

$this->registerJs($js);
?>
