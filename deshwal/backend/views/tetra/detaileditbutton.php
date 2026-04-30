<?php

use app\models\Opportunity;
use app\models\PurchaseOrder;
use app\models\Quotes;
use app\models\QuotesDit;
use app\models\SalesOrder;
use app\models\Sourcingdeal;
$deal = new Sourcingdeal();
$quote = new Quotes();
$quotedit = new QuotesDit();
$po = new PurchaseOrder();
$oppor = new Opportunity();
$editButtonControl = 1;
if (isset($Edit) && $Edit === false)
    $editButtonControl = 0;
// if ($TabId == 7 && isset($Record['vertical_manager']) && $Record['vertical_manager'] == Yii::$app->user->id && isset($Record['leadstatus'])) {//old concet
if ($TabId == 24 && (isset($Record['pickup_status']) && $Record['pickup_status'] != 6) && $role == "H53") { ?>
    <button class="pickup-hold detail-view-btn-gen"><span class="">Pickup Hold by Client</span></button>
    <button class="pickup-hold-DWMPL detail-view-btn-gen"><span class="">Pickup Hold by DWMPL</span></button>
    <button class="pickup-cancelled detail-view-btn-gen"><span class="">Pickup Cancelled</span></button>
    <?php
}
if ($TabId == 7 && $Record['ownerid'] == Yii::$app->user->id && isset($Record['leadstatus']) && ($Record['leadstatus'] == 4 || $Record['leadstatus'] == 5)) {
    if ($Record['leadstatus'] == 4)//approval pending
    {
        ?>
        <div class="div-regroup">
            <button class="approve">
                <span class="">Approve</span></button>
            <!-- <button class="delegate" id="delegate">
                                        <span class="">Delegate</span>
                                    </button> -->
            <button class="modify" id="modify">
                <span class="">Modify</span>
            </button>
            <button class="reject" id="reject">
                <span class="">Reject</span>
            </button>


        </div>
        <?php
    }
    // else if ($Record['leadstatus'] == 5) // Disqualified
    // {
    ?>
    <!-- <div class="div-regroup">

                                        <button class="reactivate" id="reactivate">
                                            <span class="">Reactivate</span>
                                        </button>


                                    </div> -->
    <?php
    // }
} else if ($TabId == 24 && ($Record['pickup_status'] && (($Record['pickup_status'] == 10 && ($role == "H52" || $role == "H53")) || ($Record['pickup_status'] == 11 && $role == "H52")))) { ?>
        <div class="div-regroup">
            <button class="approve">
                <span class="">Approve</span></button>
            </button>
            <button class="reject-general" id="reject-general" data-bs-toggle="modal" data-bs-target="#reject-general-modal">
                <span class="">Reject</span>
            </button>
            <button class="modify" id="modify">
                <span class="">Modify</span>
            </button>
        </div>
    <?php
} else if ($TabId == 13 && ($Record['stage'] && (($Record['stage'] == 2 && ($role == "H19" || $role == "H63"))))) { ?>
            <div class="div-regroup">
                <a href="<?= Yii::$app->urlManager->createUrl(['/purchaseorder/generatepdf', 'Record' => $Recordid]) ?>"
                    target="_blank">
                    <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                </a>
                <button class="approve">
                    <span class="">Approve</span></button>
                </button>

                <button class="modify" id="modify">
                    <span class="">Modify</span>
                </button>
            </div>
    <?php
}
// if ($TabId == 13 && ($Record['stage']) && (($Record['stage'] == 2 && ($role != "H19" && $role != "H63")) || ($Record['stage'] == 3 ))) { 
else if ($TabId == 13 && (($Record['stage']) && ($Record['stage'] == 2 && ($role != "H19" && $role != "H63" && Yii::$app->user->id != 1 && $hasadminpower != 1)) || (isset($Record['stage']) && $Record['stage'] == 3 ))) {
                /**
             * change as per ERP Point 444 While creating the PO, till the time PO stage is not equal to Approved, we can see the draft PO in PDF form with back ground (Draft) watermark
             */
            // echo $role;die;
                ?>
                    <a href="<?= Yii::$app->urlManager->createUrl(['/purchaseorder/generatepdf', 'Record' => $Recordid]) ?>"
                        target="_blank">
                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                    </a>
            <?php
            } 
