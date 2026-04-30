<?php

use backend\components\SvgRenderHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Modules Setting';
$logo = 'modulesetting';
$baseUrl = Url::base();
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;

$toggleUrl          = Url::to(['modulesetting/toggle-visible']);
$updateModulesSeqUrl    = Url::to(['modulesetting/update-tab-sequence']);
$updateParentSeqUrl = Url::to(['modulesetting/update-parent-sequence']);
$addModulesUrl          = Url::to(['modulesetting/add-tab']);
$editModulesUrl         = Url::to(['modulesetting/edit-tab']);
$addParentModules      = Url::to(['modulesetting/add-parenttab']);
$editParentModules      = Url::to(['modulesetting/edit-parenttab']);
$downloadBase = Url::to(['modulesetting/download']);

$logoGetUrl    = Url::to(['modulesetting/get-logo']);
$logoUpdateUrl = Url::to(['modulesetting/update-logo']);
?>
<style>
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:#f4f6f9}.tab-list{display:none}.parent-title{cursor:pointer}.card{max-width:auto;margin:auto;background:#fff;border-radius:10px;box-shadow:0 8px 24px #0f172a14;overflow:hidden;border:1px solid #e5e7eb}.card-header{padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:12px}.card-header h1{margin:0;font-size:22px;font-weight:600;color:#111827;flex:1}.add-btn,.fields-btn,.add-field-btn{padding:6px 14px;cursor:pointer;background:#2563eb;color:#fff;border:none;border-radius:6px;margin:0;font-size:13px;font-weight:500;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;box-shadow:0 1px 2px #0f172a1f;transition:background .15s ease,transform .05s ease,box-shadow .15s ease}.add-btn:hover,.fields-btn:hover,.add-field-btn:hover{background:#1d4ed8;transform:translateY(-1px);box-shadow:0 4px 10px #2563eb40}.add-btn:active,.fields-btn:active,.add-field-btn:active{transform:translateY(0);box-shadow:0 1px 3px #0f172a2e}.search-input{padding:6px 10px;border-radius:999px;border:1px solid #d1d5db;font-size:13px;min-width:230px;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;background:#f9fafb}.search-input:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px #2563eb40}.card-body{padding:18px 22px 22px;background:#f9fafb}.parent-block{margin-bottom:14px;border-radius:9px;border:1px solid #e5e7eb;background:#fff;box-shadow:0 2px 6px #0f172a08;padding:0}.parent-block-header{padding:8px 12px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:8px;background:#f9fafb}.parent-block-header .parent-drag{cursor:move;color:#9ca3af}.parent-block-header .parent-title{font-weight:600;font-size:13px;color:#374151}.parent-block.drag-over{border-color:#2563eb;box-shadow:0 0 0 1px #2563eb59}.tab-list{list-style:none;padding:8px 10px 10px;margin:0}.tab-item{padding:7px 10px;margin-bottom:5px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;cursor:move;display:flex;align-items:center;gap:10px;font-size:13px;color:#111827;transition:box-shadow .15s ease,border-color .15s ease,background .15s ease,transform .05s ease}.tab-item:last-child{margin-bottom:0}.tab-item:hover{box-shadow:0 4px 10px #0f172a14;border-color:#cbd5f5;transform:translateY(-1px)}.tab-item.drag-over{border-color:#2563eb;background:#eff6ff;box-shadow:0 0 0 1px #2563eb59}.tab-main{flex:1;display:flex;flex-wrap:wrap;align-items:center;gap:6px 10px}.tab-id{color:#6b7280;font-weight:500}.tab-label{font-weight:600;color:#111827}.tab-meta{color:#6b7280;font-size:12px}.tab-controls{display:flex;align-items:center;gap:6px}.tab-controls label{font-size:12px;color:#4b5563}.tab-visible-checkbox{width:15px;height:15px;cursor:pointer}.tab-actions{margin-left:auto;display:flex;gap:6px}.simple-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;font-family:inherit}.simple-modal-backdrop{position:absolute;inset:0;background:#0f172a8c}.simple-modal-dialog{position:relative;background:#fff;border-radius:18px;box-shadow:0 24px 60px #0f172a73;width:640px;max-width:calc(100% - 40px);padding:26px 30px 22px;z-index:1;border:1px solid #e5e7eb}.simple-modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}.simple-modal-header span{font-size:20px;font-weight:700;color:#111827}.simple-modal-close{border:none;background:transparent;font-size:22px;line-height:1;cursor:pointer;color:#6b7280}.simple-modal-body{max-height:70vh;overflow-y:auto;padding-right:6px;margin-bottom:18px}.simple-modal-footer{display:flex;justify-content:flex-end;gap:10px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:24px;row-gap:14px}.form-row{display:flex;flex-direction:column;margin-bottom:0}.form-row-full{grid-column:1 / -1}.form-row label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}.form-row small{display:block;font-size:11px;color:#9ca3af;margin-top:3px}.form-control{width:100%;padding:9px 13px;border-radius:10px;border:1px solid #e5e7eb;font-size:13px;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease,transform .05s ease;background:#f9fafb}.form-control:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 1px #2563eb40 0 0 0 4px #2563eb1a;transform:translateY(-1px)}.checkbox-row{display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-top:4px}.head-img{position:static}.checkbox-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}
</style>

