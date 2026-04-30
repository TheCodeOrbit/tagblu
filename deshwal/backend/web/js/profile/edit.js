$(document).ready(function () {
  $("#dtrecord").DataTable({
    processing: true,
    serverSide: false,
    ajax: "profiledata",
    columns: [
      { data: "id" },
      { data: "name" },
      { data: "description" },
      { data: "action" },
    ],
  });

  // code added by ptpatel on date 01-07-25
   $(document).on("click", '.profiletabhideshow', function () {
      const tabid = $(this).data("tabid");
      const ele = this;
      hideshow(ele, tabid);
    });
    function hideshow(ele, field) {
        const content = $(ele).closest('tr').siblings("tr." + field);
        //alert($(ele).html());
        content.fadeToggle();
        $(ele).children('.fa.' + field).toggleClass('fa-chevron-up fa-chevron-down');


    }

    function toggleContent(element) {
        const content = element.nextElementSibling;
        content.style.display = content.style.display === "block" ? "none" : "block";
        element.classList.toggle("active");
    }



    $(document).ready(function() {


        const csrfParam = $('meta[name="csrf-param"]').attr('content');     // usually '_csrf'
      const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!$.fn.DataTable.isDataTable('#dtrecord')) {
            var dataTable = $('#dtrecord').DataTable({
                "processing": true,
                "serverSide": true,
                "paging": false,
                "ajax": {
                    url: "tabs",
                    data: {
                        //'<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->getCsrfToken() ?>'
                        [csrfParam]: csrfToken
                    },
                    type: "post",

                    error: function() {
                        alert('error');
                    }

                },
                "columns": [{
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox" class="tabs" value="' + row[0] + '" name="tabs[]" >';
                            else
                                return '<input type="checkbox"  class="tabs" >';
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['2'] != null)
                                return row[1] + "(" + row[2] + ")";
                            else
                                return row[1];
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox"  class="" value="1"  name="1_' + row[0] + '" >';
                            else
                                return '<input type="checkbox"  class="" >';
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox"   class=""  value="1"  name="2_' + row[0] + '" >';
                            else
                                return '<input type="checkbox"  class="" >';
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox"  class="" value="1"  name="3_' + row[0] + '"  >';
                            else
                                return '<input type="checkbox"  class="" >';
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox"  class="" value="1"  name="4_' + row[0] + '" >';
                            else
                                return '<input type="checkbox"  class="" >';
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox"  class=""  value="1"  name="5_' + row[0] + '" >';
                            else
                                return '<input type="checkbox"  class="" >';
                        }
                    },
                    {
                        "data": "null",
                        "render": (data, type, row, meta) => {
                            if (row['1'] != null)
                                return '<input type="checkbox"  class=""  value="1"  name="6_' + row[0] + '" >';
                            else
                                return '<input type="checkbox"  class="" >';
                        }
                    },
                ],



            });
        }





    });
//code end added by ptpatel on date 01-07-25
});

document.addEventListener("DOMContentLoaded", function () {
  const toggles = document.querySelectorAll(".toggle");

  toggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      let visible = parseInt(this.dataset.visible, 10);
      let readonly = parseInt(this.dataset.readonly, 10);

      // Toggle the states based on current state
      if (visible === 0 && readonly === 0) {
        // From Write to Invisible
        visible = 1;
        readonly = 1;
      } else if (visible === 1 && readonly === 1) {
        // From Invisible to Read-only
        visible = 0;
        readonly = 1;
      } else {
        // From Read-only to Write
        visible = 0;
        readonly = 0;
      }

      // Update UI and hidden inputs
      this.dataset.visible = visible;
      this.dataset.readonly = readonly;
      this.querySelector(".toggle-visible").value = visible;
      this.querySelector(".toggle-readonly").value = readonly;

      // Update class based on the new state
      if (visible === 1 && readonly === 1) {
        this.className = "toggle state-invisible";
      } else if (visible === 0 && readonly === 1) {
        this.className = "toggle state-read-only";
      } else {
        this.className = "toggle state-write";
      }
    });
  });
});

