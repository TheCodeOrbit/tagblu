document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.getElementById("csrfToken")?.value || '';
    var moduleName = jQuery("#module").val();
    const pathParts = window.location.pathname.split('/');
    const moduleIndex = pathParts.indexOf(moduleName);
    const baseUrl = window.location.origin + pathParts.slice(0, moduleIndex + 1).join('/');

    $(document).on("click", "#improveimportButton", function () {
        // document.getElementById("improveimportButton").addEventListener("click", function () {
        startLoading();
        const url = `improvedimport`;
        // document.getElementById('improvedimportdiv').innerHTML = '<p>Loading…</p>';

        fetch(url)
            .then(res => res.json())
            .then(response => {
                document.getElementById('improvedimportdiv').innerHTML = response.html;
                console.log(getAbsoluteUrl() + '/thememain/js/importdata.js');
                //this needed to bind events because content is loaded dynamically
                bindStepNavigationEvents();
                $.getScript(getAbsoluteUrl() + '/thememain/js/importdata.js')
                    .done(() => {
                        console.log("importdata js is loaded");
                    })
                    .fail(() => {
                        console.error('Failed to load importdata.js');
                    });

                document.getElementById('tablelist').style.display = 'none';
                document.getElementById('improvedimportdiv').style.display = 'block';
                stopLoading();
            })
            .catch(err => {
                console.error(err);
                // document.getElementById('improvedimportdiv').innerHTML = '<p class="text-danger">Failed to load details.</p>';
                stopLoading();
            });
    });

    function goToStep(step) {
        $('.step-content').addClass('d-none').removeClass('active');  // hide all steps
        $('#step-' + step).removeClass('d-none').addClass('active'); // show current step

        $('.stepwizard-step .step-label').removeClass('active fw-bold text-primary').addClass('text-muted');
        $('.stepwizard-step .step-label').each(function () {
            var index = $(this).parent().index() + 1;
            if (index === step) {
                $(this).addClass('active fw-bold text-primary').removeClass('text-muted');
            }
        });

    }

    function bindStepNavigationEvents() {
        // $('#next-1').off('click').on('click', function () { goToStep(2); uploadcsv(); });
        $('#next-1').off('click').on('click', function () {
            uploadcsv()
                .then(() => {
                        goToStep(2);
                        updateStepIndicator(2);
                    }) // Only on success
                .catch(err => {
                    console.warn("Upload failed:", err);
                    // Do not go to step 2
                });
        });
        $('#next-2').off('click').on('click', function (e) { 
            if (validateforovewrite(e) === false) {
                console.log("Validation failed — staying on current step");
                return false; // stop execution here
            }
            goToStep(3);updateStepIndicator(3); });
        $('#prev-2').off('click').on('click', function () { goToStep(1); updateStepIndicator(1);});
        // $('#prev-3').off('click').on('click', function () { goToStep(2); updateStepIndicator(2);});
        $(document).on('click', '#prev-3', function () {
                goToStep(2);
                updateStepIndicator(2);
            });

        document.getElementById("closeWizard").addEventListener("click", function () {
            if (confirm("Are you sure you want to close the wizard?")) {
                document.getElementById('tablelist').style.display = 'block';
                document.getElementById('improvedimportdiv').style.display = 'none';
            }
        });

        // document.getElementById("addBtn").onclick = function () {
        //     copyOptions("availableFields", "selectedFields");
        //     updateHiddenInput();
        // };
        // document.getElementById("removeBtn").onclick = function () {
        //     removeOptions("selectedFields");
        //     updateHiddenInput();
        // };
       $(document).on('change', '#availableFields', function () {
            copyOptions("availableFields", "selectedFields");
        });

        function copyOptions(from, to) {
            let fromSelect = document.getElementById(from);
            let toSelect = document.getElementById(to);
            //to select single option only
            toSelect.innerHTML = '';
            Array.from(fromSelect.selectedOptions).forEach(option => {
                // Avoid duplicates for multiple option
                // if (!Array.from(toSelect.options).some(o => o.value === option.value)) {
                //     let newOption = option.cloneNode(true);
                //     toSelect.appendChild(newOption);
                // }
                // Get the selected option from source
                let selectedOption = fromSelect.options[fromSelect.selectedIndex];
                if (selectedOption) {
                    // Clone and append to target
                    let newOption = selectedOption.cloneNode(true);
                    toSelect.appendChild(newOption);
                    newOption.selected = true;
                    updateHiddenInput();
                }
                // toSelect.dispatchEvent(new Event('change'));
            });
             // ✅ Trigger validation after copying
            setTimeout(() => {
                console.log("Calling validation after DOM update");
                validateforovewrite({ preventDefault: function(){} });
            }, 0);
            console.log("validateforovewrite");
        }

        function removeOptions(selectId) {
            let select = document.getElementById(selectId);
            Array.from(select.selectedOptions).forEach(option => {
                option.remove();
            });
        }

        function updateHiddenInput() {
            let selected = document.getElementById("selectedFields");
            let values = Array.from(selected.options).map(opt => opt.value);
            document.getElementById("selectedfieldsid").value = values.join(",");
        }

    }

    function getAbsoluteUrl() {
        var newURL = window.location.href;
        var module = jQuery("#module").val();
        var str = newURL.indexOf(module);

        var slicestr = newURL.substring(0, str);
        return slicestr;
    }

    // === New CSV Upload & Mapping logic ===

    // Assuming your page has:
    // <input type="file" id="csvFileInput" accept=".csv" />
    // <button id="uploadCsvBtn">Upload CSV</button>
    // <div id="mappingArea"></div>

    // $(document).on("click", "#next-1", function () {
    // document.getElementById('#next-1')?.addEventListener('click', function () {
    /*function uploadcsv() {
        const fileInput = document.getElementById('csvfile');

        console.log(fileInput.files);
        if (!fileInput || fileInput.files.length === 0) {
            alert('Please select a CSV file.');
            return;
        }

        startLoading();

        const formData = new FormData();
        formData.append('csv_file', fileInput.files[0]);
        fetch('uploadcsv', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-Token': csrfToken
            }
        })
            .then(async (res) => {
                stopLoading();

                let data;
                try {
                    data = await res.json(); // This can throw
                } catch (jsonErr) {
                    console.error('Failed to parse JSON:', jsonErr);
                    alert('Upload failed: Invalid server response.');
                    return;
                }

                console.log('Server response:', res);
                console.log('Parsed JSON:', data);

                if (!res.ok) {
                    alert(data.error || `Upload failed with status ${res.status}`);
                    return;
                }

                if (data.error) {
                    alert(data.error);
                    return;
                }

                renderMappingTable(data.headers, data.firstRow);
            })
            .catch((err) => {
                stopLoading();
                console.error('Fetch failed:', err);
                alert('Upload failed due to network or JavaScript error.');
            });


    }*/

    function uploadcsv() {
        return new Promise((resolve, reject) => {
            const fileInput = document.getElementById('csvfile');

            if (!fileInput || fileInput.files.length === 0) {
                alert('Please select a CSV file.');
                reject('No file selected');
                return;
            }

            startLoading();

            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);

            fetch('uploadcsv', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            })
                .then(async (res) => {
                    stopLoading();

                    let data;
                    try {
                        data = await res.json();
                    } catch (jsonErr) {
                        console.error('Failed to parse JSON:', jsonErr);
                        alert('Upload failed: Invalid server response.');
                        reject('Invalid JSON');
                        return;
                    }

                    if (!res.ok || data.error) {
                        alert(data.error || `Upload failed with status ${res.status}`);
                        reject('Server error');
                        return;
                    }

                    // Success
                    renderMappingTable(data.headers, data.firstRow);
                    resolve(); //  it will continue to next step
                })
                .catch((err) => {
                    stopLoading();
                    console.error('Fetch failed:', err);
                    alert('Upload failed due to network or JavaScript error.');
                    reject(err);
                });
        });
    }

    let availableFields = [];
    function renderMappingTable(headers, firstRow) {
        // alert(headers);
         availableFields = Array.from(document.querySelectorAll('#availableFields option')).map(opt => ({
            id: opt.getAttribute('data-id'),
            value: opt.value,
            label: opt.textContent.trim(),
            mandatory : opt.getAttribute('data-mandatory'),
            }));
        if (availableFields.length === 0) {
            alert('No available fields to map. Please select fields in Step 2.');
            return;
        }

            let html = `<style>.select2-container .select2-selection--single .select2-selection__rendered
            {   padding :3px 0px 0px 8px !important;    }
            .select2-container .select2-selection--single { height: 37px !important; }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                top: 5px !important;}</style>
            <form id="mappingForm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>CSV Header</th>
                            <th>CSV First Row Value</th>
                            <th>Map To Field With ERP</th>
                            <th>Default Value</th>
                        </tr>
                    </thead>
                    <tbody>`;

            headers.forEach((header, i) => {
                html += `<tr>
                    <td>
                        ${header}
                        <input type="hidden" name="headerNames[]" value="${header}">
                    </td>
                    <td>${firstRow[i] || ''}</td>
                    <td>
                        <div class="form-group">
                            <select name="mappedFields[]" class="form-control singleselect">
                            <option value=""> Select Field </option>`;
                availableFields.forEach(field => {
                    html += `<option data-id="${field.id}" value="${field.value}">${field.label}${field.mandatory == 1 ? ' <span class="text-danger">*</span>' : ''}</option>`;
                });
                html += `</select>
                        </div>
                    </td>
                    <td><input type="text" name="defaultValues[]" class="form-control" /></td>
                </tr>`;
            });

            html += `</tbody></table>
                
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-success me-2">Save</button>
                    <button type="button" class="btn btn-secondary" id="prev-3">Back</button>
                </div>
            </form>`;

        $(document).ready(function() {
            $('.singleselect').select2({
                placeholder: '-- Select Field --',
                width: '100%'
            });
        });

        document.getElementById('mappingArea').innerHTML = html;
        // document.querySelectorAll('select[name="mappedFields[]"]').forEach((select) => {
            // select.addEventListener('change', function () {
        $(document).on('change', 'select[name="mappedFields[]"]', function () {
                const fieldname = this.value;
                const fieldId = this.options[this.selectedIndex].getAttribute('data-id');
                const row = this.closest('tr');
                const targetCell = row.querySelector('td:last-child');
                    // singleEdit(uitype, tabid, headername, fieldid, recordId, field, "list");
                // onclick="singleEdit('4','','','57','','','list')"
                loadDefaultValueInput(fieldId, targetCell);

                waitForElement(".singleselect", function ($el) {
                    const $select = $($el);

                    // Make sure Select2 is available
                    if (typeof $.fn.select2 !== 'function') {
                        console.warn("Select2 plugin not loaded!");
                        return;
                    }

                    // Destroy and reinitialize Select2 to avoid duplicate initialization
                    $select.each(function () {
                        const $this = $(this);
                        if ($this.hasClass('select2-hidden-accessible')) {
                            $this.select2('destroy');
                        }
                        $this.select2({
                            placeholder: "Select Field",
                            allowClear: true,
                            width: '100%'
                        });
                    });
            });
        });   

        document.getElementById('mappingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            startLoading();

            const formData = new FormData(this);
            // Step 1
            formData.append('csvfile', $('#csvfile')[0].files[0]);
            // formData.append('hasheader', $('#hasheader').is(':checked') ? 1 : 0);
            // formData.append('encoding', $('#encoding').val());
            formData.append('delimiter', $('input[name="delimiter"]:checked').val());

            // Step 2
            let duplicateAction = $('#duplicateAction').val();
            formData.append('duplicateAction', $('#duplicateAction').val());
            formData.append('selectedfieldsid', $('#selectedfieldsid').val());

            // Step 3 – assume you have hidden inputs or generate them like:
            $('select[name="mappedFields[]"]').each(function (i) {
                formData.append('mappedFields[]', $(this).val());
            });
            $('input[name="headerNames[]"]').each(function (i) {
                formData.append('headerNames[]', $(this).val());
            });

            // Default values (if any)
            $('input[name^="<?= $TableName ?>"]').each(function () {
                formData.append($(this).attr('name'), $(this).val());
            });

            if (!checkallmandatoryfieldsselected(e)) {
                stopLoading();
                return; 
            }
            fetch('savemapping', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            })
                .then(async (res) => {
                    stopLoading();

                    let result;
                    try {
                        result = await res.json();
                    } catch (err) {
                        console.error("Invalid JSON response", err);
                        alert("Invalid server response");
                        return;
                    }

                    if (!result.success) {
                        alert(result.error || "Save mapping failed.");
                        return;
                    }

                    // === SUCCESS ===
                    const inserted = result.inserted || 0;
                    const failed = result.failed || 0;
                    const fileduplicate = result.fileDuplicateCount || 0;
                    const dbduplicate = result.dbDuplicateCount

                    let message = '';
                    if(inserted > 0 ){
                        message +=`${inserted} records inserted successfully.`; // Always show
                    }
                    if (failed > 0) {
                        message += `\n${failed} records failed. Hence these records cannot be imported`;
                    }
                    if (fileduplicate > 0) {
                        message += `\n${fileduplicate} records are duplicate in CSV file. Hence these records cannot be imported.`;
                    }
                    if (dbduplicate > 0) {
                        //message += `\n${dbduplicate} records already exist in database. Hence these records cannot be imported.`;
                        if(duplicateAction === "Overwrite")
                            message += `\n${dbduplicate} records already exist in database. Hence these records has been overwrite.`;
                        else
                            message += `\n${dbduplicate} records already exist in database. Hence these records cannot be imported.`;
                    }
                    // Optional: show error rows (for debug)
                    if (failed > 0 && result.errors?.length) {
                        console.warn("Failed rows:", result.errors);
                        console.table(result.errors);  // browser console table
                    }

                    alert(message);
                    window.location.reload();
                })
                .catch(err => {
                    stopLoading();
                    alert("Error submitting mapping.");
                    console.error("Error:", err);
                });
        });
    }

    // === End of CSV upload & mapping ===

    // Close function

    function loadDefaultValueInput(fieldId, targetCell) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (!fieldId || !targetCell) return;

        targetCell.innerHTML = '<span class="text-muted">Loading...</span>';

        if (fieldId.indexOf('H') === -1) {           
                fetch('getfilterinputs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': csrfToken
                },
                body: `field_id=${encodeURIComponent(fieldId)}`
            })
            .then(res => res.json())
            .then(data => {
                targetCell.innerHTML = data.html || '<input type="text" name="defaultValues[]" class="form-control" />';
            })
            .catch(err => {
                console.error('Failed to load input for field:', fieldId, err);
                targetCell.innerHTML = '<input type="text" name="defaultValues[]" class="form-control" />';
            });
        }
        else
        {
            targetCell.innerHTML ='';
        }
        
    }

    $(document).on('change', 'select[name="mappedFields[]"]', function () {
    let selectedValues = [];
    let hasDuplicate = false;

    $('select[name="mappedFields[]"]').each(function () {
        let val = $(this).val();
        if (val !== '') {
            if (selectedValues.includes(val)) {
                hasDuplicate = true;
                return false; // exit loop
            }
            selectedValues.push(val);
        }
    });

    if (hasDuplicate) {
        alert('You have selected the same field more than once. Please choose unique fields.');
        // $(this).val(''); // clear current select
        $(this).val(null).trigger('change');
    }
    });

    function checkallmandatoryfieldsselected(e) {
        const mandatoryFields = availableFields.filter(field => field.mandatory === '1');
        const selected = [];

        $('select[name="mappedFields[]"]').each(function () {
            const val = $(this).val();
            if (val) {
                selected.push(val);
            }
        });

        const missingFields = mandatoryFields.filter(field => !selected.includes(field.value));

        if (missingFields.length > 0) {
            e.preventDefault(); // stop form
            stopLoading();
            alert("Please map all mandatory fields:\n" + missingFields.map(f => f.label).join(", "));
            return false;
        }

        return true; // all good
    }