<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add" style="
    padding: 12px;
    margin-top: 10px;
">
         <span class="icons-coll head-img" >
            <?= SvgRenderHelper::renderIcon($logo.'.svg',true); ?>
        </span>
        <span class="sm-modname"><strong><?= $this->title; ?></strong></span>
        <br>
      </div>

      
    </div>
  </div>
</div>

<div class="card">
    <div class="card-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <button id="add-tab-btn" class="add-btn" style="display: none;">Add Modules</button>
        <input type="text" id="tab-search" class="search-input"
            placeholder="Search by tablabel / parent">
        <button id="add-parenttab-btn" class="add-btn">
            Add Parent Modules
        </button>
        <!-- <button id="update-sequence-btn" class="add-btn" style="margin-left:auto;">
            Update Modules Seq
        </button> -->

        <button id="update-parent-seq-btn" class="add-btn" style="margin-left:8px;display:none;">
            Update Parent Seq
        </button>
    </div>

    <div class="card-body" id="parent-container">
   
        <?php foreach ($tabTree as $parentKey => $group): ?>
            <div class="parent-block"
                draggable="true"
                data-parentid="<?= (int)($group['parent_id'] ?? 0) ?>"
                data-parentlabel="<?= Html::encode($group['parent_label']) ?>">

                <div class="parent-block-header">
                    <span class="parent-drag">≡</span>
                    <span class="parent-title">
                        <?= Html::encode($group['parent_label']) ?>
                        <?php if (!empty($group['sequence'])): ?>
                            (Seq: <span class="parent-seq-label"><?= (int)$group['sequence'] ?></span>)
                        <?php endif; ?>
                    </span>

                    <?php if (!empty($group['parent_id'])): ?>
                        <button type="button"
                            class="add-btn update-parent-tabs-seq-btn"
                            data-parentid="<?= (int)$group['parent_id'] ?>"
                            style="display:none;margin-left:6px;">
                            Update Module Sequence
                        </button>
                        <button type="button"
                            class="fields-btn edit-parenttab-btn"
                            style="margin-left:auto;"
                            data-parentid="<?= (int) $group['parent_id'] ?>"
                            data-label="<?= Html::encode($group['parent_label']) ?>"
                            data-sequence="<?= (int)($group['sequence'] ?? 0) ?>"
                            data-icon="<?= Html::encode($group['icon'] ?? '') ?>"
                            data-visible="<?= (int) $group['visible'] ?>">
                            Edit Parent
                        </button>
                    <?php endif; ?>
                </div>

                <ul class="tab-list" data-parent="<?= Html::encode($parentKey) ?>" data-parentid="<?= (int)$group['parent_id'] ?>">
                    <?php foreach ($group['children'] as $tab): ?>
                        <li class="tab-item"
                            draggable="true"
                            data-id="<?= $tab->tabid ?>"
                            data-name="<?= Html::encode($tab->name ?? $tab->tablabel) ?>"
                            data-label="<?= Html::encode($tab->tablabel) ?>"
                            data-parent="<?= Html::encode($tab->parent) ?>"
                            data-tablename="<?= Html::encode($tab->tablename) ?>"
                            data-tablekeyid="<?= Html::encode($tab->tablekeyid) ?>"
                            data-icon="<?= Html::encode($tab->icon_name) ?>"
                            data-source="<?= Html::encode($tab->source) ?>"
                            data-tabsequence="<?= Html::encode($tab->tabsequence) ?>"
                            data-module_type="<?= Html::encode($tab->module_type) ?>"
                            data-default_view="<?= Html::encode($tab->default_view) ?>"
                            data-customized="<?= (int)$tab->customized ?>"
                            data-visible="<?= (int)$tab->visible ?>"
                            data-presence="<?= (int)$tab->presence ?>"
                            data-isentitytype="<?= (int)$tab->isentitytype ?>"
                            data-issyncable="<?= (int)$tab->issyncable ?>"
                            data-allowduplicates="<?= (int)$tab->allowduplicates ?>"
                            data-sync_action_for_duplicates="<?= (int)$tab->sync_action_for_duplicates     ?>"
                            data-import_allowed="<?= (int)$tab->import_allowed     ?>"
                            data-export_allowed="<?= (int)$tab->export_allowed     ?>"
                            data-search_allowed="<?= (int)$tab->search_allowed     ?>"
                            data-modifiedby="<?= Html::encode($tab->modifiedby) ?>"
                            data-modifiedtime="<?= Html::encode($tab->modifiedtime) ?>">
                            <div class="tab-main">
                                <span class="tab-label"><?= Html::encode($tab->tablabel) ?></span>
                                <span class="tab-id">(Modules <?= $tab->tabid ?>)</span>
                                <span class="tab-meta">
                                    Seq <span class="seq-label"><?= (int)$tab->tabsequence ?></span>
                                </span>
                            </div>

                            <div class="tab-controls">
                                <label>
                                    Visible
                                    <input type="checkbox"
                                        class="tab-visible-checkbox"
                                        data-id="<?= $tab->tabid ?>"
                                        <?= $tab->visible == 0 ? 'checked' : '' ?>>
                                </label>
                            </div>

                            <div class="tab-actions">
                                <a href="<?= Url::to(['modulesetting/fields', 'tabid' => $tab->tabid]) ?>"
                                    class="fields-btn">View Fields</a>

                                <button type="button" class="fields-btn edit-tab-btn">
                                    Edit
                                </button>
                            </div>

                            <input type="hidden"
                                class="tab-sequence-input"
                                data-id="<?= $tab->tabid ?>"
                                value="<?= (int)$tab->tabsequence ?>">
                            <input type="hidden"
                                class="tab-parent-input"
                                data-id="<?= $tab->tabid ?>"
                                value="<?= Html::encode($tab->parent) ?>">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div id="site-logo-modal" class="simple-modal" style="display:none;">
    <div class="simple-modal-backdrop" data-close-site-logo-modal></div>
    <div class="simple-modal-dialog">
        <div class="simple-modal-header">
            <span>Change Site Logo</span>
            <button type="button" class="simple-modal-close" data-close-site-logo-modal>&times;</button>
        </div>

        <div class="simple-modal-body">
            <div class="form-row">
                <label for="site-company-name">Company</label>
                <select id="site-company-name" class="form-control">
                    <option value="deshwal">deshwal</option>
                    <option value="oxypc">oxypc</option>
                </select>
                <small>Select the company whose logo you want to change.</small>
            </div>

            <div class="form-row" style="margin-top:12px;">
                <label>Current Logo</label>
                <div id="site-logo-preview-wrap" style="min-height:70px;display:flex;align-items:center;">
                    <img id="site-logo-preview"
                         src="<?= $baseUrl . $logoPath; ?>"
                         style="max-height:70px;width:auto;border:1px solid #e5e7eb;border-radius:6px;padding:4px;background:#f9fafb;">
                </div>
            </div>

            <div class="form-row" style="margin-top:12px;">
                <label for="site-logo-file">Upload Logo</label>
                <input type="file"
                       id="site-logo-file"
                       class="form-control"
                       accept=".png,.jpg,.jpeg,.svg">
                <small>Allowed: PNG, JPG, JPEG, SVG</small>
            </div>
        </div>

        <div class="simple-modal-footer">
            <button type="button" id="save-site-logo-btn" class="add-btn">Save Logo</button>
            <button type="button" class="fields-btn" data-close-site-logo-modal>Cancel</button>
        </div>
    </div>
