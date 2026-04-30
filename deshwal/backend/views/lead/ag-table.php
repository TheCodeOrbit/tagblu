<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ag-Grid Test</title>
  <!-- ag-Grid CSS for the Alpine theme -->
  <link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-grid.css">
  <link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-alpine.css">
</head>
<style>
  /* Clip text with ellipsis */
  .ag-theme-alpine .ag-cell {
    white-space: nowrap;
    /* Prevent wrapping by default */
    overflow: hidden;
    text-overflow: ellipsis;
    /* Clip text with ellipsis */
  }

  /* Class to allow wrapped text */
  .ag-theme-alpine .ag-cell-wrap {
    white-space: normal;
    word-wrap: break-word;
    /* Enable wrap text */
  }

  /* Ensure AG Grid container does not clip the dropdown */
  .ag-theme-alpine {
    overflow: visible !important;
    /* Allow dropdowns to appear outside container */
  }

  /* Ensure AG Grid container does not clip the dropdown */
  .ag-theme-alpine {
    overflow: visible !important;
    /* Allow dropdowns to appear outside container */
  }

  .custom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
  }

  .dropdown {
    position: relative;
  }

  .dropdown-toggle {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 12px;
    color: #333;
  }

  .dropdown-menu {
    display: none;
    position: fixed;
    /* Fixed position so it renders outside table boundaries */
    background: white;
    border: 1px solid #ccc;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 10000;
    /* High z-index to ensure visibility */
    min-width: 120px;
    white-space: nowrap;
  }

  .dropdown-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 12px;
    color: #333;
    background: white;
    border: none;
    width: 100%;
    text-align: left;
  }

  .dropdown-item:hover {
    background: #f0f0f0;
  }


  /* Default: Clip text (no wrapping) */
  .ag-cell {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
</style>

<body>

  <!-- Container for ag-Grid with Alpine theme -->
  <div id="myGrid" class="ag-theme-alpine" style="height: 200px; width: 100%;"></div>

  <!-- ag-Grid JavaScript -->
  <script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>

  <!-- Custom JavaScript to initialize ag-Grid -->
  <script>
    class CustomHeader {
        init(params) {
            console.log(params); // Debugging line
            this.params = params;
            this.eGui = document.createElement('div');
            this.eGui.className = 'custom-header';
            this.eGui.innerHTML = `
                <span>${params.displayName}</span>
                <div class="dropdown">
                    <button class="dropdown-toggle">▼</button>
                    <div class="dropdown-menu" style="display: none;">
                        <button class="dropdown-item" data-action="freezeColumn">Freeze Column</button>
                        <button class="dropdown-item" data-action="unfreezeColumn">Unfreeze Column</button>
                        <button class="dropdown-item" data-action="wrapText">Wrap Text</button>
                        <button class="dropdown-item" data-action="clipText">Clip Text</button>
                    </div>
                </div>
            `;
            this.setupEventListeners();
        }

        setupEventListeners() {
            const dropdownToggle = this.eGui.querySelector('.dropdown-toggle');
            const dropdownMenu = this.eGui.querySelector('.dropdown-menu');

            dropdownToggle.addEventListener('click', () => {
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            this.eGui.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', (event) => {
                    const action = event.target.getAttribute('data-action');
                    this.handleAction(action);
                    dropdownMenu.style.display = 'none';
                });
            });

            document.addEventListener('click', (event) => {
                if (!this.eGui.contains(event.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        }

        handleAction(action) {
            const columnField = this.params.column.getColId();

            switch (action) {
                case 'wrapText':
                    this.toggleWrapText(true);
                    break;
                case 'clipText':
                    this.toggleWrapText(false);
                    break;
                case 'freezeColumn':
                    this.params.api.applyColumnState({
                        state: [{ colId: columnField, pinned: 'left' }],
                        applyOrder: true
                    });
                    break;
                case 'unfreezeColumn':
                    this.params.api.applyColumnState({
                        state: [{ colId: columnField, pinned: null }],
                        applyOrder: true
                    });
                    break;
            }
        }

        toggleWrapText(wrap) {
            const columnDef = this.params.api.getColumnDef(this.params.column.getColId());
            if (columnDef) {
                columnDef.cellClass = wrap ? 'ag-cell-wrap' : '';
                this.params.api.refreshCells({ force: true });
            }
        }

        getGui() {
            return this.eGui;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const columnDefs = [
            { headerName: 'First Name', field: 'firstName', headerComponent: CustomHeader, resizable: true },
            { headerName: 'Last Name', field: 'lastName', headerComponent: CustomHeader, resizable: true },
            { headerName: 'Position', field: 'position', headerComponent: CustomHeader, resizable: true },
            { headerName: 'Office', field: 'office', headerComponent: CustomHeader, resizable: true },
            { headerName: 'Age', field: 'age', headerComponent: CustomHeader, resizable: true },
            { headerName: 'Start Date', field: 'startDate', headerComponent: CustomHeader, resizable: true },
            { headerName: 'Salary', field: 'salary', headerComponent: CustomHeader, resizable: true }
        ];

        const rowData = [
            { firstName: 'John', lastName: 'Doe', position: 'Developer', office: 'New York', age: 30, startDate: '2020-01-01', salary: 50000 },
            { firstName: 'Jane', lastName: 'Smith', position: 'Designer', office: 'London', age: 25, startDate: '2019-04-15', salary: 45000 }
        ];

        const gridOptions = {
            columnDefs: columnDefs,
            rowData: rowData,
            defaultColDef: { sortable: true, filter: true, resizable: true },
            domLayout: 'autoHeight',
        };

        const gridDiv = document.querySelector('#myGrid');
        agGrid.createGrid(gridDiv, gridOptions); // Use 'createGrid' instead of 'new agGrid.Grid'
    });
</script>

</body>

</html>