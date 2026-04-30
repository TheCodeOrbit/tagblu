<?php

use yii\helpers\Html;

/** @var array $parentMap */
?>
<style>
    #tab-modal.simple-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}#tab-modal .simple-modal-backdrop{position:absolute;inset:0;background:#0f172a8c}#tab-modal .simple-modal-dialog{position:relative;background:#fff;border-radius:18px;box-shadow:0 24px 60px #0f172a73;width:880px;max-width:calc(100% - 40px);padding:26px 30px 22px;z-index:1;border:1px solid #e5e7eb}#tab-modal .simple-modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}#tab-modal .simple-modal-header span{font-size:22px;font-weight:700;color:#111827}#tab-modal .simple-modal-close{border:none;background:transparent;font-size:22px;line-height:1;cursor:pointer;color:#6b7280}#tab-modal .simple-modal-body{max-height:70vh;overflow-y:auto;padding-right:6px;margin-bottom:18px}#tab-modal .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:24px;row-gap:14px}#tab-modal .form-row{display:flex;flex-direction:column}#tab-modal .form-row-full{grid-column:1 / -1}#tab-modal .form-row label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}#tab-modal .form-row small{display:block;font-size:11px;color:#9ca3af;margin-top:3px}#tab-modal .form-control{width:100%;padding:9px 13px;border-radius:10px;border:1px solid #e5e7eb;font-size:13px;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease,transform .05s ease;background:#f9fafb}#tab-modal .form-control:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 1px #2563eb40 0 0 0 4px #2563eb1a;transform:translateY(-1px)}#tab-modal .checkbox-row{display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-top:4px}#tab-modal .checkbox-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}#tab-modal .simple-modal-footer{display:flex;justify-content:flex-end;gap:10px}.tab-flags-grid{grid-column:1 / -1;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 20px;margin-top:6px}.tab-flag{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:#f3f4f6;font-size:13px;color:#374151;cursor:pointer;border:1px solid transparent;transition:background .15s ease,border-color .15s ease,box-shadow .15s ease,transform .05s ease}.tab-flag:hover{background:#e5edff;border-color:#2563eb;box-shadow:0 2px 6px #2563eb40;transform:translateY(-1px)}.tab-flag input[type="checkbox"]{width:15px;height:15px;accent-color:#2563eb;cursor:pointer;flex-shrink:0}.tab-flag span{line-height:1.3}@media (max-width: 768px){.tab-flags-grid{grid-template-columns:1fr}}
</style>

<div id="tab-modal" class="simple-modal">
    <div class="simple-modal-backdrop" data-close-tab-modal></div>
    <div class="simple-modal-dialog">
        <div class="simple-modal-header">
            <span id="tab-modal-title">Add Tab</span>
            <button type="button" class="simple-modal-close" data-close-tab-modal>&times;</button>
        </div>
        <div class="simple-modal-body">
            <input type="hidden" id="tab-id">
            <div class="form-row form-row-full" style="margin-bottom:8px;">
                <div style="font-size:12px;color:#6b7280;">
                    <strong>Modified by:</strong>
                    <span id="tab-modified-by">–</span>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <strong>Modified time:</strong>
                    <span id="tab-modified-time">–</span>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row" style="display: none;">
                    <label>Tab Name</label>
                    <input type="text" id="tab-name" class="form-control" placeholder="Internal name, e.g. Contacts">
                </div>

                <div class="form-row">
                    <label>Tab Label</label>
                    <input type="text" id="tab-label" class="form-control" placeholder="Display label in menu">
                </div>

                <div class="form-row">
                    <label>Parent Tab</label>
                    <select id="tab-parent" class="form-control">
                        <option value="">No Parent</option>
                        <?php if (!empty($parentMap)): ?>
                            <?php foreach ($parentMap as $id => $label): ?>
                                <option value="<?= Html::encode($id) ?>"><?= Html::encode($label) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small>Choose which parent group this tab belongs to.</small>
                </div>

                <div class="form-row" style="display: none;">
                    <label>Table Name</label>
                    <input type="text" id="tab-tablename" class="form-control" placeholder="DB table, e.g. vtiger_contacts">
                </div>

                <div class="form-row" style="display: none;">
                    <label>Table Key ID</label>
                    <input type="text" id="tab-tablekeyid" class="form-control" placeholder="Primary key, e.g. contactid">
                </div>
                <div class="form-row">
                    <label>Tab Sequence</label>
                    <input type="number" id="tab-sequence" class="form-control" placeholder="Leave empty for next value" disabled>
                </div>
                <div class="form-row">
                    <label>Default View</label>
                    <input type="text" id="tab-default-view" class="form-control" placeholder="list, detail, etc." disabled>
                </div>

                <div class="form-row" style="display: none;">
                    <label>Source</label>
                    <input type="text" id="tab-source" class="form-control" placeholder="custom, system, etc.">
                </div>

                <div class="form-row" style="display: none;">
                    <label>Module Type</label>
                    <input type="text" id="tab-module-type" class="form-control" placeholder="SimpleTwoCol">
                </div>
                <div class="form-row">
                    <label>Icon</label>
                    <input type="file" id="tab-icon-file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.gif">
                    <small>If left empty, existing icon will remain. asdfa</small>
                    <div id="tab-uploaded-file" class="uploaded-file" style="margin-top:4px;font-size:12px;"></div>
                </div>
                <div class="tab-flags-grid">
                    <label class="tab-flag" style="display: none;">
                        <input type="checkbox" id="tab-issyncable">
                        <span>Syncable</span>
                    </label>
                    <label class="tab-flag">
                        <input type="checkbox" id="tab-allowduplicates">
                        <span>Allow duplicates</span>
                    </label>
                    <label class="tab-flag" style="display: none;">
                        <input type="checkbox" id="tab-customized">
                        <span>Customized</span>
                    </label>
                    <label class="tab-flag">
                        <input type="checkbox" id="tab-import-allowed">
                        <span>Import allowed</span>
                    </label>
                    <label class="tab-flag">
                        <input type="checkbox" id="tab-export-allowed">
                        <span>Export allowed</span>
                    </label>
                    <label class="tab-flag">
                        <input type="checkbox" id="tab-search-allowed">
                        <span>Search allowed (Global search)</span>
                    </label>
                    <label class="tab-flag">
                        <input type="checkbox" id="tab-visible">
                        <span>Visible</span>
                    </label>
                    <label class="tab-flag">
                        <input type="checkbox" id="tab-presence" disabled>
                        <span>Presence</span>
                    </label>
                    <label class="tab-flag" style="display: none;">
                        <input type="checkbox" id="tab-sync-action">
                        <span>Sync action for duplicates</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="simple-modal-footer">
            <button type="button" class="add-btn" id="save-tab-btn">Save</button>
            <button type="button" class="fields-btn" data-close-tab-modal>Cancel</button>
        </div>
    </div>
</div>