</div>
<?php
echo $this->render('_modules_form', ['parentMap' => $parentMap]);
echo $this->render('_parentmodule_form');
?>

<?php
$js = <<<JS
var csrfToken = '$csrfToken';
var csrfParam = '$csrfParam';

var toggleUrl          = '$toggleUrl';
var updateModulesSeqUrl    = '$updateModulesSeqUrl';
var updateParentSeqUrl = '$updateParentSeqUrl';
var addModulesUrl          = '$addModulesUrl';
var editModulesUrl         = '$editModulesUrl';
var addParenttabUrl      = '$addParentModules';
var editParenttabUrl      = '$editParentModules';
var downloadBase = '$downloadBase';
$(document).on('change', '.tab-visible-checkbox', function () {
    var id = $(this).data('id');
    var visible = $(this).is(':checked') ? 0 : 1;

    var data = {};
    data['id'] = id;
    data['visible'] = visible;
    data[csrfParam] = csrfToken;
    startLoading();
    $.post(toggleUrl, data, function (resp) {
        if (!resp || !resp.success) {
            alert('Failed to update visibility');
            stopLoading();
            return;
        }
        if(resp && resp.success == true){
            window.location.reload();
        }
    }, 'json').fail(function () {
        alert('Error while updating visibility');
        stopLoading();
        return;
    });
});
function showUpdateSeqForParent(parentId) {
    // $('.update-parent-tabs-seq-btn').hide();
    $('.update-parent-tabs-seq-btn[data-parentid="' + parentId + '"]').show();
}
var originalTabOrderByParent = {};

