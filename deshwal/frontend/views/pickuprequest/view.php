<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = "Pickup Request Detail";
?>

<div class="container">
    <h4 class="mt-2"><?= Html::encode($this->title) ?></h4>
    <div class="d-flex justify-content-end">
        <?php if($pickupRequestData["status"] ==1 || empty($pickupRequestData["status"])){ ?>
            <?= Html::a('Update', ['update', 'pickup_request_id' => $model->pickup_request_id], ['class' => 'btn btn-primary btn-sm']) ?>
       <?php } ?>
    </div>
    
    <div class="row">
        <div class="col-12 title-tab mt-2">
            <label class="title-info">COLLECTION ADDRESS</label>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Pickup Request ID</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["pickup_request"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Location</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["location"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Address</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["address"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Country</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["country"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">City</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["city"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">State</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["state"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">PIN</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["pincode"]??"" ?>
                </div>
            </div>
        </div>
        <!-- hide 2.	Please HIDE the below fields in Pickup Requested Form:-
a.	SPOC Name
b.	SPOC number
c.	SPOC Email
d.	Escalation Name
e.	Escalation Number
as per 24 june 2025 client mail -->
        <!-- <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">SPOC Name</div>
                <div class="field-value col-8 ms-3">
                    <?php //echo $pickupRequestData["spoc_name"]??"" ?>
                </div>
            </div>
        </div> -->
        <!-- <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">SPOC Mobile</div>
                <div class="field-value col-8 ms-3">
                    <?php //echo $pickupRequestData["spoc_number"]??"" ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">SPOC Email</div>
                <div class="field-value col-8 ms-3">
                    <?php //echo $pickupRequestData["spoc_email"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Escalation Name</div>
                <div class="field-value col-8 ms-3">
                    <?php //echo $pickupRequestData["escalation_name"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Escalation Mobile</div>
                <div class="field-value col-8 ms-3">
                    <?php //echo $pickupRequestData["escalation_number"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Escalation Email</div>
                <div class="field-value col-8 ms-3">
                    <?php //echo $pickupRequestData["escalation_email"]??"" ?>
                </div>
            </div>
        </div> -->
            <!-- end client changes 24 june 2025 -->

        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Sender Email And Phone</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["sender_email_phone"]??"" ?>
                </div>
            </div>
        </div>


        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Alternate Name</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["alternate_name"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Alternate Email</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["alternate_email"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Alternate Mobile</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["alternate_mobile"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Add to the Permanent Data</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["add_to_permanent_data"]==1?"Yes":"No"; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Pickup Request Status</div>
                <div class="field-value col-8 ms-3">
                    <?php
                     if($pickupRequestData['status_value'] != '1')
                        $pickupRequestData['status_value'] = $pickupRequestData["sourcingdeal_stage"];
                    echo $pickupRequestData["status_value"]??""; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Pickup Request Assigned to</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["assigned_to"]??""; ?>
                </div>
            </div>
        </div>
        <div class="col-12 title-tab mt-2">
            <label class="title-info">PICKUP REQUEST STATUS HISTORY</label>
        </div>
        <?php if(!empty($history)){ ?>
        <div class="">
            <div class="history-section">
                <!-- <div class="activity-1">History</div> -->
                <div class="timeline-1w">
                    <?php foreach($history as $hist){?>
                        <div class="timeline-event">
                            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                            <div class="timeline-details">
                                <p> <?php echo $hist["created_by"]??"";?></p>
                                <p>
                                    <strong>Pickup Request Status:</strong> <?php echo $hist["status"]??"";?>
                                </p>
                                <p><?php echo $hist["created_on"]??"";?></p>
                            </div>
                        </div>
                    <?php }?>                                                                                
                </div>
            </div>          
        </div>
        <?php } ?>
        <div class="col-12 title-tab mt-2">
            <label class="title-info">PICKUP INSTRUCTIONS</label>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Agreed / Requested Collection Date</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["preferred_pickup_date"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Safety Info</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["additional_info"]??"" ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Pickup Document Required</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["pickup_document"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Upload Contract Transit Document</div>
                <div class="field-value col-8 ms-3">
                    <?php if($pickupRequestData["doc_received"]){ 
                        echo Html::a($pickupRequestData["doc_received"], ['pickuprequest/download','id' => $pickupRequestData["pickup_request_id"]],['target' => '_blank']);
                        ?>
                        <!-- <a href="<?php echo Yii::getAlias('@web/uploads/') . $pickupRequestData["doc_received"];?>" target='_blank'>
                          <?php echo $pickupRequestData["doc_received"];?>  
                        </a> -->
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-12 title-tab mt-2">
            <label class="title-info">COLLECTION DETAILS</label>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">What are the working timings?</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["working_timings"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Do we have any provision to extend the timings</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["extend_time_provision"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">What is the procedure to inform/update regarding extention</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["extension_provision"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">What are the formalities for entry personnel</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["entry_formalities_person"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Material lying at which location/Floor</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["material_location_floor"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">At which floor all the material is stored?</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["material_floor"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Please share the floor Number with material count</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["floor_num_material_count"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">What are the lift timings?</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["lift_timing"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Does stairs has sufficient space from where we can move the the material out from the premises?</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["stairs_space"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">How we can move the mateiral out</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["material_move"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">All items are segerated or Segregation require</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["segregation"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Do we have space availbale for this segregation</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["space_for_segregation"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">What is the material movement from premises?</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["movement_from_premises"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Distance between material and vehicle parked</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["distance"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Please share the basement floor / number from where we need to take out the mateiral</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["floor_num_for_take_out"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Do facility has sufficient space that vehicle can go inside the basement to pick the material</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["space_for_vehicle"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Do we require small vehicle for this movement</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["small_vehicle"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Please share the vehicle Name/Size which is allowed as per height</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["vehicle_as_per_height"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Please share how we can move the material from basement to Ground floor where vehicle parked?</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["material_from_basement_to_grnd"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">What are the formalities for vehicle entry</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["vehicle_entry_formalities"]??"" ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex">
                <div class="field-lable col-4">Vehicle can parked inside the premises</div>
                <div class="field-value col-8 ms-3">
                    <?php echo $pickupRequestData["vehicle_inside_premises"]??"" ?>
                </div>
            </div>
        </div>

        <div class="col-12 title-tab mt-2">
            <label class="title-info">ITEMS FOR COLLECTION</label>
        </div>
        <div class="table-container mt-1">
            <table class="table table-hover custom-table-border">
                <thead>
                    <tr>
                        <th>Product Name </th>
                        <th>Other Product Name </th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Qty</th>
                        <th>Serial No</th>
                        <th>Processor</th>
                        <th>RAM</th>
                        <th>HDD / SDD</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pickupItemsTransformed as $item): ?>
                        <tr>
                            <td>
                                <?= $item["product_name_value"]??""; ?>
                            </td>
                            <td>
                                <?= $item["other_product_name"]??""; ?>
                            </td>
                            <td>
                                <?= $item["make"]??""; ?>
                            </td>
                            <td>
                                <?= $item["model"]??""; ?>
                            </td>
                            <td>
                                <?= $item["total_quantity"]??""; ?>
                            </td>
                            <td>
                                <?= $item["serial_no"]??""; ?>
                            </td>
                            <td>
                                <?= $item["processor"]??""; ?>
                            </td>
                            <td>
                                <?= $item["ram"]??""; ?>
                            </td>
                            <td>
                                <?= $item["hdd_sdd"]??""; ?>
                            </td>
                            <td>
                                <?= $item["remarks"]??""; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-12 title-tab mt-2">
            <label class="title-info">TERMS AND CONDITIONS</label>
        </div>
        <div class="col mb-3">
            <div class="d-flex">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckCheckedDisabled" <?php echo $pickupRequestData["terms_and_condition"]?" checked ":" " ?> disabled>
                    <label class="form-check-label" for="flexCheckCheckedDisabled">
                        Count me in! I'd like to receive insights into extending the lifecycle of electronics in data centers and businesses.
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>