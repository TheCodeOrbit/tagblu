$(document).ready(function () {
    var newURL = window.location.href;
    var newURL = window.location.href;
    var module = "iqclaptop";
    var str = newURL.split(module);
    var editusrl = str[0] + "iqclaptop/list";
    let today = new Date().toISOString().split("T")[0];
    function toggleSectionCategory() {
        if ($('input[name="iqc_laptop[hdd]"]:checked').val() == "1") {
            $('.section-category').show();
            $('.section-hdd_health').show();
            $('.section-hdd_serial_numbers').show();
        } else {
            $('.section-category').hide();
            $('input[name="iqc_laptop[category]"]:checked').prop('checked', false);
            $('.section-hdd_health').hide();
            $('input[name="iqc_laptop[hdd_health]"]:checked').prop('checked', false);
            $('.section-hdd_serial_numbers').hide();
            $("#hdd_serial_numbers").val("");
        }
    }
    function fetchSsdOptions(selectedCategory) {
        var selectedValue = $('#ssd_capacity').val();
        var data = {
            category: selectedCategory,
            _csrf: $('#csrfToken').val()
        };
        $.ajax({
            url: 'getssdcapacity',
            type: 'POST',
            data: data,
            success: function(response){
                if (response && response.data) {
                    // Assuming response is an array of models
                    var models = response.data;
                    var $modelSelect = $('#ssd_capacity');

                    // Clear existing options
                    $modelSelect.empty();

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
                    $('.section-sd_capacity').show();
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
    function toggleHddcapacity() {
        var categoryValue = $('input[name="iqc_laptop[category]"]:checked').val();
        if (categoryValue) {
            if (categoryValue == "1") {
                $('.section-hdd_capacity').show();
                $('.section-ssd_capacity').hide();
                //$('input[name="iqc_laptop[ssd_capacity]"]:checked').prop('checked', false);
            } else {
                $('.section-hdd_capacity').hide();
                fetchSsdOptions(categoryValue);
                $('input[name="iqc_laptop[hdd_capacity]"]:checked').prop('checked', false);
                $('.section-ssd_capacity').show();
            }
        } else {
            $('.section-hdd_capacity').hide();
            $('.section-ssd_capacity').hide();
            $('input[name="iqc_laptop[hdd_capacity]"]:checked').prop('checked', false);
            //$('input[name="iqc_laptop[ssd_capacity]"]:checked').prop('checked', false);
        }
        if (categoryValue && categoryValue == "7") {
            $('.section-other_category').show();
        } else {
            $('.section-other_category').hide();
            $("#other_category").val("")
        }
    }
    function toggleRamType() {
        if ($('input[name="iqc_laptop[ram]"]:checked').val() == "1") {
            $('.section-ram_type').show();
            if ($('input[name="iqc_laptop[ram_type]"]:checked').val()) {
                $('.section-ram_capacity').show();
                $('.section-ram_status').show();
            } else {
                $('.section-ram_capacity').hide();
                $('.section-ram_status').hide();
            }
        } else {
            $('.section-ram_type').hide();
            $('input[name="iqc_laptop[ram_type]"]:checked').prop('checked', false);
            $('.section-ram_capacity').hide();
            $('input[name="iqc_laptop[ram_capacity]"]:checked').prop('checked', false);
            $('.section-ram_status').hide();
            $('input[name="iqc_laptop[ram_status]"]:checked').prop('checked', false);
        }
    }
    function fetchRamcapacity(ramType) {
        var selectedValue = $('#ram_capacity').val();
        var data = {
            ram_type: ramType,
            _csrf: $('#csrfToken').val()
        };
        $.ajax({
            url: 'getramcapacity',
            type: 'POST',
            data: data,
            success: function(response){
                if (response && response.data) {
                    // Assuming response is an array of models
                    var models = response.data;
                    var $modelSelect = $('#ram_capacity');

                    // Clear existing options
                    $modelSelect.empty();

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
                    $('.section-ram_capacity').show();
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
    function toggleRamcapacity() {
        var ramType = $('input[name="iqc_laptop[ram_type]"]:checked').val();
        if (ramType) {
            fetchRamcapacity(ramType);
        } else {
            $('.section-ram_capacity').hide();
            $('#ram_capacity').val("").trigger('change');
        }
    }
    function toggleHingeDefectedSide() {
        var selectedValue = $('input[name="iqc_laptop[hinge]"]:checked').val();
        if (selectedValue && selectedValue == "3") {
            $('.section-defected_side').hide();
            $('input[name="iqc_laptop[defected_side]"]:checked').prop('checked', false);
        } else {
            $('.section-defected_side').show();
        }
    }
    function toggleBatteryHealth() {
        var selectedValue = $('input[name="iqc_laptop[battery]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-battery_health').show();
        } else {
            $('input[name="iqc_laptop[battery_health]"]:checked').prop('checked', false);
            $('.section-battery_health').hide();
        }
    }

    function toggleInternalBatteryHealth() {
        var selectedValue = $('input[name="iqc_laptop[int_battery]"]:checked').val();
        if (selectedValue && selectedValue == "1") {
            $('.section-int_battery_health').show();
        } else {
            $('input[name="iqc_laptop[int_battery_health]"]:checked').prop('checked', false);
            $('.section-int_battery_health').hide();
        }
    }
    function toggleScreenDescription() {
        var selectedValue = $('input[name="iqc_laptop[screen_size]"]:checked').val();
        if (selectedValue && selectedValue == "7") {
            $('.section-provide_screen_description').show();
        } else {
            $('#provide_screen_description').val('');
            $('.section-provide_screen_description').hide();
        }
    }
    // Initial check on page load
    toggleSectionCategory();
    toggleHddcapacity();
    toggleRamType();
    toggleRamcapacity();
    toggleHingeDefectedSide();
    toggleBatteryHealth();
    toggleInternalBatteryHealth();
    toggleScreenDescription();
    $('input[name="iqc_laptop[hdd]"]').change(function(){
        toggleSectionCategory();
    });
    $('input[name="iqc_laptop[category]"]').change(function(){
        toggleHddcapacity();
    });
    $('input[name="iqc_laptop[ram]"]').change(function(){
        toggleRamType();
    });
    $('input[name="iqc_laptop[ram_type]"]').change(function () {
        toggleRamcapacity();
    });
    $('input[name="iqc_laptop[hinge]"]').change(function () {
        toggleHingeDefectedSide();
    });
    $('input[name="iqc_laptop[battery]"]').change(function () {
        toggleBatteryHealth();
    });
    $('input[name="iqc_laptop[int_battery]"]').change(function () {
        toggleInternalBatteryHealth();
    });
    $('input[name="iqc_laptop[screen_size]"]').change(function () {
        toggleScreenDescription();
    });
    $('input[name="iqc_laptop[make]"]').change(function(){
        var selectedValue = $(this).val();
        var data = {
            make: selectedValue,
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
                        $modelSelect.append('<option value="'+model.value+'">'+model.text+'</option>');
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
    });
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