function captureOriginalTabOrder() {
    originalTabOrderByParent = {};
    $('.tab-list').each(function () {
        var parentId = $(this).data('parentid');
        if (!parentId) return;

        var ids = [];
        $(this).find('.tab-item').each(function () {
            ids.push($(this).data('id'));
        });
        originalTabOrderByParent[parentId] = ids.join(',');
    });
}

$(function () {
    captureOriginalTabOrder();
});
var originalParentOrder = '';

function captureOriginalParentOrder() {
    var ids = [];
    $('.parent-block').each(function () {         
        ids.push($(this).data('parentid'));
    });
    originalParentOrder = ids.join(',');
}

$(function () {
    captureOriginalParentOrder();
});

function hideAllUpdateSeqButtons() {
    $('.update-parent-tabs-seq-btn').hide();
}

$('#tab-search').on('input', function () {
    var q = $(this).val().toLowerCase().trim();

    $('#parent-container .parent-block').each(function () {
        var parentName = $(this).find('.parent-title').text().toLowerCase();
        var showParent = parentName.indexOf(q) !== -1;
        var anyChildVisible = false;

        $(this).find('.tab-item').each(function () {
            var label = $(this).data('label').toString().toLowerCase();
            var match = label.indexOf(q) !== -1;
            $(this).toggle(match || !q);
            if (match) anyChildVisible = true;
        });

        $(this).toggle(showParent || anyChildVisible || !q);
    });
});
let draggedModules = null;
let snapshotHtml = null;
let originalSeq = null;
let draggedParentBlock = null;
var draggedTab = null;
function takeSnapshot() {
    snapshotHtml = $('#parent-container').html();
    originalSeq = {};
    $('.tab-item').each(function () {
        var id  = $(this).data('id').toString();
        var seq = parseInt($('.tab-sequence-input[data-id=' + id + ']').val(), 10) || 0;
        originalSeq[id] = seq;
    });
}
function restoreSnapshot() {
    if (snapshotHtml !== null) {
        $('#parent-container').html(snapshotHtml);
        snapshotHtml = null;
        originalSeq = null;
    }
}


$(document).on('dragstart', '.tab-item', function (e) {
    var ev = e.originalEvent || e;          
    var itemTab = $(this);
     var target = e.target;
    if (target.classList.contains('tab-item')) {
        draggedTab = target;
        var parentId = $(draggedTab).data('parent');
        showUpdateSeqForParent(parentId);
    }
    draggedTab = itemTab[0];

    var parentId = itemTab.data('parent');
    showUpdateSeqForParent(parentId);

    if (ev.dataTransfer) {
        ev.dataTransfer.effectAllowed = 'move';
        try {
            ev.dataTransfer.setData('text/plain', 'tab');
        } catch (err) {}
    }
});
document.addEventListener('dragover', function (e) {
    if (!draggedTab) return;

    var targetOverDrag = $(e.target).closest('.tab-item, .tab-list');
    if (!targetOverDrag.length || !targetOverDrag.closest('.tab-list').length) return;

    e.preventDefault(); 

    if (targetOverDrag.hasClass('tab-item')) {
        var target = targetOverDrag[0];
        if (target === draggedTab) return;

        var rect   = target.getBoundingClientRect();
        var offset = e.clientY - rect.top;
        var list   = target.parentNode;

        if (offset < rect.height / 2) {
            list.insertBefore(draggedTab, target);           
        } else {
            list.insertBefore(draggedTab, target.nextSibling);  
        }
    }
}, false);

$(document).on('dragleave', '.tab-list', function (e) {
    if (e.target !== this) return;
    $(this).removeClass('drag-over');
});

// document.addEventListener('drop', function (e) {
//     if (!draggedTab) return;

//     var listDrag = $(e.target).closest('.tab-list');

//     e.preventDefault();

//     if (listDrag.length && !$.contains(listDrag[0], draggedTab)) {
//         listDrag[0].appendChild(draggedTab);
//     }

//     draggedTab = null;
// }, false);

document.addEventListener('drop', function (e) {
    if (!draggedTab) return;

    var listDrag = $(e.target).closest('.tab-list');

    e.preventDefault(); 

    if (listDrag.length) {
        var newParentId = listDrag.data('parentid'); 

        if (!$.contains(listDrag[0], draggedTab)) {
            listDrag[0].appendChild(draggedTab);
        }

        $(draggedTab).data('parent', newParentId);
        $(draggedTab).attr('data-parent', newParentId);

        showUpdateSeqForParent(newParentId);
    }

    draggedTab = null;
}, false);




function updateModulesSequencesForList(listSeq) {
    var seq = 1;
    listSeq.find('.tab-item').each(function () {
        var id = $(this).data('id').toString();
        $(this).find('.seq-label').text(seq);
        $('.tab-sequence-input[data-id=' + id + ']').val(seq);
        seq++;
    });
}


