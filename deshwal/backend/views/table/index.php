<?php

use yii\helpers\Html;

$this->title = 'Resizable Table';
?>

<style>
    .table {
        border-collapse: collapse;
        width: 100%;
    }

    .table th,
    .table td {
        border: 1px solid #ddd;
        padding: 8px;
        position: relative; /* Needed for resizable line */
    }

    .table th {
        background-color: #f2f2f2;
        cursor: ew-resize; /* Change cursor for resizable columns */
    }

    .resizable {
        position: relative;
    }

    .resizable:after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 10px; /* Width of the resize handle */
        cursor: ew-resize; /* Change cursor to resize */
    }
</style>

<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>
    <table id="resizableTable" class="table table-bordered">
        <thead>
            <tr>
                <th class="resizable">ID</th>
                <th class="resizable">First Name</th>
                <th class="resizable">Last Name</th>
                <th class="resizable">Email</th>
                <th class="resizable">Phone</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John</td>
                <td>Doe</td>
                <td>john.doe@example.com</td>
                <td>123-456-7890</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane</td>
                <td>Smith</td>
                <td>jane.smith@example.com</td>
                <td>098-765-4321</td>
            </tr>
            <!-- Add more rows as needed -->
        </tbody>
    </table>
</div>

<?php
$this->registerJs("
    $(document).ready(function() {
        // Cache the table reference for use in event handlers
        var \$table = $('#resizableTable');

        // Add mouse down event to header cells
        \$table.find('th.resizable').mousedown(function(e) {
            var \$this = $(this);
            var startWidth = \$this.width();
            var startX = e.pageX;

            // Mouse move event
            $(document).mousemove(function(e) {
                var newWidth = startWidth + (e.pageX - startX);
                if (newWidth > 50) { // Minimum width
                    \$this.width(newWidth);
                }
            });

            // Mouse up event
            $(document).mouseup(function() {
                $(document).off('mousemove'); // Remove mousemove listener
                $(document).off('mouseup'); // Remove mouseup listener
            });

            e.preventDefault(); // Prevent text selection
        });
    });
", \yii\web\View::POS_READY);
?>
