<style>
    #blockModal.simple-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}#blockModal .simple-modal-backdrop{position:absolute;inset:0;background:#0f172a8c}#blockModal .simple-modal-dialog{position:relative;background:#fff;border-radius:18px;box-shadow:0 24px 60px #0f172a73;width:520px;max-width:calc(100% - 40px);padding:22px 26px 20px;z-index:1;border:1px solid #e5e7eb}#blockModal .simple-modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}#blockModal .simple-modal-header span{font-size:20px;font-weight:700;color:#111827}#blockModal .simple-modal-close{border:none;background:transparent;font-size:22px;line-height:1;cursor:pointer;color:#6b7280}#blockModal .simple-modal-body{max-height:60vh;overflow-y:auto;padding-right:4px;margin-bottom:14px}#blockModal .form-row{margin-bottom:10px}#blockModal .form-row label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}#blockModal .form-control{width:100%;padding:9px 13px;border-radius:10px;border:1px solid #e5e7eb;font-size:13px;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease,transform .05s ease;background:#f9fafb}#blockModal .form-control:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 1px #2563eb40 0 0 0 4px #2563eb1a;transform:translateY(-1px)}#blockModal .checkbox-row{display:flex;align-items:center;gap:8px;font-size:13px;color:#374151}#blockModal .checkbox-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}#blockModal .simple-modal-footer{display:flex;justify-content:flex-end;gap:10px}.block-flags-grid{grid-column:1 / -1;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 20px;margin-top:6px}.block-flag{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:#f3f4f6;font-size:13px;color:#374151;cursor:pointer;border:1px solid transparent;transition:background .15s,border-color .15s,box-shadow .15s,transform .05s}.block-flag:hover{background:#e5edff;border-color:#2563eb;box-shadow:0 2px 6px #2563eb40;transform:translateY(-1px)}.block-flag input[type="checkbox"]{width:15px;height:15px;accent-color:#2563eb;cursor:pointer}@media (max-width: 768px){.block-flags-grid{grid-template-columns:1fr}}
</style>


<div id="blockModal" class="simple-modal">
    <div class="simple-modal-backdrop" data-close-block-modal></div>
    <div class="simple-modal-dialog">
        <div class="simple-modal-header">
            <span>Save Block</span>
            <button type="button" class="simple-modal-close" data-close-block-modal>&times;</button>
        </div>
        <div class="simple-modal-body">
            <input type="hidden" id="block_tabid" value="<?= (int)$tab->tabid ?>">
            <input type="hidden" id="block_id_edit">
            <div class="form-row">
                <label>Block Label</label>
                <input type="text" id="block_label" class="form-control" placeholder="e.g. Basic Information">
            </div>

            <div class="form-row" style="display: none;">
                <label>Block Type</label>
                <input type="text" id="block_type" class="form-control" placeholder="default / information / other">
            </div>

            <div class="block-flags-grid">
                <label class="block-flag">
                    <input type="checkbox" id="block-visible" disabled>
                    <span>Visible</span>
                </label>
                <label class="block-flag">
                    <input type="checkbox" id="block-show-title">
                    <span>Show title</span>
                </label>
                <label class="block-flag">
                    <input type="checkbox" id="block-create-view">
                    <span>Create view</span>
                </label>
                <label class="block-flag">
                    <input type="checkbox" id="block-edit-view">
                    <span>Edit view</span>
                </label>
                <label class="block-flag">
                    <input type="checkbox" id="block-detail-view">
                    <span>Detail view</span>
                </label>
                <label class="block-flag">
                    <input type="checkbox" id="block-display-status">
                    <span>Display status</span>
                </label>
                <label class="block-flag" style="display: none;">
                    <input type="checkbox" id="block-iscustom">
                    <span>Is custom</span>
                </label>
            </div>

        </div>
        <div class="simple-modal-footer">
            <button type="button" class="add-btn" id="save-block-btn">Save</button>
            <button type="button" class="btn btn-secondary fields-btn" data-close-block-modal>Cancel</button>
        </div>
    </div>
</div>