$(document).on('drop', '.tab-item', function (e) {
    e.preventDefault();
    $('.tab-item').removeClass('drag-over');
    if (!draggedModules || draggedModules === this) return;

    var targetListChange = $(this).closest('.tab-list');
    var dragListf   = $(draggedModules).closest('.tab-list');

    if (targetListChange[0] !== dragListf[0]) {
        $(this).before(draggedModules);
    } else {
        $(this).before(draggedModules);
    }

    updateSequencesForList(dragListf);
    if (targetListChange[0] !== dragListf[0]) {
        updateSequencesForList(targetListChange);
    }
});


function updateSequencesForList(listT) {
    var seq = 1;
    listT.find('.tab-item').each(function () {
        var id = $(this).data('id').toString();
        $(this).find('.seq-label').text(seq);
        $('.tab-sequence-input[data-id=' + id + ']').val(seq);
        seq++;
    });
}


function updateSequencesInDOM() {
    $('.tab-list').each(function () {
        var seq = 1;
        $(this).find('.tab-item').each(function () {
            var id = $(this).data('id').toString();
            $(this).find('.seq-label').text(seq);
            $('.tab-sequence-input[data-id=' + id + ']').val(seq);
            seq++;
        });
    });
}
$(document).on('click', '.update-parent-tabs-seq-btn', function () {
    var parentId = $(this).data('parentid');
    var listParentTabsseqbtn = $('.tab-list[data-parentid="' + parentId + '"]');
    var seqData = [];
    var pos = 1;
    var ids = [];
    listParentTabsseqbtn.find('.tab-item').each(function () {
        var id = $(this).data('id');
        ids.push(id);
        seqData.push({
            tabid: $(this).data('id'),
            sequence: pos++
        });
    });

    if (!seqData.length) {
        alert('No modules under this parent.');
        return;
    }
    var currentKey  = ids.join(',');
    var originalKey = originalTabOrderByParent[parentId] || '';
    if (currentKey === originalKey) {
        alert('No sequence change to update.');
        return;
    }
    if (!confirm('Are you sure you want to update Menu order? For this parent Modules')) {
        location.reload();
        return;
    }
    startLoading();
    $.ajax({
        url: updateModulesSeqUrl,
        type: 'POST',
        data: {
            [csrfParam]: csrfToken,
            parentid: parentId,
            items: JSON.stringify(seqData)
        },
        dataType: 'json',
        success: function (res) {
            stopLoading();
            if (res && res.success) {
                hideAllUpdateSeqButtons();
            } else {
                alert((res && res.message) || 'Failed to update sequence');
            }
        },
        error: function () {
            stopLoading();
            alert('Error while updating sequence');
        }
    });
});


$(document).on('dragstart', '.parent-block', function (e) {
    draggedParentBlock = this;
    e.originalEvent.dataTransfer.effectAllowed = 'move';
    var target = e.target;
    if (target.classList.contains('parent-block')) {
        draggedTab = target;
        $('#update-parent-seq-btn').show();
    }
});

$(document).on('dragover', '.parent-block', function (e) {
    e.preventDefault();
    e.originalEvent.dataTransfer.dropEffect = 'move';
});

$(document).on('drop', '.parent-block', function (e) {
    e.preventDefault();

    if (draggedParentBlock && draggedParentBlock !== this) {
        $(this).before(draggedParentBlock);
        draggedParentBlock = null;
        return;
    }

    if (draggedModules) {
        var targetlistSeq = $(this).find('.tab-list').first();
        var dragListSeq   = $(draggedModules).closest('.tab-list');
        targetlistSeq.append(draggedModules);
        updateModulesSequencesForList(dragListSeq);
        if (targetlistSeq[0] !== dragListSeq[0]) {
            updateModulesSequencesForList(targetlistSeq);
        }
        draggedModules = null;
    }
});


function updateParentSequencesInDOM() {
    var seq = 1;
    $('#parent-container .parent-block').each(function () {
        $(this).find('.parent-seq-label').text(seq);
        $(this).data('sequence', seq);
        seq++;
    });
}

$('#update-parent-seq-btn').on('click', function () {
    updateParentSequencesInDOM();
    var ids = [];
    var updates = [];
    $('#parent-container .parent-block').each(function () {
        var id  = $(this).data('parentid');
        ids.push(id);
        var seq = $(this).data('sequence');
        if (id) {
            updates.push({ id: id, sequence: seq });
        }
    });
    var currentKey = ids.join(',');

    if (currentKey === originalParentOrder) {
        alert('No parent sequence change to update.');
        return;
    }
    if (!updates.length) {
        alert('No parent tabs found.');
        return;
    }

    if (!confirm('Are you sure you want to update parent modules order?')) {
        location.reload();
        return;
    }

    var data = {};
    data['updates'] = updates;
    data[csrfParam] = csrfToken;

    $.ajax({
        url: updateParentSeqUrl,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (resp) {
            if (resp && resp.success) {
                alert('Parent tab order updated successfully.');
                location.reload();
            } else {
                alert('Failed to update parent tab order.');
            }
        },
        error: function () {
            alert('Error occurred while updating parent tab order.');
        }
    });
});

