<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\grid\GridView;
use yii\widgets\Pjax;

AppAsset::register($this);

$this->title = Yii::t('app', 'Table List');

//Add DataTables CSS CDN
$this->registerCssFile('https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css', ['depends' => [AppAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css', ['depends' => [AppAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.min.css', ['depends' => [AppAsset::class]]);

// Corrected variable assignment
$li_1 = 'Table List';
$title = 'Table';
?>

<style>
    #datatable td {
        white-space: nowrap;
        /* Prevents the text from wrapping */
        overflow: hidden;
        /* Hides overflowed text */
        text-overflow: ellipsis;
        /* Adds '...' for clipped text */
        max-width: 160px;
        /* Same width as defined in DataTables */
        border-collapse: collapse;


    }

    #datatable {
        table-layout: fixed;
        /* Fix the layout of the table */
        width: 160%;
        /* Make sure the table occupies the full width */
        border-collapse: collapse;
        /* Collapse borders */
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18"><?= Html::encode($title) ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><?= Html::a(Html::encode($li_1), 'javascript: void(0);') ?></li>
                    <?php if (isset($title)): ?>
                        <li class="breadcrumb-item active"><?= Html::encode($title) ?></li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="table-list">
    <table id="datatable" class="table table-bordered dt-responsive">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="0">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="0">Un</button>
                    <button class="btn btn-sm btn-primary wrapBtn" data-col="0">Wp</button>
                    <button class="btn btn-sm btn-secondary clipBtn" data-col="0">Cp</button>

                </th>
                <th>Last Name
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="1">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="1">Un</button>
                    <button class="btn btn-sm btn-primary wrapBtn" data-col="1">Wp</button>
                    <button class="btn btn-sm btn-secondary clipBtn" data-col="1">Cp</button>
                </th>
                <th>Email
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="2">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="2">Un</button>
                    <button class="btn btn-sm btn-primary wrapBtn" data-col="2">Wp</button>
                    <button class="btn btn-sm btn-secondary clipBtn" data-col="2">Cp</button>
                </th>
                <th>Phone
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="3">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="3">Un</button>
                    <button class="btn btn-sm btn-primary wrapBtn" data-col="3">Wp</button>
                    <button class="btn btn-sm btn-secondary clipBtn" data-col="3">Cp</button>
                </th>
                <th>Country
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="4">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="4">Un</button>
                    <button class="btn btn-sm btn-primary wrapBtn" data-col="4">Wp</button>
                    <button class="btn btn-sm btn-secondary clipBtn" data-col="4">Cp</button>
                </th>
                <th>City
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="5">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="5">Un</button>
                    <button class="btn btn-sm btn-primary wrapBtn" data-col="5">Wp</button>
                    <button class="btn btn-sm btn-secondary clipBtn" data-col="5">Cp</button>
                </th>
                <th>Owner
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="6">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="6">Un</button>
                </th>
                <th>Company Name
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="7">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="7">Un</button>
                </th>
                <th>Address
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="8">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="8">Un</button>
                </th>
                <th>Company Address
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="9">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="9">Un</button>
                </th>
                <th>Company Website
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="10">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="10">Un</button>
                </th>
                <th>Employee Age
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="11">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="11">Un</button>
                </th>
                <th>Employee Name
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="12">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="12">Un</button>
                </th>
                <th>Created At
                    <button class="btn btn-sm btn-primary freeze-btn" data-col="13">F</button>
                    <button class="btn btn-sm btn-secondary unfreeze-btn" data-col="13">Un</button>
                </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>


</div>

<?php



// Registering DataTables assets


$this->registerJsFile('https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js', ['depends' => [AppAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js', ['depends' => [AppAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js', ['depends' => [AppAsset::class]]);

// <!-- Include colResizable JS for column resizing -->

$this->registerJsFile('https://cdn.jsdelivr.net/npm/colresizable/colResizable-1.6.min.js', ['depends' => [AppAsset::class]]);



$this->registerJs("
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            ajax: {
                url: '" . Url::to(['table/get-data']) . "',
                type: 'GET',
                dataSrc: ''
            },
            columns: [
                { data: 'id' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'country' },
                { data: 'city' },
                { data: 'owner' },
                { data: 'company_name' },
                { data: 'address' },
                { data: 'company_address' },
                { data: 'company_website' },
                { data: 'employee_age' },
                { data: 'employee_name' },
                { data: 'created_at' }
            ],
            scrollX: true,
            scrollY: '400px',
            scrollCollapse: true,
            paging: true,
            ordering: false,
            fixedHeader: true,
            responsive: true,
            autoWidth: false, // Disable auto width to respect manually set column widths
            initComplete: function(settings, json) {
                // Apply colResizable after DataTable is fully initialized
                $('#datatable').colResizable({
                    liveDrag: true,
                    headerOnly: true,
                    minWidth: 50,
                    resizeMode: 'fit',
                    onResize: function(e) {
                        // Adjust columns and redraw the table after resizing
                        table.columns.adjust().draw();
                    }
                });
            }
            
        });


        // In-place editing function
    $('#datatable tbody').on('click', 'td', function() {
        var \$cell = $(this);
        var originalValue = \$cell.text();
        var column = table.column(\$cell).index(); // Get the column index
        var rowId = table.row(\$cell.closest('tr')).data().id; // Get the row ID

        // Create an input element inside the cell
        var \$input = $('<input>', {
            type: 'text',
            value: originalValue,
            blur: function() {
                var newValue = \$input.val();
                if (newValue !== originalValue) {
                    // Send an AJAX request to update the value
                    // Get the CSRF token from the meta tag
                   
                    $.ajax({
                        url: '" . Url::to(['table/update-cell']) . "', // Update this with your backend URL
                        type: 'POST',
                        data: {
                            id: rowId,        // Row ID
                            column: column,   // Column index
                            value: newValue   // New value
                            
                        },
                        success: function(response) {
                            if (response.success) {
                                \$cell.text(newValue); // Update cell with new value
                            } else {
                                \$cell.text(originalValue); // Revert on failure
                            }
                        },
                        error: function() {
                            \$cell.text(originalValue); // Revert on error
                        }
                    });
                } else {
                    \$cell.text(originalValue); // No change
                }
            },
            keyup: function(e) {
                if (e.which === 13) { // Enter key to confirm
                    $(this).blur();
                }
            }
        }).appendTo(\$cell.empty()).focus();
    });

        
   
        // Wrap button click event
        $('.wrapBtn').on('click', function() {
            var colIdx = $(this).data('col');
            $('#datatable tbody td:nth-child(' + (colIdx + 2) + ')').css({
                'white-space': 'normal',   // Enable text wrapping
                'overflow': 'visible'      // Show the overflowing text
            });
        });

        // Clip button click event
        $('.clipBtn').on('click', function() {
            var colIdx = $(this).data('col');
            $('#datatable tbody td:nth-child(' + (colIdx + 2) + ')').css({
                'white-space': 'nowrap',    // Disable wrapping
                'overflow': 'hidden',       // Hide overflowed text
                'text-overflow': 'ellipsis' // Show ellipsis for overflowed text
            });
        });

        function reinitializeDataTable(freezeColumns) {
            // Destroy the current table instance
            table.destroy();

            // Define base options for reinitialization
            var options = {
                ajax: {
                    url: '" . Url::to(['table/get-data']) . "',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: 'id'},
                    { data: 'first_name' },
                    { data: 'last_name' },
                    { data: 'email' },
                    { data: 'phone' },
                    { data: 'country' },
                    { data: 'city' },
                    { data: 'owner' },
                    { data: 'company_name' },
                    { data: 'address' },
                    { data: 'company_address' },
                    { data: 'company_website' },
                    { data: 'employee_age' },
                    { data: 'employee_name' },
                    { data: 'created_at' }
                ],
            scrollX: true,
            scrollY: '400px',
            scrollCollapse: true,
            paging: true,
            ordering: false,
            fixedHeader: true,
            responsive: true,
            autoWidth: false, // Disable auto width to respect manually set column widths
            };

            // Only include fixedColumns if freezing columns
            if (freezeColumns > 0) {
                options.fixedColumns = { leftColumns: freezeColumns };
            }

            // Reinitialize DataTable with or without the fixedColumns option
            table = $('#datatable').DataTable(options);
        }

        // Freeze Button Click Event
        $('.freeze-btn').on('click', function(event) {
            event.preventDefault();
            var colIdx = $(this).data('col');
            reinitializeDataTable(colIdx + 2); // Reinitialize with frozen columns
        });

        // Unfreeze Button Click Event with Data Refresh
        $('.unfreeze-btn').on('click', function(event) {
            event.preventDefault();
            location.reload();
        });
    });
");

?>