else if ($TabId == 51 && ($Record['stage'] && $Record['stage'] == 29 && $role == "H62")) {
    ?>
                <div class="div-regroup">
                    <button class="approve">
                        <span class="">Approve</span></button>
                    </button>
                    <button class="reject" id="reject">
                        <span class="">Reject</span>
                    </button>
                </div>
    <?php
} else if ($TabId == 65 && ($Record['stage'] && $Record['stage'] == 2 && $role == "H16")) {
    ?>
                    <div class="div-regroup">
                        <button class="approve">
                            <span class="">Approve</span></button>
                        </button>
                        <button class="reject" id="reject">
                            <span class="">Reject</span>
                        </button>
                    </div>
    <?php
} else if ($TabId == 65 && ($Record['stage'] && $Record['stage'] == 3 && $role == "H63")) {
    ?>
                        <div class="div-regroup">
                            <button class="approve">
                                <span class="">Approve</span></button>
                            </button>
                            <button class="reject" id="reject">
                                <span class="">Reject</span>
                            </button>
                        </div>
    <?php
//commneted on 16 dec 2025 for autoapproval of quotes dit
//} else if ($TabId == 72 && ($Record['quote_stage'] && $Record['quote_stage'] == 2 && $role == "H83")) {
    ?>
                            <!-- <div class="div-regroup">
                                <button class="approve">
                                    <span class="">Approve</span></button>
                                </button>
                                <button class="reject" id="reject">
                                    <span class="">Reject</span>
                                </button>
                            </div> -->
    <?php
// } else if ($TabId == 72 && ($Record['quote_stage'] && $Record['quote_stage'] == 3 && $role == "H84")) {
    ?>
                                <!-- <div class="div-regroup">
                                    <button class="approve">
                                        <span class="">Approve</span></button>
                                    </button>
                                    <button class="reject" id="reject">
                                        <span class="">Reject</span>
                                    </button>
                                </div> -->
<?php
//added on 16 dec 2025 for autoapproval of quotes dit
} else if ($TabId == 72 && isset($showapprove) && $showapprove == 1) { 
    ?>
      <div class="div-regroup">
                                    <button class="approve">
                                        <span class="">Approve</span></button>
                                    </button>
                                    <button class="reject" id="reject">
                                        <span class="">Reject</span>
                                    </button>
                                </div>
<?php
// } else if ($TabId == 74 && ($Record['so_stage'] && $Record['so_stage'] == 2 && $role == "H86")) {
// } else if ($TabId == 74 && ($Record['so_stage'] && $Record['so_stage'] == 2 && $role == "H106")) {//changed on 11 oct 2025
?>
<!-- //                                     <div class="div-regroup">
//                                         <button class="approve">
//                                             <span class="">Approve</span></button>
//                                         </button>
//                                         <button class="reject" id="reject">
//                                             <span class="">Reject</span>
//                                         </button>
//                                     </div> -->
<?php
// // } else if ($TabId == 74 && ($Record['so_stage'] && $Record['so_stage'] == 3 && $role == "H87")) {
// //change as per V11-point 17 changed by ptpatel on date 11-10-2025 H105 DevIT Finance Executive Approver
//     } else if ($TabId == 74 && ($Record['so_stage'] && $Record['so_stage'] == 3 && $role == "H105")) {
//     ?>
<!-- //                                         <div class="div-regroup">
//                                             <button class="approve">
//                                                 <span class="">Approve</span></button>
//                                             </button>
//                                             <button class="reject" id="reject">
//                                                 <span class="">Reject</span>
//                                             </button>
//                                         </div> -->
<?php
// }
} else if ($TabId == 74 && isset($showapprove) && $showapprove == 1) { //salesorderdit
  ?>
      <div class="div-regroup">
                                             <button class="approve">
                                                 <span class="">Approve</span></button>
                                             </button>
                                            <button class="reject" id="reject">
                                                 <span class="">Reject</span>
                                             </button>
                                        </div>
                                        <?php
}else if ($TabId == 78 && ($Record['stage'] && $Record['stage'] == 2 && $role == "H87")) {
    ?>
                                            <div class="div-regroup">
                                                <button class="approve">
                                                    <span class="">Approve</span></button>
                                                </button>
                                                <button class="reject" id="reject">
                                                    <span class="">Reject</span>
                                                </button>
                                            </div>
    <?php
} else if ($TabId == 78 && ($Record['stage'] && $Record['stage'] == '3' && $role == "H84")) {
   
    ?>
                                                <div class="div-regroup">
                                                    <button class="approve">
                                                        <span class="">Approve</span></button>
                                                    </button>
                                                    <button class="reject" id="reject">
                                                        <span class="">Reject</span>
                                                    </button>
                                                </div>
    <?php
} else if ($TabId == 24 && ($Record['pickup_status'] && $Record['pickup_status'] == 16 && $role == "H56" && Yii::$app->user->id == $Record['fe_name'])) { ?>
                                                    <div class="div-regroup">
                                                        <button class="pickup-start">
                                                            <span class="">Start Pickup</span></button>
                                                        </button>
                                                    </div>
    <?php
} else if ($TabId == 24 && ($Record['pickup_status'] && ($Record['pickup_status'] == 10 || $Record['pickup_status'] == 11 || $Record['pickup_status'] == 6 || $Record['pickup_status'] == 13 || $Record['pickup_status'] == 17))) {
    $Record['ownerid']='';//added on 31 dec 2025 to disallow edit in pickup module

} else if ($TabId == 12 && isset($ApproveContract) && $ApproveContract == 1) { //contract 
    ?>
                                                            <div class="div-regroup">
                                                                <button class="approve">
                                                                    <span class="">Approve</span></button>
                                                                </button>
                                                                <button class="reject" id="reject">
                                                                    <span class="">Reject</span>
                                                                </button>
                                                            </div>
    <?php
} else if ($TabId == 88 && isset($ApproveDeliverychallandit) && $ApproveDeliverychallandit == 1) { //deliverychallan dit 
    ?>
                                                                <div class="div-regroup">
                                                                    <button class="approve">
                                                                        <span class="">Approve</span></button>
                                                                    </button>
                                                                    <button class="reject" id="reject">
                                                                        <span class="">Reject</span>
                                                                    </button>
                                                                </div>
    <?php
} else if ($TabId == 91 && isset($ApproveFocdit) && $ApproveFocdit == 1) { //FOC dit 
    ?>
                                                                    <div class="div-regroup">
                                                                        <button class="approve">
                                                                            <span class="">Approve</span></button>
                                                                        </button>
                                                                        <button class="reject" id="reject">
                                                                            <span class="">Reject</span>
                                                                        </button>
                                                                    </div>
    <?php
} else if ($TabId == 87 && isset($ApproveInvoicedit) && $ApproveInvoicedit == 1) { //Invoicedit dit 
    ?>
                                                                        <div class="div-regroup">
                                                                            <button class="approve">
                                                                                <span class="">Approve</span></button>
                                                                            </button>
                                                                            <button class="reject" id="reject">
                                                                                <span class="">Reject</span>
                                                                            </button>
                                                                        </div>
    <?php
} else if ($TabId == 97 && isset($ApproveRaiseRequestByClient) && $ApproveRaiseRequestByClient == 1) { //Invoicedit dit 
    ?>
                                                                        <div class="div-regroup">
                                                                            <button class="approve">
                                                                                <span class="">Approve</span></button>
                                                                            </button>
                                                                            <button class="reject" id="reject">
                                                                                <span class="">Reject</span>
                                                                            </button>
                                                                        </div>
    <?php
} else if ($TabId == 96 && isset($ApproveRaiseRequestByVendor) && $ApproveRaiseRequestByVendor == 1) { //Invoicedit dit 
    ?>
                                                                        <div class="div-regroup">
                                                                            <button class="approve">
                                                                                <span class="">Approve</span></button>
                                                                            </button>
                                                                            <button class="reject" id="reject">
                                                                                <span class="">Reject</span>
                                                                            </button>
                                                                        </div>
    <?php
} 
if ($TabId == 14 && isset($ApproveSalesOrder) && $ApproveSalesOrder == 1) { //salesorder 
    ?>
                                                                <div class="div-regroup">
                                                                    <button class="approve">
                                                                        <span class="">Approve</span></button>
                                                                    </button>
                                                                    <button class="reject" id="reject">
                                                                        <span class="">Reject</span>
                                                                    </button>
                                                                </div>
    <?php
} 

