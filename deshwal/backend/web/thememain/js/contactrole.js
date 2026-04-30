

function getAbsoluteUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr;
}
function startLoading() {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  // Add the active class to show the overlay
  $("#loading-overlay").addClass("active");

  // Prevent scrolling
  $("body").addClass("loading").css("top", `-${scrollTop}px`);
}

function stopLoading() {
  const scrollTop = Math.abs(parseInt($("body").css("top"), 10));

  // Remove the active class to hide the overlay
  $("#loading-overlay").removeClass("active");

  // Re-enable scrolling
  $("body").removeClass("loading").css("top", "");
  window.scrollTo(0, scrollTop);
}

function getModuleUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr + module;
}

$(document).ready(function () {
  // Hide the modal when close or cancel buttons are clicked
 
  //modal for add contact role
  $("#edit-contact-role").on("click", function () {
    startLoading(); // Show loading overlay
    const urlParams = new URLSearchParams(window.location.search);
    const sourcemodule = urlParams.get("sourcemodule");
    const sourceid = urlParams.get("sourceid");
    const Record = urlParams.get("Record");
    var url = "edit?Record="+Record;
    if (sourcemodule && sourceid) {
      // Check if both sourcemodule and sourceid are not null or undefined
      url += `?sourcemodule=${encodeURIComponent(
        sourcemodule
      )}&sourceid=${encodeURIComponent(sourceid)}`;
    }
    // alert(url);
    $.get(url, function (data) {
      $("#add-lead-modal").modal("show").find(".modal-content").html(data);
      $("#toggle-switch2").removeClass("active");
      //added on 21/12/2024 for back to top
      const modalBody = document.getElementById("modalBody");
      const backToTopButton = document.getElementById("backToTop");
      if (modalBody) {
        modalBody.addEventListener("scroll", function () {
          //alert(modalBody);
          if (modalBody.scrollTop > 200) {
            backToTopButton.style.display = "block";
          } else {
            backToTopButton.style.display = "none";
          }
        });

        // Scroll back to top when the button is clicked
        backToTopButton.addEventListener("click", function () {
          modalBody.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        });
      }
      //end back to top
    }).always(function () {
      stopLoading(); // Hide loading overlay
    });
  });
  //end contact role
});