function openModulesModal(mode, data) {
    $('#tab-uploaded-file').empty();
    $('#tab-modified-by').text('–');
    $('#tab-modified-time').text('–');
    if (mode === 'add') {
        $('#tab-modified-by').remove();
        $('#tab-modified-time').remove();
        $('#tab-modal-title').text('Add Modules');
        $('#tab-id').val('');
        $('#tab-name').val('');
        $('#tab-label').val('');
        $('#tab-parent').val('');
        $('#tab-tablename').val('');
        $('#tab-tablekeyid').val('');
        $('#tab-sequence').val('');
        $('#tab-default-view').val('list');
        $('#tab-source').val('custom');
        $('#tab-module-type').val('SimpleTwoCol');
        $('#tab-issyncable').prop('checked', false);
        $('#tab-allowduplicates').prop('checked', true);
        $('#tab-import-allowed').prop('checked', false);
        $('#tab-export-allowed').prop('checked', false);
        $('#tab-search-allowed').prop('checked', false);
        $('#tab-customized').prop('checked', false);
        $('#tab-visible').prop('checked', true);
        $('#tab-presence').prop('checked', true);

        $('#tab-uploaded-file').empty();
    } else {
       $('#tab-modal-title').text('Edit ' + (data.tablabel || '') + ' Modules');
        $('#tab-id').val(data.tabid);
        $('#tab-name').val(data.name || '');
        $('#tab-label').val(data.tablabel || '');
        $('#tab-parent').val(data.parent || '');
        $('#tab-tablename').val(data.tablename || '');
        $('#tab-tablekeyid').val(data.tablekeyid || '');

        $('#tab-sequence').val(data.tabsequence || '');
        
        $('#tab-default-view').val(data.default_view || '');
        $('#tab-source').val(data.source || '');
        $('#tab-module-type').val(data.module_type || '');

        $('#tab-issyncable').prop('checked', data.issyncable == 0);
        $('#tab-allowduplicates').prop('checked', data.allowduplicates == 0);
        $('#tab-import-allowed').prop('checked', data.import_allowed == 0);
        $('#tab-export-allowed').prop('checked', data.export_allowed == 0);
        $('#tab-search-allowed').prop('checked', data.search_allowed == 0);
        $('#tab-visible').prop('checked', data.visible == 0);
        $('#tab-presence').prop('checked', data.presence == 0);
        $('#tab-customized').prop('checked', data.customized == 0);
        $('#tab-sync-action').prop('checked', data.sync_action_for_duplicates == 0);
        $('#tab-modified-by').val(data.modifiedby);
        $('#tab-modified-time').val(data.modifiedtime);
        $('#tab-uploaded-file').empty();
    }
    $('#tab-modal').css('display', 'flex');
}
$(document).on('click', '[data-close-tab-modal]', function () {
    $('#tab-modal').hide();
});
$('#add-tab-btn').on('click', function () {
    openModulesModal('add', {});
});
$(document).on('click', '.edit-tab-btn', function () {
    startLoading();
    var liEditBtn = $(this).closest('.tab-item');
    $('#tab-modified-by').text(liEditBtn.data('modifiedby') || '–');
    $('#tab-modified-time').text(liEditBtn.data('modifiedtime') || '–');

    openModulesModal('edit', {
        tabid:                  liEditBtn.data('id'),
        name:                   liEditBtn.data('name') || liEditBtn.data('label'),
        tablabel:               liEditBtn.data('label'),
        parent:                 liEditBtn.data('parent') || '',
        tablename:              liEditBtn.data('tablename') || '',
        tablekeyid:             liEditBtn.data('tablekeyid') || '',
        icon_name:              liEditBtn.data('icon') || '',
        visible:                liEditBtn.data('visible'),
        presence:               liEditBtn.data('presence'),
        tabsequence:            liEditBtn.data('tabsequence') || '',
        customized:             liEditBtn.data('customized'),
        default_view:           liEditBtn.data('default_view') || '',
        source:                 liEditBtn.data('source') || '',
        issyncable:             liEditBtn.data('issyncable'),
        allowduplicates:        liEditBtn.data('allowduplicates'),
        sync_action_for_duplicates: liEditBtn.data('sync_action_for_duplicates'),
        module_type:            liEditBtn.data('module_type') || '',
        import_allowed:         liEditBtn.data('import_allowed'),
        export_allowed:         liEditBtn.data('export_allowed'),
        search_allowed:         liEditBtn.data('search_allowed')
    });

    var icon = liEditBtn.data('icon');
    var containerEditLi = $('#tab-uploaded-file');
    containerEditLi.empty();

    if (icon) {
        var html = '<div class="upd-file">Uploaded file:<br>' +
                   '<a href="' + downloadBase +
                   '?type=tab&filename=' + encodeURIComponent(icon) + '">' +
                   icon + '</a></div>';
        containerEditLi.html(html);
    }else {
         $('#tab-uploaded-file').empty();
    }

    stopLoading();
});
$('#save-tab-btn').on('click', function () {
    startLoading();
    var id        = $('#tab-id').val();
    var name      = $('#tab-name').val();
    var tablabel  = $('#tab-label').val();
    var parent    = $('#tab-parent').val();
    var tablename = $('#tab-tablename').val();
    var tablekey  = $('#tab-tablekeyid').val();
    var visible   = $('#tab-visible').is(':checked') ? 0 : 1;
    var presence  = $('#tab-presence').is(':checked') ? 0 : 1;
    var file      = $('#tab-icon-file')[0].files[0] || null;

    if (!name) {
        alert('Modules name is required.');
        stopLoading();
        return;
    }

    var formData = new FormData();
    formData.append(csrfParam, csrfToken);
    formData.append('name', name);
    formData.append('tablabel', tablabel || name);
    formData.append('parent', parent || '');
    formData.append('tablename', tablename);
    formData.append('tablekeyid', tablekey);
    formData.append('visible', visible);
    formData.append('presence', presence);
    formData.append('tabsequence', $('#tab-sequence').val());
    formData.append('customized', $('#tab-customized').val());
    formData.append('default_view', $('#tab-default-view').val());
    formData.append('source', $('#tab-source').val());
    formData.append('module_type', $('#tab-module-type').val());
    formData.append('issyncable', $('#tab-issyncable').is(':checked') ? 0 : 1);
    formData.append('sync_action_for_duplicates', $('#tab-sync-action').is(':checked') ? 0 : 1);
    formData.append('allowduplicates', $('#tab-allowduplicates').is(':checked') ? 0 : 1);
    formData.append('import_allowed', $('#tab-import-allowed').is(':checked') ? 0 : 1);
    formData.append('export_allowed', $('#tab-export-allowed').is(':checked') ? 0 : 1);
    formData.append('search_allowed', $('#tab-search-allowed').is(':checked') ? 0 : 1);

    var url;
    if (id) {
        url = editModulesUrl;
        formData.append('tabid', id);
    } else {
        url = addModulesUrl;
    }

    if (file) {
        formData.append('icon_file', file);
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
            if (resp && resp.success) {
                stopLoading();
                location.reload();
            } else {
                stopLoading();
                alert((resp && resp.message) || 'Failed to save tab');
            }
        },
        error: function () {
            stopLoading();
            alert('Error while saving tab');
        }
    });
});


