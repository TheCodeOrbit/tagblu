<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Add '.$Tabname);
//Add DataTables CSS CDN
$this->registerCssFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('https://unpkg.com/ag-grid-community/styles/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://unpkg.com/ag-grid-community/styles/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);

$url =Url::to(['create']);
?>

<div class="group-15">
      <div class="d-flex1">
        <div class="all-open-lead">All <?= $Tabname; ?> </div>
        <img class="ep-arrow-down-8" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg">
      </div>
      <div class="d-flex2">
        <!-- <img class="flowbite-refresh" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg"> -->
        <img class="" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg">
      </div>
      <div class="d-flex3">
        <div class="overlap-16">
        <div class="text-wrapper-126">Import</div>
        <img class="lets-icons-import" src="https://c.animaapp.com/4Te5O9cu/img/lets-icons-import-light.svg">
      </div>
      </div>
      <div class="d-flex4">
        <div class="overlap-group-14">
          
          <div class="text-wrapper-127">
            <!-- <select class="view-selector" id="viewSelector">
              <option value="list">List View</option>
              <option value="kanban">Kanban</option>
            </select> -->
            <div class="kl-dropdown">
            <button class="dropdown-button">
                <div class="listoption">
                  <span class="icon"><img class="tdesign-list" src="https://c.animaapp.com/4Te5O9cu/img/tdesign-list.svg"><img class="vector-67" src="https://c.animaapp.com/4Te5O9cu/img/vector-73-1.svg"></span> List View
                  <span class="btn-arrow"  onclick="toggleDropdown()"><img class="ic-round-arrow-left-65" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-68.svg"></span>
                </div>
                <div class="kanbanoption">
                  
                  <span class="icon">📊</span><img class="vector-67" src="https://c.animaapp.com/4Te5O9cu/img/vector-73-1.svg"> Kanben
                  <span class="btn-arrow"  onclick="toggleDropdown()"><img class="ic-round-arrow-left-65" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-68.svg"></span>
                </div>
            </button>
            <div class="dropdown-content">
                <!-- <a href="#"><span class="icon">📋</span> List View</a>
                <a href="#"><span class="icon">📊</span> Kanban</a> -->
                <a href="#" class="listview"><span class="icon"><img class="tdesign-list-inner" src="https://c.animaapp.com/4Te5O9cu/img/tdesign-list.svg"></span> List View</a>
                <a href="#" class="kabanview"><span class="icon">📊</span> Kanban</a>
            </div>
        </div>
          </div>
          
        </div>
      </div>
      <div class="d-flex5">
        <img class="tr" src="https://c.animaapp.com/4Te5O9cu/img/typcn-filter.svg">
      </div>
      <div class="d-flex6">
        <!-- <img class="fluent-column-triple" src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg" alt="Column Selector" style="width: 24px; height: 24px;"> -->
        <img class="" src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg" alt="Column Selector" style="width: 24px; height: 24px;">
      </div>
      <div class="d-flex7">
        <button type="button" id="add-lead-btn" class="btn add-lead-btn" style="background-color: var(--color-primary) !important; color:white">+ Add</button>
      </div>
</div>



<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     

     
    </div>
  </div>
</div>

<!-- end add model -->

<div class="overlap-4">
  <div class="frame-8">
    <div class="overlap-5">



      <!-- Table -->
      <div class="table-list">
        <div id="myGrid" class="ag-theme-alpine"></div>
        <div id="customPagination" class="custom-pagination"></div>

        <!-- <div class="page-div">
          <div class="result-page">
            <label for="page-size" class="page-size">Results Per Page</label>
            <select id="page-size" onchange="updatePageSize(this.value)">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>

          </div>

          <div class="custom-pagination">
            <button onclick="goToFirstPage()">First</button>
            <button onclick="goToPreviousPage()">&#8249;</button>
            <span id="current-page"></span>
            <button onclick="goToNextPage()">&#8250;</button>
            <button onclick="goToLastPage()">Last</button>
          </div>
        </div> -->




        <!-- pagination -->


      </div>
      <!-- End of Table -->


      <!-- Kanban Board -->
      <div class="kanban-board kanban-view">
        <!-- Open Column -->

        <div class="outer-kanban" id="open" ondrop="drop(event)" ondragover="allowDrop(event)">


          <div class="kanban-header-open">
            <div class="dv-kanban-title">
              <span class="kanban-title">Open</span>
            </div>
            <div class="kanban-add-btn">+</div>
          </div>

          <div class="kanban-column">
            <div class="inner-kanban" id="open">
              <?php foreach ($openCards as $card): ?>
                <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
                  <h4><?= htmlspecialchars($card->title) ?></h4>
                  <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>


        <!-- In Progress Column -->
        <div class="outer-kanban" id="in_progress" ondrop="drop(event)" ondragover="allowDrop(event)">
          <div class="kanban-header-inprogress">
            <div class="dv-kanban-title">
              <span class="kanban-title">In Progress</span>
            </div>
            <div class="kanban-add-btn">+</div>
          </div>

          <div class="kanban-column">
            <div class="inner-kanban" id="in_progress">
              <?php foreach ($inProgressCards as $card): ?>
                <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
                  <h4><?= htmlspecialchars($card->title) ?></h4>
                  <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>


        <!-- Done Column -->
        <div class="outer-kanban" id="done" ondrop="drop(event)" ondragover="allowDrop(event)">

          <div class="kanban-header-done">
            <div class="dv-kanban-title">
              <span class="kanban-title">Done</span>
            </div>
            <div class="kanban-add-btn">+</div>
          </div>

          <div class="kanban-column">
            <div class="inner-kanban" id="done">
              <?php foreach ($doneCards as $card): ?>
                <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
                  <h4><?= htmlspecialchars($card->title) ?></h4>
                  <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- end kanban -->




  </div>

</div>



</div>


<?php
// Registering DataTables assets
// $this->registerJsFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js', ['depends' => [AdminAsset::class]]);

// <!-- Include colResizable JS for column resizing -->
$this->registerJsFile('https://cdn.jsdelivr.net/gh/jeffreydwalter/ColReorderWithResize@9ce30c640e394282c9e0df5787d54e5887bc8ecc/ColReorderWithResize.js', ['depends' => [AdminAsset::class]]);


$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://code.jquery.com/ui/1.9.2/jquery-ui.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJs("
    // $('#add-lead-btn').click(function() {
    //     $('#add-lead-modal').modal('show');
    // });
    $('.btn-close, .btn-secondary').click(function() {
       $('#add-lead-modal').modal('hide');
    });

    //modal create
    
    $('#add-lead-btn').on('click', function () {
        $.get('{$url}', function(data) {
            $('#add-lead-modal').modal('show')
                .find('.modal-content')
                .html(data);
        });
    });



 
");
?>


<!-- Custom JavaScript to initialize ag-Grid -->
<script>
  class CustomHeader {
    init(params) {
      this.params = params;
      this.eGui = document.createElement('div');
      this.eGui.className = 'custom-header';
      this.eGui.innerHTML = `
        <span>${params.displayName}</span>
        <span class="sort-arrow" title="Sort">⬆⬇</span> <!-- Sort arrows -->
        <div class="dropdown">
          <img class="dropdown-toggle" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-67.svg" style="width: 16px; cursor: pointer;">
          <div class="dropdown-menu">
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
      const sortArrowButton = this.eGui.querySelector('.sort-arrow');
      const dropdownToggle = this.eGui.querySelector('.dropdown-toggle');
      const dropdownMenu = this.eGui.querySelector('.dropdown-menu');


      sortArrowButton.addEventListener('click', () => {
        const currentSort = this.params.column.getSort();
        let nextSort;

        // Toggle sorting between ascending, descending, and none
        if (currentSort === 'asc') {
          nextSort = 'desc';
        } else if (currentSort === 'desc') {
          nextSort = null;
        } else {
          nextSort = 'asc';
        }

        // Set the sort state
        this.params.setSort(nextSort);
      });

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

    onGridReady(params) {
      this.gridApi = params.api;
      this.columnApi = params.columnApi;
      console.log("Grid API and Column API initialized.");
    }

    handleAction(action) {
      const columnField = this.params?.column?.getColId();
      if (!columnField) {
        console.error("Column field is undefined.");
        return;
      }

      switch (action) {

        case 'wrapText':
          this.toggleWrapText(true);
          break;
        case 'clipText':
          this.toggleWrapText(false);
          break;
        case 'freezeColumn':
          if (!this.params || !this.params.api) {
            console.error('api is not available. Ensure params are set correctly.');
            return;
          }

          const allColumns = this.params.api.getColumnDefs();
          const columnIndex = allColumns.findIndex(col => col.field === columnField);

          // Update column states for all columns up to the selected one
          const newColumnState = allColumns.map((col, index) => ({
            colId: col.field, // Use 'field' as colId when working with column definitions
            pinned: index <= columnIndex ? 'left' : null
          }));

          this.params.api.applyColumnState({
            state: newColumnState,
            applyOrder: true
          });
          break;
        case 'unfreezeColumn':
          if (!this.params || !this.params.api) {
            console.error('api is not available. Ensure params are set correctly.');
            return;
          }

          // Get all column definitions
          const all_Columns = this.params.api.getColumnDefs();

          // Set `pinned` to `null` for all columns
          const new_ColumnState = all_Columns.map(col => ({
            colId: col.field,
            pinned: null
          }));

          // Apply the new column state to unfreeze all columns
          this.params.api.applyColumnState({
            state: new_ColumnState,
            applyOrder: true
          });

          // Refresh cells to apply the changes
          this.params.api.refreshCells({
            force: true
          });
          break;
      }
    }


    toggleWrapText(wrap) {
      //alert('chch')
      const columnDef = this.params.column.getColDef();
      columnDef.cellClass = wrap ? 'ag-cell-wrap' : '';
      this.params.api.refreshCells({
        force: true
      });
    }

    getGui() {
      return this.eGui;
    }
  }

  // Initialize grid with columnDefs
  const columnDefs = [{
      headerName: 'First Name',
      field: 'firstName',
      headerComponent: CustomHeader,
      resizable: true
    },
    {
      headerName: 'Last Name',
      field: 'lastName',
      headerComponent: CustomHeader,
      resizable: true
    },
    {
      headerName: 'Position',
      field: 'position',
      headerComponent: CustomHeader,
      resizable: true
    },
    {
      headerName: 'Office',
      field: 'office',
      headerComponent: CustomHeader,
      resizable: true
    },
    {
      headerName: 'Age',
      field: 'age',
      headerComponent: CustomHeader,
      resizable: true
    },
    {
      headerName: 'Start Date',
      field: 'startDate',
      headerComponent: CustomHeader,
      resizable: true
    },
    {
      headerName: 'Salary',
      field: 'salary',
      headerComponent: CustomHeader,
      resizable: true
    }
  ];
  let gridApi;
  let gridColumnApi;

  const gridOptions = {
    columnDefs: columnDefs,
    rowData: [{
        firstName: 'John test test test test test test test test test dsfgdfg',
        lastName: 'Doe',
        position: 'Developer',
        office: 'New York',
        age: 30,
        startDate: '2020-01-01',
        salary: 50000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'London',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
      {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      }, {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
 {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },

       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },
       {
        firstName: 'Jane',
        lastName: 'Smithtesttesttesttest testtesttesttest testtest testtest',
        position: 'Designer',
        office: 'usa',
        age: 25,
        startDate: '2019-04-15',
        salary: 45000
      },

    ],
    onGridReady: (params) => {
      gridApi = params.api; // Initialize gridApi
      gridColumnApi = params.columnApi; // Initialize columnApi
      // Change the "Page Size" label to "Results Per Page"
      const pageSizeLabel = document.querySelector('.ag-label[aria-hidden="false"]');
      if (pageSizeLabel && pageSizeLabel.textContent.trim() === "Page Size:") {
        pageSizeLabel.textContent = "Results Per Page:";
      }

      const pagingRowSummaryPanel = document.querySelector('.ag-paging-row-summary-panel');
      if (pagingRowSummaryPanel) {
        pagingRowSummaryPanel.remove();
      }

      const firstButton = document.querySelector('[data-ref="btFirst"]');
      const lastButton = document.querySelector('[data-ref="btLast"]');

      if (firstButton) {
        firstButton.innerHTML = 'First';
      }
      if (lastButton) {
        lastButton.innerHTML = 'Last';
      }

      // Remove unnecessary elements
      const previousButton = document.querySelector('[data-ref="btPrevious"]');
      const pagingDescription = document.querySelector('.ag-paging-description');
      const nextButton = document.querySelector('[data-ref="btNext"]');

      if (previousButton) previousButton.remove();
      if (pagingDescription) pagingDescription.remove();
      if (nextButton) nextButton.remove();



      // Add custom pagination div
      const customPaginationContainer = document.createElement('div');
      customPaginationContainer.id = "customPagination";
      customPaginationContainer.className = "custom-pagination";

      // Insert the custom pagination div between "First" and "Last" buttons
      const paginationContainer = document.querySelector('.ag-paging-page-summary-panel');
      if (paginationContainer && lastButton) {
        paginationContainer.insertBefore(customPaginationContainer, lastButton);
      }


      createCustomPagination();
    },

    pagination: true,
    paginationPageSize: 10, // Set initial page size
    paginationPageSizeSelector: [10, 20, 50, 100],
    defaultColDef: {
      sortable: true,
      filter: true,
      resizable: true,
      wrapText: true,
      autoHeight: true
    },
    // onPaginationChanged: updateCurrentPage, // Update page number display when pagination changes
    suppressHorizontalScroll: false,
  };

  // Create grid after DOM content is loaded
  document.addEventListener('DOMContentLoaded', () => {
    const gridDiv = document.querySelector('#myGrid');
    agGrid.createGrid(gridDiv, gridOptions);

    // Update the current page display initially
    // updateCurrentPage();
  });



  function createCustomPagination() {
    const customPaginationDiv = document.getElementById("customPagination");

    // Clear any existing buttons
    customPaginationDiv.innerHTML = '';

    // Calculate total number of pages
    const totalPages = gridApi.paginationGetTotalPages();

    // Create numbered buttons for each page
    for (let i = 0; i < totalPages; i++) {
      const pageButton = document.createElement("button");
      pageButton.innerText = i + 1;
      pageButton.classList.add("page-button");

      // Highlight the active page button
      if (i === gridApi.paginationGetCurrentPage()) {
        pageButton.classList.add("active");
      }

      // Add click event to navigate to the selected page
      pageButton.addEventListener("click", () => {
        gridApi.paginationGoToPage(i);
        createCustomPagination(); // Refresh pagination to reflect the current page
      });

      customPaginationDiv.appendChild(pageButton);
    }
  }
</script>

<script>
  function allowDrop(event) {
    event.preventDefault();
  }

  function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
  }

  function drop(event) {
    event.preventDefault();
    var cardId = event.dataTransfer.getData("text");
    var card = document.getElementById(cardId);
    event.target.appendChild(card);

    // Update the pipeline stage in the database via AJAX
    var newStage = event.target.id;
    $.ajax({
      url: '<?= Yii::$app->urlManager->createUrl('lead/update-stage') ?>',
      type: 'POST',
      data: {
        id: cardId.split("-")[1],
        pipeline_stage: newStage,
        _csrf: '<?= Yii::$app->request->getCsrfToken() ?>'
      },
      success: function(response) {
        console.log("Card stage updated");
      }
    });
  }
</script>
<script>
  // Switch to List View
  function switchToListView() {
    document.querySelector('.kanban-view').style.display = 'none'; // Hide Kanban
    document.querySelector('.table-list').style.display = 'block'; // Show Table
    localStorage.setItem('selectedView', 'list'); // Save preference
  }

  // Switch to Kanban View
  function switchToKanbanView() {
    document.querySelector('.table-list').style.display = 'none'; // Hide Table
    document.querySelector('.kanban-view').style.display = 'flex'; // Show Kanban
    localStorage.setItem('selectedView', 'kanban'); // Save preference
  }

  // Add event listener for view change
    document.querySelector(".listview").addEventListener('click', function() {
     
        switchToListView();
        document.querySelector('.kanbanoption').style.display = "none";
        document.querySelector('.listoption').style.display = "block";
        toggleDropdown();

      
    });
     document.querySelector(".kabanview").addEventListener('click', function() {
      
        switchToKanbanView();
         document.querySelector('.kanbanoption').style.display = "block";
        document.querySelector('.listoption').style.display = "none";
        toggleDropdown();
     
    });
    function toggleDropdown() {
    const dropdownContent = document.querySelector('.dropdown-content');
    if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
    } else {
        dropdownContent.style.display = "block";
    }
}

  // Load the selected view on page load
  window.onload = function() {
     switchToListView();
    // const selectedView = localStorage.getItem('selectedView') || 'list'; // Default to list if none selected
    // const viewSelector = document.getElementById('viewSelector');

    // if (selectedView === 'kanban') {
    //   switchToKanbanView();
    //   viewSelector.value = 'kanban';
    // } else {
    //   switchToListView();
    //   viewSelector.value = 'list';
    // }

    // // Add event listener for view change
    // viewSelector.addEventListener('change', function() {
    //   if (this.value === 'kanban') {
    //     switchToKanbanView();
    //   } else {
    //     switchToListView();
    //   }
    // });
  }
</script>