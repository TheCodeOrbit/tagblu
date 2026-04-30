// <!-- JavaScript to trigger the form submission when the link is clicked -->
document.querySelector('.logout').addEventListener('click', function(event) {
// alert('dfgfd');

    event.preventDefault();  // Prevent default link behavior
    document.getElementById('logout-form').submit();  // Trigger form submission
});

const menuToggle = document.querySelector('.menu-toggle');
const sidebar = document.querySelector('.sidebar');

menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});
document.addEventListener("DOMContentLoaded", function () {
    const topScroll = document.querySelector(".top-scroll");
    if (topScroll) {
        const tableContainer = document.querySelector(".table-container");
        const topScrollDiv = topScroll.querySelector("div");

        // Match width of top scrollbar with table
        topScrollDiv.style.width = tableContainer.scrollWidth + "px";

        // Sync scrolling
        topScroll.addEventListener("scroll", function () {
            tableContainer.scrollLeft = topScroll.scrollLeft;
        });
        tableContainer.addEventListener("scroll", function () {
            topScroll.scrollLeft = tableContainer.scrollLeft;
        });
    }
});
document.addEventListener("DOMContentLoaded", function () {
    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto'; // Reset height to recalculate
        textarea.style.height = textarea.scrollHeight + 'px'; // Set new height
    }

    document.querySelectorAll('.auto-expand-textarea').forEach(textarea => {
        autoResizeTextarea(textarea); // Resize on page load
        textarea.addEventListener('input', function () {
            autoResizeTextarea(this); // Resize on input
        });
    });
});
$(document).ready(function () {
    //$('[data-bs-toggle="tooltip"]').tooltip();
    $('#customerpickuprequest-additional_info,#customerpickuprequest-pickup_document').fSelect();
    $("#loading-overlay").css("display", "none");
    function clearSelectedData() {
        $("#customerpickuprequest-address").val("")
        $("#customerpickuprequest-city").val("")
        $("customerpickuprequest-state").val("")
        $("#customerpickuprequest-pincode").val("")
        $("#customerpickuprequest-country").val("");
        $("#customerpickuprequest-spoc_name").val("");
        $("#customerpickuprequest-spoc_number").val("");
        $("#customerpickuprequest-spoc_email").val("");
        $("#customerpickuprequest-escalation_name").val("");
        $("#customerpickuprequest-escalation_number").val("");
        $("#customerpickuprequest-escalation_email").val("");
    }
    function controlFileUploadField(){
        var valueToCheck = "3";
        if ($("#customerpickuprequest-pickup_document option[value='" + valueToCheck + "']").is(":selected")) {
            $(".field-customerpickuprequest-doc_received").show();
        } else {
            $(".field-customerpickuprequest-doc_received").hide();
        }
    }
    function controlProcedureToUpgradeExtension() {
        var selected = $("#customerpickuprequest-extend_time_provision").val()
        if (selected == 1) {
            //$("#customerpickuprequest-extension_provision").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-extension_provision').parent().show();

        } else {
            //$("#customerpickuprequest-extension_provision").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-extension_provision').parent().hide();

        }
    }
    function controlMaterialFloorLocation() {
        var selected = $("#customerpickuprequest-material_location_floor").val()
        if (selected == 1) {
            //$("#customerpickuprequest-material_floor").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-material_floor').parent().show();

            //$("#customerpickuprequest-floor_num_material_count").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-floor_num_material_count').parent().hide();

        } else if (selected == 2) {
            //$("#customerpickuprequest-floor_num_material_count").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-floor_num_material_count').parent().show();

            //$("#customerpickuprequest-material_floor").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-material_floor').parent().hide();
        } else {
            //$("#customerpickuprequest-material_floor").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-material_floor').parent().hide();
            //$("#customerpickuprequest-floor_num_material_count").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-floor_num_material_count').parent().hide();
        }
    }
    function howToMoveMaterialControl() {
        var selected = $("#customerpickuprequest-stairs_space").val()
        if (selected == 2) {
            //$("#customerpickuprequest-material_move").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-material_move').parent().show();
        } else {
            //$("#customerpickuprequest-material_move").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-material_move').parent().hide();
        }
    }
    function segregationControl() {
        var selected = $("#customerpickuprequest-segregation").val()
        if (selected == 1) {
            //$("#customerpickuprequest-space_for_segregation").removeClass("readonly");
             //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-space_for_segregation').parent().show();
        } else {
            //$("#customerpickuprequest-space_for_segregation").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-space_for_segregation').parent().hide();
        }
    }
    function materialMovementFromPremisesConrol() {
        var selected = $("#customerpickuprequest-movement_from_premises").val()
        if (selected == 1) {
           // $("#customerpickuprequest-distance").removeClass("readonly");
             //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-distance').parent().show();
            //$("#customerpickuprequest-floor_num_for_take_out").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-floor_num_for_take_out').parent().hide();
        } else if (selected == 2) {
           // $("#customerpickuprequest-floor_num_for_take_out").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-floor_num_for_take_out').parent().show();
            //$("#customerpickuprequest-distance").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-distance').parent().hide();
        } else {
            //$("#customerpickuprequest-distance").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-distance').parent().hide();
            //$("#customerpickuprequest-floor_num_for_take_out").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-floor_num_for_take_out').parent().hide();
        }
    }
    function sufficientSpaceForVehicleGoinsideConrol() {
        var selected = $("#customerpickuprequest-space_for_vehicle").val()
        if (selected == 2) {
            //$("#customerpickuprequest-small_vehicle").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-small_vehicle').parent().show();
        } else {
            //$("#customerpickuprequest-small_vehicle").val("").addClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-small_vehicle').parent().hide();
            $("#customerpickuprequest-small_vehicle").trigger("change");
        }
    }
    function smallVehicleMovementConrol() {
        var selected = $("#customerpickuprequest-small_vehicle").val()
        if (selected == 1) {
            //$("#customerpickuprequest-vehicle_as_per_height").removeClass("readonly");
            //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-vehicle_as_per_height').parent().show();
            //end deepika
            //$("#customerpickuprequest-material_from_basement_to_grnd").val("").addClass("readonly");
        } else if (selected == 2) {
            //$("#customerpickuprequest-material_from_basement_to_grnd").removeClass("readonly");
            //commented below by deepika
            // $("#customerpickuprequest-vehicle_as_per_height").val("").addClass("readonly");
             //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-vehicle_as_per_height').parent().hide();
        } else {
            //commented by deepika
            //$("#customerpickuprequest-vehicle_as_per_height").val("").addClass("readonly");
              //added on 25 june 2025 by deepika
            $('.field-customerpickuprequest-vehicle_as_per_height').parent().hide();
            //end by deepika
            //$("#customerpickuprequest-material_from_basement_to_grnd").val("").addClass("readonly");
        }
    }
    //added on 25 june 2025 by deepika
    function serviceliftcontrol()
    {
          var selected = $("#customerpickuprequest-service_lift").val()
        if (selected == 1) {
           
            $('.field-customerpickuprequest-stairs_space').parent().show();
           
        } else if (selected == 2) {
           
            $('.field-customerpickuprequest-stairs_space').parent().hide();
        } else {
           
            $('.field-customerpickuprequest-stairs_space').parent().hide();
           
        }

    }
    controlFileUploadField()
    controlProcedureToUpgradeExtension()
    controlMaterialFloorLocation()
    howToMoveMaterialControl()
    segregationControl()
    materialMovementFromPremisesConrol()
    sufficientSpaceForVehicleGoinsideConrol()
    smallVehicleMovementConrol()
    //added by deepika on 25 june 2025
    serviceliftcontrol();

    var $container = $('.container-items'); // Container where rows are added
    
    function addItem1(product_name = '', make = '', model = '', total_quantity = '', serial_no = '', processor = '', ram = '', hdd_sdd = '', remarks = '') {
        var $template = $('.item').first();
        var $clone = $template.clone();
        $clone.removeClass('hidden');

        // **Clear existing values**
        $clone.find('input').val('');

        // **Get new index**
        var index = $container.find('.item').length;

        // **Update input names dynamically**
        $clone.find('input').each(function () {
            var inputName = $(this).attr('name');
            $(this).attr('name', inputName.replace(/\[\d+\]/, '[' + index + ']'));
        });

        // **Assign values from CSV**
        $clone.find('input[name^="CustomerPickupAssets"][name$="[product_name]"]').val(product_name);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[make]"]').val(make);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[model]"]').val(model);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[total_quantity]"]').val(total_quantity);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[serial_no]"]').val(serial_no);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[processor]"]').val(processor);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[ram]"]').val(ram);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[hdd_sdd]"]').val(hdd_sdd);
        $clone.find('input[name^="CustomerPickupAssets"][name$="[remarks]"]').val(remarks);

        $container.append($clone);
    }
    function addItem2(product_name = '', make = '', model = '', total_quantity = '', serial_no = '', processor = '', ram = '', hdd_sdd = '', remarks = '') {
        var $template = $('.item').first();
        var $clone = $template.clone();
        $clone.removeClass('hidden');

        // **Clear existing values**
        $clone.find('input').val('');
        $clone.find('textarea').val('');

        // **Get new index**
        var index = $container.find('.item').length;

        // **Update input names dynamically**
        $clone.find('input').each(function () {
            var inputName = $(this).attr('name');
            $(this).attr('name', inputName.replace(/\[\d+\]/, '[' + index + ']'));
        });
        $clone.find('textarea').each(function () {
            var inputName = $(this).attr('name');
            $(this).attr('name', inputName.replace(/\[\d+\]/, '[' + index + ']'));
        });

        // **Assign values from CSV**
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[product_name]"]').val(product_name);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[make]"]').val(make);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[model]"]').val(model);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[total_quantity]"]').val(total_quantity);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[serial_no]"]').val(serial_no);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[processor]"]').val(processor);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[ram]"]').val(ram);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[hdd_sdd]"]').val(hdd_sdd);
        $clone.find('textarea[name^="CustomerPickupAssets"][name$="[remarks]"]').val(remarks);

        $container.append($clone);
    }
    
    function addItem(product_name = '', make = '', model = '', total_quantity = '', serial_no = '', processor = '', ram = '', hdd_sdd = '', remarks = '',isFromCSV = false) {
        var $rows = $('.item'); // Get all existing rows
        var $firstRow = $rows.first();

        // Check if at least one field in the first row is filled
        var isFirstRowFilled = $firstRow.find('input, textarea,select').toArray().some(input => $(input).val().trim() !== '');

        if ($rows.length === 1 && !isFirstRowFilled) {
            // If only one row exists and it's empty, fill it instead of cloning
            populateRow($firstRow, product_name, make, model, total_quantity, serial_no, processor, ram, hdd_sdd, remarks,isFromCSV);
        } else {
            // Otherwise, clone the first row and append it after the last row
            var $clone = $firstRow.clone().removeClass('hidden');
            $clone.find('input, textarea,select').val(''); // Clear inputs
            // Remove Yii validation attributes
            removeValidation($clone);
            updateInputNames($clone, $rows.length);
            populateRow($clone, product_name, make, model, total_quantity, serial_no, processor, ram, hdd_sdd, remarks,isFromCSV);
            $('.item').last().after($clone); // Append after the last row
            triggerInput($firstRow)
        }
    }

    // Function to populate values into a row
    function populateRow2($row, product_name, make, model, total_quantity, serial_no, processor, ram, hdd_sdd, remarks) {
        $row.find('select[name^="CustomerPickupAssets"][name$="[product_name]"]').val(product_name);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[make]"]').val(make);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[model]"]').val(model);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[total_quantity]"]').val(total_quantity);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[serial_no]"]').val(serial_no);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[processor]"]').val(processor);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[ram]"]').val(ram);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[hdd_sdd]"]').val(hdd_sdd);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[remarks]"]').val(remarks);
    }
    function populateRow($row, product_name, make, model, total_quantity, serial_no, processor, ram, hdd_sdd, remarks,isFromCSV) {
        var $productSelect = $row.find('select[name^="CustomerPickupAssets"][name$="[product_name]"]');
        var $otherProductInput = $row.find('textarea[name^="CustomerPickupAssets"][name$="[other_product_name]"]');

        if (isFromCSV) {
            // Check if the product name exists in the dropdown options (based on displayed text)
            var productExists = false;
            $productSelect.find('option').each(function () {
                if ($(this).text().trim().toLowerCase() === product_name.trim().toLowerCase()) {
                    productExists = true;
                    $productSelect.val($(this).val()); // Set the matching value
                    return false; // Break loop
                }
            });

            if (!productExists) {
                // Select "Others" (assuming value="89" for Others)
                $productSelect.val('97');
                $otherProductInput.val(product_name); // Store the actual product name in other_product_name
            } else {
                // Clear other_product_name if the product exists
                $otherProductInput.val('');
            }
        } else {
            $productSelect.val(''); 
            $otherProductInput.val('');
        }
        // Populate other fields
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[make]"]').val(make);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[model]"]').val(model);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[total_quantity]"]').val(total_quantity);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[serial_no]"]').val(serial_no);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[processor]"]').val(processor);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[ram]"]').val(ram);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[hdd_sdd]"]').val(hdd_sdd);
        $row.find('textarea[name^="CustomerPickupAssets"][name$="[remarks]"]').val(remarks);
    }

    // Function to update input names dynamically
    function updateInputNames($row, index) {
        $row.find('input, textarea,select').each(function () {
            var inputName = $(this).attr('name');
            var inputId = $(this).attr('id');
            if (inputName) {
                $(this).attr('name', inputName.replace(/\[\d+\]/, '[' + index + ']'));
            }
            if (inputId) {
                $(this).attr('id', inputId.replace(/customerpickupassets-\d+-/, 'customerpickupassets-' + index + '-'));
            }
        });
    }
    function triggerInput($row) {
        $row.find('input, textarea,select').each(function () {
            $(this).trigger('change');
        });
    }

    function removeValidation($row) {
        $row.find('input, textarea, select').each(function () {
            $(this).removeAttr('data-validation').removeClass('is-invalid'); // Remove validation classes
        });

        // Remove validation error messages
        $row.find('.help-block').remove();
    }
    // Add new row
    $('#addFromCSV').on('click', function (e) {
        var fileInput = $('#csvFileInput')[0];
        if (!fileInput.files.length) {
            alert('Please select a CSV file first.');
            return;
        }
        var file = fileInput.files[0];
        var fileType = file.name.split('.').pop().toLowerCase();
        var maxSize = 10 * 1024 * 1024; // 10MB in bytes

        if (!['csv','CSV', 'xlsx','XLSX', 'xls','XLS'].includes(fileType)) {
            alert('Invalid file type. Please select a CSV or Excel file.');
            return;
        }
        if (file.size > maxSize) {
            alert('File size exceeds 10MB. Please upload a smaller file.');
            return;
        }
        $("#loading-overlay").css("display", "grid");
        if (fileType === 'csv') {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result.split("\n");
                rows.forEach(function (row, i) {
                    if (i === 0) return; // Skip header row if needed
                    var columns = row.split(",");

                    if (columns.length >= 9) { // Ensure all fields exist
                        addItem(
                            columns[0].trim(), // product_name
                            columns[1].trim(), // make
                            columns[2].trim(), // model
                            columns[3].trim(), // total_quantity
                            columns[4].trim(), // serial_no
                            columns[5].trim(), // processor
                            columns[6].trim(), // ram
                            columns[7].trim(), // hdd_sdd
                            columns[8].trim(),  // remarks
                            true
                        );
                    }
                });
                $("#loading-overlay").css("display", "none");
            };
            reader.readAsText(file);
        } else {
            var reader = new FileReader();
            reader.onload = function (e) {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, { type: 'array' });
                var sheetName = workbook.SheetNames[0]; // Read first sheet
                var sheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { header: 1 });

                sheet.forEach(function (row, i) {
                    if (i === 0) return; // Skip header row
                    if (row.length >= 9) {
                        addItem(
                            row[0] ? String(row[0]).trim() : '', // product_name
                            row[1] ? String(row[1]).trim() : '', // make
                            row[2] ? String(row[2]).trim() : '', // model
                            row[3] ? String(row[3]).trim() : '', // total_quantity
                            row[4] ? String(row[4]).trim() : '', // serial_no
                            row[5] ? String(row[5]).trim() : '', // processor
                            row[6] ? String(row[6]).trim() : '', // ram
                            row[7] ? String(row[7]).trim() : '', // hdd_sdd
                            row[8] ? String(row[8]).trim() : '', // remarks
                            true
                        );
                    }
                });
                $("#loading-overlay").css("display", "none");
            };
            reader.readAsArrayBuffer(file);
            //$("#loading-overlay").css("display", "none");
        }
        
    });
    $(document).on("click", '.add-item', function (e) {
        e.preventDefault(); 
        addItem('', '', '', '', '', '', '', '', '', false);
    });

    // Remove a row
    $(document).on("click", '.remove-item', function (e) {
        e.preventDefault(); 
        var row = $(this).closest('.item'); // Get the closest row
        var rowCount = $container.find('.item').length; // Get the total number of rows

        if (rowCount > 1) {
            // If more than one row, remove the row
            row.remove();
        } else {
            // If only one row left, clear the input fields
            row.find('input, textarea,select').val('');
        }
    });

    $(document).on("change", '#customerpickuprequest-location', function (e) {
        e.preventDefault();
        var location = $(this).val();
        if (!location) {
            clearSelectedData()
        } else {
            clearSelectedData()
            let data = { location: location, _csrf: $('#csrfToken').val() };
            $.ajax({
            type: 'POST',
            url: "getlocation",
            data: data,
            success: function (response) {
                if (response && response.data) {
                    $("#customerpickuprequest-address").val(response.data.address);
                    $("#customerpickuprequest-city").val(response.data.city);
                    $("#customerpickuprequest-state").val(response.data.state);
                    $("#customerpickuprequest-pincode").val(response.data.pincode);
                    $("#customerpickuprequest-country").val(response.data.country);
                    $("#customerpickuprequest-spoc_name").val(response.data.spoc_name);
                    $("#customerpickuprequest-spoc_number").val(response.data.spoc_mobile);
                    $("#customerpickuprequest-spoc_email").val(response.data.spoc_email);
                    $("#customerpickuprequest-escalation_name").val(response.data.escalation_spoc_name);
                    $("#customerpickuprequest-escalation_number").val(response.data.escalation_spoc_mobile);
                    $("#customerpickuprequest-escalation_email").val(response.data.escalation_spoc_email);
                } else {
                    console.log("Invalid response format or missing data");
                }

            },
            error: function (data) { // if error occured
                alert('Error occured.please try again');
            },
            dataType: 'json'
            });
        }
    });
    $("#customerpickuprequest-pickup_document").change(function () {
        controlFileUploadField()
    });
    $("#customerpickuprequest-extend_time_provision").change(function () {
        controlProcedureToUpgradeExtension()
    });
    $("#customerpickuprequest-material_location_floor").change(function () {
        controlMaterialFloorLocation()
    });
    $("#customerpickuprequest-stairs_space").change(function () {
        howToMoveMaterialControl()
    });
    $("#customerpickuprequest-segregation").change(function () {
        segregationControl()
    });
    $("#customerpickuprequest-movement_from_premises").change(function () {
        materialMovementFromPremisesConrol()
    });
    $("#customerpickuprequest-space_for_vehicle").change(function () {
        sufficientSpaceForVehicleGoinsideConrol()
    });
    $("#customerpickuprequest-small_vehicle").change(function () {
        smallVehicleMovementConrol()
    });
    //added on 25 june 2025 by deepika
    $("#customerpickuprequest-service_lift").change(function () {
        serviceliftcontrol()
    });

    $(document).on("click", '.web-input-lb', function (e) {
        $(".web-input-option").show();
        $(".file-input-option").hide();
    });
    $(document).on("click", '.file-upload-input-lb', function (e) {
        $(".web-input-option").hide();
        $(".file-input-option").show();
    });
    $(document).on('click', '.submit-buttonxxxx', function(e) {
        e.preventDefault();  // Prevent the default form submission
        var form = $('#w0');  // Replace with your actual form ID
        
        // Custom validation for preferred_pickup_date
        if (!$("#customerpickuprequest-preferred_pickup_date").val()) {
            var errorElement = $("#customerpickuprequest-preferred_pickup_date").closest(".form-group").find(".help-block");
            errorElement.html("Agreed / Requested Collection Date cannot be blank.");
        } else {
            $("#customerpickuprequest-preferred_pickup_date")
                $("#customerpickuprequest-preferred_pickup_date").closest(".form-group").find(".help-block").html("")
        }
        
        if ($("#customerpickuprequest-pickup_document").val().length === 0) {
            console.log($("#customerpickuprequest-pickup_document").parents(".form-group"))
            console.log($("#customerpickuprequest-pickup_document").parents(".form-group").find(".help-block"))
            $("#customerpickuprequest-pickup_document").parents(".form-group").find(".help-block").html('Pickup Document Required cannot be blank.');
        } else {
            $("#customerpickuprequest-pickup_document").parents(".form-group").find(".help-block").html("");
        }

        if (!$("#customerpickuprequest-working_timings").val()) {
            $("#customerpickuprequest-working_timings").parents(".form-group").find(".help-block").html('What are the working timings? cannot be blank.');
        } else {
            $("#customerpickuprequest-working_timings").parents(".form-group").find(".help-block").html("");
        }

        if (!$("#customerpickuprequest-entry_formalities_person").val()) {
            $("#customerpickuprequest-entry_formalities_person").parents(".form-group").find(".help-block").html('What are the formalities for entry personnel cannot be blank.');
        } else {
            $("#customerpickuprequest-entry_formalities_person").parents(".form-group").find(".help-block").html("");
        }

        if (!$("#customerpickuprequest-vehicle_entry_formalities").val()) {
            $("#customerpickuprequest-vehicle_entry_formalities").parents(".form-group").find(".help-block").html('What are the formalities for vehicle entry cannot be blank.');
        } else {
            $("#customerpickuprequest-vehicle_entry_formalities").parents(".form-group").find(".help-block").html("");
        }
        if (!$("#customerpickuprequest-terms_and_condition").prop('checked')) {
            $("#customerpickuprequest-terms_and_condition").parents(".form-group").find(".help-block").html('You must agree to the terms before submitting');
        } else {
            $("#customerpickuprequest-terms_and_condition").parents(".form-group").find(".help-block").html("");
        }
        // Trigger Yii2's client-side validation
        form.yiiActiveForm('validate');
        // Check if the form is valid, then submit
        if (form.find('.has-error').length === 0) {
            form.submit(); // If valid, submit the form
        }
    });
    //for data sanitization
    $(document).on("click", 'td[data-request-id]', function (e) {
        const requestId = $(this).data('request-id');
        const module = $(this).data('module');
        $('#attachment-list').empty();
        $('#attachment-list').append('<tr><td colspan="3">Loading...</td></tr>');
        $.ajax({
            url: 'get-module-assets',
            method: 'POST',
            data: { record: requestId,module: module },
            success: function (response) {
                $('#attachment-list').empty();
                const attachments = response.attachments || [];
                if (attachments.length > 0) {
                    attachments.forEach((file, index) => {
                        const rowHtml = `<tr>
                            <td>${index + 1}</td>
                            <td>${file.serialNumber || '—'}</td>
                            <td><a href="${file.fileUrl}" target="_blank">Download</a></td>
                        </tr>`;
                        $('#attachment-list').append(rowHtml);
                    });
                } else {
                    $('#attachment-list').append('<tr><td colspan="3">No attachments found.</td></tr>');
                }
            },
            error: function () {
                $('#attachment-list').html('<tr><td colspan="3" class="text-danger">Error loading attachments.</td></tr>');
            }
        });
    });
    //validation code of change password start from here added by ptpatel on date 06-09-2025
     $(document).on("submit", '#resetpassword-form', function (e) {
        if (!validatePasswords()) {   //  call your existing validation function
            e.preventDefault();  //  stop submit if it fails
        }
    });
    $(document).on("blur", '#password,#confirm_password', function (e) {
       validatePasswords();
    });
     
    function validatePasswords() {
        var $password = $("#password");
        var $confirm = $("#confirm_password");
        var $saveBtn = $("#resetpassword-btn");

        var passVal = $password.val().trim();
        var confirmVal = $confirm.val().trim();
        var valid = true;

        // Reset previous errors
        $password.closest(".form-group").removeClass("error").find(".help-block").text("");
        $confirm.closest(".form-group").removeClass("error").find(".help-block").text("");

        // Regex for strong password
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&#])[A-Za-z\d@$!%?&#]{8,}$/;

        // 1. Check password blank
        if (passVal === "") {
            $password.closest(".form-group").addClass("error")
                .find(".help-block").text("Password cannot be blank.");
            valid = false;
        }
        // 2. Check password strength
        else if (!passwordRegex.test(passVal)) {
            $password.closest(".form-group").addClass("error")
                .find(".help-block").text("Password must be at least 8 characters long, include uppercase, lowercase, number, and special character.");
            valid = false;
        }

        // 3. Check confirm password blank
        if (confirmVal === "") {
            $confirm.closest(".form-group").addClass("error")
                .find(".help-block").text("Confirm Password cannot be blank.");
            valid = false;
        }

        // 4. Check both match
        if (passVal !== "" && confirmVal !== "" && passVal !== confirmVal) {
            $confirm.closest(".form-group").addClass("error")
                .find(".help-block").text("Password and Confirm Password do not match.");
            valid = false;
        }

        // 🔹 Enable/Disable Save button
        if (valid) {
            $saveBtn.prop("disabled", false);
        } else {
            $saveBtn.prop("disabled", true);
        }

        return valid;
    }
     //validation code of change password end  here added by ptpatel on date 06-09-2025

});
