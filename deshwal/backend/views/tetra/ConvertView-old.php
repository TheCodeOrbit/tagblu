<?php
error_reporting(-1);
ini_set("display_errors", true);
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use backend\assets\AdminAsset;

AdminAsset::register($this);

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself

$siteDir = Yii::$app->params["dirName"];
$ModuleName = $ActionList["ModuleName"];

$ActionName = $ActionList["ActionName"];
$ModuleLabel = $ActionList["ModuleLabel"];
$_SESSION["countpro"] = "";
$_SESSION["taxcounterr"] = "";
$sesionid = isset($_SESSION[$siteDir . "_id"])
    ? $_SESSION[$siteDir . "_id"]
    : "deshwal";

$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/edit.js";
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';
$this->registerCssFile('@web/thememain/css/multiple.css', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/js/leads/convertlead.js', ['depends' => [AdminAsset::class]]);


$fullurl = Yii::$app->request->getUrl();
$baseUrl = Yii::$app->HomeUrl;

//echo $fullurl ; exit ;
$first_name = $Record['firstname'];
$last_name = $Record['lastname'];
if (empty($contacts_id)) {
    // $first_name = $Record['firstname'];
    // $last_name = $Record['lastname'];
    $message = "No contact  exist for Mobile no (" . $Record['phone'] . ")";
    if (empty($email))
        $email = $Record['email'];
    if (empty($mobile))
        $mobile = $Record['mobile'];

} else {
    $message = "Contact already exist for Mobile no (" . $Record['phone'] . ")";

}
$lead_category = $Record['category'];
if ($lead_category != 6) {//not cyber security
    $showopportunity = 0;
} else {
    $showopportunity = 1;
}
$vendor_name = $Record['vendor'];
$acountname = '';
// echo $Record['customer_type'];die;
if (!empty($vendor_name) && $Record['customer_type'] !=1) {
    $createaccount = 0;
    //get account name
    $res = Yii::$app->db->createCommand("select acc_name from vendor_account where vendoraccid =:vendoraccid")->bindValue(":vendoraccid", $vendor_name)
        ->queryOne();
        if($res)
    $acountname = $res['acc_name'];
} else {
    $createaccount = 1;
    $acountname = '';
}

?>
<link href="<?= $baseUrl . 'thememain/css/multiple.css' ?>" rel="stylesheet">

<div class="modal-header">
    <h5 class="modal-title base-color" id="addLeadModalLabel"><img
            src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img-create">Convert
        <?= $TabLabel ?>

    </h5>
    <button type="button" class="btn-close mod-close" aria-label="Close"></button>
</div>
<div class="modal-body" id="modalBody">
    <div class="create-form">

        <?php $form = ActiveForm::begin([
            "id" => "pristine-valid-example",
            'options' => [
                'enctype' => 'multipart/form-data', // Required for file uploads
            ],
        ]); ?>
        <div class="mainrow">
            <p class="red cont-alert"></p>

            <?php
            $currentdate = date("Y-m-d");
            $finaldate = strtolower(
                date("Y-m-d", strtotime("-1 day", strtotime($currentdate)))
            );

            $currentdate1 = date("d-m-Y");
            $finaldateshow = strtolower(
                date("d-m-Y", strtotime("-1 day", strtotime($currentdate1)))
            );
            ?>
            <!-- <span class="note">Fields with <span class="required star">&nbsp;*&nbsp;</span> are required.</span> -->
            <input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode" />
            <!-- <input type="hidden" value="<?php
            //echo $RecordID;
            ?>" id="recordid" name="recordid"/> -->
            <input type="hidden" value="convertlead" id="module" name="module" />
            <input type="hidden" value="<?php echo Yii::$app->user->id; ?>" id="creatorid" name="creatorid" />
            <input type="hidden" value="<?php echo Yii::$app->user->id; ?>" id="ownerid" name="ownerid" />
            <input type="hidden" value="<?php echo Yii::$app->user->id; ?>" id="modifiedby" name="modifiedby" />
            <input type="hidden" value="<?php echo $contacts_id ?>" id="contacts_id" name="contacts_id" />
            <input type="hidden" value="<?php echo $Record['phone'] ?>" id="mobile" name="mobile" />
            <input type="hidden" value="<?php echo $Record['leadid'] ?>" id="leadid" name="leadid" />
            <input type="hidden" value="<?php echo $Record['vendor'] ?>" id="vendor_account_name"
                name="vendor_account_name" />
            <!-- for opportuity -->
            <input type="hidden" value="<?php echo $Record['email']; ?>" name="opportunity[contact_email]">
            <input type="hidden" value="<?php echo $Record['vendor']; ?>" name="opportunity[vendor_account_name]">
            <input type="hidden" value="<?php echo $Record['phone']; ?>" name="opportunity[contact_mobile]">
            <input type="hidden" value="<?php echo $Record['currency']; ?>" name="opportunity[currency]">
            <input type="hidden" value="<?php echo $Record['exchange_rate']; ?>" name="opportunity[exchange_rate]">
            <input type="hidden" value="1" name="opportunity[stage]">
            <!-- for contacts -->
            <input type="hidden" value="<?php echo $mobile; ?>" name="contacts[mobile]">
            <input type="hidden" value="<?php echo $email; ?>" name="contacts[email]">
            <input type="hidden" value="<?php echo $Record['vendor']; ?>" name="contacts[vendor_account_name]">
            <!-- currency and exchange rate -->
            <input type="hidden" value="<?php echo $Record['currency']; ?>" name="currency">
            <input type="hidden" value="<?php echo $Record['exchange_rate']; ?>" name="exchange_rate">




            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
            <?php
            if ($createaccount == 1) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Account</h4>
                    </div>
                    <div class="col-md-6">
                        <input type="radio" value="1" id="create_account" name="account_type" checked> Create Account
                    </div>
                    <div class="col-md-6">
                        <input type="radio" value="2" id="choose_account" name="account_type"> Choose Existing Account
                    </div>
                    <div class="col-md-6">
                        <!-- HTML Form without Model -->
                        <div class="form-group">
                            <label for="deal_name">Account Name</label>
                            <input type="text" id="deal_name" name="vendor_account[acc_name]" class="form-control V~M"
                                maxlength="100" data-pristine-required="true"
                                data-pristine-required-message="Account Name is required "
                                value="<?php echo $Record['account_name'] ?>">
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="opportunity_type">Choose Account</label>
                            <div>
                                <div class="vendor-input-wrapper">
                                    <!-- Cross Icon on the Left -->
                                    <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                        onclick="removeTextValue(vendor_name1,vendor);" aria-label="Remove vendor">
                                        <path
                                            d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                        </path>
                                    </svg>

                                    <input class="effect" style="flex-grow:1;" type="hidden" id="vendor_name1"
                                        name="vendor_name" value="" readonly="readonly">
                                    <input type="text" id="vendor_name" class="form-control ref-form-control" name=""
                                        value="" fieldid="2" data-pristine-required="true"
                                        data-pristine-required-message="Select Account is required ">
                                    <!-- Search Icon on the Right -->
                                    <svg class="icon-right search-icon cvo" width="15" height="15" viewBox="0 0 24 25"
                                        fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal"
                                        data-target="#myModal22" role="button" aria-hidden="true" tabindex="0"
                                        onclick="showCustomer1('vendor_name1','vendor_name','acc_name','vendoraccount',13)"
                                        aria-label="Search vendor">
                                        <path
                                            d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                            stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                    </svg>

                                    <!-- Plus Icon on the Right -->
                                    <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                        onclick="addVendor('vendor1','vendor','acc_name','vendoraccount',13)"
                                        aria-label="Add vendor">
                                        <path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
                                    </svg>
                                </div>




                            </div>
                            <div class="help-block"></div>
                        </div>
                    </div>

                </div>
                <?php
            } else {//show account name
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Account</h4>
                    </div>
                    <div class="col-md-12">
                        Account Name: <?= $acountname; ?>
                        <input type="hidden" id="vendor1" value="<?= $vendor_name; ?>">
                    </div>


                </div>
                <?php
            } ?>
            <div class="row  mt-4">
                <div class="col-md-12">
                    <h4>Contact</h4>
                    <p class="red"><?php // $message; ?></p>
                </div>
                <div class="col-md-6">
                    <input type="radio" name="create_contact" id="create_contact" value="1" checked> Create Contact
                </div>
                <div class="col-md-6">
                    <input type="radio" name="create_contact" id="choose_contact" value="2"> Choose from Contacts
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" class="form-control  AN~M" name="contacts[first_name]"
                            maxlength="100" data-pristine-required="true"
                            data-pristine-required-message="First Name is required " value="<?php echo $first_name; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" class="form-control  AN~M" name="contacts[last_name]"
                            maxlength="100" data-pristine-required="true"
                            data-pristine-required-message="Last Name is required " value="<?php echo $last_name; ?>">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group chooseontact">
                        <label for="opportunity_type">Contacts</label>
                        <div>



                            <div class="vendor-input-wrapper">
                                <!-- Cross Icon on the Left -->
                                <svg class="icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                    onclick="removeTextValue(contact_name1,contact_name);" aria-label="Remove vendor">
                                    <path
                                        d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z">
                                    </path>
                                </svg>

                                <input class="effect" style="flex-grow:1;" type="hidden" id="contact_name1"
                                    name="opportunity[contact_name]" value="" readonly="readonly">
                                <input type="text" id="contact_name" class="form-control ref-form-control AN~O" name=""
                                    value="" maxlength="100" fieldid="247" data-pristine-required="true"
                                    data-pristine-required-message="Contact Name is required ">

                                <!-- Search Icon on the Right -->
                                <svg class="icon-right search-icon" width="15" height="15" viewBox="0 0 24 25"
                                    fill="none" xmlns="http://www.w3.org/2000/svg" data-toggle="modal"
                                    data-target="#myModal22" role="button" aria-hidden="true" tabindex="0"
                                    onclick="showCustomer1('contact_name1','contact_name','first_name','contacts',40)"
                                    aria-label="Search vendor">
                                    <path
                                        d="M21 21.5L16.514 17.006L21 21.5ZM19 11C19 13.2543 18.1045 15.4163 16.5104 17.0104C14.9163 18.6045 12.7543 19.5 10.5 19.5C8.24566 19.5 6.08365 18.6045 4.48959 17.0104C2.89553 15.4163 2 13.2543 2 11C2 8.74566 2.89553 6.58365 4.48959 4.98959C6.08365 3.39553 8.24566 2.5 10.5 2.5C12.7543 2.5 14.9163 3.39553 16.5104 4.98959C18.1045 6.58365 19 8.74566 19 11V11Z"
                                        stroke="#2F80ED" stroke-width="2" stroke-linecap="round"></path>
                                </svg>

                                <!-- Plus Icon on the Right -->
                                <svg class="icon-right plus-icon" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0"
                                    onclick="addVendor('contact_name1','contact_name','first_name','contacts',40)"
                                    aria-label="Add vendor">
                                    <path d="M12 5v7H5v2h7v7h2v-7h7v-2h-7V5z"></path>
                                </svg>
                            </div>




                        </div>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <?php
            if ($showopportunity == 1) { ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Create Opportunity</h4>
                    </div>
                    <div class="col-md-6">
                        <!-- HTML Form without Model -->
                        <div class="form-group">
                            <label for="deal_name">Opportunity Name</label>
                            <input type="text" id="deal_name" name="opportunity[deal_name]" class="form-control V~M"
                                maxlength="100" data-pristine-required="true"
                                data-pristine-required-message="Opportunity Name is required " value="<?= $opporname; ?>">
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="opportunity_type">Opportunity Type</label>
                            <select id="message" name="opportunity[opportunity_type]" class="form-control V~M">
                                <option value="">-Select-</option>
                                <option value="1">Contracted</option>
                                <option value="2">Non Contracted</option>
                            </select>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <?php
            } else { ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Create Sourcing Deal</h4>
                    </div>
                    <div class="col-md-6">
                        <!-- HTML Form without Model -->
                        <div class="form-group">
                            <label for="deal_name">Sourcing Deal Name</label>
                            <input type="text" id="deal_name" name="sourcingdeal[deal_name]" class="form-control V~M"
                                maxlength="100" data-pristine-required="true"
                                data-pristine-required-message="Opportunity Name is required " value="<?= $opporname; ?>">
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <!-- <div class="col-md-6">
                    <div class="form-group">
                        <label for="opportunity_type">Opportunity Type</label>
                        <select id="message" name="opportunity[opportunity_type]" class="form-control V~M">
                            <option value="">-Select-</option>
                            <option value="1">Contracted</option>
                            <option value="2">Non Contracted</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div> -->
                </div>
                <?php
            } ?>
        </div>
    </div>

