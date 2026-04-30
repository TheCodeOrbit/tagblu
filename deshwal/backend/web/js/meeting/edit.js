$(document).ready(function () {
  var newURL = window.location.href;
  var newURL = window.location.href;
  var module = "leads";
  var str = newURL.split(module);
  console.log("str" + str[0]);
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "leads/list";
  console.log("url" + editusrl);

 
  $('#participants_reminder').val('5');
  $('.section-repeat_type').hide();

  $('#repeat').change(function() {
      if ($(this).is(':checked')) {
          $('.section-repeat_type').show();  // Show the field when checked
      } else {
          $('.section-repeat_type').hide();  // Hide the field when unchecked
      }
  });

  //code added by ptpatel for MOM Block
    // Hide by default
    $('.row2821').hide();

    const prefixMap = {
        mom_date: ['DT'],
        mom_location: ['V'],
        mom_time: ['V'],
        attendees :['V'],
        discussion_points:['V'],
        next_action:['V'],
    };

    function toggleMOMShared() {

        const isChecked = $('#MOM_shared').prop('checked');

        console.log(
            'checked:', isChecked,
            'value:', $('#MOM_shared').val()
        );

        if (isChecked) {

            // Show MOM block
            $('.row2821').show();

            // Make ALL mapped fields mandatory
            Object.keys(prefixMap).forEach((key) => {
                const input = document.getElementById(key);
                if (!input) return;

                const prefixes = Array.isArray(prefixMap[key])
                    ? prefixMap[key]
                    : [prefixMap[key]];

                prefixes.forEach((prefix) => {

                    // Remove old prefix class
                    [...input.classList].forEach((cls) => {
                        if (cls.startsWith(prefix + "~")) {
                            input.classList.remove(cls);
                        }
                    });

                    // Add mandatory class
                    input.classList.add(prefix + "~M");
                });
            });
            //this need to remove Select attendess option which create issue
           $('#attendees option[value=""]').remove();

        } else {

            // Hide MOM block
            $('.row2821').hide();

            // Make ALL mapped fields optional + CLEAR values
            Object.keys(prefixMap).forEach((key) => {
                const input = document.getElementById(key);
                if (!input) return;

                const prefixes = Array.isArray(prefixMap[key])
                    ? prefixMap[key]
                    : [prefixMap[key]];

                prefixes.forEach((prefix) => {

                    // Remove old prefix class
                    [...input.classList].forEach((cls) => {
                        if (cls.startsWith(prefix + "~")) {
                            input.classList.remove(cls);
                        }
                    });

                    // Add optional class
                    input.classList.add(prefix + "~O");
                });

                
                if ($(input).hasClass('select2-hidden-accessible')) {
                    // Select2 (single or multi)
                    $(input).val(null).trigger('change');

                } else {
                    input.value = '';
                }

                // Clear validation UI
                $(input)
                    .removeClass('has-error is-invalid')
                    .closest('.form-group')
                    .find('.help-block')
                    .html('');
            });
        }
    }


    // On checkbox change
    $(document).on('change', '#MOM_shared', function () {
        toggleMOMShared();
    });

    $(document).ready(function () {
        toggleMOMShared();
    });

  //end code added by ptpatel for MOM block
});
$(window).on("load", function () {

    function toggleMeetingFields(meetingTypeValue) {

        var isF2F = meetingTypeValue === "1";

        $.each(["from", "to"], function (_, field) {

            var $section = $(".section-" + field);
            if (!$section.length) return;

            var $inputs = $section.find("input");

            // Flatpickr visible input
            var $fpVisible = $section.find(".flatpickr-input");

            if (isF2F) {
                // SHOW
                $section.show();

                // ADD mandatory
                $inputs.addClass("DTT~M");

            } else {
                // HIDE EVERYTHING

                // REMOVE mandatory + clear
                $inputs.removeClass("DTT~M").val("");
               
                $section.hide();
                $(".section-to").hide();
            }
        });
    }

    //  PAGE LOAD
    var $meetingType = $("#type_of_meeting");
    if ($meetingType.length) {
        toggleMeetingFields($meetingType.val());
    }

    //  ON CHANGE
    $(document).on("change", "#type_of_meeting", function () {
        toggleMeetingFields($(this).val());
    });

});

