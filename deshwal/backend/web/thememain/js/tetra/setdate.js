$(document).ready(function () {

  function applyFlatpickrToSelector(classFragment) {
    const elements = document.querySelectorAll(`[class*="${classFragment}"]`);
    elements.forEach(el => {
      if (!el._flatpickr) {
        flatpickr(el, {
           dateFormat: "d-m-Y", 
        //   enableTime: true
        //    allowInput: false,  // Prevent typing in the input field
            // clickOpens: false,  // Prevent the calendar from opening on input click
            // onChange: function(selectedDates, dateStr, instance) {
            //     const mysqlFormattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            //     console.log("Formatted MySQL Date: ", mysqlFormattedDate);  // Send this to the server in MySQL-compatible format
            // }
        });
      }
    });
  }

  function applyFlatpickrs() {
    applyFlatpickrToSelector("DT~M");
    applyFlatpickrToSelector("DT~O");
  }

  // Watch for DOM changes to dynamically attach Flatpickr
  const observer = new MutationObserver(() => {
    applyFlatpickrs();
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true
  });


});