// $(".savebutton").on("click", function (e) {
 $(document).on("click",".savebutton",function (e) {
  // alert("dfds");
  isvalid = true;
  var form = document.getElementById("pristine-valid-example");
  errorMessage = "This filed is required";
  if ($("#profile-profilename").val() == '') {
    var errorElement = $field.closest(".form-group").find(".help-block");
    errorElement.html(errorMessage); // Replace errorMessage with the actual message   
    var errorElement = $field.closest(".form-group").next(".help-block");
    errorElement.html(errorMessage); // Replace errorMessage with the actual message
    isvalid = false;
  }
  else if ($("#profile-description").val() == '') {
    var errorElement = $field.closest(".form-group").find(".help-block");
    errorElement.html(errorMessage); // Replace errorMessage with the actual message
    var errorElement = $field.closest(".form-group").next(".help-block");
    errorElement.html(errorMessage); // Replace errorMessage with the actual message
    isvalid = false;
  }


  if (isvalid)
    form.submit();
});

// added by zitendra on 7jan 2025
document.addEventListener("DOMContentLoaded", function () {
  const selectAllCheckbox = document.querySelector(".alltabs");
  const rowCheckboxes = document.querySelectorAll("input[name='tabs[]']");

  // Select All functionality
  selectAllCheckbox.addEventListener("change", function () {
      rowCheckboxes.forEach(rowCheckbox => {
          rowCheckbox.checked = selectAllCheckbox.checked;
          toggleRowItems(rowCheckbox);
      });
  });

  // Row-wise functionality for the first checkbox in each row
  rowCheckboxes.forEach(rowCheckbox => {
      rowCheckbox.addEventListener("change", function () {
          toggleRowItems(this);
          updateSelectAllState();
      });
  });

  // Individual checkbox handling based on name
  document.querySelectorAll("#dtrecord1 tbody input[type='checkbox']").forEach(checkbox => {
      checkbox.addEventListener("change", function () {
          const row = this.closest("tr");
          const rowCheckbox = row.querySelector("input[name='tabs[]']");
          // const rowItems = row.querySelectorAll("input:not([name='tabs[]'])");

          // Check if all row items are checked
          const allRowItemsChecked = Array.from(rowItems).every(item => item.checked);
          rowCheckbox.checked = allRowItemsChecked;

          updateSelectAllState();
      });
  });

  // Function to toggle row items based on the first checkbox in the row
  function toggleRowItems(rowCheckbox) {
      const row = rowCheckbox.closest("tr");
      const rowItems = row.querySelectorAll("input:not([name='tabs[]'])");
      rowItems.forEach(item => item.checked = rowCheckbox.checked);
  }

  // Function to update the "Select All" checkbox state
  function updateSelectAllState() {
      const totalRows = rowCheckboxes.length;
      const checkedRows = Array.from(rowCheckboxes).filter(chk => chk.checked).length;

      selectAllCheckbox.checked = checkedRows === totalRows;
      selectAllCheckbox.indeterminate = checkedRows > 0 && checkedRows < totalRows;
  }

  // code of form.php 
  function hideshow(ele, field) {
        const content = $(ele).closest('tr').siblings("tr." + field);
        //alert($(ele).html());
        content.fadeToggle();
        $(ele).children('.fa.' + field).toggleClass('fa-chevron-up fa-chevron-down');


    }

    function toggleContent(element) {
        const content = element.nextElementSibling;
        content.style.display = content.style.display === "block" ? "none" : "block";
        element.classList.toggle("active");
    }



    $(document).ready(function() {

      const csrfParam = $('meta[name="csrf-param"]').attr('content');     // usually '_csrf'
      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      if (!$.fn.DataTable.isDataTable('#dtrecord')) {
        var dataTable = $('#dtrecord').DataTable({
            "processing": true,
            "serverSide": true,
            "paging": false,
            "ajax": {
                url: "tabs",
                data: {
                    //'<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->getCsrfToken() ?>'
                    [csrfParam]: csrfToken
                },
                type: "post",

                error: function() {
                    alert('error');
                }

            },
            "columns": [{
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox" class="tabs" value="' + row[0] + '" name="tabs[]" >';
                        else
                            return '<input type="checkbox"  class="tabs" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['2'] != null)
                            return row[1] + "(" + row[2] + ")";
                        else
                            return row[1];
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class="" value="1"  name="1_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"   class=""  value="1"  name="2_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class="" value="1"  name="3_' + row[0] + '"  >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class="" value="1"  name="4_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class=""  value="1"  name="5_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class=""  value="1"  name="6_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
            ],



        });

    }




    });
  //end code of form.php
});