/*$(document).ready(function () {

    var $accountDropdown = $('#account_name');
    var $userDropdown = $('#external_participants');

    function isAccountSelected() {
        return $.trim($accountDropdown.val()) !== '';
    }

    function loadExternalUsers(accountName, selectedUserId = null) {
        $userDropdown.empty();
        if (!accountName) return;

        $.ajax({
            url: 'getexternalusers',
            type: 'GET',
            data: { account_name: accountName },
            dataType: 'json',
            success: function (response) {
                if (response && response.length > 0) {
                    $.each(response, function (index, user) {
                        var selected = (selectedUserId && user.contacts_id == selectedUserId) ? ' selected' : '';
                        $userDropdown.append(
                            '<option value="' + user.contacts_id + '"' + selected + '>' + user.name + '</option>'
                        );
                    });
                } else {
                    alert('No users found for this account.');
                }
            },
            error: function () {
                alert('Error fetching users. Please try again.');
            }
        });
    }

    /* ===============================
       1. ACCOUNT CHANGE → LOAD USERS
       =============================== */
    // $accountDropdown.on('change', function () {
    //     var accountName = $(this).val();

    //     // Clear previous selection
    //     $userDropdown.val(null).trigger('change');

    //     loadExternalUsers(accountName);
    // });

    /* ===============================
       2. BLOCK OPENING (NORMAL SELECT)
       =============================== */
   /* $userDropdown.on('mousedown', function (e) {
        if (!isAccountSelected()) {
            e.preventDefault();
            alert('Please select Account Name first.');
            return false;
        }
    });

    /* ===============================
       3. BLOCK OPENING (SELECT2)
       =============================== */
    /*$userDropdown.on('select2:opening', function (e) {
        if (!isAccountSelected()) {
            e.preventDefault();
            alert('Please select Account Name first.');
        }
    });

    /* ===============================
       4. EXTRA SAFETY ON CHANGE
       =============================== */
    /*$userDropdown.on('change', function () {
        if (!isAccountSelected()) {
            alert('Please select Account Name first.');
            $(this).val(null).trigger('change');
        }
    });

    /* ===============================
       5. EDIT MODE (PAGE LOAD)
       =============================== */
    /*var initialAccount = $accountDropdown.val();
    var initialUser = $userDropdown.data('selected'); // set from backend

    if (initialAccount) {
        loadExternalUsers(initialAccount, initialUser);
    }

    function observeHiddenField(fieldId) {
    const input = document.getElementById(fieldId + "1");
    if (!input) return;

    const observer = new MutationObserver(function () {
        console.log("Hidden field changed:", fieldId);     
        loadExternalUsers(input.value);
    });

    observer.observe(input, {
        attributes: true,
        attributeFilter: ["value"]
    });
}
    /* Initialize observers */
    /*observeHiddenField("account_name");
});*/

