$(document).ready(function () {

  var newURL = window.location.href;

// Create a regular expression to match the desired endings
const adminPattern = /\/?(admin|admin\/|admin\/site\/|admin\/site\/index)\/?$/;
var adminurl=false;
if (adminPattern.test(newURL)) {
  adminurl = true;
  
} else {
  adminurl = false;
}
  // const adminurl = newURL.includes("admin/");
  var siteurl = "getnotifications";
  if (adminurl)
    siteurl = "site/getnotifications";


  fetchNotifications(); // Fetch on page load
  setInterval(fetchNotifications, 1000); // Fetch every second

  function fetchNotifications() {
    $.ajax({
      url: siteurl,
      method: "GET",
      success: function (response) {
        let data = JSON.parse(response);
        let notifications = data.notifications;
        let unreadCount = data.unread_count;
        let notificationList = $("#notification-list");
        let notificationCount = $("#notification-count");

        notificationList.empty();

        if (notifications.length > 0) {
          notifications.forEach(function (notif) {
            // Format the created time using formatTime()
            let formattedTime = formatNotificationTime(notif.createdtime);
            let item = `<div class="notification-item">
            <a href="${notif.source_link}" class="notification-link" target="_blank" data-id="${notif.id}">
                <p>${notif.message}</p>
                <span class="close-msg">&times;</span>
                <small>${formattedTime}</small>
                
            </a>
            
        </div>`;
            notificationList.append(item);
          });

          // Show count only if unread notifications exist
          if (unreadCount > 0) {
            notificationCount.text(unreadCount).show();
          } else {
            notificationCount.hide();
          }
        } else {
          notificationList.html(
            "<p class='no-notifications'>You don't have any notifications right now.</p>"
          );
          notificationCount.hide();
        }
      },
    });
  }

  function formatNotificationTime(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    // Define options for formatting time in 12-hour format with minutes.
    const timeOptions = { hour: "numeric", minute: "2-digit", hour12: true };

    // Check if the notification date is today
    if (date.toDateString() === now.toDateString()) {
      return `Today at ${date.toLocaleTimeString([], timeOptions)}`;
    } else {
      // Customize this as needed; here, we show the date and time.
      return `${date.toLocaleDateString()} at ${date.toLocaleTimeString(
        [],
        timeOptions
      )}`;
    }
  }

  // Show dropdown when clicking the notification icon
  $("#notification-btn").click(function () {
    $("#notification-dropdown").toggle();
    fetchNotifications(); // Refresh notifications when opened
    var siteurl = "marknotificationsseen";
  if (adminurl)
    siteurl = "site/marknotificationsseen";
    // Mark notifications as seen (display_status = 1)
    $.ajax({
      url: siteurl,
      method: "GET",
      success: function () {
        $("#notification-count").hide(); // Hide the count after opening
      },
    });
  });

  // Mark notification as read when clicked
  $(document).on("click", ".notification-link", function () {
    let notifId = $(this).data("id");

    console.log(notifId);

    // alert(notifId);
        var siteurl = "marknotificationread";
  if (adminurl)
    siteurl = "site/marknotificationread";

    $.ajax({
      url: siteurl,
      method: "POST",
      data: { id: notifId, _csrf: csrfToken },
      success: function () {
        fetchNotifications(); // Refresh list after marking as read
      },
    });
  });

  // Close dropdown
  $("#close-notifications").click(function () {
    $("#notification-dropdown").hide();
  });

  $(document).on("click", ".close-msg", function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $this = $(this);
    // Get notification id from the closest notification link
    var notifId = $this
      .closest(".notification-item")
      .find(".notification-link")
      .data("id");

    // alert(notifId)
  var siteurl = "updatereadstatus";
  if (adminurl)
    siteurl = "site/updatereadstatus";
    $.ajax({
      url: siteurl, // your endpoint to update read_status
      method: "POST",
      data: { notifId: notifId, read_status: 1, _csrf: csrfToken },
      success: function (response) {
        if (response.success) {
          // Remove the notification item or you could add a class to mark it as read
          $this.closest(".notification-item").remove();
        } else {
          alert("Failed to mark notification as read.");
        }
      },
      error: function () {
        alert("Error updating notification status.");
      },
    });
  });
});
