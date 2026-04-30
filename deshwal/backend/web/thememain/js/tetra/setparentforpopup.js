$(document).ready(function () {
    $(document).on("click", ".showinParent_server", function () {
        var $cell = $(this);
        var recordId = $cell.data("recordid");
        var display = $cell.data("display");
        var ref = $cell.data("ref");
        var hidden = $cell.data("hidden");

        if (typeof showinParent === 'function') {
            showinParent(recordId, display, ref, hidden);
        } else {
            console.warn("showinParent is not defined");
        }
    });

     $(document).on("click", ".p-close", function () {closeModalP();});
     $(document).on("click", ".v-icon-left", function(){
      var $input = $(this);
      var fname1 = $input.data('removefiltervalue')
        removefilterValue(fname1);
    });

     $(document).on("click", ".relatedsearch", function () {
      var $input = $(this);
        var fname1 = $input.data('fname1');
        var fname = $input.data('fname');
        var display = $input.data('display');
        var module = $input.data('module');
        var fieldid = $input.data('fieldid');

        showCustomer1(fname1, fname, display, module, fieldid);
    });

    
     $(document).on("click", ".related-search-icon", function () {
        var $input = $(this);
        // var arg = extractparams($input.data('onrefclick'));
        //change data to attr by ptpatel on date 02-09-2025
        var onrefclick = $input.attr("data-onrefclick"); // always fresh
        var arg = extractparams(onrefclick);
        showCustomer1(arg[0], arg[1], arg[2], arg[3], arg[4]);
    });

    function extractparams(fnString)
    {
        var argsString = fnString.match(/\((.*)\)/)[1];

        // Split by commas and remove quotes/whitespace
         return argsString.split(",").map(arg => arg.trim().replace(/^['"]|['"]$/g, ''));

    }

    curpage = 1;
rowsPerPage = 5;

$(document).on("change", ".page-select", function(){
    //alert("DFgf");
    filterTable();
});
$(document).on("click", ".select-btn", function(){
    filterTable();
});

// Function to open the modal
function openModal() {
  document.getElementById("modal").style.display = "block";
  displayTable();
}

function removefilterValue(keyid)
	{
			$("#"+keyid).val('');
			filterTable();
	}
  //below line code ptpatel added on date 19-11-2025 to resolve issue if search in popup pagination not working 
  let lastSearchValue = "";
  window.popupState = {};
function filterTable() {
    var searchTerms = [];
    var searchTerms_child = [];

    // Dynamically collect all PARENT inputs with id starting with "search-"
    var parentInputs = document.querySelectorAll('input[id^="search-"]:not(.child-search-input)');
    
    parentInputs.forEach(input => {
        var match = input.id.match(/^search-(.+)$/); // Extract column key
        if (match) {
            var key = match[1];
            var value = input.value.trim().toLowerCase();
            if (value !== '') {
                searchTerms.push([key, value]);
            }
        }
    });

    var childInputs = document.querySelectorAll('input.child-search-input');
    
    childInputs.forEach(input => {
        var value = input.value.trim().toLowerCase();
        var childKey = input.getAttribute('data-child-key'); // e.g. "pickup_asset_detail.serial_no"
        if (childKey && value !== '') {
            searchTerms_child.push([childKey, value]);
        }
    });

    // Pagination logic
    let pageselectval = $(".page-select").val();
    let searchpageselectval = 0;

    if (pageselectval && pageselectval !== '0') {
        pageselectval = pageselectval - 1;
        searchpageselectval = pageselectval;
    }

    let currentSearch = searchTerms.concat(searchTerms_child).join(" ").trim();

    // Reset page ONLY when search text changes
    if (currentSearch !== lastSearchValue) {
        pageselectval = 0;
        lastSearchValue = currentSearch;
    }
    else {
        pageselectval = searchpageselectval;
    }

    // Get cell data from thead
    var $cell = $('.showinParent_server_thead');
    //below var commented by ptpatel on date 17-01-2025 to resolve blank search issue 
    /*var display = $cell.data("rdisfield");
    var ref = $cell.data("ref");
    var hidden = $cell.data("hidden");
    var mname = $cell.data("mname");
    var maintabid = $cell.data("maintabid");
    var sourceid = $cell.data("sourceid");
    var sourcemodule = $cell.data("sourcemodule");

    //added byptpatel on date 17-01-2026
    var dependent = $cell.data("dependent");
    var conditionfield = $cell.data("conditionfield");
    var dependentval = $cell.data("dependentval");*/
    //below code added  by ptpatel on date 17-01-2025 to resolve blank search issue 
    window.popupState = {
        hidden: $cell.data("hidden"),
        ref:  $cell.data("ref"),
        display: $cell.data("rdisfield"),
        mname: $cell.data("mname"),
        maintabid: $cell.data("maintabid"),
        sourceid: $cell.data("sourceid"),
        sourcemodule: $cell.data("sourcemodule"),

        dependent: $cell.data("dependent"),
        conditionfield:  $cell.data("conditionfield"),
        dependentval: $cell.data("dependentval")
    };

    var state = window.popupState;
    // console.log(state);
    var hidden = state.hidden;
    var ref = state.ref;
    var display = state.display;
    var mname = state.mname;
    var maintabid = state.maintabid;
    var sourceid = state.sourceid;
    var sourcemodule = state.sourcemodule;

    var dependent = state.dependent;
    var conditionfield = state.conditionfield;
    var dependentval = state.dependentval;
    //end code added  by ptpatel on date 17-01-2025 to resolve blank search issue 
    // Call showCustomer1 with BOTH parent and child search terms
    showCustomer1(
        hidden,
        ref,
        display,
        mname,
        maintabid,
        '',
        pageselectval,
        searchTerms,
        sourcemodule,
        sourceid,
        searchTerms_child,  // NEW: pass child search terms
        //added byptpatel on date 17-01-2026
        dependent,
        conditionfield,
        dependentval
    );
}
$(document).on('click', '#adv-search-btn', function() {
    var row = $('#child-search-row');
    if (!row.length) return;
    
    if (row.is(':visible')) {
        row.hide();
        // Only clear if user manually closes (not on initial hide)
        if (!$(this).data('manual-close')) {
            $('.child-search-input').val('');
        }
        $(this).data('manual-close', true);
    } else {
        row.show();
    }
});

    // Function to display the current page
function displayTable() {
  const table = document.getElementById("data-table").getElementsByTagName("tbody")[0];
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
    displayTable();
  }
}

// Function to go to the next page
function nextPage() {
	// alert("vcxv");die;
  const table = document.getElementById("data-table").getElementsByTagName("tbody")[0];
  const rows = Array.from(table.getElementsByTagName("tr"));
  
  if (curpage < Math.ceil(rows.length / rowsPerPage)) {
    curpage++;
    displayTable();
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