else if ($Record['ownerid'] == Yii::$app->user->id || Yii::$app->user->id == 1 || $hasadminpower == 1 || $AllowEditGeneral === true) {
    ?>
                                                                            <div class="div-regroup">
            <?php
            if (isset($ApproveOpportuntiy) && $ApproveOpportuntiy) {
                ?>
                                                                                    <button class="approve">
                                                                                        <span class="">Approve</span></button>
            <?php
            }
            if (isset($RejectOpportunity) && $RejectOpportunity) {

                ?>
                                                                                    <button class="reject" id="reject">
                                                                                        <span class="">Reject</span>
                                                                                    </button>

            <?php
            }

            ?>
        <?php if (isset($ShowProductDetail) && !empty($ShowProductDetail)): ?>
                                                                                    <div class="div-regroup"><a href="<?= $ShowProductDetail ?>" target="_blank"><button
                                                                                                class="ShowProductDetail detail-view-btn-gen"><span class="">Product Detail</span></button></a></div>
        <?php endif ?>

        <?php if (isset($ShreddingClientSignature) && $ShreddingClientSignature === true): ?>
                                                                                    <div class="div-regroup"><button class="shredding-client-sign detail-view-btn-gen" data-bs-toggle="modal"
                                                                                            data-bs-target="#detail-view-general-info"><span class="">Client Signature</span></button></div>
        <?php endif ?>
        <?php if (isset($ShreddingCompletd) && $ShreddingCompletd === true): ?>
                                                                                    <div class="div-regroup"><button class="shredding-completed detail-view-btn-gen"><span class="">Shredding
                                                                                                Completed</span></button></div>
        <?php endif ?>
        <?php if (isset($DrillingClientSignature) && $DrillingClientSignature === true): ?>
                                                                                    <div class="div-regroup"><button class="drilling-client-sign detail-view-btn-gen" data-bs-toggle="modal"
                                                                                            data-bs-target="#detail-view-general-info"><span class="">Client Signature</span></button></div>
        <?php endif ?>
        <?php if (isset($DrillingCompletd) && $DrillingCompletd === true): ?>
                                                                                    <div class="div-regroup"><button class="drilling-completed detail-view-btn-gen"><span class="">Drilling
                                                                                                Completed</span></button></div>
        <?php endif ?>
        <?php if (isset($DataWipingClientSignature) && $DataWipingClientSignature === true): ?>
                                                                                    <div class="div-regroup"><button class="data-wiping-client-sign detail-view-btn-gen" data-bs-toggle="modal"
                                                                                            data-bs-target="#detail-view-general-info"><span class="">Client Signature</span></button></div>
        <?php endif ?>
        <?php if (isset($DataWipingCompletd) && $DataWipingCompletd === true): ?>
                                                                                    <div class="div-regroup"><button class="data-wiping-completed detail-view-btn-gen"><span class="">Wiping
                                                                                                Completed</span></button></div>
        <?php endif ?>
        <?php if (isset($DegaussingClientSignature) && $DegaussingClientSignature === true): ?>
                                                                                    <div class="div-regroup"><button class="degaussing-client-sign detail-view-btn-gen" data-bs-toggle="modal"
                                                                                            data-bs-target="#detail-view-general-info"><span class="">Client Signature</span></button></div>
        <?php endif ?>
        <?php if (isset($DegaussingCompletd) && $DegaussingCompletd === true): ?>
                                                                                    <div class="div-regroup"><button class="degaussing-completed detail-view-btn-gen"><span class="">Degaussing
                                                                                                Completed</span></button></div>
        <?php endif ?>

            <?php
            // if ($TabId == 42 && (($Record['quote_stage']) && ($Record['quote_stage'] == 1))) { //as per client change on date 20-06-25 code added by ptpatel
            if ($TabId == 42 && (($Record['quote_stage']) && ($Record['quote_stage'] != 4))) {
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/quotes/generatepdf', 'Record' => $Recordid]) ?>" target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a>
            <?php
            }

            // if ($TabId == 72 && (($Record['quote_stage']) && ($Record['quote_stage'] == 4))) { // commneted on 16 dec 2025 to show quotes devit pdf when quote stage != rejected
            if ($TabId == 72 && (($Record['quote_stage']) && ($Record['quote_stage'] == 4 || $Record['quote_stage'] == 1))) {
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/quotesdit/generatepdf', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a>
            <?php
            }
            if ($TabId == 78 && (($Record['stage']) && ($Record['stage'] == 4 || $Record['stage'] == 1))) {
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/purchaseorderdit/generatepdf', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a>
            <?php
            }
            if ($TabId == 74 && (($Record['so_stage']) && ($Record['so_stage'] == 4 || $Record['so_stage'] == 5 || $Record['so_stage'] == 6))) {
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/salesorderdit/generatepdf', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a>
            <?php
            }

            //if ($TabId == 13 && (($Record['stage']) && ($Record['stage'] == 3 ))) { // old code
            /**
             * change as per ERP Point 444 While creating the PO, till the time PO stage is not equal to Approved, we can see the draft PO in PDF form with back ground (Draft) watermark
             */
            // echo $role;die;
            // this code is commented to resolve two PDF button is showing before approval to deshwal ISR on date 19-02-2026 by ptpatel and merge code in before condition on line no 88
            // if ($TabId == 13 && ($Record['stage']) && (($Record['stage'] == 2 && ($role != "H19" && $role != "H63")) || ($Record['stage'] == 3 ))) {
                ?>
                                                                                    <!-- <a href="<?= Yii::$app->urlManager->createUrl(['/purchaseorder/generatepdf', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a> -->
            <?php
            // }
            if ($TabId == 88 && (($Record['status']) && ($Record['status'] == 3) && ($Record['delivery_challan_type'] != 4))) {
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/deliverychallandit/generatepdf', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a>
            <?php
            }
            if ($TabId == 87 && isset($AllowgeneratepdfInvoicedit) && $AllowgeneratepdfInvoicedit === true) {
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/invoicedit/generatepdf', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank">
                                                                                        <button class="detail-view-btn-gen gen-form6">Generate PDF</button>
                                                                                    </a>
            <?php
            }
            if ($TabId == 7 && ((isset($Record['leadstatus']) && $Record['converted'] == 0 && $Record['leadstatus'] != 9 && $Record['leadstatus'] != 14 && $Record['leadstatus'] != 15 && $Record['leadstatus'] != 16) || $Record['leadstatus'] == 3)) //show if not converted and if changes required
            { ?>
                                                                                    <button class="button-frame convert-btn"><span class="span-convert">Convert</span></button>
            <?php
                // echo $Record['leadstatus'];
            }
            if ($TabId == 24 && isset($AllowCertificateGeneration) && $AllowCertificateGeneration === true) { ?>
                                                                                    <button class="pickup-complete"><span class="">Pickup Completed</span></button>
            <?php
            }
            if ($TabId == 24 && isset($AllowFormSix) && $AllowFormSix === true) {
                $AllowFormSix = false; // so that it does not appear again for later conditon conflict
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/pickup/generateformsix', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank"><button class="detail-view-btn-gen gen-form6">Form 6</button></a>
        <?php }
            if ($TabId == 24 && isset($AllowFormTen) && $AllowFormTen === true) {
                $AllowFormTen = false; // so that it does not appear again for later conditon conflict
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/pickup/generateformten', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank"><button class="detail-view-btn-gen gen-form6">Form 10</button></a>
        <?php }
            if ($TabId == 24 && isset($AllowGreenCertificate) && $AllowGreenCertificate === true) {
                $AllowGreenCertificate = false; // so that it does not appear again for later conditon conflict
                ?>
                                                                                    <a href="<?= Yii::$app->urlManager->createUrl(['/pickup/generategreencert', 'Record' => $Recordid]) ?>"
                                                                                        target="_blank"><button class="detail-view-btn-gen gen-form6">Green Certificate</button></a>
        <?php }
            //if ($TabId == 24 && (isset($Record['pickup_status']) && $Record['pickup_status'] == 2) && $role == "H55") {
            if ($TabId == 24 && (isset($Record['pickup_status']) && $Record['pickup_status'] == 2) && ($role == "H25" || $role == "H50")) {//role shopuld deshwal isr or account manager added on 10 oct 2025 as per 8 cot 2025 changes ?> 
                                                                                    <button class="pickup-submit-for-logistic detail-view-btn-gen"><span class="">Submit For Logistic</span></button>
            <?php
            }
            if ($TabId == 24 && isset($SchedulePickup) && $SchedulePickup === true) { ?>
                                                                                    <button class="pickup-schedule detail-view-btn-gen"><span class="">Schedule Pickup</span></button>
            <?php
            }
            //Added by vishwas the conditon for won or quote_approve and no quote present alive 16-03-2026 vishwas
            if ($TabId == 8  && isset($Record['opportunity_stage']) && ($Record['opportunity_stage'] == 10 || $Record['opportunity_stage'] == 8) && !$oppor->showEditBtnOpportunity($Record,Yii::$app->user->identity)) { ?>
                <button class="move_to_prospect detail-view-btn-gen btn-danger"><span class="">Change Stage to Prospect</span></button>
            <?php }
            if ($layout == 'multiple' || $layout == 'single') {
                $sourcemodule = '';
                $sourceid = '';
                if (isset($_GET['sourceid']))
                    $sourceid = filter_var($_GET['sourceid'], FILTER_VALIDATE_INT);
                if (isset($_GET['sourcemodule']))
                    $sourcemodule = filter_var($_GET['sourcemodule'], FILTER_VALIDATE_INT);
                if (!empty($sourceid) && !empty($sourcemodule))
                    $urledit = "edit?Record=" . $Recordid . "&sourcemodule=" . $sourcemodule . "&sourceid=" . $sourceid;
                else {
                    $urledit = "edit?Record=" . $Recordid;
                }
                if ($TabId == 7) {
                    // echo $Record['leadstatus'];die;
                    if (($Record['leadstatus'] != '4' && $Record['leadstatus'] != '6') && $Record['leadstatus'] != '5' && $Record['leadstatus'] != '9') //can edit if converted also
                    { ?>

                                                                                            <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                                                                                                        class="span-edit">Edit</span>
                                                                                                </button></a>
                    <?php
                    }
                    //Added functionalithy For stage = 3 (Approve) and No payments against it so display the edit btn only to super admin 
                } else if ($TabId == 13 && (($Record['stage']) && ($Record['stage'] == 5 || ($Record['stage'] == 3 && $po->showEditBtnPO($Record,Yii::$app->user->identity) != null)))) {
                    //as per V11 sheet point 21 -change by ptpatel on date 10-10-2025
                    // if($hasadminpower == 1)
                    // {
                        ?>
                            <!-- <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                                                                                                        class="span-edit">Edit</span>
                                                                                                </button></a> -->
                        
                        <?php
                    // }
                    //as per V11 sheet point 21 -change by ptpatel on date 10-10-2025
                } else if ($TabId == 2 && (($Record['stages']) && ($Record['stages'] == 8))) {
    
                } else if ($TabId == 33) {
                } //inventory does not require edit button
                //condition for 1 (approved) added by ptpatel on date 05-04-25
                // 1 = approved if quote is in approved stage then don't show edit button
                // 4 = quote cancelled 
                //Added functionalithy For stage = 1 (Approve) and PUchase Order against it so display the edit btn only to super admin 
                else if ($TabId == 42  && (($Record['quote_stage']) && ($Record['quote_stage'] == 4 || ($Record['quote_stage'] == 1 && $quote->showEditBtnQuotes($Record,Yii::$app->user->identity) != null )))) {
                 } else if ($TabId == 72 && (($Record['quote_stage']) && ($Record['quote_stage'] == 7 || ($Record['quote_stage'] == 4 && $quotedit->showEditBtnQuotes($Record,Yii::$app->user->identity) != null ))) ) {
                } else if ($TabId == 74 && (($Record['so_stage']) && ($Record['so_stage'] != 1 && $Record['so_stage'] != 4 ))) {
                // } else if ($TabId == 78 && (($Record['stage']) && ($Record['stage'] != 1) && ($Record['stage'] != 4))) {
                //as per v11-sheet point no 22
                } else if ($TabId == 78 && (($Record['stage']) && ($Record['stage'] != 1) && (($Record['stage'] >= 4) && $hasadminpower != 1))) {
                }
                //code added by ptpatel on date 05-04-25
                // 51 = sourcing deal 27 = LOST LOSE then dont show edit button
                //functionalithy For stage = 14 (Won) and No payments,No Quotes and no services taken  against it so display the edit btn only to super admin 
                else if ($TabId == 51 && (($Record['stage']) && ($Record['stage'] == 27 || ($Record['stage'] == 14  && $deal->showEditBtnSD($Record,Yii::$app->user->identity) != null  ))) ) {
                }
                // 8 = opportunity 9 = LOST and 8 = WON if stage is WON or LOSE then dont show edit button
                else if ($TabId == 8 && (($Record['opportunity_stage']) && ($Record['opportunity_stage'] == 8))) {

                }
                else if ($TabId == 99 ) { //Edit not allowed in gateoutward

                }
                else if ($TabId == 102 && $Record['vehicle_loading_done'] == '1') { //Vehicle Loading not edited once marked done

                }
                else if ($TabId == 14 && (($Record['stage']) && ($Record['stage'] != 7 && $Record['stage'] > 1))) { //Sales order edit only allowed on shipped stage

                }else if($TabId == 32 && !$AllowEditGeneral){

                }
                
                else if ($TabId == 79) {//hide edit from grndit 
                    ?>
                                                                                                                                <!-- Button to trigger the export -->
                                                                                                                                <a href="<?= Yii::$app->urlManager->createUrl([
                                                                                                                                    '/' . $ModuleName . '/exportitems',
                                                                                                                                    'record' => $Recordid,
                                                                                                                                    'section' => 2761
                                                                                                                                ]) ?>" target="_blank">
                                                                                                                                    <button class="exportBtnn" data-section="2761">Export Serial No Excel</button>
                                                                                                                                </a>
                    <?php
                    if (isset($AllowBulkImportGrnditBarcodes) && $AllowBulkImportGrnditBarcodes == true) { ?>
                                                                                                                                    <button class="import-btn" data-section="2761" data-record="<?= $Recordid ?>" data-bs-toggle="modal"
                                                                                                                                        data-bs-target="#dataimport-modal">Import Serial No</button>
                    <?php
                    }

                }
                //delivery challan if status is return do not show edit btn
                //8-returned 3-approved
                else if ($TabId == 88 && (($Record['status']) && ($Record['status'] == 8 || $Record['status'] == 3 || $Record['status'] == 4))) {

                }
                //hide edit button if invoicedit status>=5
                else if ($TabId == 87 && (($Record['invoice_status']) && ($Record['invoice_status'] >= 5))) {

                }
                //FOC if stage is approved do not show edit btn
                else if ($TabId == 91 && (($Record['stage']) && ($Record['stage'] != 1))) {

                }
                //export record status=2 means completed 
                else if ($TabId == 92 && (($Record['status']) && ($Record['stage'] == 2))) {

                } 
                
                //2-submit for approval and 3= approved when request in this stage dont show edit btn
                
                else if (($TabId == 96) && (($Record['status']) && ($Record['status'] == 2 || $Record['status'] == 3 || $Record['status'] == 4))) {

                }
                //raisereqiest for client-97 or vendor-96
                else if (($TabId == 97) && (($Record['status']) && ($Record['status'] == 2 || $Record['status'] == 3 || $Record['status'] == 4))) {

                }
                 else if ($TabId == 14 && (($Record['stage']) && ($Record['stage'] == 2))) {

                }
                //code added by ptpatel on date 06-11-25
                // 101 // payment update module is status = 12 for SO then edit should be hide
                //edit not allowed bcoz once payment created for SO and if three record created against that so and user edit second payment and change received amount and blance amount is left is equal to received amount then issue arise, this discuss with deepa ma'am and remove edit
                else if ($TabId == 101) {

                    // $relatesso = SalesOrder::find()
                    //     ->where(['salesorder_id' => $Record['so_number']])
                    //     ->one();

                    // //  Show Edit button only if related SO stage is 11
                    // if ($relatesso && $relatesso['stage'] == 11) {
                    //     ?>
                     <!-- <a href="<?php //echo $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span class="span-edit">Edit</span> </button></a> -->
                     <?php
                    // }
                }
                //end code added by ptpatel on date 06-11-25
                else if ($editButtonControl) {
                    // echo $TabId;die;
                    //code added by ptpatel to show resetpassword btn
                    if ($TabId == 19 && isset($AllowResetPassword) && $AllowResetPassword == 1) { //contact module 
                        ?>
                                                                                                                                                        <a href="#" class="add-lead-btn2" id='contact_passwordresetbtn' data-bs-toggle="modal"
                                                                                                                                                            data-bs-target="#contactresetpasswordModal">
                                                                                                                                                            <button class="button-frame-resetpass"><span class="span-edit">Reset password</span></button>
                                                                                                                                                        </a>

                    <?php
                    }
                    if ($hasadminpower == 1 && $TabId == 41 && isset($AllowUserResetPassword) && $AllowUserResetPassword == 1) { //contact module 
                        ?>
                                                                                                                                                        <a href="#" class="add-lead-btn2" id='user_deactivate_btn'>
                                                                                                                                                            <button class="button-frame-resetpass"><span
                                                                                                                                                                    class="span-edit"><?= ($Record['status'] == 10) ? 'Inactivate' : 'Activate'; ?></span></button>
                                                                                                                                                        </a>
                                                                                                                                                        <a href="#" class="add-lead-btn2" id='user_passwordresetbtn' data-bs-toggle="modal"
                                                                                                                                                            data-bs-target="#contactresetpasswordModal">
                                                                                                                                                            <button class="button-frame-resetpass"><span class="span-edit">Reset password</span></button>
                                                                                                                                                        </a>

                    <?php
                    }
                    //code added by ptpatel end here to resetpassword btn
                    //code added by ptpatel on date 22-12-2025 to resolve issue v11-167
                    //edit button will not show if user i snot owner or not admin or not has
                    // if($TabId == 8 && $Record['ownerid'] != Yii::$app->user->id && Yii::$app->user->id != 1 && $hasadminpower != 1)
                    // {}
                    // else{
                    //code added by ptpatel on date 22-12-2025  to resolve issue v11-167
                    ?>
                                                                                                                                                    <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                                                                                                                                                                    class="span-edit">Edit</span>
                                                                                                                                                        </button></a>
                    <?php
                    // }
                }

            } else {

                if ($TabId == 7) {
                    if (($Record['leadstatus'] != '4' && $Record['leadstatus'] != '13') && $Record['converted'] != 1) //can't edit if send for approval or qualified
                    { ?>
                                                                                            <button class="button-frame-38" id="edit-lead-btn"><span class="span-edit" id="edit-lead-btn">Edit</span>
                                                                                            </button>
                    <?php
                    }
                } 
                // else if ($TabId == 13 && $po->showEditBtnPO(Yii::$app->user->identity,$Record)) {

                // }
                 //Added functionalithy For stage = 3 (Approve) and No payments against it so display the edit btn only to super admin @Date 13/11/2025
                 else if ($TabId == 13 && (($Record['stage']) && ($Record['stage'] == 5 || ($Record['stage'] == 3  && $po->showEditBtnPO($Record,Yii::$app->user->identity) != null)))) {
                    //as per V11 sheet point 21 -change by ptpatel on date 10-10-2025
                    if($hasadminpower == 1)
                    {
                        ?>
                            <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                                                                                                        class="span-edit">Edit</span>
                                                                                                </button></a>
                        
                        <?php
                    }
                    //as per V11 sheet point 21 -change by ptpatel on date 10-10-2025
                }
                // || $Record['quote_stage'] == 1 added by ptpatel on date 05-04-25
                 //Added functionalithy For stage = 1 (Approve) and PUchase Order against it so display the edit btn only to super admin @Date 13/11/2025
                else if ($TabId == 42 && (($Record['quote_stage']) && ($Record['quote_stage'] == 4 || ($Record['quote_stage'] == 1 && $quote->showEditBtnQuotes($Record,Yii::$app->user->identity) != null )))) {
                   
                }
                //code added by ptpatel on date 05-04-25
                // 51 = sourcing deal 27 = LOST and LOSE then dont show edit button
                 //functionalithy For stage = 14 (Won) and No payments,No Quotes and no services taken  against it so display the edit btn only to super admin @Date 13/11/2025
                 else if ($TabId == 51 && (($Record['stage']) && ($Record['stage'] == 27 || ($Record['stage'] == 14 &&  $deal->showEditBtnSD($Record,Yii::$app->user->identity) != null  )))) {
                   
                }
                //end code added by ptpatel on date 05-04-25
                else {
                    ?>
                                                                                                    <button class="button-frame-38" id="edit-lead-btn"><span class="span-edit" id="edit-lead-btn"> Edit</span>
                                                                                                    </button>

                <?php
                }
            } ?>

                                                                                <!-- <div class="div-frame">
                <span class="span-more">More</span>
                <div class="div-mdi-menu-down">
                  <div class="div-vector-39"></div>
                </div>
              </div> -->

                                                                            </div>
    <?php
} else if (isset($EditOpportunity) && !empty($EditOpportunity)) {
    $urledit = "edit?Record=$Recordid"; ?>
                                                                                <div class="div-regroup">
                                                                                    <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                                                                                                class="span-edit">Edit</span>
                                                                                        </button></a>
                                                                                </div>
    <?php
}


?>
<?php
if ($TabId == 24 && isset($AllowFormSix) && $AllowFormSix === true) { ?>
    <a href="<?= Yii::$app->urlManager->createUrl(['/pickup/generateformsix', 'Record' => $Recordid]) ?>"
        target="_blank"><button class="detail-view-btn-gen gen-form6">Form 6</button></a>
<?php } ?>
<?php
if ($TabId == 24 && isset($AllowFormTen) && $AllowFormTen === true) { ?>
    <a href="<?= Yii::$app->urlManager->createUrl(['/pickup/generateformten', 'Record' => $Recordid]) ?>"
        target="_blank"><button class="detail-view-btn-gen gen-form6">Form 10</button></a>
<?php } ?>
<?php
if ($TabId == 24 && isset($AllowGreenCertificate) && $AllowGreenCertificate === true) { ?>
    <a href="<?= Yii::$app->urlManager->createUrl(['/pickup/generategreencert', 'Record' => $Recordid]) ?>"
        target="_blank"><button class="detail-view-btn-gen gen-form6">Green Certificate</button></a>
<?php }