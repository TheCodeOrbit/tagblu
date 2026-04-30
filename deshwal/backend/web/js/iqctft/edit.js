$(document).ready(function () {
    var newURL = window.location.href;
    var newURL = window.location.href;
    var module = "iqctft";
    var str = newURL.split(module);
    var editusrl = str[0] + "iqcftf/list";
    let today = new Date().toISOString().split("T")[0];
    $(".c-faqs__item-question").trigger("click");
    function toggleOthersDescriptions() {
        if ($('#make').val() == "7") {
            $('.section-other_description').show();
        } else {
            $('.section-other_description').hide();
            $("#other_description").val("");
        }
    }
    toggleOthersDescriptions();
    $('#make').change(function(){
        toggleOthersDescriptions();
    });
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