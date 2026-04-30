$(document).ready(function () {
  var signaturePad
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);
  const slicestr = newURL.substring(0, str);
  function showErrorMessage(message) {
    $(".custom-alert").remove()
    var alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert alert alert-danger alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <strong>Error! </strong> ${message}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    $('#myDIV').append(alertDiv);
    setTimeout(() => {
        $(alertDiv).fadeOut(500, function () {
            $(this).remove();
        });
    }, 30000);
  }
  function showSuccessMessage(message) {
    $(".custom-alert").remove()
    var alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert alert alert-success alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    $('#myDIV').append(alertDiv);
    setTimeout(() => {
        $(alertDiv).fadeOut(500, function () {
            $(this).remove();
        });
    }, 30000);
  }
  function getexchangerate(data) {
    $.ajax({
      type: 'POST',
      url: slicestr + "leads/getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //location.reload();
        $("#exchange_rate").val(data);

      },
      error: function (data) { // if error occured

        alert('Error occured.please try again');
      },
      dataType: 'html'
    });

  }
  function fetchOpportunityDetails() {
    data = { opportunity: $("#opportunity_name1").val(), _csrf: $('#csrfToken').val() };
    $("#loading-overlay").css("display", "grid");
      $.ajax({
        type: 'POST',
        url: "getopportunity",
        data: data,
        success: function (response) {
            $("#loading-overlay").css("display", "none");
            if (response && response.data) {
                $("#account_name").val(response.data.account_name);
                $("#spoc_name").val(response.data.spoc_name);
                $("#spoc_mobile_number").val(response.data.spoc_mobile);
                $("#bill_address").val(response.data.bill_address);
                $("#bill_location").val(response.data.bill_location);
                $("#state").val(response.data.bill_state);
                $("#pincode").val(response.data.bill_pincode);
                $("#gstin_no").val(response.data.bill_gstin_no);
                $("#city").val("");
            } else {
                console.log("Invalid response format or missing data");
            }
        },
        error: function (data) { // if error occured
          alert('Error occured.please try again');
          $("#loading-overlay").css("display", "none");
        },
        dataType: 'json'
      });
  }
  function fetchActivitySpocDetails() {
    data = { spoc: $("#activtiy_spoc1").val(), _csrf: $('#csrfToken').val() };
    $("#loading-overlay").css("display", "grid");
    $("#activtiy_spoc_email").val("");
    $("#activtiy_spoc_mobile").val("");
    $.ajax({
      type: 'POST',
      url: "getspoc",
      data: data,
      success: function (response) {
          $("#loading-overlay").css("display", "none");
          if (response && response.data) {
              $("#activtiy_spoc_email").val(response.data.spoc_email);
              $("#activtiy_spoc_mobile").val(response.data.spoc_mobile);
          } else {
              console.log("Invalid response format or missing data");
          }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        $("#loading-overlay").css("display", "none");
      },
      dataType: 'json'
    });
  }
  function fetchBillSpocDetails() {
    data = { spoc: $("#bill_spoc1").val(), _csrf: $('#csrfToken').val() };
    $("#loading-overlay").css("display", "grid");
    $("#bill_spoc_number").val("");
    $("#bill_spoc_email").val("");
    $.ajax({
      type: 'POST',
      url: "getspoc",
      data: data,
      success: function (response) {
          $("#loading-overlay").css("display", "none");
          if (response && response.data) {
              $("#bill_spoc_email").val(response.data.spoc_email);
              $("#bill_spoc_number").val(response.data.spoc_mobile);
          } else {
              console.log("Invalid response format or missing data");
          }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        $("#loading-overlay").css("display", "none");
      },
      dataType: 'json'
    });
  }
  function fetchSpocDetails() {
    data = { spoc: $("#spoc_name1").val(), _csrf: $('#csrfToken').val() };
    $("#loading-overlay").css("display", "grid");
    $("#spoc_mobile_number").val("");
    $.ajax({
      type: 'POST',
      url: "getspoc",
      data: data,
      success: function (response) {
          $("#loading-overlay").css("display", "none");
          if (response && response.data) {
              $("#spoc_mobile_number").val(response.data.spoc_mobile);
          } else {
              console.log("Invalid response format or missing data");
          }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        $("#loading-overlay").css("display", "none");
      },
      dataType: 'json'
    });
  }
  function fetchPickupSpocDetails() {
    data = { spoc: $("#pickup_spoc1").val(), _csrf: $('#csrfToken').val() };
    $("#loading-overlay").css("display", "grid");
    $("#pickup_spoc_number").val("");
    $.ajax({
      type: 'POST',
      url: "getspocuser",
      data: data,
      success: function (response) {
          $("#loading-overlay").css("display", "none");
          if (response && response.data) {
              $("#pickup_spoc_number").val(response.data.spoc_mobile);
          } else {
              console.log("Invalid response format or missing data");
          }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        $("#loading-overlay").css("display", "none");
      },
      dataType: 'json'
    });
  }
  function fetchDeliverySpocDetails() {
    data = { spoc: $("#receiver_spoc_name1").val(), _csrf: $('#csrfToken').val() };
    $("#loading-overlay").css("display", "grid");
    $("#receiver_spoc_number").val("");
    $.ajax({
      type: 'POST',
      url: "getspocuser",
      data: data,
      success: function (response) {
          $("#loading-overlay").css("display", "none");
          if (response && response.data) {
              $("#receiver_spoc_number").val(response.data.spoc_mobile);
          } else {
              console.log("Invalid response format or missing data");
          }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        $("#loading-overlay").css("display", "none");
      },
      dataType: 'json'
    });
  }
  function fetchLocation(location_type) {
    let data = {}
    let url = "getlocationddress";
    if (location_type == "pickup") {
      data = { location: $("#pickup_location_client1").val(), _csrf: $('#csrfToken').val() };
      $("#pickup_address,#pickup_city,#pickup_state,#pickup_pin").val("");
    } else if (location_type == "activity") {
      var sourcing_deal = $("#opportunity_name1").val();
      data = { location: $("#activity_location1").val(),sourcing_deal:sourcing_deal, _csrf: $('#csrfToken').val() };
      $("#activity_address,#activity_city,#activity_state,#activity_pincode").val("");
    } else if (location_type == "delivery_internal") {
      url = "warehouse"
      data = { warehouse: $("#delivery_location_internal1").val(), _csrf: $('#csrfToken').val() };
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
    } else if (location_type=="pickup_internal") { 
      url = "warehouse"
      data = { warehouse: $("#pickup_location1").val(), _csrf: $('#csrfToken').val() };
    } else if (location_type == "delivery_client") {
      data = { location: $("#delivery_location_client1").val(), _csrf: $('#csrfToken').val() };
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
    } else if (location_type == "billing") {
      data = { location: $("#bill_location1").val(), _csrf: $('#csrfToken').val() };
      $("#bill_address,#city,#state,#pincode").val("");
    }
    $("#loading-overlay").css('display', 'grid');
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        success: function (response) {
            $("#loading-overlay").css('display', 'none');
            console.log(response);
          if (response && response.data) {
            if (location_type == "pickup" || location_type=="pickup_internal") { 
              $("#pickup_address").val(response.data.address);
              $("#pickup_city").val(response.data.city_name);
              $("#pickup_state").val(response.data.state);
              $("#pickup_pin").val(response.data.pincode);
            }else if (location_type == "activity") { 
              $("#activity_address").val(response.data.address);
              $("#activity_city").val(response.data.city_name);
              $("#activity_state").val(response.data.state);
              $("#activity_pincode").val(response.data.pincode);

              //start here
              let hdd_count = response.hdd_count || "";
              let billable_type = response.billable_type || null;
              let bill_to_locations = response.bill_to_locations || "";
              let total_exclusive_gst = response.total_exclusive_gst || "";
              //alert(hdd_count);
              $("#hdd_count").val(hdd_count);
              $("#billable").val(billable_type).trigger("change")
              $("#billing_amount").val(total_exclusive_gst);
              $("#bill_location").attr("data-dynamic-dependent", bill_to_locations);
            }else if (location_type == "delivery_internal" || location_type == "delivery_client") { 
              $("#delivery_address").val(response.data.address);
              $("#delivery_city").val(response.data.city_name);
              $("#delivery_state").val(response.data.state);
              $("#delivery_pin").val(response.data.pincode);
            }else if (location_type == "billing") { 
              $("#bill_address").val(response.data.address);
              $("#city").val(response.data.city_name);
              $("#state").val(response.data.state);
              $("#pincode").val(response.data.pincode);
              $("#gstin_no").val(response.data.gstin_no_uin)
            }
          } else {
            console.log("Invalid response format or missing data");
          }
        },
        error: function (data) { // if error occured
            $("#loading-overlay").css('display', 'none');
            alert('Error occured.please try again');
        },
        dataType: 'json'
    });
  }
  function manageAggrementCopy() {
    var aggrement = $("#agreement").val();
    if (aggrement == 2) {
      $(".section-agreement_copy").show();
    } else {
      $(".section-agreement_copy").hide();
      $("#agreement_copy").val("")
    }
  }
  function manageEmailDate() {
    var email_confirmation = $("#email_confirmation").val();
    if (email_confirmation == 2) {
      $(".section-email_date").show();
    } else {
      $(".section-email_date").hide();
      $("#email_date").val("")
    }
  }
  function manageProvisionToExtendTiming() {
    var extend_time_provision = $("#extend_time_provision").val();
    if (extend_time_provision == 1) {
      $("#extension_provision").prop("readonly", false).removeClass("readonly")
    } else {
      $("#extension_provision").val(null).prop("readonly", true).addClass("readonly").trigger("change");
    }
  }
  function manageServiceLift() {
    var service_lift = $("#service_lift").val();
    if (service_lift == 1) {
      $("#lift_timings").prop("readonly", false).removeClass("readonly")
      $("#stairs_area").val(null).prop("readonly", true).addClass("readonly").trigger("change");
    } else if (service_lift == 2) {
      $("#lift_timings").val(null).prop("readonly", true).addClass("readonly").trigger("change");
      $("#stairs_area").prop("readonly", false).removeClass("readonly")
    } else {
      $("#lift_timings").val(null).prop("readonly", true).addClass("readonly").trigger("change");
      $("#stairs_area").val(null).prop("readonly", true).addClass("readonly").trigger("change");
    }
  }
  function manageDeliveryLocation() {
    var delivery_location_type = $("#delivery_location_type").val();
    if (delivery_location_type == 1) {
      $(".section-delivery_location_internal").show()
      $(".section-delivery_location_client,.section-delivery_location_engineer").hide()
      $("#delivery_location_client1,#delivery_location_client,#delivery_location_engineer").val("")
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", true).addClass("readonly");
    } else if (delivery_location_type == 2) {
      $(".section-delivery_location_internal,.section-delivery_location_engineer").hide()
      $("#delivery_location_internal1,#delivery_location_internal,#delivery_location_engineer").val("")
      $(".section-delivery_location_client").show()
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", true).addClass("readonly");
    } else if (delivery_location_type == 3) {
      $(".section-delivery_location_internal,.section-delivery_location_client").hide()
      $(".section-delivery_location_engineer").show()
      $("#delivery_location_internal1,delivery_location_internal,#delivery_location_client1,#delivery_location_client").val("")
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", false).removeClass("readonly")
    } else {
      $(".section-delivery_location_internal,.section-delivery_location_client,.section-delivery_location_engineer").hide()
      $("#delivery_location_internal1,#delivery_location_internal,#delivery_location_engineer,#delivery_location_client1,#delivery_location_client").val("")
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", true).addClass("readonly").val("");
    }
  }
  function managePickupLocation() {
    var pickup_location_type = $("#pickup_location_type").val();
    if (pickup_location_type == 1) {
      $(".section-pickup_location").show()
      $(".section-pickup_location_client,.section-pickup_location_engineer").hide()
      $("#pickup_location_client1,#pickup_location_client,#pickup_location_engineer").val("")
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", true).addClass("readonly");
    } else if (pickup_location_type == 2) {
      $(".section-pickup_location,.section-pickup_location_engineer").hide()
      $("#pickup_location1,#pickup_location,#pickup_location_engineer").val("")
      $(".section-pickup_location_client").show()
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", true).addClass("readonly");
    } else if (pickup_location_type == 3) {
      $(".section-pickup_location,.section-pickup_location_client").hide()
      $(".section-pickup_location_engineer").show()
      $("#pickup_location1,pickup_locationl,#pickup_location_client1,#pickup_location_client").val("")
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", false).removeClass("readonly")
    } else {
      $(".section-pickup_location,.section-pickup_location_client,.section-pickup_location_engineer").hide()
      $("#pickup_location1,#pickup_location,#pickup_location_engineer,#pickup_location_client1,#pickup_location_client").val("")
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", true).addClass("readonly").val("");
    }
  }
  function manageFieldsDynamicConditions() {
    var service_to_locations = $("#activity_location1").val()
    var bill_to_locations = $("#bill_location1").val()
    $("#activity_location").attr("data-dynamic-dependent", service_to_locations)
    $("#bill_location").attr("data-dynamic-dependent", bill_to_locations);
  }
  function manageDongleMovement() {
    var hsap_key_require = $("#hsap_key_require").val();
    if (hsap_key_require == 1) {
      $(".blocktitle2610").parents(".titlerow").show()
      $(".blocktitle2611").parents(".titlerow").show()
    } else {
      $("#hsap_count").val("");
      $(".blocktitle2610").parents(".titlerow").hide()
      $(".blockrow2611,.blockrow2610").find("input, select").each(function () {
        if ($(this).is("select")) {
            $(this).val(null).trigger("change");
        } else {
            $(this).val("");
        }
      });
      $(".blocktitle2611").parents(".titlerow").hide()
    }
    
  }
  function getFeDetails() {
        data = { user: $("#fe_name1").val(), _csrf: $('#csrfToken').val() };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: 'POST',
            url: "getuserdetails",
            data: data,
            success: function (response) {
                $("#loading-overlay").css('display', 'none');
                console.log(response);
                if (response && response.data) {
                    $("#fe_number").val(response.data.mobile);
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function (data) { // if error occured
                alert('Error occured.please try again');
                $("#loading-overlay").css('display', 'none');
            },
            dataType: 'json'
        });
  }
  function getLogistcExeDetails() {
      data = { user: $("#logistic_spoc_name1").val(), _csrf: $('#csrfToken').val() };
      $("#loading-overlay").css('display', 'grid');
      $.ajax({
          type: 'POST',
          url: "getuserdetails",
          data: data,
          success: function (response) {
              $("#loading-overlay").css('display', 'none');
              console.log(response);
              if (response && response.data) {
                  $("#logistic_spoc_number").val(response.data.mobile);
              } else {
                  console.log("Invalid response format or missing data");
              }
          },
          error: function (data) { // if error occured
              alert('Error occured.please try again');
              $("#loading-overlay").css('display', 'none');
          },
          dataType: 'json'
      });
  }
  async function getvendor() {
      data = { opportuity_name1: $("#opportunity_name1").val(), _csrf: $('#csrfToken').val() };
      try {
          $("#loading-overlay").css('display', 'grid');
          let response = await $.ajax({
              type: 'POST',
              url: "getvendor",
              data: data,
              dataType: 'json'
          });
          if (response && response.data) {
            let account = response.data.account || null;
            // let hdd_count = response.data.hdd_count || null;
            // let related = response.data.related || null;
            // let billable_type = response.data.billable_type || null;
            let service_to_locations = response.data.service_to_locations || "";
            if (account) {
              $("#account_name").val(account.acc_name);
              $("#account_name1").val(account.vendor_account_name);
              // if (hdd_count) {
              //   $("#hdd_count").val(hdd_count);
              // }
              $("#billing_type").val(account.billing_type).trigger("change")
              $("#activity_location").attr("data-dynamic-dependent", service_to_locations);
              
              $("#productTable2613").find(".remove-row-btn").each(function () {
                  $(this).trigger("click");
              });
              // if (related && Array.isArray(related)) {
              //     for (const item of related) {
              //         await addAssetsDynamicRows(item);
              //     }
              // }
            } else {
                console.log("Invalid response format or missing data");
            }
        }
        $("#loading-overlay").css('display', 'none');
      }catch (error) {
          $("#loading-overlay").css('display', 'none');
          alert('Error occurred. Please try again.');
      }
  }
  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $('#csrfToken').val() };
    getexchangerate(data);
  });
  //end exchange rate
  $(document).on("change", "#agreement", function () {
    manageAggrementCopy();
  })
  $(document).on("change", "#email_confirmation", function () {
    manageEmailDate();
  })
  $(document).on("change", "#extend_time_provision", function () {
    manageProvisionToExtendTiming();
  })
  $(document).on("change", "#delivery_location_type", function () {
    manageDeliveryLocation();
  })
  $(document).on("change", "#pickup_location_type", function () {
    managePickupLocation();
  })
  $(document).on("change", "#service_lift", function () {
    manageServiceLift();
  })
  $(document).on("change", "#hsap_key_require", function () {
    manageDongleMovement();
  })
  
  $(document).on("change", "select[id^='drilling_completed_']", function () {
    
    let index = $(this).attr("id").match(/drilling_completed_(\d+)/)[1];
    let certField = $(`#certificate_${index}`);
    let helpBlock = certField.closest(".form-group").find(".help-block");
    if ($(this).val() == "1") {
      //certField.prop("required", true);
      certField.removeClass("F~O").addClass("F~M");
      //helpBlock.text("Uploading a certificate is required.");
    } else {
      //certField.prop("required", false);
      certField.removeClass("F~M").addClass("F~O");
      helpBlock.text("");
    }
  });
  $(document).on("click", ".drilling-completed ", function () {
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            drilling_completed: "Yes",
        };
        //alert("dgd");
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            type: "POST",
            url: "drillingcompleted",
            data: data,
            success: function (data) {
                if (data.status === "success") {
                  $(".drilling_completed ,.add-lead-btn2").remove()
                  showSuccessMessage(data.message || "Updated successfully")
                  location.reload();
                } else {
                  $("#loading-overlay").css('display', 'none');
                    showErrorMessage(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                showErrorMessage("Error occured.please try again");
            },
            dataType: "json",
        });
        
  });
  
  $(document).on("click", ".drilling-client-sign", function () {
        let data = {
            Recordid: $("#Recordid").val(),
            _csrf: $("#csrfToken").val(),
            drilling_asset_details: "Yes",
        };
    $("#loading-overlay").css('display', 'grid');
    $("#detailViewGeneralLabel").text("Drilling Assets")
        $.ajax({
            type: "POST",
            url: "drillingassets",
            data: data,
          success: function (data) {
              $("#loading-overlay").css('display', 'none');
                if (data.status === "success") {
                  let asset_data = data.data || [];
                  let dynamic_html = `<table class="table table-bordered text-center">
                                        <thead class="align-middle">
                                            <tr class="table-info">
                                                <th>#</th>
                                                <th>Laptop Serial No</th>
                                                <th>HDD Serial No</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                  if (asset_data) {
                        let tabular_data = asset_data.map((ele,index) => `
                        <tr>
                        <td>${++index}</td>
                        <td>${ele.laptop_serial_no}</td>
                        <td>${ele.hdd_sdd_serial_no}</td>
                        </tr>`).join("");
                    dynamic_html = dynamic_html + tabular_data;
                  }
                  dynamic_html = dynamic_html + `<tr><td></td><td>Client Signature</td>
                  <td><div><canvas id="signature-pad"></canvas></div>
                      <div class="text-center"><button class="btn btn-danger clear_image">Clear</button></div>
                    </td></tr>`;
                  dynamic_html = dynamic_html + `</tbody></table>`
                  $(".modal-dynamic-content").html(dynamic_html);
                  $(".clear_image").trigger("click");
                } else {
                  
                  $("#detail-view-general-info").modal("hide")
                  showErrorMessage(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
              $("#loading-overlay").css('display', 'none');
              $("#detail-view-general-info").modal("hide")
              showErrorMessage("Error occured.please try again");
            },
            dataType: "json",
        });
        
  });
  $(document).on("click", "#detail-view-general-submit", function (e) {
    e.preventDefault()
    e.preventDefault()
    
    if (!signaturePad || signaturePad.isEmpty()) {
        $(".detail-view-general-error").text("Please provide a signature first.");
        return;
    }

    var signatureData = signaturePad.toDataURL("image/png");
    console.log(signatureData)
    if (signatureData) {
      let data = {
        Recordid: $("#Recordid").val(),
        _csrf: $("#csrfToken").val(),
        put_client_sign: "Yes",
        image:signatureData
      };
        $("#loading-overlay").css('display', 'grid');
        $.ajax({
            url: 'drillingclientsign',
            type: 'POST',
            data: data,
            success: function (data) {
                if (data.status === "success") {
                  $(".drilling-client-sign,.add-lead-btn2").remove()
                  $("#detail-view-general-info").modal("hide")
                  showSuccessMessage(data.message || "Updated successfully")
                  location.reload();
                } else {
                  $("#loading-overlay").css('display', 'none');
                  $(".detail-view-general-error").text(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                $(".detail-view-general-error").text(data.errors || "sometinhg went wrong");
            },
            dataType: "json",
        });
    } else {
      $(".detail-view-general-error").text("Please put your signature");
    }
        /*$.ajax({
            type: "POST",
            url: "drillingcompleted",
            data: data,
            success: function (data) {
                if (data.status === "success") {
                  $(".drilling_completed ,.add-lead-btn2").remove()
                  showSuccessMessage(data.message || "Updated successfully")
                  location.reload();
                } else {
                  $("#loading-overlay").css('display', 'none');
                    showErrorMessage(data.errors || "sometinhg went wrong");
                }
            },
            error: function (data) {
                $("#loading-overlay").css('display', 'none');
                showErrorMessage("Error occured.please try again");
            },
            dataType: "json",
        });*/
        
  });
  $(document).on("click", ".clear_image", function () { 
    clearPad()
  })
  function clearPad() {
    var canvas = document.getElementById('signature-pad');
    signaturePad = new SignaturePad(canvas);
    signaturePad.clear();
  }

  manageAggrementCopy()
  manageEmailDate()
  manageProvisionToExtendTiming()
  // manageServiceLift();
  manageDeliveryLocation()
  managePickupLocation()
  manageFieldsDynamicConditions()
  manageDongleMovement()
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // alert(modeInput);
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    data = { currency: 1, _csrf: $('#csrfToken').val() };

    //end ddepika
    getexchangerate(data);
  }
  
  // Create a MutationObserver to detect changes to the opportuniy
  var targetNodeOpportunity = document.getElementById('opportunity_name1');
  var observerOpportunity = new MutationObserver(function (mutationsList) {
      for (var mutation of mutationsList) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            $("#account_name").val("");
            $("#account_name1").val("");
            $("#spoc_name1").val("");
            $("#spoc_name").val("");
          $("#spoc_mobile_number").val("")
          $("#hdd_count").val("")
          $("#billing_amount").val("")
          $("#billable").val(null).trigger("change")
          $("#billing_type").val(null).trigger("change")
            // $("#productTable84").find(".remove-row-btn").each(function () {
            //     $(this).trigger("click");
          // })
          console.log("changed to ",targetNodeOpportunity.value)
            if (targetNodeOpportunity.value !== '') {
                getvendor();
            }
        }
      }
  });
  if (targetNodeOpportunity) {
    observerOpportunity.observe(targetNodeOpportunity, { attributes: true });
  }

  // Create a MutationObserver to detect changes to the activity spoc
  var targetNodeActivitySpoc = document.getElementById('activtiy_spoc1');
  var observerActivitySpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#activtiy_spoc_email").val("");
        $("#activtiy_spoc_mobile").val("");
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            fetchActivitySpocDetails();
        }
      }
  });
  // Configuration for the observer for activtiy spoc (observe attribute changes)
  var configActivitySpoc = { attributes: true };
  observerActivitySpoc.observe(targetNodeActivitySpoc, configActivitySpoc);

  //spoc observer
  var targetNodeSpoc = document.getElementById('spoc_name1');
  var observerSpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#spoc_mobile_number").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodeSpoc.value !== '') {
          fetchSpocDetails();
        }
        }
      }
  });
  observerSpoc.observe(targetNodeSpoc, { attributes: true });
  //Billing spoc observer
  var targetNodeBillingSpoc = document.getElementById('bill_spoc1');
  var observerBillingSpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#bill_spoc_number").val("");
      $("#bill_spoc_email").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodeBillingSpoc.value !== '') {
          fetchBillSpocDetails();
        }
      }
    }
  });
  observerBillingSpoc.observe(targetNodeBillingSpoc, { attributes: true });

  //pickup spoc observer
  var targetNodePickupSpoc = document.getElementById('pickup_spoc1');
  var observerPickupSpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#pickup_spoc_number").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodePickupSpoc.value !== '') {
          fetchPickupSpocDetails();
        }
      }
    }
  });
  observerPickupSpoc.observe(targetNodePickupSpoc, { attributes: true });

  //delivery spoc observer
  var targetNodeDeliverySpoc = document.getElementById('receiver_spoc_name1');
  var observerDeliverySpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#receiver_spoc_number").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodeDeliverySpoc.value !== '') {
          fetchDeliverySpocDetails();
        }
      }
    }
  });
  observerDeliverySpoc.observe(targetNodeDeliverySpoc, { attributes: true });
  //Pickup location
  var targetNodePickupLocation = document.getElementById('pickup_location_client1');
  var observerPickupLocation = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#pickup_address,#pickup_city,#pickup_state,#pickup_pin").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodePickupLocation.value !== '') {
          fetchLocation("pickup");
        }
        }
      }
  });
  observerPickupLocation.observe(targetNodePickupLocation, { attributes: true });

  //Pickup Internal location
  var targetNodePickupLocationInternal = document.getElementById('pickup_location1');
  var observerDeliveryLocationInternal = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#pickup_address,#pickup_city,#pickup_state,#pickup_pin").val("");
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            fetchLocation("pickup_internal");
        }
      }
  });
  observerDeliveryLocationInternal.observe(targetNodePickupLocationInternal, { attributes: true });
  //Billing location
  var targetNodeBillingLocation = document.getElementById('bill_location1');
  var observerBillingLocation = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#bill_address,#city,#state,#pincode,#gstin_no").val("");
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            fetchLocation("billing");
        }
      }
  });
  observerBillingLocation.observe(targetNodeBillingLocation, { attributes: true });
  //Activity location
  var targetNodeActivityLocation = document.getElementById('activity_location1');
  var observerActitvityLocation = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#activity_address,#activity_city,#activity_state,#activity_pincode").val("");
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            fetchLocation("activity");
        }
      }
  });
  observerActitvityLocation.observe(targetNodeActivityLocation, { attributes: true });

  //Delivery Internal location
  var targetNodeDeliveryLocationInternal = document.getElementById('delivery_location_internal1');
  var observerDeliveryLocationInternal = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            fetchLocation("delivery_internal");
        }
      }
  });
  observerDeliveryLocationInternal.observe(targetNodeDeliveryLocationInternal, { attributes: true });

  //Delivery client location
  var targetNodeDeliveryLocationClient = document.getElementById('delivery_location_client1');
  var observerDeliveryLocationClient = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
        $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
            fetchLocation("delivery_client");
        }
      }
  });
  observerDeliveryLocationClient.observe(targetNodeDeliveryLocationClient, { attributes: true });

  // start for FE
    var targetNodeFE = document.getElementById('fe_name1');
    if (targetNodeFE) {
        var observerFE = new MutationObserver(function (mutationsList) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    $("#fe_number").val('');
                    if (targetNodeFE.value !== '') {
                        getFeDetails();
                    }
                }
            }
        });
        observerFE.observe(targetNodeFE, { attributes: true });
    }
    //end for FE

    // start for Logistics Exe
    var targetNodeLogisticExe = document.getElementById('logistic_spoc_name1');
    if (targetNodeLogisticExe) {
        var observerLogisticExe = new MutationObserver(function (mutationsList) {
          for (var mutation of mutationsList) {
              if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                  $("#logistic_spoc_number").val('');
                  if (targetNodeLogisticExe.value !== '') {
                      getLogistcExeDetails();
                  }
              }
          }
      });
      observerLogisticExe.observe(targetNodeLogisticExe, { attributes: true });
    }
    //end for Logistics Exe

  //code added by ptpatel on date 09-05-25
      /////////////create mutation for sourcing deal/////////////////
    // Create a MutationObserver to detect changes to the input vendor account
    function getQueryParam(name) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(name);
  }

    var targetNode = document.getElementById("opportunity_name1");
    if(targetNode.value != '' && getQueryParam('sourcemodule') == 51) //51 is sourcing deal
      getsourcingdetail(targetNode.value)
   
    /////////get sourcing deal detail///////
    function getsourcingdetail(sourcingdeal) {
      // alert("getsourvingcall");
      if (sourcingdeal) {
        data = {
          sourcingdeal: sourcingdeal,
          _csrf: $("#csrfToken").val(),
        };

        $.ajax({
          type: "POST",
          url: "getsourcingdetail",
          // async:false,
          data: data,
          success: function (response) {

            // Check if the data object exists and contains 'first_name'
            if (response && response.data) {
              $("#account_name").val(response.data.acc_name);
              $("#account_name1").val(response.data.vendoraccid);

            } else {
              console.log("Invalid response format or missing data");
            }
          },
          error: function (data) {
            // if error occured

            alert("Error occured.please try again");
          },
          dataType: "json",
        });
      }

    }
    //end code added by ptpatel on date 09-05-25
});
