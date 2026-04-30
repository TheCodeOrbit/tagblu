  //detail page code start from here
  $(document).ready(function () {

    var table = $('#historyTable').DataTable({
        data: [],
        columns: [
            { data: 'field' },
            { data: 'pre_visible' },
            { data: 'pre_readonly' },
            { data: 'post_visible' },
            { data: 'post_readonly' }
        ],
        pageLength: 10
    });

    // On dropdown change
    $('#tabFilter').on('change', function () {
        var selectedTab = $(this).val();
      console.log(typeof allData);
        if (!selectedTab || ! window.allData[selectedTab]) {
            table.clear().draw();
            return;
        }

        // load selected tab data
        table.clear();
        table.rows.add(allData[selectedTab]);
        table.draw();
    });

    $(document).on("click", "#backToProfilehistory", function () {
      window.location.href = "index"; 
  });
});