function openParenttabModal(mode, data) {
    $('#parenttab-uploaded-file').empty();
    if (mode === 'add') {
        $('#parenttab-modal-title').text('Add Parent Modules');
        $('#parenttab-id').val('');
        $('#parenttab-label').val('');
        $('#parenttab-icon').val('');
        $('#parenttab-sequence').val('');
        $('#parenttab-visible').prop('checked', true);
        
    } else {
        console.log(data.visible,'visible');
        $('#parenttab-modal-title').text('Edit '+ data.label +' Module');
        $('#parenttab-id').val(data.id);
        $('#parenttab-label').val(data.label || '');
        $('#parenttab-icon').val(data.icon || '');
        $('#parenttab-sequence').val(data.sequence || 0);
        $('#parenttab-visible').prop('checked', data.visible == 0);
    }
    $('#parenttab-modal').css('display', 'flex');
}
$(document).on('click', '[data-close-parenttab-modal]', function () {
    $('#parenttab-modal').hide();
});
$('#add-parenttab-btn').on('click', function () {
    openParenttabModal('add', {});
});
$(document).on('click', '.edit-parenttab-btn', function () {
    startLoading();
    var btnParentEdit = $(this);
    openParenttabModal('edit', {
        id:       btnParentEdit.data('parentid'),
        label:    btnParentEdit.data('label'),
        icon:     btnParentEdit.data('icon') || '',
        sequence: btnParentEdit.data('sequence') || 0,
        visible:  btnParentEdit.data('visible') || 0
    });
    var icon = btnParentEdit.data('icon');
    // var containerEdit = $('#parenttab-uploaded-file');
    // containerEdit.remove(); 

    if (icon) {
        var html = '<div class="upd-file">Uploaded file:<br>' +'<a href="' + downloadBase +'?type=parenttab&filename=' + encodeURIComponent(icon) + '">' + icon + '</a></div>';
        $('#parenttab-uploaded-file').append(html);
    }
    stopLoading();
});

