$(document).ready(function () {

  ///////////autofill from  invoice/////////////
  var targetNode1 = document.getElementById("invoice_number_lookup1");
  var observer1 = new MutationObserver(function (mutationsList1) {
    for (var mutation1 of mutationsList1) {
      if (
        mutation1.type === "attributes" &&
        mutation1.attributeName === "value"
      ) {
        console.log("inv value changed to:", targetNode1.value);

        getinvoicedetail(targetNode1.value);
        
      }
    }
  });

  // Configuration for the observer (observe attribute changes)
  var config1 = { attributes: true };
  observer1.observe(targetNode1, config1);

  ///////////get dc detail///////
  function getinvoicedetail(dcid) {
    if (dcid) {
      data = {
        dcid: dcid,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "GET",
        url: "getinvoicedetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
           
            

            $("#invoice_number").val(response.data.invoicedit_no);
            // $("#balance_amount").val(response.data.balance_amount);
            $("#invoice_amount").val(response.data.total_invoice_amount);
             flatpickr("#invoice_due_date", {
              dateFormat: "d-m-Y",
              defaultDate: response.data.invoice_due_date, // expects dd-mm-yyyy
              allowInput: false,
              readOnly: true,
            });

          
         

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
});
  