function waitForElement(selector, callback, maxAttempts = 50, interval = 100) {
  let attempts = 0;
  const checkExist = setInterval(function () {
    const el = document.querySelector(selector);
    if (el) {
      clearInterval(checkExist);
      callback(el);
    } else if (++attempts >= maxAttempts) {
      clearInterval(checkExist);
      console.warn(selector + " still not found after waiting.");
    }
  }, interval);
}

function updateStepIndicator(step) {
    document.querySelectorAll('.stepwizard-step .step-label').forEach((label, index) => {
        const numberSpan = label.querySelector('.step-number');

        if (index + 1 === step) {
            label.classList.add('active', 'fw-bold');
            label.classList.remove('text-muted');
            numberSpan.classList.add('bg-primary', 'text-white');
            numberSpan.classList.remove('border');
        } else {
            label.classList.remove('active', 'fw-bold');
            label.classList.add('text-muted');
            numberSpan.classList.remove('bg-primary', 'text-white');
            numberSpan.classList.add('border');
        }
    });
}

});


$(document).on('click', '#cancelWizard', function () {
    if (confirm("Are you sure you want to close the wizard?")) {
        document.getElementById('tablelist').style.display = 'block';
        document.getElementById('improvedimportdiv').style.display = 'none';
    }
});

/**
 * Generic reusable CSV generator
 * @param {Array} columns - CSV column headers
 * @param {Array} data - Array of row arrays
 * @param {String} filename - CSV filename
 */
