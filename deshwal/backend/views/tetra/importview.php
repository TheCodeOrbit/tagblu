<style>
    .stepwizard-step p {
        margin-top: 10px;
    }

    .stepwizard {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .stepwizard-step {
        text-align: center;
        flex: 1;
    }

    .stepwizard-step .btn {
        border-radius: 0;
    }

    .stepwizard-step .btn.active {
        background-color: #007bff;
        color: white;
    }

    .stepwizard-step .btn.disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
    }

    .header-title {
        /* font-size: 1 rem; */
        font-weight: 600;
    }

    .closebtn {
        font-size: 1.5rem;
        font-weight: 600;
        color: red;
        cursor: pointer;
    }

    .wizard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        gap: 10px;
    }

    .form-row label {
        min-width: 150px;
        font-weight: 500;
    }

    .form-row input[type="text"],
    .form-row input[type="file"] {
        flex: 1;
        padding: 6px;
    }

    .delimiter-options label {
        margin-right: 15px;
        font-weight: normal;
    }
</style>
<?php
$columns = $uitypes = $picklistValues = $sampleData = $vendorextracols = [];
// echo "<pre>";print_r($DataImport);die;
foreach ($DataImport as $keval) {

    if ($keval['mandatory'] == 1) {
        $columns[] = $keval['fieldlabel'] . ' * (mandatory)';
    } else {
        $columns[] = $keval['fieldlabel'];
    }
    $uitypes[] = $keval['uitype'];
   if ($keval['uitype'] == 8 || $keval['uitype'] == 22) {
        // get target table from picklist
        $targetTable = Yii::$app->db
            ->createCommand("SELECT * FROM picklist WHERE fieldid=:fieldid")
            ->bindValue(':fieldid', $keval['fieldid'])
            ->queryOne();
        // echo "<pre>";print_r($targetTable);die;
        $excludetable =['user','city','state','country','contacts'];
        if ($targetTable) {
            if(!in_array($targetTable['targettable'],$excludetable)){
                $values = Yii::$app->db
                    ->createCommand("SELECT {$targetTable['dispfield']} FROM {$targetTable['targettable']} WHERE is_active = 1")
                    ->queryColumn();

                // Wrap each value in double quotes safely
                $values = array_map(function($v) {
                    return '"' . $v . '"';
                }, $values);               
                
                $sampleData[$keval['fieldlabel']] = implode("||", $values);
            } else if($targetTable['targettable'] == "user" || $targetTable['targettable'] == "contacts")
            {
                $sampleData[$keval['fieldlabel']] = "John Doe (Firstname + Lastname)";
            }
            else {
                $sampleData[$keval['fieldlabel']] = "";
            }

        }
    }
}
// echo "<pre>";print_r($sampleData);die;
//for account module
    if($TabId == 18)
    {
        // $columns[] = 'CX Manager';
        $vaos = 'vendor_account_orgaisation_section';
        $vaossql = "select * from role where showinaccounts=1 and (parentrole like '%H2::%' || parentrole like '%H57::%')";
    
        $vaomd = 'vendor_account_oem_manager_detail';
        $vaomdsql = "select * from role where showinaccounts=1 and (parentrole not like '%H2::%' and parentrole not like '%H57::%')";
        

        $vaosresult = Yii::$app->db->createCommand($vaossql)->queryAll();
        $vaomdresult = Yii::$app->db->createCommand($vaomdsql)->queryAll();
        
        $vaoscount = count($vaosresult);
        // echo $count;die;
        foreach ($vaosresult as $vaosvalue) {
            $columns[] =  $vaosvalue['rolename'];
            $vendorextracols[] = $vaosvalue['rolename'];
        }
        $vaomdcount = count($vaomdresult);
        // echo $count;die;
        foreach ($vaomdresult as $vaomdvalue) {
            $columns[] =  $vaomdvalue['rolename'];
            $vendorextracols[] = $vaomdvalue['rolename'];
        }
            
    }
    //end for account module
// echo "<pre>";print_r($columns);die;

