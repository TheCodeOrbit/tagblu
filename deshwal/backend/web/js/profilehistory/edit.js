$(document).ready(function () {

  $(document).on("click", "#refresh-icon", function () {
    location.reload();
  });

  //  Define table FIRST
  var table = $("#dtrecord").DataTable({
    processing: true,
    serverSide: false,
    ajax: "getallprofilehistory",
    columns: [
      { data: "crmid" },
      { data: "whodid" },
      { data: "changedon" },
      { data: "status" },
    ],
  });

  //  Then use it
  $('#dtrecord tbody').on('click', 'tr', function () {
    var data = table.row(this).data();

    console.log(data); // debug

    if (data) {
      window.location.href = "view?Record=" + data.id;
    }
  });

});