</div>

</div>

<div class="modal-footer">
    <?= Html::Button("Convert", [
        "class" => "btn btn-primary savebutton",
    ]) ?>
    <?= Html::Button("Cancel", [
        "class" => "btn mod-close btn-secondary",
        "name" => "btncancel",
    ]) ?>

</div>


<?php ActiveForm::end(); ?>
<script type="text/javascript">



</script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/select2.min.css">
<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/multilist-dd.css">
<script type="text/javascript" src="<?= $scriptPath ?>"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/libs/pristinejs/pristinejs.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>theme/js/pages/form-validation.init.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/editview.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/single-dd.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script>
<script type="text/javascript" src="<?= $baseUrl ?>js/leads/convertlead.js"></script>

<!-- for deepika validator start -->

<!-- Link to your validator.js file -->
<script src="<?= $baseUrl; ?>thememain/js/tetra/validator.js"></script>

<!-- Your custom script (optional) -->
<script>

    $(document).ready(function () {
        // Create a MutationObserver to detect changes to the input vendor account
        var targetNode = document.getElementById("contact_name1");
        var observer = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (
                    mutation.type === "attributes" &&
                    mutation.attributeName === "value"
                ) {
                    if(targetNode.value)
                    checkcontacts();

                    console.log("contact_name1 value changed to:", targetNode.value);
                }
            }
        });
        function checkcontacts() {
            $(".savebutton").attr("disabled",true);
            var create_contact = $("#create_contact").prop("checked");
            var contact = $("#contact_name1").val();
            if ($('#vendor_name1').length)         // use this if you are using id to check
            {
                var account = $("#vendor_name1").val();
            }
            else if ($('#vendor1').length) {
                var account = $("#vendor1").val();
            }


            // alert(account);
            //check contact with account
            data = { contact: contact, vendor_account_name: account, _csrf: $("#csrfToken").val() };

            // alert("choose_contact "+choose_contact);
            if (choose_contact) {
                $("#contact_name1").addClass("V~M");
                $("#contact_name").addClass("V~M");
                $("#first_name").removeClass("V~M");
                $("#last_name").removeClass("V~M");
                isValid = false;
                $.ajax({
                    type: "POST",
                    url: "checkcontact",
                    // async:false,
                    data: data,
                    success: function (data) {
                        //location.reload();
                        $(".cont-alert").text('');
                        // alert(data.data);
                        var msg = '';
                        isValid = false;
                        if (data.data === 'matched') {
                            isValid = true;
                            // alert(isValid);
                            $(".savebutton").attr("disabled",false);

                        }
                        else {
                            msg = 'Specified Contact must be parented by specified Account';
                            isValid = false;
                        }
                        $(".cont-alert").text(msg);

                    },
                    error: function (data) {
                        // if error occured
                        isValid = false;
                        alert("Error occured.please try again");
                    },
                    dataType: "json",
                });
            }
            else {
                $(".savebutton").attr("disabled",false);

                $("#contact_name1").removeClass("V~M");
                $("#contact_name").removeClass("V~M");
                $("#first_name").addClass("V~M");
                $("#last_name").addClass("V~M");
            }

        }
        //end check contacts

        // Configuration for the observer (observe attribute changes)
        var config = { attributes: true };
        observer.observe(targetNode, config);

        // alert("1");
        if ($("#create_account").length) {
            var create_account = $("#create_account").prop("checked");
            if (create_account) {
                $("#vendor_name1").removeClass("V~M");
                $("#vendor_name").removeClass("V~M");
                $("#deal_name").addClass("V~M");

                //disable choose contact
                $("#choose_contact").attr("disabled", true);
                $(".chooseontact").addClass("tr-hidden");
            }
        }
        // alert("2");
        // jQuery to detect when a radio button is checked
        $('input[type="radio"][id="create_account"]').change(function () {
            if ($(this).is(':checked')) {
                console.log('Radio button checked!');
                // Perform your logic here
                $("#vendor_name1").removeClass("V~M");
                $("#vendor_name").removeClass("V~M");
                $("#deal_name").addClass("V~M");

                //disable choose contact
                var contacts_id = "<?php echo $contacts_id ?>";
                if (!contacts_id)
                    $("#create_contact").prop("checked", true);
                $("#choose_contact").prop("checked", false);
                $("#choose_contact").attr("disabled", true);
                $(".chooseontact").addClass("tr-hidden");
                $("#contact_name1").val('');
                $("#contact_name").val('');
            }
        });

        $('input[type="radio"][id="choose_account"]').change(function () {
            if ($(this).is(':checked')) {
                console.log('Radio button checked!');
                // Perform your logic here
                //check if vendor is blank or not
                $("#vendor_name1").addClass("V~M");
                $("#vendor_name").addClass("V~M");
                $("#deal_name").removeClass("V~M");
                //enable choose contact
                var contacts_id = "<?php echo $contacts_id ?>";
                if (!contacts_id)
                    $("#create_contact").prop("checked", true);
                $("#choose_contact").prop("checked", false);
                $("#choose_contact").attr("disabled", false);
                $(".chooseontact").removeClass("tr-hidden");
                $("#contact_name1").val('');
                $("#contact_name").val('');

            }
        });

        $('input[type="radio"][id="create_contact"]').change(function () {
            if ($(this).is(':checked')) {
                $(".savebutton").attr("disabled",false);

                console.log('Radio button checked!');
                // Perform your logic here
                //blank contact is blank or not
                $("#contact_name").removeClass("V~M");
                $("#contact_name1").removeClass("V~M");
                //enable choose contact
                
                $("#contact_name1").val('');
                $("#contact_name").val('');

                $("#first_name").addClass("AN~M");
                $("#last_name").addClass("AN~M");

            }
        });

        $('input[type="radio"][id="choose_contact"]').change(function () {
            if ($(this).is(':checked')) {
                $(".savebutton").attr("disabled",true);

                console.log('Radio button checked!');
                // Perform your logic here
                //blank contact is blank or not
                $("#contact_name").addClass("V~M");
                $("#contact_name1").addClass("V~M");
                //enable choose contact
                
                $("#first_name").removeClass("AN~M");
                $("#last_name").removeClass("AN~M");

            }
        });

        //

        const validator = new Validator();

        $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave").on("change", function () { //alert('dsfs');
            if ($(this).is(":visible") || $(this).hasClass("leave")) {
                validator.validateField($(this));
            }
        });

        $(".savebutton").on("click", function (e) {

            let isValid = true;

            var contacts_id = "<?php echo $contacts_id ?>";

            //check if contact is selected of same account
            //Specified Contact must be parented by specified Account
           
            // $("#exchange_rate").val('');
            isValid = false;
            $(".cont-alert").text('');

            //check if choose account is checked
            var choose_account = $("#choose_account").prop("checked");
            var create_account = $("#create_account").prop("checked");
            // alert(choose_account);
            if (choose_account) {
                //alert(choose_account);
                //check if vendor is blank or not
                $("#vendor_name1").addClass("V~M");
                $("#vendor_name").addClass("V~M");
                $("#deal_name").removeClass("V~M");
                //enable choose contact
                $("#choose_contact").attr("disabled", false);

            }
            else if (create_account) {
                $("#vendor_name1").removeClass("V~M");
                $("#vendor_name").removeClass("V~M");
                $("#deal_name").addClass("V~M");

                //disable choose contact
                $("#choose_contact").attr("disabled", true);
            }
            // Check if choose_contact is selected
            var choose_contact = $("#choose_contact").prop("checked");
            var create_contact = $("#create_contact").prop("checked");

            // alert("choose_contact "+choose_contact);
            if (choose_contact) {
                $("#contact_name1").addClass("V~M");
                $("#contact_name").addClass("V~M");
                $("#first_name").removeClass("AN~M");
                $("#last_name").removeClass("AN~M");
                // isValid = false;
            }
            else {
                $("#contact_name1").removeClass("V~M");
                $("#contact_name").removeClass("V~M");
                $("#first_name").addClass("AN~M");
                $("#last_name").addClass("AN~M");
            }
            // alert("create_contact "+create_contact);
            // alert("choose_contact "+choose_contact);
            // alert("contacts_id "+contacts_id);
            // alert("1 " + isValid);
            if (contacts_id !== '' && (create_contact || choose_contact)) {
                if (confirm('Contact already exist with this number, please verify or proceed without creating contact')) {
                    $("#create_contact").prop("checked", false);
                    $("#choose_contact").prop("checked", false);
                    $("#contact_name1").removeClass("V~M");
                    $("#contact_name").removeClass("V~M");
                    isValid = false;
                }
                else
                    isValid = false;

                $(".cont-alert").text('Contact already exist with this number, please verify or proceed without creating contact');
            }
            else {
                isValid = true;
            }
            // alert("2 " + isValid);

            $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave").each(function () {
                if ($(this).is(":visible") || $(this).hasClass("leave")) {
                    if (!validator.validateField($(this))) {
                        isValid = false;
                    }
                }
            });
            // alert("last " + isValid);
            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $(".help-block:visible:first").offset().top
                }, 500);
            } else {
                // alert(isValid);

                $("#pristine-valid-example").submit();
            }
        });
        // Make sure the Validator object exists and validateField is a function
        console.log(window.Validator); // This should display the Validator object in console
        window.Validator.validateField($('leadinformation[firstname]')); // Replace with an actual field

        $(".form-control").on("blur change", function () {
            // Check if Validator.validateField is available and call it
            if (typeof window.Validator.validateField === "function") {
                window.Validator.validateField($(this));
            } else {
                console.error("Validator.validateField is not a function.");
            }
        });
    });
</script>


<!-- end deepika validator -->
<?php

// $scriptPath=$baseUrl."js/$ModuleName/Edit.js";
// $this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile($scriptPath, ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/libs/theme/js/pages/form-validation.init.js', ['depends' => [AdminAsset::class]]);

// $this->registerJsFile($scriptPath, [
//     "depends" => [AdminAsset::class],
// ]);
// $this->registerJsFile("@web/theme/libs/pristinejs/pristinejs.min.js", [
//     "depends" => [AdminAsset::class],
// ]);
// $this->registerJsFile("@web/theme/js/pages/form-validation.init.js", [
//     "depends" => [AdminAsset::class],
// ]);
// $this->registerJsFile('@web/theme/js/app.min.js', ['depends' => [AppAsset::class]]);
// ob_flush();

die();
?>