?>
<div class="container" style="position: relative;">
    <!-- Wizard Header -->
    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
        <h5 class="mb-0">Import <?= $TabLabel; ?></h5>
        <button class="btn btn-sm btn-light" id="closeWizard">×</button>
    </div>

    <!-- Step Navigation -->
    <div class="d-flex justify-content-around text-center my-4">
        <div class="stepwizard-step">
            <span class="step-label active fw-bold">
                <span class="step-number bg-primary text-white rounded-circle d-inline-block px-3 py-2">1</span><br />
                Upload CSV File
            </span>
        </div>
        <div class="stepwizard-step">
            <span class="step-label text-muted">
                <span class="step-number rounded-circle border d-inline-block px-3 py-2">2</span><br />
                Duplicate Handling
            </span>
        </div>
        <div class="stepwizard-step">
            <span class="step-label text-muted">
                <span class="step-number rounded-circle border d-inline-block px-3 py-2">3</span><br />
                Field Mapping
            </span>
        </div>
    </div>

    <!-- Step 1 -->
    <div class="step-content active" id="step-1">
        <div class="row mb-3 align-items-center">
            
        </div>
        <div class="row mb-3 align-items-center">
            <div class="col-md-2">
                <label for="csvfile" class="form-label">Select CSV file</label>
            </div>
            <div class="col-md-4">
                <input type="file" id="csvfile" name="csvfile" class="form-control">
            </div>
            <div class="col-md-4">
                <?php $samplelink = Yii::$app->urlManager->baseUrl.'/thememain/samples/bulk_upload_sample_for_'.$ModuleName.'.csv';?>
                <label for="csvfile" class="form-label fs-5 pt-2">
                    <a href="javascript:void(0);" id='download_csv_btn'>Download Sample File</a>
                     <!-- <button type="button" id="download_csv_btn">Download CSV Format</button> -->
                </label>
                <input type="hidden" name="tablename" value="<?php
                    foreach ($DataImport as $keval) {
                        $tablename = $keval['tablename'];
                    }
                    echo $tablename;
                    ?>" id="tablename" >
                    
            <!-- Pass values to JS -->
            <input type="hidden" id="columns" value='<?= json_encode($columns) ?>'>
            <input type="hidden" id="uitypes" value='<?= json_encode($uitypes) ?>'>
            <input type="hidden" id="picklist_data" value='<?= json_encode($sampleData) ?>'>            
            <input type="hidden" id="vendorextracols" value='<?= json_encode($vendorextracols) ?>'>

            </div>
        </div>

        <!-- <div class="row mb-3 align-items-center">
            <div class="col-md-2">
                <label for="hasheader" class="form-label">Has Header</label>
            </div>
            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox" id="hasheader" name="hasheader" checked class="form-check-input">
                </div>
            </div>
        </div> -->

        <!-- <div class="row mb-3 align-items-center">
            <div class="col-md-2">
                <label for="encoding" class="form-label">Character Encoding</label>
            </div>
            <div class="col-md-4">
                <input type="text" id="encoding" name="encoding" value="UTF-8" class="form-control">
            </div>
        </div> -->

        <!--<div class="row mb-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label">Delimiter</label>
            </div>
            <div class="col-md-6">
                <div class="form-check form-check-inline">
                    <input type="radio" id="comma" name="delimiter" value="comma" checked class="form-check-input">
                    <label for="comma" class="form-check-label">Comma</label>
                </div>
                 <div class="form-check form-check-inline">
                    <input type="radio" id="semicolon" name="delimiter" value="semicolon" class="form-check-input">
                    <label for="semicolon" class="form-check-label">Semicolon</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" id="pipe" name="delimiter" value="pipe" class="form-check-input">
                    <label for="pipe" class="form-check-label">Pipe</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" id="caret" name="delimiter" value="caret" class="form-check-input">
                    <label for="caret" class="form-check-label">Caret</label>
                </div> 
            </div>
        </div>-->

        <div class="d-flex justify-content-start gap-2">
            <button class="btn btn-primary" id="next-1">Next</button>
            <button class="btn btn-link text-danger" id="cancelWizard">Cancel</button>
        </div>
    </div>

    <!-- Step 2 -->
    <div class="step-content d-none" id="step-2">
        <div class="row mb-3 align-items-center">
            <div class="col-md-4">
                <label for="duplicateAction" class="form-label">Duplicate Handling</label>
            </div>
            <div class="col-md-8">
                <select id="duplicateAction" name="duplicateAction" class="form-select">
                    <option value="skip">Skip</option>
                    <option value="Overwrite">Overwrite</option>
                    <!--<option value="merge">Merge</option> -->
                </select>
                <span class="overwrite-msg text-red" ></span>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-5">
                        <label>Available Fields</label>
                        <!-- <select id="availableFields" multiple size="10" class="form-control"> -->
                        <select id="availableFields" size="10" class="form-control">
                        <?php foreach ($allFields as $field): ?>
                            <?php if (isset($field['fieldid'])): ?>
                                <option data-mandatory="<?= $field['mandatory'] ?>" data-id="<?= $field['fieldid'] ?>" value="<?= $field['fieldname'] ?>">
                                    <?= $field['fieldlabel'] ?>
                                </option>
                            <?php elseif (isset($field['roleid'])): ?>
                                <option data-id="<?= $field['roleid'] ?>"  value="<?= $field['roleid'] ?>">
                                    <?=  $field['rolename'] ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 text-center" style="margin-top:40px;">
                        <!-- <button type="button" class="btn btn-primary btn-block" id="addBtn">→</button>
                        <button type="button" class="btn btn-info btn-block mt-2" id="removeBtn">←</button> -->
                    </div>

                    <div class="col-md-5">
                        <label>Selected Fields</label>
                        <!-- <select id="selectedFields" multiple size="10" class="form-control" name="selectedFields[]"></select> -->
                         <select id="selectedFields" size="10" class="form-control" name="selectedFields[]"></select>
                        <input type="hidden" name="selectedfieldsid" id="selectedfieldsid">
                        <div id="fieldError" style="color:red;display:none;margin-top:5px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-start gap-2">
            <button class="btn btn-primary" id="next-2">Next</button>
            <button class="btn btn-secondary" id="prev-2">Back</button>
        </div>
    </div>

    <!-- Step 3 -->
    <div class="step-content d-none" id="step-3">
        <div class="mb-3">
            <div><b>Note</b> : * marks fields are Manadatory. </div>
            <div class="mb-3" id="mappingArea"></div>
        </div>
    </div>
</div>