$(document).ready(function () {

    var $accountDropdown = $('#account_name');
    var $userDropdown = $('#external_participants');

    function isAccountSelected() {
        return $.trim($accountDropdown.val()) !== '';
    }

    function loadExternalUsers(accountName, selectedUsers = []) {
        startLoading();
        $userDropdown.empty();

        if (!accountName){
            stopLoading();
          return;  
        } 

        $.ajax({
            url: 'getexternalusers',
            type: 'GET',
            data: { account_name: accountName },
            dataType: 'json',
            success: function (response) {

                if (!response || !response.length) return;

                $.each(response, function (index, user) {
                    $userDropdown.append(
                        $('<option>', {
                            value: user.contacts_id,
                            text: user.name
                        })
                    );
                });

                //  SET SELECTED VALUES AFTER OPTIONS LOAD
                if (selectedUsers.length) {
                    $userDropdown.val(selectedUsers).trigger('change');
                }
                stopLoading();
            }
        });
    }

    /* ===============================
       EDIT MODE — FORCE LOAD ON READY
       =============================== */
    // var initialAccount = $accountDropdown.val();
    var initialAccount = $("#account_name1").val();
    // backend must render selected ids like: data-selected="1,3,5"
    var selectedUsers = $userDropdown.val().toString().split(',');
    console.log("selectedUsers"+initialAccount + "--"+ selectedUsers);
    if (initialAccount) {
        console.log("initialAccount selectedUsers"+initialAccount + "--"+ selectedUsers);
        loadExternalUsers(initialAccount, selectedUsers);
    }

    /* ===============================
       ACCOUNT CHANGE
       =============================== */
    $accountDropdown.on('change', function () {
        var accountName = $(this).val();
        $userDropdown.val(null).trigger('change');
        loadExternalUsers(accountName);
    });

    /* ===============================
       BLOCK OPEN WITHOUT ACCOUNT
       =============================== */
    $userDropdown.on('select2:opening mousedown', function (e) {
        if (!isAccountSelected()) {
            e.preventDefault();
            // alert('Please select Account Name first.');
        }
    });

    function observeHiddenField(fieldId) {
        const input = document.getElementById(fieldId + "1");
        if (!input) return;

        const observer = new MutationObserver(function () {
            console.log("Hidden field changed:", fieldId);     
            loadExternalUsers(input.value);
        });

        observer.observe(input, {
            attributes: true,
            attributeFilter: ["value"]
        });
    }
    /* Initialize observers */
    observeHiddenField("account_name");

});
function validateTimesCombined() {
    const startVal = $("#from_location").val();
    const endVal   = $("#to_location").val();

    const $start = $("#from_location");
    const $end   = $("#to_location");

    const startHelp = $start.closest(".form-group").find(".help-block");
    const endHelp   = $end.closest(".form-group").find(".help-block");

    startHelp.text("").hide();
    endHelp.text("").hide();

    if (!startVal || !endVal) {
        $(".savebutton").prop("disabled", false);
        return true;
    }

   
    const startDate = new Date(startVal.replace(" ", "T"));
    const endDate   = new Date(endVal.replace(" ", "T"));

    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
        console.warn("Invalid date values:", startVal, endVal);
        $(".savebutton").prop("disabled", false);
        return true;
    }

    if (endDate <= startDate) {
        endHelp.text("End time must be greater than start time.").show();
        $(".savebutton").prop("disabled", true);
        return false;
    }

    $(".savebutton").prop("disabled", false);
    return true;
}

