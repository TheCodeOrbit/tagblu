<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php
if($model->additional_info && !is_array($model->additional_info)){
    $model->additional_info = explode(",",$model->additional_info);
}
if($model->pickup_document && !is_array($model->pickup_document)){
    $model->pickup_document = explode(",",$model->pickup_document);
}

?>
<div class="container">
    <div class="mt-3 pickup-request-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="form-group-container">
            <div class="form-group-title">COLLECTION ADDRESS</div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'location')->dropDownList(
                        $vendorLocations, 
                        ['prompt' => '---Select---', 'class' => 'form-select']
                    )->label('Location <span class="draft-mandatory-asterisk">*</span>', ['encode' => false]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
            

            
                <div class="col-6">
                    <?= $form->field($model, 'city')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'state')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
           
                <div class="col-6">
                    <?= $form->field($model, 'country')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'pincode')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
            
            <!-- hide 2.	Please HIDE the below fields in Pickup Requested Form:-
            a.	SPOC Name
            b.	SPOC number
            c.	SPOC Email
            d.	Escalation Name
            e.	Escalation Number
            as per 24 june 2025 client mail -->
            <!-- <div class="row mt-2">
                <div class="col">
                    < $form->field($model, 'spoc_name')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
                <div class="col">
                    < $form->field($model, 'spoc_number')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
            </div> -->
            <!-- <div class="row mt-2">
                <div class="col">
                    < $form->field($model, 'spoc_email')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
                <div class="col">
                    < $form->field($model, 'escalation_name')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
            </div> -->
            <!-- <div class="row mt-2">
                <div class="col">
                    < $form->field($model, 'escalation_number')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
                <div class="col">
                    < $form->field($model, 'escalation_email')->textInput(['maxlength' => true,'class' => 'form-control readonly','readonly' => true]) ?>
                </div>
            </div> -->
            <!-- end client changes 24 june 2025 -->
            
                <div class="col-6">
                    <?= $form->field($model, 'alternate_name')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'alternate_email')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                </div>
            
                <div class="col-6">
                    <?= $form->field($model, 'alternate_mobile')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'add_to_permanent_data')->checkbox(['class' => 'form-controlxx']) ?>
                </div>
            </div>
        </div>
        <div class="form-group-margin-top">
            <div class="form-group-container">
                <div class="form-group-title">PICKUP INSTRUCTIONS</div>
                <div class="row">
                    <div class="col-6">
                        <?= $form->field($model, 'preferred_pickup_date')
                        ->input('date', ['value' => $model->preferred_pickup_date]) 
                        ->label('Agreed / Requested Collection Date <span class="submit-mandatory-asterisk">*</span>', ['encode' => false])
                        ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'additional_info')->dropDownList(
                            $additionalInfo, 
                            ['multiple' => true,'prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'pickup_document')->dropDownList(
                            $pickupDocumentType, 
                            ['multiple' => true,'prompt' => '---Select---','class' => 'form-select'])
                        ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'doc_received')->fileInput(['class' => 'form-control']
                        )->label('Upload Contract Transit Document <span class="small-font"> (Only jpg, png, pdf files are allowed, with a maximum size limit of 2MB)</span>', ['encode' => false]) ?>
                        <?php if($model->doc_received){ 
                            echo Html::a("Uploaded Document: ".$model->doc_received, ['pickuprequest/download','id' => $model->pickup_request_id],['target' => '_blank']);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group-margin-top">
            <div class="form-group-container">
                <div class="form-group-title">COLLECTION DETAILS</div>
                <div class="row">
                    <div class="col-6">
                        <?= $form->field($model, 'working_timings')->dropDownList(
                            $workingTimingsOptions, 
                            ['prompt' => '---Select---','class' => 'form-select',
                            'data-bs-toggle' => 'tooltip',
                            'data-bs-placement' => 'top',
                            'title' => 'What formaliites we need to follow for the entry of Labour and Field Engineer']
                        )->label('What are the working timings? <span class="submit-mandatory-asterisk">*</span>', ['encode' => false]) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'extend_time_provision')->dropDownList(
                            $provisionToExtendTiming, 
                            ['prompt' => '---Select---','class' => 'form-select',
                            'data-bs-toggle' => 'tooltip',
                            'data-bs-placement' => 'top',
                            'title' => 'Material pickup location at the premises']
                        ) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'extension_provision')->dropDownList(
                            $extensionProvisionOptions, 
                            ['prompt' => '---Select---','class' => 'form-select',
                            'data-bs-toggle' => 'tooltip',
                            'data-bs-placement' => 'top',
                            'title' => 'Floor location/Number']
                        ) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'entry_formalities_person')->dropDownList(
                            $entryFormalitiesPersonOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        )->label('What are the formalities for entry personnel <span class="submit-mandatory-asterisk">*</span>', ['encode' => false]) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'material_location_floor')->dropDownList(
                            $materialLocationFloorOptiond, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'material_floor')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'floor_num_material_count')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'service_lift')->dropDownList(
                            $serviceLiftOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                
                    <div class="col-6 mt-4">
                        <?= $form->field($model, 'lift_timing')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'stairs_space')->dropDownList(
                            $stairsSpaceOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'material_move')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'segregation')->dropDownList(
                            $segregationOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'space_for_segregation')->dropDownList(
                            $spaceForSegregationOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'movement_from_premises')->dropDownList(
                            $movementFromPremisesOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                
                    <div class="col-6 mt-4">
                        <?= $form->field($model, 'distance')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'floor_num_for_take_out')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                
                    <div class="col-6">
                        <?= $form->field($model, 'space_for_vehicle')->dropDownList(
                            $spaceForVehicleOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                    <div class="col-6 mt-4">
                        <?= $form->field($model, 'small_vehicle')->dropDownList(
                            $smallVehicleOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                
                    <div class="col-6 mt-4">
                        <?= $form->field($model, 'vehicle_as_per_height')->dropDownList(
                            $vehicleAsPerHeightOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'material_from_basement_to_grnd')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                    </div>
                   <div class="col-6">
                        <?= $form->field($model, 'vehicle_entry_formalities')->dropDownList(
                            $vehicleEntryFormalitiesOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        )->label('What are the formalities for vehicle entry <span class="submit-mandatory-asterisk">*</span>', ['encode' => false]) ?>
                    </div>
                    <div class="col-6">
                        <?= $form->field($model, 'vehicle_inside_premises')->dropDownList(
                            $vehicleInsidePremisesOptions, 
                            ['prompt' => '---Select---','class' => 'form-select']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group-margin-top">
            <div class="form-group-container">
                <div class="form-group-title">ITEMS FOR COLLECTION</div>
                <div class="col-12 mt-1">
                    <div class="table-container mt-1">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table-border products-table">
                                <thead>
                                    <tr>
                                        <th>Product Name <span class="ms-2 submit-mandatory-asterisk">*</span></th>
                                        <th>Other Product Name </th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th class="reduced-elem-width">Qty<span class="ms-2 submit-mandatory-asterisk">*</span></th>
                                        <th>Serial No</th>
                                        <th class="reduced-elem-width">Processor</th>
                                        <th class="reduced-elem-width">RAM</th>
                                        <th class="reduced-elem-width">HDD / SDD</th>
                                        <th>Remarks</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="container-items">
                                    <?php foreach ($pickupItems as $i => $pickupItem): ?>
                                        <tr class="item">
                                            <td>
                                                <?= $form->field($pickupItem, "[{$i}]product_name", ['template' => '{input}{error}'])->dropDownList($products_list, ['prompt' => '---Select---','class' => 'form-select']) ?>
                                                <!-- <?= $form->field($pickupItem, "[{$i}]product_name", ['template' => '{input}{error}'])->textarea(['maxlength' => true]) ?> -->
                                            </td>
                                            <td>
                                                <?= $form->field($pickupItem, "[{$i}]other_product_name", ['template' => '{input}{error}'])->textarea(
                                                    [
                                                        'maxlength' => true,
                                                        'class' => 'auto-expand-textarea form-control',
                                                        'rows' => 1
                                                    ]) ?>
                                            </td>
                                            <td>
                                                <?= $form->field($pickupItem, "[{$i}]make", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control','rows' => 1]) ?>
                                            </td>
                                            <td>
                                                <?= $form->field($pickupItem, "[{$i}]model", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control','rows' => 1]) ?>
                                            </td>
                                            <td class="reduced-elem-width">
                                                <?= $form->field($pickupItem, "[{$i}]total_quantity", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control reduced-input-width','rows' => 1]) ?>
                                            </td>
                                            <td>
                                                <?= $form->field($pickupItem, "[{$i}]serial_no", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control','rows' => 1]) ?>
                                            </td>

                                            <td class="reduced-elem-width">
                                                <?= $form->field($pickupItem, "[{$i}]processor", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control reduced-input-width','rows' => 1]) ?>
                                            </td>
                                            <td class="reduced-elem-width">
                                                <?= $form->field($pickupItem, "[{$i}]ram", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control reduced-input-width','rows' => 1]) ?>
                                            </td>
                                            <td class="reduced-elem-width">
                                                <?= $form->field($pickupItem, "[{$i}]hdd_sdd", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control reduced-input-width','rows' => 1]) ?>
                                            </td>
                                            <td>
                                                <?= $form->field($pickupItem, "[{$i}]remarks", ['template' => '{input}{error}'])->textarea(['maxlength' => true,'class' => 'auto-expand-textarea form-control','rows' => 1]) ?>
                                            </td>
                                            <td>
                                                <button type="button" class="remove-item btn btn-danger btn-sm">Remove</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-1">
                            <p>
                                Add more items using
                                <input type="radio" class="btn-check" name="add_more_items_option" id="web-input" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm web-input-lb" for="web-input">Web Input</label>
                                <input type="radio" class="btn-check" name="add_more_items_option" id="file-upload-input" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm file-upload-input-lb" for="file-upload-input">File Upload</label>
                            </p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <div class="web-input-option">
                                <button type="button" class="btn btn-primary btn-sm add-item ms-2">Add</button>
                            </div>
                            <div class="file-input-option">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-column">
                                        <input class="" type="file" id="csvFileInput" accept=".csv, .xls, .xlsx">
                                        <div class="small-font">Only CSV or Excel files are allowed, with a maximum size limit of 10MB</div>
                                    </div>
                                    <button type="button" id="addFromCSV" class="btn btn-info btn-sm ms-2">Upload Items</button>
                                    <?php echo Html::a('Download Sample File', ['pickuprequest/sample'], ['class' => 'btn btn-sm ms-1','target'=>"_blank"]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group-margin-top">
            <div class="form-group-container">
                <div class="form-group-title">TERMS AND CONDITIONS</div>
                <div class="row mt-2">
                    <div class="col">
                        <?= $form->field($model, 'terms_and_condition')
                        ->checkbox(['class' => 'form-controlxx'])
                        ->label("<span class='submit-mandatory-asterisk'>*</span>", ['encode' => false])  ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-2">
            <!-- <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary btn-sm']) ?> -->
            <?= Html::submitButton('Save as Draft', ['class' => 'btn btn-primary btn-sm','name' => 'action', 'value' => 'draft']) ?>
            <?= Html::submitButton('Submit for Pickup', ['class' => 'btn btn-primary btn-sm ms-2 submit-button','name' => 'action', 'value' => 'submit']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="d-flex justify-content-center mt-2">
        <p>Note: <span class="ms-2 draft-mandatory-asterisk">*</span> Mandatory for draft,   <span class="ms-2 submit-mandatory-asterisk">*</span> Mandatory for submit</p>
    </div>
</div>