function validateIconFile(input) {
    var file = input.files[0];
    if (!file) return true;

    var allowedExt = ['png', 'jpg', 'jpeg', 'svg'];
    var name = file.name.toLowerCase();
    var okExt = allowedExt.some(function(ext) { return name.endsWith('.' + ext); });
    if (!okExt) {
        alert('Only PNG, JPG, JPEG, SVG icons are allowed.');
        input.value = '';
        return false;
    }

    if (file.size > 10 * 1024) {
        alert('Icon must be smaller than 10KB.');
        input.value = '';
        return false;
    }

    if (!name.endsWith('.svg')) {
        var img = new Image();
        img.onload = function () {
            if (img.width > 50 || img.height > 50) {
                alert('Icon must be at most 50x50 pixels.');
                input.value = '';
            }
        };
        img.src = URL.createObjectURL(file);
    }

    return true;
}

$(document).on('change', '#tab-icon-file, #parenttab-icon-file', function () {
    validateIconFile(this);
});

$('#save-parenttab-btn').on('click', function () {
    startLoading();
    var id       = $('#parenttab-id').val();
    var label    = $('#parenttab-label').val();
    var sequence = $('#parenttab-sequence').val();
    var visible  = $('#parenttab-visible').is(':checked') ? 0 : 1;
    var file     = $('#parenttab-icon-file')[0].files[0] || null;

    if (!label) {
        alert('Parent tab label is required.');
        stopLoading();
        return;
    }

    var formData = new FormData();
    formData.append(csrfParam, csrfToken);
    formData.append('parenttab_label', label);
    formData.append('sequence', sequence);
    formData.append('visible', visible);

    if (id) {
        formData.append('parenttabid', id);   
    } else {
        formData.append('parenttablabel', label); 
    }

    if (file) {
        formData.append('icon_file', file);
    }

    var url = id ? editParenttabUrl : addParenttabUrl;

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
            if (resp && resp.success) {
                stopLoading();
                location.reload();
            } else {
                stopLoading();
                alert((resp && resp.message) || 'Failed to save parent tab');
            }
        },
        error: function () {
            stopLoading();
            alert('Error while saving parent tab');
        }
    });
});

$(document).ready(function () {
    $('.tab-list').hide();

    $(document).on('click', '.parent-title', function (e) {
        if ($(e.target).closest('.edit-parenttab-btn').length) return;

        let block = $(this).closest('.parent-block');
        let table = block.find('.tab-list');

        table.slideToggle(200);
    });
});



// ---------- SITE LOGO CHANGE ----------
var logoGetUrl = '$logoGetUrl';
var logoUpdateUrl = '$logoUpdateUrl';
$(document).on('click', '#change-logo-btn', function () {
    $('#site-logo-modal').css('display', 'flex');
});

$(document).on('click', '[data-close-site-logo-modal]', function () {
    $('#site-logo-modal').hide();
    $('#site-company-name').val('');
    $('#site-logo-file').val('');
});

$(document).on('change', '#site-company-name', function () {
    var company = $(this).val();
    if (!company) {
        return;
    }

    $.getJSON(logoGetUrl, { company: company }, function (resp) {
        if (resp && resp.success && resp.url) {
            $('#site-logo-preview').attr('src', resp.url + '?_=' + Date.now());
            $('#current-site-logo').attr('src', resp.url + '?_=' + Date.now());
        } else {
            $('#site-logo-preview').attr('src', '<?= $baseUrl; ?>/thememain/img/login/logo.png');
        }
    });
});

$(document).on('click', '#save-site-logo-btn', function () {
    var company = $('#site-company-name').val();
    var fileInput = $('#site-logo-file')[0];
    var file = fileInput.files[0];

    if (!company) {
        alert('Please select company.');
        return;
    }
    if (!file) {
        alert('Please choose logo file.');
        return;
    }

    showConfirm('Are you sure? This will replace the current logo for "' + company + '".')
        .then(function (ok) {
            if (!ok) return;

            var formData = new FormData();
            formData.append(csrfParam, csrfToken);
            formData.append('company', company);
            formData.append('logo_file', file);

            startLoading();

            $.ajax({
                url: logoUpdateUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (resp) {
                    stopLoading();
                    if (resp && resp.success) {
                        if (resp.url) {
                            $('#site-logo-preview').attr('src', resp.url + '?_=' + Date.now());
                            $('#current-site-logo').attr('src', resp.url + '?_=' + Date.now());
                        }
                        alert('Logo updated successfully.');
                        $('#site-logo-modal').hide();
                        $('#site-logo-file').val('');
                        location.reload();
                    } else {
                        alert((resp && resp.message) || 'Failed to save logo.');
                    }
                },
                error: function () {
                    stopLoading();
                    alert('Error while saving logo.');
                }
            });
        });
});
JS;

$this->registerJs($js, \yii\web\View::POS_END);