$(document).on("input blur", "#from_location, #to_location", validateTimesCombined);
$(document).ready(function () {
    function leadCheck(){
        var $relatedTo    = $('#related_to');
        var $relatedRec   = $('#related_to_id'); 
        var $blockManual  = $('.section-account_name_manual'); 
        var $blockManualExt  = $('.section-external_participants_manual'); 
        var $manualInput  = $('#account_name_manual'); 
        var $blockVendor  = $('.section-account_name'); 
        var $vendorHidden = $('#account_name1'); 
        var $vendorText   = $('.section-account_name #account_name'); 

        var $extManualBlock = $('.section-external_participants_manual'); 
        var $extManualInput = $('#external_participants_manual');        
        var $extSelectBlock = $('.section-external_participants');       
        var $extSelect      = $('#external_participants');

        function applyNewCustomer(manualName) {
            $blockVendor.hide();
            $blockManual.show();

            $vendorHidden.val('');
            $vendorText
                .val('')
                .removeClass('V~M')
                .addClass('V~O')
                .attr('readonly', false);
            $(".section-account_name #removeTextValue").show();
            $(".section-account_name #showCustomer1").show();

            $manualInput.val(manualName || '');

            $extManualBlock.show();
            $extSelectBlock.hide();
            $extManualInput.val('');
            $extManualBlock.trigger('show-external-manual'); 
        }

        function applyExistingCustomer(vendorId, vendorName) {
            $blockManual.hide();
            $blockVendor.show();

            $vendorHidden.val(vendorId || '');
            $vendorText
                .val(vendorName || '')
                .removeClass('V~O')
                .addClass('V~M')
                .attr('readonly', true);
            $(".section-account_name #removeTextValue").hide();
            $(".section-account_name #showCustomer1").hide();

            $extManualBlock.hide();
            $extSelectBlock.show();
            
        }

        function fillFromLead() {
            var leadNo = $relatedRec.val();
            var tabid  = $relatedTo.val();
            if ((tabid != '7') || !leadNo) {
                return;      
            }
            $.ajax({
                url: 'getleaddetails',
                type: 'GET',
                dataType: 'json',
                data: { lead_no: leadNo },
                success: function (res) {
                    if (!res) return;

                    var customerType  = res.customer_type;
                    var manualName    = res.account_name || ''; 
                    var vendorId      = res.vendor || res.vendoraccid || '';
                    var vendorAccName = res.vendor_acc_name || ''; 

                    if (customerType == '1') {
                        applyNewCustomer(manualName);
                    } else {
                        applyExistingCustomer(vendorId, vendorAccName);
                    }
                },
                error: function () {
                    stopLoading();
                }
            });
        }
        $blockManualExt.hide();
        $blockManual.hide();
        fillFromLead();
    }

    leadCheck(); 
(function() {
    'use strict';

    function initEmailField() {
        var fieldId = 'external_participants_manual';
        var original = document.getElementById(fieldId);
        if (!original) return;

        // Avoid double init
        if (document.getElementById(fieldId + '_container')) return;

        // Read existing DB value (comma separated) and normalize
        var initialValue = (original.value || '').trim();
        var initialEmails = initialValue
            ? initialValue.split(',').map(function(e){ return e.trim(); }).filter(Boolean)
            : [];

        // Build container
        var container = document.createElement('div');
        container.id = fieldId + '_container';
        container.style.cssText = '' +
            'border:1px solid #ced4da;border-radius:0.25rem;' +
            'padding:0.375rem 0.75rem;min-height:38px;' +
            'display:flex;flex-wrap:wrap;gap:4px;' +
            'align-items:flex-start;background:#fff;cursor:text;';

        var inputWrapper = document.createElement('div');
        inputWrapper.style.cssText = 'flex:1;min-width:120px;display:flex;align-items:center;';

        var newInput = original.cloneNode(true);
        newInput.id = fieldId + '_input';
        newInput.removeAttribute('maxlength');
        newInput.value = ''; // start empty; use chips for existing values
        newInput.placeholder = 'Enter emails (comma, enter separated)';
        newInput.style.cssText = '' +
            'border:none;outline:none;box-shadow:none;' +
            'background:transparent;padding:4px 0;margin:0;' +
            'font-size:inherit;flex:1;min-width:100px;height:auto;line-height:normal;width:auto;';

        inputWrapper.appendChild(newInput);
        container.appendChild(inputWrapper);

        // Replace original with container + hidden field
        original.parentNode.replaceChild(container, original);

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'meeting_information[external_participants_manual]';
        hidden.id = fieldId;
        container.appendChild(hidden);

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var validEmails = [];

        function syncHidden() {
            hidden.value = validEmails.join(',');
        }

        function createChip(email) {
            var chip = document.createElement('div');
            chip.dataset.email = email.toLowerCase();
            chip.style.cssText = '' +
                'background:#d4edda;color:#155724;border-radius:16px;' +
                'padding:2px 8px;font-size:0.875rem;display:flex;' +
                'align-items:center;gap:4px;max-width:calc(100% - 30px);';

            var span = document.createElement('span');
            span.textContent = email;
            span.title = email;
            span.style.cssText = 'overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;';
            chip.appendChild(span);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '&times;';
            btn.style.cssText = '' +
                'background:none;border:none;color:inherit;font-size:1.2rem;' +
                'cursor:pointer;padding:0 4px;line-height:1;opacity:0.7;flex-shrink:0;';
            btn.onclick = function(e) {
                e.stopPropagation();
                removeEmail(email.toLowerCase());
            };
            chip.appendChild(btn);

            container.insertBefore(chip, inputWrapper);
        }

        function addEmail(raw) {
            var email = (raw || '').trim();
            var lower = email.toLowerCase();
            if (!email || !emailRegex.test(email)) return;        
            if (validEmails.indexOf(lower) !== -1) return;        
            validEmails.push(lower);
            createChip(email);
            syncHidden();
            newInput.value = '';
        }

        function removeEmail(lower) {
            validEmails = validEmails.filter(function(e){ return e !== lower; });
            var chips = container.querySelectorAll('div[data-email]');
            Array.prototype.forEach.call(chips, function(chip) {
                if (chip.dataset.email === lower) chip.remove();
            });
            syncHidden();
        }

        // Pre-populate from DB value
        initialEmails.forEach(function(e){ addEmail(e); });

        // Events
        newInput.addEventListener('keydown', function(e) {
            var val = newInput.value.trim();
            if ((e.key === 'Enter' || e.key === ',') && val) {
                e.preventDefault();
                addEmail(val);
            } else if (e.key === 'Backspace' && !val && validEmails.length) {
                removeEmail(validEmails[validEmails.length - 1]);
            }
        });

        newInput.addEventListener('blur', function() {
            var val = newInput.value.trim();
            if (val) addEmail(val);
        });

        container.addEventListener('click', function(ev) {
            if (!ev.target.closest('button')) newInput.focus();
        });

        // Final safety on submit
        document.addEventListener('submit', function() {
            var val = newInput.value.trim();
            if (val) addEmail(val);
            syncHidden();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEmailField);
    } else {
        initEmailField();
    }
    setTimeout(initEmailField, 100);
})();
 function getQueryParam(param) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param); 
    }


    var sourcemodule = getQueryParam('sourcemodule');
    var sourceid = getQueryParam('sourceid');

    if (sourcemodule && sourceid) {
        getaccountname(sourceid,sourcemodule);
    }
var targetNode_v = document.getElementById("related_to_id1");
var observer = new MutationObserver(function (mutationsList) {
  for (var mutation of mutationsList) {
    if (
      mutation.type === "attributes" &&
      mutation.attributeName === "value"
    ) {
      console.log("rel value changed to:", targetNode_v.value);
      var related_to = document.getElementById("related_to").value;

      getaccountname(targetNode_v.value,related_to);
    }
  }
});
if (targetNode_v) {
    observer.observe(targetNode_v, {
        attributes: true,
        attributeFilter: ["value"]
    });
}

function getaccountname(related_to_id,related_to) {
  data = {
    related_to: related_to,
    related_to_id: related_to_id,
    _csrf: $("#csrfToken").val(),
  };
  if(related_to == 7){
    leadCheck(); return;
  }
   $("#account_name1").val('');
       $("#account_name").val('');
       $("#account_name").attr("readonly",false);
        $(".section-account_name").find("#removeTextValue").css("display","block");
       $(".section-account_name").find("#showCustomer1").css("display","block");

  $.ajax({
    type: "POST",
    url: "getaccountname",
    data: data,
    success: function (response) {

      if (response && response.data) {

            $("#account_name1").val(response.data.vendor || "");
            $("#account_name").val(response.data.acc_name || "")
                              .attr("readonly", true);

            $(".section-account_name #removeTextValue").hide();
            $(".section-account_name #showCustomer1").hide();

            $(".section-account_name").show();
            $("#account_name").removeClass("V~O").addClass("V~M");

            $(".section-acc_name").hide();
            $("#acc_name").val("")
                          .removeClass("V~M")
                          .addClass("V~O");

        }
        else if (
            response &&
            response.status === "error"
        ) {

            $("#account_name1").val("");
            $("#account_name").removeClass("V~M").addClass("V~O");

            $(".section-account_name").hide();
            $(".section-acc_name").show();

            $("#acc_name").removeClass("V~O").addClass("V~M");

        }
      else {
        console.log("Invalid response format or missing data");
      }
    },
    error: function (data) {

      alert("Error occured.please try again");
    },
    dataType: "json",
  });
}
});