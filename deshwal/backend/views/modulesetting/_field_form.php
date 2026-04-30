<?php

use yii\bootstrap5\Html;

?>
<style>
   .field-flags-grid{grid-column:1 / -1;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 20px;margin-top:6px}.field-flag{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:#f3f4f6;font-size:13px;color:#374151;cursor:pointer;border:1px solid transparent;transition:background .15s,border-color .15s,box-shadow .15s,transform .05s}.field-flag:hover{background:#e5edff;border-color:#2563eb;box-shadow:0 2px 6px #2563eb40;transform:translateY(-1px)}.field-flag input[type="checkbox"]{width:15px;height:15px;accent-color:#2563eb;cursor:pointer}@media (max-width: 768px){.field-flags-grid{grid-template-columns:1fr}}
</style>
<div id="fieldModal" class="field-modal">
    <div class="field-modal-backdrop"></div>

    <div class="field-modal-content">
        <div class="field-modal-header">
            <h3>Save Field Properties</h3>
            <span id="closeFieldModal" class="field-modal-close">&times;</span>
        </div>

        <div class="field-modal-body">
            <form id="fieldEditForm">
                <input type="hidden" id="modal_fieldid">

                <div class="flex-form">
                    <div class="field-column">
                        <label style="display: none;">Column Name</label>
                        <input style="display: none;" type="text" id="modal_columnname" required>

                        <label style="display: none;">Table Name</label>
                        <input style="display: none;" type="text" id="modal_tablename" required>

                        
                        <label>Field Label</label>
                        <input type="text" id="modal_fieldlabel">
                        <label>UI Type</label>
                        <select id="field-uitype" class="form-control" placeholder="Select Uitype">
                            <?php foreach ($uiTypes as $ui): ?>
                                <option value="<?= Html::encode($ui['uitype']) ?>">
                                    <?= Html::encode($ui['fieldtype']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label>Block</label>
                        <select id="field-block" class="form-control" placeholder="Select Block Type">
                            <?php if (isset($blockList) && !empty($blockList)) {
                                foreach ($blockList as $bid => $blabel): ?>
                                    <option value="<?= (int)$bid ?>"><?= Html::encode($blabel) ?></option>
                            <?php endforeach;
                            } ?>
                        </select>
                        <label>Display Type</label>
                        <select id="modal_displaytype" class="form-control"placeholder="Select Display Type">
                            <option value="1">Editable</option>
                            <option value="2">Visible and readonly</option>
                            <option value="3">Hidden</option>
                            <option value="4">Listview only</option>
                        </select>
                    </div>

                    <div class="field-column">
                        <label style="display: none;">Field Name</label>
                        <input style="display: none;" type="text" id="modal_fieldname">

                        <label>Maximum Length</label>
                        <input type="number" id="modal_maximumlength">
                        <label style="display: none;">Validator Name</label>
                        <input style="display: none;" type="text" id="modal_validator_name">

                        <label style="display: none;">Default Value</label>
                        <input style="display: none;" type="text" id="modal_defaultvalue">
                        <label>Dynamic Class</label>
                        <select id="modal_dynamic_class" class="form-control" placeholder="Select Dynamic Class">
                            <?php $isFirst = true; ?>
                            <?php foreach ($dynamicClass as $dd): ?>
                                <option value="<?= Html::encode($dd['code']) ?>" <?= $isFirst ? 'selected' : '' ?>>
                                    <?= Html::encode($dd['code']) ?>
                                </option>
                                <?php $isFirst = false; ?>
                            <?php endforeach; ?>
                        </select>

                        <div class="form-row">


                        </div>
                        <div class="form-row">
                            <label>Type of Data</label>

                            <select id="modal-typeofdata" class="form-control" placeholder="Select Data Type" disabled>
                                <?php foreach ($dataTypes as $dt): ?>
                                    <option value="<?= Html::encode($dt['code']) ?>">
                                        <?= Html::encode($dt['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label>Description</label>
                        <textarea id="modal_description"></textarea>
                    </div>
                </div>

                <div class="field-flags-grid">
                    <label class="field-flag">
                        <input type="checkbox" id="field-readonly">
                        <span>Read-only</span>
                    </label>
                    <label class="field-flag" style="display:none;">
                        <input type="checkbox" id="field-presence">
                        <span>Presence</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-mandatory">
                        <span>Mandatory</span>
                    </label>
                    <label class="field-flag" style="display:none;">
                        <input type="checkbox" id="field-masseditable">
                        <span>Mass editable</span>
                    </label>
                    <label class="field-flag" style="display:none;">
                        <input type="checkbox" id="field-summaryfield">
                        <span>Summary field</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-list-view">
                        <span>List view</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-edit-view">
                        <span>Edit view</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-create-view">
                        <span>Create view</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-detail-view">
                        <span>Detail view</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-kanban-view">
                        <span>Kanban view</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-header-view">
                        <span>Header view</span>
                    </label>
                    <label class="field-flag" style="display:none;">
                        <input type="checkbox" id="field-is-conditional">
                        <span>Is Conditional</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-is-singleedit">
                        <span>Is Single Edit</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-kanbanviewfield">
                        <span>Kanban View Field</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-export">
                        <span>Export</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-import">
                        <span>Import</span>
                    </label>
                    <label class="field-flag">
                        <input type="checkbox" id="field-admin_edit_allow">
                        <span>Admin Edit Allow</span>
                    </label>
                </div>

                <div class="field-modal-actions">
                    <button type="button" class="save-btn" id="fieldSaveBtn">Save</button>
                    <button type="button" class="cancel-btn" id="cancelModalBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

