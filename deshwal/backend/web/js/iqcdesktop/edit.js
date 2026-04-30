$(document).ready(function () {
    var newURL = window.location.href;
    var newURL = window.location.href;
    var module = "iqcdesktop";
    var str = newURL.split(module);
    var editusrl = str[0] + "iqcdesktop/list";
    let today = new Date().toISOString().split("T")[0];
    function toggleModels() {
        var selectedMake = $('#make').val() || null;
        var selectedValue = $('#model').val() || null;
        var data = {
            make: selectedMake,
            _csrf: $('#csrfToken').val()
        };
        $.ajax({
            url: 'getmodels',
            type: 'POST',
            data: data,
            success: function(response){
                if (response && response.data) {
                    // Assuming response is an array of models
                    var models = response.data;
                    var $modelSelect = $('#model');

                    // Clear existing options
                    $modelSelect.empty();

                    // Add a placeholder option
                    $modelSelect.append('<option value="">Select a model</option>');

                    // Add new options
                    $.each(models, function(index, model){
                        var option = $('<option></option>')
                            .attr('value', model.value)
                            .text(model.text);
                        if (model.value == selectedValue) {
                            option.attr('selected', 'selected');
                        }
                        $modelSelect.append(option);
                    });

                    // Refresh Select2
                    $modelSelect.trigger('change');
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function(xhr, status, error){
                // Handle error response
                console.log('Error:', error);
            },
            dataType: 'json'
        });
    }
    function toggleMotherBoardFields() {
        var selectedValue = $('input[name="iqc_desktop[motherboard]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-ram_slot').show();
            $('.section-cpu').show();
            $('.section-cabinate').show();
            $('.section-generation').show();
            $('.section-slot_no').show();
            $('.section-usb').show();
            $('.section-ram').show();
            $('.section-motherboard_status').show();
            //hdd_casing, display,, smps
            $('.section-hdd').show();
            
            $('.section-cpu_status').show();
        } else {
            $('.section-ram_slot').hide();
            $('input[name="iqc_desktop[ram]"]:checked').prop('checked', false);
            $('.section-cpu').hide();
            $('input[name="iqc_desktop[cpu]"]:checked').prop('checked', false);
            $('.section-cabinate').hide();
            $('#cabinate').val("");
            $('.section-generation').hide();
            $('#generation').val("");
            $('.section-slot_no').hide();
            $('input[name="iqc_desktop[slot_no]"]:checked').prop('checked', false);

            $('.section-usb').hide();
            $('input[name="iqc_desktop[usb]"]:checked').prop('checked', false);
            $('.section-ram').hide();
            $('input[name="iqc_desktop[ram]"]:checked').prop('checked', false);
            $('.section-hdd').hide();
            $('input[name="iqc_desktop[hdd]"]:checked').prop('checked', false);
            $('.section-motherboard_status').hide();
            $('input[name="iqc_desktop[motherboard_status]"]:checked').prop('checked', false);
            
            $('.section-cpu_status').hide();
            $('input[name="iqc_desktop[cpu_status]"]:checked').prop('checked', false);
        }
        toggleOtherDescription();
        toggleOtherDescriptionProcessor();
        toggleCPUProcessor();
        toggleRamCapacity();
        toggleRamDescription();
        toggleHDDChilds();
        toggleHDDCapacity();
        toggleSMPSstatus();
    }
    function toggleOtherDescription() {
        var selectedValue = $('#generation').val();
        if (selectedValue && selectedValue == "16") {
            $('.section-provide_description_mb').show();
        } else {
            $('.section-provide_description_mb').hide();
            $('#provide_description_mb').val("");
        }
    }
    function toggleOtherDescriptionProcessor() {
        var selectedValue = $('#processors').val();
        if (selectedValue && selectedValue == "10") {
            $('.section-provide_description_cpu').show();
        } else {
            $('.section-provide_description_cpu').hide();
            $('#provide_description_cpu').val("");
        }
    }
    function toggleCPUProcessor() {
        var selectedValue = $('input[name="iqc_desktop[cpu]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-processors').show();
        } else {
            $('.section-processors').hide();
            $('#processors').val(null).trigger('change');
        }
    }
    function toggleRamCapacity() {
        var selectedValue = $('input[name="iqc_desktop[ram]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-capacity').show();
            $('.section-ram_status').show();
        } else {
            $('.section-capacity').hide();
            $('#capacity').val("").trigger('change');
            $('.section-ram_status').hide();
            $('input[name="iqc_desktop[ram_status]"]:checked').prop('checked', false);
        }
    }
    function toggleRamDescription() {
        var selectedValue = $('#capacity').val();
        if (selectedValue && selectedValue == "12") {
            $('.section-provide_description_ram').show();
        } else {
            $('.section-provide_description_ram').hide();
            $('#provide_description_ram').val("");
        }
    }
    function toggleHDDChilds() {
        var selectedValue = $('input[name="iqc_desktop[hdd]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-hdd_category').show();
        } else {
            $('.section-hdd_category').hide();
            $('#hdd_category').val("").trigger('change');
            $('.section-hdd_capacity').hide();
            $('#hdd_capacity').val("").trigger('change');
            $('.section-provide_description_hdd').hide();
            $("#provide_description_hdd").val("");
            $(".section-health_per").hide();
            $('input[name="iqc_desktop[health_per]"]:checked').prop('checked', false);
        }
    }
    function toggleHDDCapacity() {
        var selectedValue = $('#hdd_category').val();
        if (selectedValue && selectedValue == "5") {
            $('.section-hdd_capacity').hide();
            $('#hdd_capacity').val("").trigger('change');
            $('.section-provide_description_hdd').show();
            $(".section-health_per").hide();
            $('input[name="iqc_desktop[health_per]"]:checked').prop('checked', false);
        } else {
            var selectedHDDOption = $('input[name="iqc_desktop[hdd]"]:checked').val();
            if (selectedHDDOption && selectedHDDOption == 1) {
                $('.section-hdd_capacity').show();
            }
            $('.section-provide_description_hdd').hide();
            $("#provide_description_hdd").val("");
        }
        if (selectedValue && selectedValue != 5) {
            $('.section-health_per').show();
        } else {
            $('.section-health_per').hide();
            $('input[name="iqc_desktop[health_per]"]:checked').prop('checked', false);
        }
    }
    function toggleSMPSstatus() {
        var selectedValue = $('input[name="iqc_desktop[smps]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-smps_status').show();
        } else {
            $('.section-smps_status').hide();
            $('input[name="iqc_desktop[smps_status]"]:checked').prop('checked', false);
        }
    }
    function fetchHDDCapacity() {
        var selectedValue = $('#hdd_category').val() || null;
        var data = {
            make: selectedValue,
            _csrf: $('#csrfToken').val()
        };
        $.ajax({
            url: 'gethddcapacity',
            type: 'POST',
            data: data,
            success: function(response){
                if (response && response.data) {
                    // Assuming response is an array of models
                    var models = response.data;
                    var $modelSelect = $('#hdd_capacity');
                    // Clear existing options
                    $modelSelect.empty();
                    // Add a placeholder option
                    $modelSelect.append('<option value="">---Select---</option>');
                    // Add new options
                    $.each(models, function(index, model){
                        var option = $('<option></option>')
                            .attr('value', model.value)
                            .text(model.text);
                        if (model.value == selectedValue) {
                            option.attr('selected', 'selected');
                        }
                        $modelSelect.append(option);
                    });

                    // Refresh Select2
                    $modelSelect.trigger('change');
                } else {
                    console.log("Invalid response format or missing data");
                }
            },
            error: function(xhr, status, error){
                // Handle error response
                console.log('Error:', error);
            },
            dataType: 'json'
        });
    }
    toggleModels();
    toggleMotherBoardFields();
    $('#make').change(function(){
        toggleModels();
    });

    $('input[name="iqc_desktop[motherboard]"]').change(function(){
        toggleMotherBoardFields();
    });
    $('#generation').change(function () {
        toggleOtherDescription();
    })
    $('#processors').change(function () {
        toggleOtherDescriptionProcessor();
    })
    $('input[name="iqc_desktop[cpu]"]').change(function(){
        toggleCPUProcessor();
    });
    $('input[name="iqc_desktop[ram]"]').change(function(){
        toggleRamCapacity();
    });
    $('#capacity').change(function () {
        toggleRamDescription();
    })
    $('input[name="iqc_desktop[hdd]"]').change(function(){
        toggleHDDChilds();
    });
    $('#hdd_category').change(function () {
        toggleHDDCapacity();
    })

    $('input[name="iqc_desktop[smps]"]').change(function () {
        toggleSMPSstatus();
    })

    $(".c-faqs__item-question").trigger("click");
});

//////////////////// on the class end validation code zitendra /////////////////////////
 
////////////////////end validation code zitendra /////////////////////////
document.querySelectorAll(".accordion-toggle").forEach(button => {
  button.addEventListener("click", () => {
    const content = button.closest(".accordion-item").querySelector(".accordion-content");
    const upArrow = button.querySelector(".up");
    const downArrow = button.querySelector(".down");
    if (content.style.display === "block") {
      content.style.display = "none"; // Hide content
      upArrow.style.display = "none"; // Hide up arrow
      downArrow.style.display = "inline"; // Show down arrow
    } else {
      content.style.display = "block"; // Show content
      upArrow.style.display = "inline"; // Show up arrow
      downArrow.style.display = "none"; // Hide down arrow
    }
  });
});
// Tab Switching Logic
document.querySelectorAll(".tab").forEach(tab => {
  tab.addEventListener("click", function () {
    // Remove active class from all tabs and contents
    document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
    document.querySelectorAll(".tab-content-detail-view").forEach(content => content.classList.remove("active"));
    // Add active class to clicked tab and corresponding content
    this.classList.add("active");
    const tabId = this.getAttribute("data-tab");
    document.getElementById(tabId).classList.add("active");
  });
});