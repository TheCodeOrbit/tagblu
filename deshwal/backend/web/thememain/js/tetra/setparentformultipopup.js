$(document).ready(function () {

    //this code is commented by ptpatel to resolve issue in PO it fetch value two time when select quote on date 27-10-2025
    // $(document).on("click", ".showinParent_server", function () {
    //     var $cell = $(this);
    //     var recordId = $cell.data("recordid");
    //     var display = $cell.data("display");
    //     var ref = $cell.data("ref");
    //     var hidden = $cell.data("hidden");

    //     if (typeof showinParent === 'function') {
    //         showinParent(recordId, display, ref, hidden);
    //     } else {
    //         console.warn("showinParent is not defined");
    //     }
    // });
        //end commented by ptpatel to resolve issue in PO it fetch value two time when select quote on date 27-10-2025

    $(document).on("click", ".p-close", function () { closeModalP(); });
    // $(document).on("click", ".v-icon-left", function () {
    $(document).on("click", ".v-icon-left-multi", function () {
        var $input = $(this);
        var fname1 = $input.data('removefiltervalue')
        removefilterValue(fname1);
    });

    // $(document).on("click", ".relatedsearch", function () {
    //     var $input = $(this);
    //     var fname1 = $input.data('fname1');
    //     var fname = $input.data('fname');
    //     var display = $input.data('display');
    //     var module = $input.data('module');
    //     var fieldid = $input.data('fieldid');

    //     showMultiCustomer1(fname1, fname, display, module, fieldid);
    // });


    // $(document).on("click", ".related-search-icon", function () {
    //     var $input = $(this);
    //     // var arg = extractparams($input.data('onrefclick'));
    //     //change data to attr by ptpatel on date 02-09-2025
    //     var onrefclick = $input.attr("data-onrefclick"); // always fresh
    //     var arg = extractparams(onrefclick);
    //     showMultiCustomer1(arg[0], arg[1], arg[2], arg[3], arg[4]);
    // });

    function extractparams(fnString) {
        var argsString = fnString.match(/\((.*)\)/)[1];

        // Split by commas and remove quotes/whitespace
        return argsString.split(",").map(arg => arg.trim().replace(/^['"]|['"]$/g, ''));

    }

    curpage = 1;
    rowsPerPage = 5;

    $(document).on("change", ".page-select-multi", function () {
        //alert("DFgf");
        filterTableMulti();

    });
    $(document).on("click", ".select-btn-multi", function () {
        filterTableMulti();


    });

    // Function to open the modal
    function openModal() {
        document.getElementById("modal").style.display = "block";
        displayTableMulti();


    }

    function removefilterValue(keyid) {
        $("#" + keyid).val('');
        filterTableMulti();


    }
//below line code ptpatel added on date 19-11-2025 to resolve issue if search in popup pagination not working 
  let multilastSearchValue = "";
    function filterTableMulti() {
        var searchTerms = [];

        // Dynamically collect all inputs with id starting with "search-"
        var inputs = document.querySelectorAll('input[id^="search-"]');

        inputs.forEach(input => {
            var match = input.id.match(/^search-(.+)$/); // Extract column key
            if (match) {
                var key = match[1];
                var value = input.value.trim().toLowerCase();
                if (value !== '') {
                    searchTerms.push([key, value]);
                }
            }
        });

        //code ptpatel added on date 19-11-2025 to resolve issue if search in popup pagination not working
        // Pagination logic
        /*let pageselectval = $(".page-select-multi").val();
        if (pageselectval && pageselectval !== '0') {
            pageselectval = pageselectval - 1;
        }
        if (searchTerms.length > 0) {
            pageselectval = 0;
        }*/
       // Pagination logic
        let pageselectval = $(".page-select-multi").val();
        let multisearchpageselectval = 0;

        if (pageselectval && pageselectval !== '0') {
            pageselectval = pageselectval - 1;
            multisearchpageselectval = pageselectval;
        }

        let mutlticurrentSearch = searchTerms.join(" ").trim(); // or however you're creating search text

        // Reset page ONLY when search text changes
        if (mutlticurrentSearch !== multilastSearchValue) {
            pageselectval = 0;   // reset page number only once
            multilastSearchValue = mutlticurrentSearch;  // update stored value
        }
        else
        {
          pageselectval = multisearchpageselectval ;
        }
        //code end here added by ptpatel on date 19-11-2025
        // var $cell = $('.showinParent_server');
        // if($cell){
        //   var display = $cell.data("rdisfield");
        //   var ref = $cell.data("ref");
        //   var hidden = $cell.data("hidden");
        //     // Get mname and maintabid from query string
        //   var mname =  $cell.data("mname");
        //   var maintabid =  $cell.data("maintabid");
        //   var sourceid =  $cell.data("sourceid");
        //   var sourcemodule =  $cell.data("sourcemodule");
        // } else {
        var $cell = $('.showinParent_multi_thead');
        var display = $cell.data("rdisfield");
        var ref = $cell.data("ref");
        var hidden = $cell.data("hidden");
        // Get mname and maintabid from query string
        var mname = $cell.data("mname");
        var maintabid = $cell.data("maintabid");
        var sourceid = $cell.data("sourceid");
        var sourcemodule = $cell.data("sourcemodule");
        // }
        // Call your server-side filtering function with dynamic search terms
        showMultiCustomer1(
            hidden,  // make sure this variable is defined globally or passed here
            ref,
            display,
            mname,
            maintabid,
            '',
            pageselectval,
            searchTerms,
            sourcemodule,
            sourceid
        );
    }
    // Function to display the current page
    function displayTableMulti() {
        const table = document.getElementById("data-tableMulti").getElementsByTagName("tbody")[0];
        const rows = Array.from(table.getElementsByTagName("tr"));

        rows.forEach((row, index) => {
            row.style.display = (index >= (curpage - 1) * rowsPerPage && index < curpage * rowsPerPage) ? "" : "none";
        });

        document.getElementById("page-info").innerText = `Page ${curpage} of ${Math.ceil(rows.length / rowsPerPage)}`;
    }

    // Function to go to the previous page
    function prevPage() {
        if (curpage > 1) {
            curpage--;
            displayTableMulti();
            initCheckboxLogic();
        }
    }

    // Function to go to the next page
    function nextPage() {
        // alert("vcxv");die;
        const table = document.getElementById("data-tableMulti").getElementsByTagName("tbody")[0];
        const rows = Array.from(table.getElementsByTagName("tr"));

        if (curpage < Math.ceil(rows.length / rowsPerPage)) {
            curpage++;
            displayTableMulti();
            initCheckboxLogic();

        }
    }

    // Close modal if user clicks outside the modal content
    // window.onclick = function(event) {
    //   const modal = document.getElementById("modal");
    //   if (event.target == modal) {
    //     closeModal();
    //   }
    // };




    function getQueryParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

});

////////////added for multi checkbox/////////////////
$(function () {
    const popupSelector = '#myModalMulti';         // your modal id
    const selectedIds = new Map();               // id (string) -> name
    let currentTextFieldId = null;
    let currentHiddenFieldId = null;

    // When clicking the search icon to open modal
    $(document).on('click', '.openPopupBtn', function () {
        //below line clear previous field selection 
         selectedIds.clear();   //  reset for new field this line added by ptpatel to resolve same ids in sa, sf and procurement in opportunity on date 11-02-2026
        const $icon = $(this);
        currentTextFieldId = $icon.data('fieldname');
        currentHiddenFieldId = $icon.data('fieldname1');

        // backup in modal data
        $(popupSelector).data({
            textFieldId: currentTextFieldId,
            hiddenFieldId: currentHiddenFieldId
        });

        //for edit mode
        mode = $("#mode").val();
        if (mode == 'Edit') {
            // Get the value from the hidden input (comma-separated IDs)
            const hiddenInputValue = $('#' + currentHiddenFieldId).val(); // Replace with your actual hidden input selector

            // Split the string into an array of IDs
            const idsArray = hiddenInputValue.split(',');


            idsArray.forEach(id => {
                // Assuming you want to store the name or other data for each ID, you can retrieve it from your DOM or dataset
                // For example, you could look for a checkbox element with the corresponding value (id)
                const $cb = $(`.selectmultid[value="${escapeSelector(id)}"]`);
                const name = $cb.data('display');

                // Set the ID and name into the selectedIds Map
                if(id!= '' && name !='')
                selectedIds.set(id, name);
            });

        }




        // $(popupSelector).modal('show');
    });

    // Add selected button (adds + removes based on checkboxes on the current modal page)
    $(document).on('click', '.addselected-multiple', function () {
        const $allBoxes = $(`${popupSelector} .selectmultid`);
        const $checkedBoxes = $(`${popupSelector} .selectmultid:checked`);

        if ($checkedBoxes.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        // Remove entries that are present in selectedIds but currently unchecked on this page
        $allBoxes.each(function () {
            const id = String($(this).val());
            if (!$(this).is(':checked') && selectedIds.has(id)) {
                selectedIds.delete(id);
            }
        });

        // Add all checked items (this will also re-add ones already present, safe)
        $checkedBoxes.each(function () {
            const $cb = $(this);
            const id = String($cb.val());
            const name = $cb.data('display');
            if(id!= '' && name !='')
            selectedIds.set(id, name);
        });

        renderSelectedTags();
        $(popupSelector).modal('hide');
    });
    // On changing the checkbox state (checked/unchecked)
    $(document).on('change', '.selectmultid', function () {
        const $allBoxes = $(`${popupSelector} .selectmultid`);
        const $checkedBoxes = $(`${popupSelector} .selectmultid:checked`);

        // Ensure there are checked boxes
        if ($checkedBoxes.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        // Remove entries from selectedIds where the box is unchecked
        $allBoxes.each(function () {
            const id = String($(this).val());
            if (!$(this).is(':checked') && selectedIds.has(id)) {
                selectedIds.delete(id);
            }
        });

        // Add checked items to selectedIds
        $checkedBoxes.each(function () {
            const $cb = $(this);
            const id = String($cb.val());
            const name = $cb.data('display'); // Use `data-display` as the name
            if(id!= '' && name !='')
            selectedIds.set(id, name);
        });

        // No renderSelectedTags() call here, it will only be called in the "Add Selected" button click event
    });


    // Render tags into the dynamic text container and update hidden input
    function renderSelectedTags() {

        // prefer current ids stored during open, fallback to modal data
        const textFieldId = currentTextFieldId || $(popupSelector).data('textFieldId');
        const hiddenFieldId = currentHiddenFieldId || $(popupSelector).data('hiddenFieldId');
        if (!textFieldId || !hiddenFieldId) return;

        const $container = $('#' + textFieldId);
        const $hiddenInput = $('#' + hiddenFieldId);

        // Clear existing tags
        $container.empty();

        console.log(selectedIds);
        // Build tags and hidden input value
        const ids = [];
        selectedIds.forEach((name, id) => {
            if (name !== '') {
                ids.push(id);
                // Use a button[type=button] for remove so it's clickable and won't submit forms
                const $tag = $('<span class="tag"></span>').text(name);
                const $removeBtn = $(
                    `<button type="button" class="remove-chip" data-id="${escapeAttr(id)}" data-hiddenfield="${hiddenFieldId}" data-textfield = "${textFieldId}" aria-label="Remove ${escapeAttr(name)}">×</button>`
                );
                $tag.append($removeBtn);
                $container.append($tag);
            }
        });

        // Update hidden input with comma separated ids (or adapt to your backend)
        $hiddenInput.val(ids.join(','));
    }

    // Delegated remove handler (works for dynamically created buttons)
    $(document).on('click', '.remove-chip', function (e) {

        e.preventDefault();
        e.stopPropagation();
        //for edit mode
        mode = $("#mode").val();
        if (mode == 'Edit') {
            
        const hiddenFieldId = $(this).attr('data-hiddenfield'); // use attr to ensure string
        const textFieldId = $(this).attr('data-textfield'); // use attr to ensure string
        currentTextFieldId = textFieldId;
        currentHiddenFieldId = hiddenFieldId;
        // Get the value from the hidden input (comma-separated IDs)
        const hiddenInputValue = $('#' + hiddenFieldId).val(); // Replace with your actual hidden input selector

        // Split the string into an array of IDs
        const idsArray = hiddenInputValue.split(',');


        idsArray.forEach(id => {
            // Assuming you want to store the name or other data for each ID, you can retrieve it from your DOM or dataset
            // For example, you could look for a checkbox element with the corresponding value (id)
            const $cb = $(`.selectmultid[value="${escapeSelector(id)}"]`);
            const name = $cb.data('display');

            // Set the ID and name into the selectedIds Map
            if(id!= '' && name !='')
            selectedIds.set(id, name);
        });

        // Debugging: Log selectedIds to check if it populated correctly
        //console.log(selectedIds);
        }
        //for edit mode 
        // end

        const idRaw = $(this).attr('data-id'); // use attr to ensure string
        const id = String(idRaw);

        // Remove from map
        const existed = selectedIds.delete(id);

        // Re-render tags/hidden input
        renderSelectedTags();

        // Uncheck checkbox in modal if present (may be on a different page)
        const $cb = $(`${popupSelector} .selectmultid[value="${id}"]`);
        if ($cb.length) {
            $cb.prop('checked', false);
        }

        // For debugging (uncomment if needed)
        console.log('remove-chip clicked, id:', id, 'was removed?:', existed);
    });

    // Keep checkboxes checked when modal content reloads (pagination)
    const popupEl = document.querySelector(popupSelector);

    if (popupEl) {
        const observer = new MutationObserver(() => {
            setTimeout(() => {
                // Debugging: Log selectedIds to the console
                console.log("selectedIds: ", selectedIds);

                // Debugging: Check if selectedIds is a Map
                if (selectedIds instanceof Map) {
                    console.log("selectedIds is a Map.");
                    console.log("Map keys:", Array.from(selectedIds.keys()));
                } else {
                    console.warn("selectedIds is not a Map, it's a:", typeof selectedIds);
                }

                // Iterate over the keys of selectedIds
                Array.from(selectedIds.keys()).forEach(id => {
                    // Debugging: Log each ID being processed
                    console.log("Processing ID:", id);

                    const $cb = $(`${popupSelector} .selectmultid[value="${escapeSelector(id)}"]`);

                    // Debugging: Log the checkbox element
                    console.log("Found checkbox:", $cb);

                    if ($cb.length) {
                        $cb.prop('checked', true);
                        console.log(`Checkbox for id ${id} checked.`);
                    }
                });
            }, 80);
        });

        observer.observe(popupEl, { childList: true, subtree: true });
    }


    // Utilities
    function escapeAttr(s) {
        // minimal attr escaper for values inserted into attributes
        return String(s).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function escapeSelector(s) {
        // escape for jQuery attribute-equals selector (basic)
        return String(s).replace(/(["'\\])/g, '\\$1');
    }


});