function generateCSV(columns, data, filename) {
    console.log("in fun generateCSV");
    let csvContent = columns.join(",") + "\n";
    data.forEach(row => {
        csvContent += row.join(",") + "\n";
    });

    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.style.display = "none";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Map uitype → sample value
 */

function getSampleValueByUitype(uitype, columnName = "", picklistData = {}) {
    const name = columnName.trim();

    if (parseInt(uitype) === 8 || parseInt(uitype) === 22) {
         const cleanColumnName = columnName.replace(/\s*\*\s*\(mandatory\)/i, '').trim();

        // if (picklistData[cleanColumnName] && picklistData[cleanColumnName].length > 0) {
        //     return picklistData[cleanColumnName]; // comma-separated values from main table
        // }
        if (picklistData[cleanColumnName] && picklistData[cleanColumnName].length > 0) {
            let val = picklistData[cleanColumnName];

            //  Escape any internal double quotes for safe CSV export
            val = val.replace(/"/g, '""');

            //  Wrap the full value in quotes so Excel treats it as one cell
            return `"${val}"`;
        } 
        // else if (columnName.toLowerCase().includes("owner")) {
        //     return "John Doe (Firstname + Lastname)";
        // }
        
    }

    if (name.toLowerCase().includes("ownerid")) return "John Doe (Firstname + Lastname)";

    switch (parseInt(uitype)) {
        case 6: // checkbox / boolean
            return "Yes/No";
        case 13: // datetime
            return "10-10-2025 12:00";
        case 17: // date
            return "10-10-2025";
        case 22: // firstname / list of names
            return "John Doe (Firstname + Lastname), Alice Musk (Firstname + Lastname)";
        case 25: // Related Module 
            return "Leads";
        case 26: // Related Record No
            return "LEA-2025-000088";
        default:
            return "";
    }
}



/**
 * Build and download CSV file based on DataImport metadata
 */
function downloadDataImportCSV() {
    console.log("in fun downloadDataImportCSV");

    const importcolumns = JSON.parse(document.getElementById("columns").value || "[]");
    const columnuitypess = JSON.parse(document.getElementById("uitypes").value || "[]");
    const picklistData = JSON.parse(document.getElementById("picklist_data").value || "{}");
    const importtablename = document.getElementById("tablename").value || "";
    const vendorextracols = JSON.parse(document.getElementById("vendorextracols").value || "[]");
    // console.log("importcolumns"+importcolumns);
    // Row 1 → sample data row based on uitype and column name
    const sampleRow = importcolumns.map((col, index) => {
        if (vendorextracols.includes(col)) {
            return 'johndeo@test.com(User Email ID)';
        } 
        else
        {
            const uitype = columnuitypess[index] || "";
            return getSampleValueByUitype(uitype, col, picklistData);
        }
    });
    const allimportdata = [sampleRow]; // only sample row

    const importfilename = `${importtablename}_data_format.csv`;

    generateCSV(importcolumns, allimportdata, importfilename);
}


// Event listener for link or button
$(document).on('click', '#download_csv_btn, #download_csv_link', function () {
    console.log("CSV download trigger clicked");
    downloadDataImportCSV();
});


// $(document).on('change', '#duplicateAction', function () {
//       if ($(this).val() === "Overwrite") {
//        alert("You selected Overwrite. All existing data will be completely replaced with the imported values.");
//     }
// });
$(document).on('change', '#duplicateAction', function () {
    const selectedVal = $(this).val();

    if (selectedVal === "Overwrite") {
        const confirmed = confirm("You have selected Overwrite option. In this Case, all existing data will be completely replaced with the imported values. Are you sure?");
        
        if (!confirmed) {
            // User clicked "Cancel" — reset dropdown and clear message
            $(this).val("skip").trigger("change");
             $('#selectedFields').empty(); // removes all <option>
            $('#selectedfieldsid').val(""); // reset hidden input too
            $('#fieldError').hide(); // hide any error message if visible
            $('.overwrite-msg').text("");
            return;
        }

        // User confirmed — show message text
        $('.overwrite-msg').text("You have selected Overwrite option. In this Case, all existing data will be completely replaced with the imported values.");
    } else {
        // For any other option, clear the message
        $('.overwrite-msg').text("");
        $('#fieldError').hide();
        $('#selectedFields').empty(); // removes all <option>
        $('#selectedfieldsid').val(""); // reset hidden input too
        $('#next-2').prop("disabled",false);
        //to  clear selection on select skip
        $('#availableFields option').prop('selected', false);
        $('#availableFields').trigger('change');
    }
});


$(document).on('change','#selectedFields', function () {
    if ($(this).val() && $(this).val().length > 0) {
        $('#next-2').prop('disabled', false);
        $('#fieldError').hide();
    }
});

function validateforovewrite(e)
{
                    console.log("in validate");
                let selectedOption = $('#duplicateAction').val();
                let selectedFields = $('#selectedFields').val() || [];
                    console.log("in validate selectedOption"+selectedOption);
                if (selectedOption === 'Overwrite') {
                    
                    console.log("len"+selectedFields.length);
                    if (selectedFields.length === 0) {
                        e.preventDefault();
                        $('#fieldError')
                            .text('Please select at least one field to proceed with Overwrite.')
                            .show();
                        $("#next-2").prop('disabled', true);
                        return false;
                    } else {
                        console.log("Fields selected — hiding error");
                        $('#fieldError').hide();
                        $("#next-2").prop('disabled', false);
                    }
                } else {
                    console.log("Not overwrite — no restriction");
                    $('#fieldError').hide();
                    $("#next-2").prop('disabled', false);
                }
            // }
}



