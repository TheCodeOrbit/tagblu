<style>
#parenttab-modal.simple-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}#parenttab-modal .simple-modal-backdrop{position:absolute;inset:0;background:#0f172a8c}#parenttab-modal .simple-modal-dialog{position:relative;background:#fff;border-radius:18px;box-shadow:0 24px 60px #0f172a73;width:640px;max-width:calc(100% - 40px);padding:26px 30px 22px;z-index:1;border:1px solid #e5e7eb}#parenttab-modal .simple-modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}#parenttab-modal .simple-modal-header span{font-size:22px;font-weight:700;color:#111827}#parenttab-modal .simple-modal-close{border:none;background:transparent;font-size:22px;line-height:1;cursor:pointer;color:#6b7280}#parenttab-modal .simple-modal-body{max-height:70vh;overflow-y:auto;padding-right:6px;margin-bottom:18px}#parenttab-modal .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:24px;row-gap:14px}#parenttab-modal .form-row{display:flex;flex-direction:column}#parenttab-modal .form-row-full{grid-column:1 / -1}#parenttab-modal .form-row label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}#parenttab-modal .form-row small{display:block;font-size:11px;color:#9ca3af;margin-top:3px}#parenttab-modal .form-control{width:100%;padding:9px 13px;border-radius:10px;border:1px solid #e5e7eb;font-size:13px;outline:none;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease,transform .05s ease;background:#f9fafb}#parenttab-modal .form-control:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 1px #2563eb40 0 0 0 4px #2563eb1a;transform:translateY(-1px)}#parenttab-modal .checkbox-row{display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-top:4px}#parenttab-modal .checkbox-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}#parenttab-modal .simple-modal-footer{display:flex;justify-content:flex-end;gap:10px}#parenttab-modal .uploaded-file a{color:#2563eb;text-decoration:underline}
</style>

<div id="parenttab-modal" class="simple-modal asds">
    <div class="simple-modal-backdrop" data-close-parenttab-modal></div>
    <div class="simple-modal-dialog">
        <div class="simple-modal-header">
            <span id="parenttab-modal-title">Add Parent Tab</span>
            <button type="button" class="simple-modal-close" data-close-parenttab-modal>&times;</button>
        </div>
        <div class="simple-modal-body">
            <input type="hidden" id="parenttab-id">

            <div class="form-grid">
                <div class="form-row">
                    <label>Parent Tab Label</label>
                    <input type="text" id="parenttab-label" class="form-control" placeholder="Group name, e.g. Sales">
                </div>
                <div class="form-row">
                    <label>Sequence</label>
                    <input type="number" id="parenttab-sequence" class="form-control" placeholder="Order in parent list" disabled>
                    <small>Lower number appears first.</small>
                </div>
                <div class="form-row">
                    <label>Icon </label>
                    <input type="file" id="parenttab-icon-file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.gif">
                    <small>If left empty, existing icon will stay unchanged.</small>
                    <div id="parenttab-uploaded-file" class="uploaded-file" style="margin-top:4px;font-size:12px;"></div>
                </div>
                <div class="form-row">
                    <label class="checkbox-row">
                        <input type="checkbox" id="parenttab-visible">
                        <span>Visible (checked = show)</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="simple-modal-footer">
            <button type="button" class="add-btn" id="save-parenttab-btn">Save</button>
            <button type="button" class="fields-btn" data-close-parenttab-modal>Cancel</button>
        </div>
    </div>
</div>