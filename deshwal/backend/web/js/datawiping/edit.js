$(document).ready(function () {
  function checkBulkUploadPermission() {
    var recordIdCheck = $('#recordid');
    if (recordIdCheck.length === 0) {
        return;
    }
    var datawipingId = recordIdCheck.val();
    if (!datawipingId) {
        return;
    }

    $.ajax({
        url: 'canbulkupload',
        type: 'POST',
        dataType: 'json',
        data: {
            id: datawipingId,
            _csrf: $('#csrfToken').val() 
        },
        success: function (res) {
            if (!res || !res.success) {
                $('#popup-bulk-upload-btn').hide();
                $('#popup-bulk-upload-file').hide();
                return;
            }

            if (res.allow) {
                $('#popup-bulk-upload-btn').show();
            } else {
                $('#popup-bulk-upload-btn').hide();
            }
        },
        error: function () {
            $('#popup-bulk-upload-btn').hide();
            $('#popup-bulk-upload-file').hide();
        }
    });
}

  checkBulkUploadPermission();

  function loadAssetCount() {
    var datawipingId = $('#recordid').val();
    if (!datawipingId) return;

    $.ajax({
      url: 'getcount',
      type: 'POST',
      dataType: 'json',
      data: { id: datawipingId, _csrf: $('#csrfToken').val() },
      success: function (res) {
        if (res && res.success) {
          const $btn = $('#detail-btn-asset');
          if ($btn.length) {
            $btn.text('View Asset Details (Count = ' + ((res.count && res.count > 0) ? res.count : 0) + ')');
          }
        }
      },
      error: function (xhr) {
        console.error('get_asset_count error:', xhr.responseText);
      }
    });
  }
  loadAssetCount();
  const addRowDiv = $('.add-more-records').closest('.col-3');
  if (!$('#doc-modal').length) {
    $('body').append(`
          <div class="modal fade" id="doc-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Instruction</h5>
                  <button type="button" class="close_dw" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body text-center">
                  <a href="documentation" download="Datawiping.docx"
                    id="doc-download-btn" class="btn btn-secondary mb-2">
                    Download Instruction
                  </a>
                  <br>
                  <button type="button" id="doc-upload-btn" style="display:none" class="btn btn-info">
                    Upload Documentation
                  </button>
                  <input type="file" id="doc-upload-file"
                        accept=".pdf,.doc,.docx" style="display:none">
                </div>
              </div>
            </div>
          </div>`);
  }

  if (addRowDiv.find('#bulk-upload-btn').length === 0) {
    addRowDiv.append(`
          <button class="btn btn-secondary ml-2" id="bulk-upload-btn" type="button">Bulk Upload CSV</button>
          <input type="file" id="bulk-upload-file" accept=".csv" style="display:none" />
          <button type="button" class="btn btn-primary ml-2" id="sample-download-btn">Sample Download</button>
          <button type="button" id="documentation-main-btn" class="btn btn-primary">
            Documentation
          </button>
        `);
  }
if (!$('#sample-download-modal').length) {
  $('body').append(`
    <div class="modal fade" id="sample-download-modal" tabindex="-1" role="dialog" aria-hidden="true">
      <style>
        #sample-download-modal .form-check {
          display: flex;
          align-items: center;
          gap: 6px;
          margin-bottom: 8px;
        }
        #sample-download-modal .form-check-input {
          margin-right: 0;
        }
        #sample-download-modal .form-check-label {
          margin-bottom: 0;
        }
      </style>
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Download Options</h5>
            <button type="button" class="close sample-download-close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="text-left mb-3">
              <div class="form-check">
                <input class="form-check-input" type="radio"
                       name="sample-download-type" id="sample-type-sample"
                       value="sample" checked>
                <label class="form-check-label" for="sample-type-sample">
                  Sample CSV
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio"
                       name="sample-download-type" id="sample-type-picklist"
                       value="picklist">
                <label class="form-check-label" for="sample-type-picklist">
                  Picklists CSV
                </label>
              </div>
            </div>
            <button type="button" class="btn btn-primary" id="sample-download-confirm">
              Download
            </button>
          </div>
        </div>
      </div>
    </div>
  `);
}
  $(document).on('click', '#sample-download-btn', function () {
  $('input[name="sample-download-type"][value="sample"]').prop('checked', true);
  $('#sample-download-modal').modal('show');
});

$(document).on('click', '.sample-download-close', function () {
  $('#sample-download-modal').modal('hide');
});

$(document).on('click', '#sample-download-confirm', function () {
  const type = $('input[name="sample-download-type"]:checked').val();

  if (type === 'sample') {
    window.location.href = 'downloadsample';
  } else {
    $.ajax({
      url: 'downloadpicklists',
      type: 'GET',
      xhrFields: { responseType: 'blob' },
      success: function (data, status, xhr) {
        const blob = new Blob([data], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'picklists_data.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(link.href);
        $('#sample-download-modal').modal('hide');
      },
      error: function () {
        alert('Error downloading picklists. Please try again.');
      }
    });
  }
});

  $('#bulk-upload-btn').on('click', function () {
    $('#bulk-upload-file').val('');
    $('#bulk-upload-file').click();
  });
  if (!$('#bulk-progress').length) {
    $('body').append(
      '<div id="bulk-progress" ' +
      'style="display:none;position:fixed;top:10px;right:10px;' +
      'background:#000;color:#fff;padding:8px 12px;border-radius:4px;' +
      'font-size:12px;z-index:9999;">' +
      'Processing CSV: <span id="bulk-progress-text">0%</span>' +
      '</div>'
    );
  }
  function showBulkProgress(message) {
    $('#bulk-progress').show();
    $('#bulk-progress-text').text(message);
  }

  function hideBulkProgress() {
    $('#bulk-progress').hide();
  }
  $(document).on('click', '#documentation-main-btn', function () {
    $('#doc-modal').modal('show');
  });

  $(document).on('click', '#doc-download-btn', function () {
    $('#doc-modal').modal('hide');
  });
  $(document).on('click', '.close_dw', function () {
    $('#doc-modal').modal('hide');
  });
  $(document).on('click', '#doc-upload-btn', function () {
    $('#doc-upload-file').val('');
    $('#doc-upload-file').click();
  });
  /////PopUp Bulk Upload
  /*******************************************************
 * POPUP BULK UPLOAD + PREVIEW + SEARCH + ZIP MAPPING
 *******************************************************/

  let popupAllCsvRows = [];
  let popupBulkAborted = false;
  let popupLaptopSerialNumbers = new Set();
  let popupCurrentPage = 1;
  let popupFilteredRows = null;
  const POPUP_PAGE_SIZE = 1000;
  let popupDropdownMaps = null;

  async function initPopupDropdownMaps() {
    if (popupDropdownMaps) return;

    await addRowBtn('2613', 'datawiping',1);

    const $tbody = $('#previewTablecsvUploadSample');
    const $row = $tbody.find('tr.product-row').last();
    if (!$row.length) return;

    const makeSet = new Set();
    const typeSet = new Set();
    const capacitySet = new Set();
    const softwareSet = new Set();
    const wipingCompletedSet = new Set();

    const makeMap = new Map();          // text -> id
    const typeMap = new Map();
    const capacityMap = new Map();
    const softwareMap = new Map();
    const wipingCompletedMap = new Map();

    $row.find('select[name*="[make]"] option').each(function () {
      const id = $(this).val();
      const txt = $(this).text().trim().toLowerCase();
      if (txt) {
        makeSet.add(txt);
        makeMap.set(txt, id);
      }
    });

    $row.find('select[name*="[type]"] option').each(function () {
      const id = $(this).val();
      const txt = $(this).text().trim().toLowerCase();
      if (txt) {
        typeSet.add(txt);
        typeMap.set(txt, id);
      }
    });

    $row.find('select[name*="[capacity]"] option').each(function () {
      const id = $(this).val();
      const txt = $(this).text().trim().toLowerCase();
      if (txt) {
        capacitySet.add(txt);
        capacityMap.set(txt, id);
      }
    });

    $row.find('select[name*="[software_name]"] option').each(function () {
      const id = $(this).val();
      const txt = $(this).text().trim().toLowerCase();
      if (txt) {
        softwareSet.add(txt);
        softwareMap.set(txt, id);
      }
    });

    $row.find('select[name*="[wiping_completed]"] option').each(function () {
      const id = $(this).val();
      const txt = $(this).text().trim().toLowerCase();
      if (txt) {
        wipingCompletedSet.add(txt);
        wipingCompletedMap.set(txt, id);
      }
    });

    $row.remove();

    popupDropdownMaps = {
      make: makeSet,
      type: typeSet,
      capacity: capacitySet,
      software_name: softwareSet,
      wiping_completed: wipingCompletedSet,
      makeMap,
      typeMap,
      capacityMap,
      softwareMap,
      wipingCompletedMap,
    };
  }


  function validateCsvDropdownsUsingMaps(row, displayIndex) {
    if (!popupDropdownMaps) return true;

    const make = (row['Make'] || '').trim().toLowerCase();
    const type = (row['Type'] || '').trim().toLowerCase();
    const capacity = (row['Capacity'] || '').trim().toLowerCase();
    const software = (row['Software Name'] || '').trim().toLowerCase();
    const wipingCompleted = (row['Wiping Completed*'] || '').trim().toLowerCase();

    if (make && !popupDropdownMaps.make.has(make)) {
      rollbackPopupBulkUpload('INVALID_DROPDOWN', displayIndex, 'Make', row['Make']);
      return false;
    }
    if (type && !popupDropdownMaps.type.has(type)) {
      rollbackPopupBulkUpload('INVALID_DROPDOWN', displayIndex, 'Type', row['Type']);
      return false;
    }
    if (capacity && !popupDropdownMaps.capacity.has(capacity)) {
      rollbackPopupBulkUpload('INVALID_DROPDOWN', displayIndex, 'Capacity', row['Capacity']);
      return false;
    }
    if (software && !popupDropdownMaps.software_name.has(software)) {
      rollbackPopupBulkUpload('INVALID_DROPDOWN', displayIndex, 'Software Name', row['Software Name']);
      return false;
    }
    if (wipingCompleted && !popupDropdownMaps.wiping_completed.has(wipingCompleted)) {
      rollbackPopupBulkUpload('INVALID_DROPDOWN', displayIndex, 'Wiping Completed', row['Wiping Completed*']);
      return false;
    }

    return true;
  }


  $(document).on('click', '#popup-bulk-upload-btn', function () {
    $('#popup-bulk-upload-file').val('').click();
  });

  $(document).on('change', '#popup-bulk-upload-file', function () {
    popupBulkAborted = false;
    popupAllCsvRows = [];
    popupLaptopSerialNumbers.clear();

    $('#loading-overlay').removeAttr('style');
    startLoading();
    showBulkProgress('Reading file...');

    const file = this.files[0];
    if (!file) {
      stopLoading();
      return;
    }
    $('#popup-bulk-upload-file').val('');

    Papa.parse(file, {
      header: true,
      skipEmptyLines: true,
      dynamicTyping: false,
      complete: async function (results) {
        try {
          if (popupBulkAborted) return;

          showBulkProgress('Validating CSV...');
          const records = results.data || [];

          if (!Array.isArray(records) || records.length === 0) {
            rollbackPopupBulkUpload('NO_VALID_RECORD');
            return;
          }
          const hddLimit = getHddCountLimit();
          if (hddLimit !== null && records.length > hddLimit) {
            rollbackPopupBulkUpload('EXCEEDS_HDD_LIMIT', null, null, {
              limit: hddLimit,
              count: records.length
            });
            return;
          }
          await initPopupDropdownMaps();
          if (popupBulkAborted) return;

          let data = records;
          const tempSerials = new Set();

          popupAllCsvRows = [];
          popupLaptopSerialNumbers.clear();


          for (let i = 0; i < data.length; i++) {
            if (popupBulkAborted) return;

            const row = data[i];
            const serial = (row['Laptop Serial No*'] || '').trim();
            const wipingCompleted = (row['Wiping Completed*'] || '').trim();
            const displayIndex = i + 1;

            if (!serial) {
              rollbackPopupBulkUpload('MISSING_LAPTOP_SERIAL_NO', displayIndex);
              return;
            }
            if (!wipingCompleted) {
              rollbackPopupBulkUpload('MISSING_WIPING_COMPLETED', displayIndex);
              return;
            }
            if (laptopSerialNumbers.has(serial) || tempSerials.has(serial)) {
              rollbackPopupBulkUpload('DUPLICATE_LAPTOP_SERIAL_NO', displayIndex, null, serial);
              return;
            }

            if (!validateCsvDropdownsUsingMaps(row, displayIndex)) {
              return;
            }

            tempSerials.add(serial);
            popupLaptopSerialNumbers.add(serial);
            popupAllCsvRows.push(row);
          }

          popupFilteredRows = null;
          popupCurrentPage = 1;

          buildPopupPreviewTable();
          $('#csvPreviewModal').modal('show');

          showSuccessMessage(`✓ ${popupAllCsvRows.length} records validated successfully!`);
        } catch (err) {
          console.error(err);
          showErrorMessage('Error while processing CSV');
        } finally {
          stopLoading();
          hideBulkProgress();
        }
      },
      error: function (err) {
        console.error('Papa.parse error', err);
        showErrorMessage('Unable to read CSV file');
        stopLoading();
        hideBulkProgress();
      }
    });
  });
  function getHddCountLimit() {
    const raw = $('#lead_display_hdd_count').clone()
      .children().remove().end()
      .text().trim();

    const num = parseInt(raw, 10);
    return Number.isFinite(num) ? num : null;
  }
  /*********************
   * PREVIEW RENDERING
   *********************/
  function showPopupPreviewModal() {
    popupCurrentPage = 1;
    buildPopupPreviewTable();
    $('#csvPreviewModal').modal('show');
  }

  function buildPopupPreviewTable(rowsOverride) {
    const rows = rowsOverride || popupFilteredRows || popupAllCsvRows;
    const $tbody = $('#previewTablecsvUpload tbody');
    $tbody.empty();

    if (!rows || !rows.length || popupBulkAborted) {
      $('#previewTotalRecords').text(0);
      $('#previewRecordInfo').text('No records to display');
      return;
    }

    const totalRecords = rows.length;
    $('#previewTotalRecords').text(totalRecords);

    const startIdx = (popupCurrentPage - 1) * POPUP_PAGE_SIZE;
    const endIdx = Math.min(startIdx + POPUP_PAGE_SIZE, totalRecords);

    for (let i = startIdx; i < endIdx; i++) {
      const r = rows[i];
      const displayIndex = i + 1;

      const certificateName = r['Certificate'] || '';
      const status = r._certStatus || '';
      const statusHtml =
        status === 'mapped'
          ? '<span style="color:green;font-size:11px;margin-left:4px;">(ZIP mapped)</span>'
          : status === 'missing'
            ? '<span style="color:red;font-size:11px;margin-left:4px;">(ZIP missing)</span>'
            : '';

      const $tr = $(`
            <tr>
                <td>${displayIndex}</td>
                <td>${r['Laptop Serial No*'] || ''}</td>
                <td>${r['HDD/SDD Serial No'] || ''}</td>
                <td>${r['Make'] || ''}</td>
                <td>${r['Type'] || ''}</td>
                <td>${r['Capacity'] || ''}</td>
                <td>${r['Software Name'] || ''}</td>
                <td>${r['Wiping Completed*'] || ''}</td>
                <td>${r['Wiping Date'] || ''}</td>
                <td>
                    ${certificateName}
                    ${statusHtml}
                </td>
            </tr>
        `);

      $tbody.append($tr);
    }

    updatePopupPagination(rows.length);
    $('#previewPagination, #previewPaginationBottom').show();
  }


  /*********************
   * SEARCH IN PREVIEW
   *********************/
  $('#previewSearch').on('input', function () {
    const q = $(this).val().trim().toLowerCase();

    if (!q) {
      popupFilteredRows = null;
      popupCurrentPage = 1;
      buildPopupPreviewTable();
      return;
    }

    popupFilteredRows = popupAllCsvRows.filter(row => {
      return [
        row['Laptop Serial No*'],
        row['HDD/SDD Serial No'],
        row['Make'],
        row['Type'],
        row['Capacity'],
        row['Software Name'],
        row['Wiping Completed*'],
        row['Wiping Date'],
        row['Certificate']
      ].some(v => (v || '').toString().toLowerCase().includes(q));
    });

    popupCurrentPage = 1;
    buildPopupPreviewTable(popupFilteredRows);
  });

  /*********************
   * POPUP PAGINATION
   *********************/
  function showPopupPage(page) {
    const rows = popupFilteredRows || popupAllCsvRows;
    const totalPages = Math.ceil(rows.length / POPUP_PAGE_SIZE) || 1;
    if (page < 1 || page > totalPages) return;

    popupCurrentPage = page;
    buildPopupPreviewTable(rows);
  }

  function updatePopupPagination(totalRecords) {
    const rows = popupFilteredRows || popupAllCsvRows;
    totalRecords = rows.length;

    const totalPages = Math.ceil(totalRecords / POPUP_PAGE_SIZE) || 1;
    const page = popupCurrentPage;

    const startRec = totalRecords === 0 ? 0 : (page - 1) * POPUP_PAGE_SIZE + 1;
    const endRec = Math.min(page * POPUP_PAGE_SIZE, totalRecords);
    $('#previewRecordInfo').text(`Showing ${startRec}–${endRec} of ${totalRecords}`);

    const $pageNumbers = $('#previewPageNumbers');
    $pageNumbers.empty();

    const windowSize = 5;
    let startPage = Math.max(1, page - 2);
    let endPage = Math.min(totalPages, startPage + windowSize - 1);
    if (endPage - startPage + 1 < windowSize) {
      startPage = Math.max(1, endPage - windowSize + 1);
    }

    for (let p = startPage; p <= endPage; p++) {
      const $btn = $(`<button class="btn btn-sm ${p === page ? 'btn-primary' : 'btn-primary'} me-1">${p}</button>`);
      if (p === page) $btn.prop('disabled', true);
      $btn.on('click', () => showPopupPage(p));
      $pageNumbers.append($btn);
    }

    $('#previewFirstPage').prop('disabled', page <= 1);
    $('#previewPrevPage').prop('disabled', page <= 1);
    $('#previewNextPage').prop('disabled', page >= totalPages);
    $('#previewLastPage').prop('disabled', page >= totalPages);
  }

  // Bind pagination buttons
  $('#previewFirstPage').on('click', function (e) { e.preventDefault(); showPopupPage(1); });
  $('#previewPrevPage').on('click', function (e) { e.preventDefault(); showPopupPage(popupCurrentPage - 1); });
  $('#previewNextPage').on('click', function (e) { e.preventDefault(); showPopupPage(popupCurrentPage + 1); });
  $('#previewLastPage').on('click', function (e) {
    e.preventDefault();
    const rows = popupFilteredRows || popupAllCsvRows;
    const totalPages = Math.ceil(rows.length / POPUP_PAGE_SIZE) || 1;
    showPopupPage(totalPages);
  });


  $(document).on('click', '#popup-choose-zip-btn', function (e) {
    e.preventDefault();

    $('#popup-zip-upload-file').val('');
    $('#popup-upload-zip-btn')
      .text('Upload ZIP')
      .prop('disabled', false)
      .hide();

    $('#popup-zip-status').text('').css('color', '');
    $('#popup-choose-zip-btn').text('Choose ZIP');

    $('#popup-zip-upload-file').attr('accept', '.zip');
    $('#popup-zip-upload-file').click();
  });

  $(document).on('change', '#popup-zip-upload-file', function () {
    const file = this.files[0];
    if (!file) return;

    if (!file.name.toLowerCase().endsWith('.zip')) {
      alert('Only .zip files are allowed.');
      $(this).val('');
      return;
    }

    startLoading();
    $('#popup-zip-status').text('');
    setTimeout(() => {
      stopLoading();
      $('#popup-upload-zip-btn').show();
    }, 300);
  });

  // 3) Click "Upload ZIP" → send to server, then map to popupAllCsvRows
  $(document).on('click', '#popup-upload-zip-btn', function (e) {
    e.preventDefault();

    const zip = $('#popup-zip-upload-file')[0].files[0];
    if (!zip) {
      alert('Please choose a ZIP file.');
      return;
    }

    const fd = new FormData();
    fd.append('certificate_zip', zip);
    fd.append('_csrf', $('#csrfToken').val()); // adjust CSRF selector if needed

    startLoading();
    $('#popup-upload-zip-btn').text('Uploading...').prop('disabled', true);
    $('#popup-zip-status').text('');

    $.ajax({
      url: 'uploadzipfiles',  // your existing endpoint
      method: 'POST',
      data: fd,
      contentType: false,
      processData: false,
      success: function (resp) {
        stopLoading();
        $('#popup-upload-zip-btn')
          .text('ZIP Uploaded ✓')
          .prop('disabled', true);

        $('#popup-zip-status').css('color', 'green').text('✔ ZIP uploaded');
        $('#popup-choose-zip-btn').text('Change ZIP');

        // resp should contain the mapping like { "filename.pdf": 1674, ... }
        if (resp && resp.files) {
          applyZipMappingToPopupRows(resp.files); // map to popupAllCsvRows
        }
      },
      error: function () {
        stopLoading();
        $('#popup-upload-zip-btn').text('Upload ZIP').prop('disabled', false);
        $('#popup-zip-status').css('color', 'red').text('Failed! Try different file.');
        $('#popup-choose-zip-btn').text('Choose ZIP');
      }
    });
  });

  /********************************************
   * ZIP → POPUP CSV ROW MAPPING
   ********************************************/
  function applyZipMappingToPopupRows(fileMap) {
    if (!Array.isArray(popupAllCsvRows) || popupAllCsvRows.length === 0) {
      return;
    }

    popupAllCsvRows = popupAllCsvRows.map(row => {
      const expected = (row['Certificate'] || '').trim();
      if (!expected) {
        row._certExpected = '';
        row._certAttachId = null;
        row._certStatus = '';
        return row;
      }

      row._certExpected = expected;
      const attachId = fileMap[expected];

      if (attachId) {
        row._certAttachId = attachId;
        row._certStatus = 'mapped';
      } else {
        row._certAttachId = null;
        row._certStatus = 'missing';
      }
      return row;
    });

    if ($('#csvPreviewModal').is(':visible')) {
      buildPopupPreviewTable();
    }
  }

  function popupHasMissingCertificates() {
    if (!Array.isArray(popupAllCsvRows)) return false;
    return popupAllCsvRows.some(r => r._certStatus === 'missing');
  }
  /*********************
   * SAVE POPUP CSV TO DB
   *********************/
  $('#saveCsvToDbBtn').on('click', async function () {
    if (!popupAllCsvRows || popupAllCsvRows.length === 0) {
      showErrorMessage('No data to save');
      return;
    }
    if (popupHasMissingCertificates()) {
      const msg =
        'Some certificates could not be matched to the uploaded files.\n\n' +
        'These records will be saved without a linked certificate.\n' +
        'Do you still want to continue and save the data?';

      const confirmed = window.confirm(msg);
      if (!confirmed) {
        return;
      }
    }
    startLoading();
    showBulkProgress('Saving to database...');

    try {
      const payload = popupAllCsvRows.map(row => {
        const makeTxt = (row['Make'] || '').trim().toLowerCase();
        const typeTxt = (row['Type'] || '').trim().toLowerCase();
        const capacityTxt = (row['Capacity'] || '').trim().toLowerCase();
        const softwareTxt = (row['Software Name'] || '').trim().toLowerCase();
        const wipingTxt = (row['Wiping Completed*'] || '').trim().toLowerCase();

        return {
          laptop_serial_no: (row['Laptop Serial No*'] || '').trim(),
          hdd_sdd_serial_no: row['HDD/SDD Serial No'] || '',

          // IDs from maps (fallback to null if not found)
          make_id: popupDropdownMaps.makeMap.get(makeTxt) || null,
          type_id: popupDropdownMaps.typeMap.get(typeTxt) || null,
          capacity_id: popupDropdownMaps.capacityMap.get(capacityTxt) || null,
          software_id: popupDropdownMaps.softwareMap.get(softwareTxt) || null,
          wiping_completed_id: popupDropdownMaps.wipingCompletedMap.get(wipingTxt) || null,

          // keep original text too if you want
          make_text: row['Make'] || '',
          type_text: row['Type'] || '',
          capacity_text: row['Capacity'] || '',
          software_text: row['Software Name'] || '',
          wiping_completed_text: row['Wiping Completed*'] || '',

          wiping_date: row['Wiping Date'] || '',
          certificate_name: row['Certificate'] || '',
          cert_attach_id: row._certAttachId || null,
          cert_status: row._certStatus || ''
        };
      });

      const formData = new FormData();
      formData.append('csvdata', JSON.stringify(payload));
      formData.append('_csrf', $('#csrfToken').val());
      formData.append('_record_id', $('#recordid').val());

      const response = await $.ajax({
        url: 'bulksavecsv',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json'
      });

      if (response.success) {
        showSuccessMessage(`${popupAllCsvRows.length} records saved successfully!`);
        popupAllCsvRows = [];
        popupLaptopSerialNumbers.clear();
        $('#csvPreviewModal').modal('hide');
        window.location.reload();
      } else {
        showErrorMessage(response.message || 'Save failed');
      }
    } catch (error) {
      console.error(error);
      showErrorMessage('Failed to save records. Please try again.');
    } finally {
      stopLoading();
      hideBulkProgress();
    }
  });


  /********************************************
   * ZIP UPLOAD UI (BUTTONS + INPUT)
   ********************************************/
  function bindPopupZipEvents() {
    // Open file chooser
    $(document).on('click', '#choose-zip-btn', function (e) {
      e.preventDefault();
      $('#zip-file-input').val('');
      $('#upload-zip-btn').text('Upload ZIP').prop('disabled', false).show();
      $('#zip-status').text('').css('color', '');
      $('#choose-zip-btn').text('Choose ZIP');

      $('#zip-file-input').attr('accept', '.zip');
      $('#zip-file-input').click();
    });

    // When ZIP file selected
    $(document).on('change', '#zip-file-input', function () {
      const file = this.files[0];
      if (!file) return;

      if (!file.name.toLowerCase().endsWith('.zip')) {
        alert('Only .zip files are allowed.');
        $(this).val('');
        return;
      }

      startLoading();
      $('#upload-zip-btn').show();
      $('#zip-status').text('');
      setTimeout(() => { stopLoading(); }, 300);
    });

    // Upload ZIP
    $(document).on('click', '#upload-zip-btn', function (e) {
      e.preventDefault();

      const zip = $('#zip-file-input')[0].files[0];
      if (!zip) {
        alert('Please choose a ZIP file.');
        return;
      }

      const fd = new FormData();
      fd.append('certificate_zip', zip);
      fd.append('_csrf', $('#csrfToken').val());

      startLoading();
      $('#upload-zip-btn').text('Uploading...').prop('disabled', true);
      $('#zip-status').text('');

      $.ajax({
        url: 'uploadzipfiles',
        method: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        success: function (resp) {
          stopLoading();
          $('#upload-zip-btn')
            .text('ZIP Uploaded ✓')
            .prop('disabled', true);

          $('#zip-status').css('color', 'green').text('✔ ZIP uploaded');
          $('#choose-zip-btn').text('Change ZIP');

          // resp.files should be like: { "filename.pdf": 1674, ... }
          if (resp && resp.files) {
            applyZipMappingToPopupRows(resp.files);
          }
        },
        error: function () {
          stopLoading();
          $('#upload-zip-btn').text('Upload ZIP').prop('disabled', false);
          $('#zip-status').css('color', 'red').text('Failed! Try different file.');
          $('#choose-zip-btn').text('Choose ZIP');
        }
      });
    });
  }

  // Call once on page load
  bindPopupZipEvents();


  /*********************
   * CANCEL POPUP
   *********************/
  $('#cancelPreviewBtn').on('click', function () {
    popupBulkAborted = true;
    popupAllCsvRows = [];
    popupLaptopSerialNumbers.clear();
    $('#csvPreviewModal').modal('hide');
  });

  /*********************
   * POPUP ROLLBACK
   *********************/
 function rollbackPopupBulkUpload(errorType = "GENERIC_ABORT", displayIndex, label, value) {
    let errorMessage = "";
    const rowNumber = (typeof displayIndex === "number" && displayIndex >= 0)
        ? displayIndex + 1
        : null;

    switch (errorType) {
        case "MISSING_LAPTOP_SERIAL_NO":
            errorMessage = "Laptop Serial No is mandatory but missing in this row.\n";
            break;

        case "MISSING_WIPING_COMPLETED":
            errorMessage = "Wiping Completed is mandatory but missing in this row.\n";
            break;

        case "INVALID_DROPDOWN":
            errorMessage = `The value "${value}" does not exist in the picklist for "${label}".\n`;
            break;

        case "DUPLICATE_LAPTOP_SERIAL_NO":
            errorMessage = `Duplicate Laptop Serial No "${value}" found in the uploaded file.`;
            break;

        case "NO_VALID_RECORD":
            errorMessage = "The uploaded file does not contain any valid records.\n";
            break;

        case "EXCEEDS_HDD_LIMIT":
            const csvCount  = value && value.count ? value.count : null;
            const hddLimit  = value && value.limit ? value.limit : null;
            errorMessage = `The CSV row count  (` + csvCount + `) exceeded HDD Count (` + hddLimit + `) \n`;  
            label = null;
            value = null;
            break;

        default:
            errorMessage = "Bulk upload aborted due to an unexpected error. Consult Developer !!";
    }

    popupBulkAborted = true;
    popupAllCsvRows = [];
    popupLaptopSerialNumbers.clear();
    $('#csvPreviewModal').modal('hide');
    stopLoading();
    hideBulkProgress();

    if (rowNumber !== null) {
        $('#errRowWrapper').show();   
        $('#errRow').text(rowNumber);
    } else {
        $('#errRowWrapper').hide();
    }

    if (label) {
        $('#errFieldWrapper').show(); 
        $('#errField').text(label);
    } else {
        $('#errFieldWrapper').hide();
    }

    let displayValue = "-";
    if (value && typeof value === "object") {
        displayValue = JSON.stringify(value);
    } else if (value) {
        displayValue = value;
    }

    if (value) {
        $('#errValueWrapper').show(); 
        $('#errValue').text(displayValue);
    } else {
        $('#errValueWrapper').hide();
    }

    $('#errReason').text(errorMessage + " Bulk upload has been aborted.");

    $('#importErrorModal').modal('show');
}



  /////PopUp Bulk Upload
  $(document).on('change', '#doc-upload-file', function () {
    const file = this.files[0];
    if (!file) return;

    const fd = new FormData();
    fd.append('mode', 'upload');
    fd.append('documentation', file);
    fd.append('_csrf', $('#csrfToken').val());

    startLoading();
    $.ajax({
      url: 'documentation',
      method: 'POST',
      data: fd,
      contentType: false,
      processData: false,
      success: function (resp) {
        stopLoading();
        if (!resp.success) {
          showErrorMessage(resp.message || 'Upload failed');
          return;
        }
        showSuccessMessage('Documentation uploaded successfully');
        $('#doc-modal').modal('hide');
      },
      error: function () {
        stopLoading();
        showErrorMessage('Upload failed');
      }
    });
  });


  let laptopSerialNumbers = new Set();
  var signaturePad
  let bulkAborted = false;
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);
  const slicestr = newURL.substring(0, str);
  function showErrorMessage(message) {
    $(".custom-alert").remove()
    var alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert alert alert-danger alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <strong>Error! </strong> ${message}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    $('#myDIV').append(alertDiv);
    setTimeout(() => {
      $(alertDiv).fadeOut(500, function () {
        $(this).remove();
      });
    }, 30000);
  }
  function showSuccessMessage(message) {
    $(".custom-alert").remove()
    var alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert alert alert-success alert-dismissible fade show';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    $('#myDIV').append(alertDiv);
    setTimeout(() => {
      $(alertDiv).fadeOut(500, function () {
        $(this).remove();
      });
    }, 30000);
  }
  function getexchangerate(data) {
    $.ajax({
      type: 'POST',
      url: slicestr + "leads/getexchangerate",
      // async:false,
      data: data,
      success: function (data) {
        //location.reload();
        $("#exchange_rate").val(data);

      },
      error: function (data) { // if error occured

        alert('Error occured.please try again');
      },
      dataType: 'html'
    });

  }
  function fetchOpportunityDetails() {
    data = { opportunity: $("#opportunity_name1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $.ajax({
      type: 'POST',
      url: "getopportunity",
      data: data,
      success: function (response) {
        stopLoading();
        if (response && response.data) {
          $("#account_name").val(response.data.account_name);
          $("#spoc_name").val(response.data.spoc_name);
          $("#spoc_mobile_number").val(response.data.spoc_mobile);
          $("#bill_address").val(response.data.bill_address);
          $("#bill_location").val(response.data.bill_location);
          $("#state").val(response.data.bill_state);
          $("#pincode").val(response.data.bill_pincode);
          $("#gstin_no").val(response.data.bill_gstin_no);
          $("#city").val("");
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function fetchActivitySpocDetails() {
    data = { spoc: $("#activtiy_spoc1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $("#activtiy_spoc_email").val("");
    $("#activtiy_spoc_mobile").val("");
    $.ajax({
      type: 'POST',
      url: "getspoc",
      data: data,
      success: function (response) {
        stopLoading();
        if (response && response.data) {
          $("#activtiy_spoc_email").val(response.data.spoc_email);
          $("#activtiy_spoc_mobile").val(response.data.spoc_mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function fetchBillSpocDetails() {
    data = { spoc: $("#bill_spoc1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $("#bill_spoc_number").val("");
    $("#bill_spoc_email").val("");
    $.ajax({
      type: 'POST',
      url: "getspoc",
      data: data,
      success: function (response) {
        stopLoading();
        if (response && response.data) {
          $("#bill_spoc_email").val(response.data.spoc_email);
          $("#bill_spoc_number").val(response.data.spoc_mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function fetchSpocDetails() {
    data = { spoc: $("#spoc_name1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $("#spoc_mobile_number").val("");
    $.ajax({
      type: 'POST',
      url: "getspoc",
      data: data,
      success: function (response) {
        stopLoading();
        if (response && response.data) {
          $("#spoc_mobile_number").val(response.data.spoc_mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function fetchPickupSpocDetails() {
    data = { spoc: $("#pickup_spoc1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $("#pickup_spoc_number").val("");
    $.ajax({
      type: 'POST',
      url: "getspocuser",
      data: data,
      success: function (response) {
        stopLoading();
        if (response && response.data) {
          $("#pickup_spoc_number").val(response.data.spoc_mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function fetchDeliverySpocDetails() {
    data = { spoc: $("#receiver_spoc_name1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $("#receiver_spoc_number").val("");
    $.ajax({
      type: 'POST',
      url: "getspocuser",
      data: data,
      success: function (response) {
        stopLoading();
        if (response && response.data) {
          $("#receiver_spoc_number").val(response.data.spoc_mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function fetchLocation(location_type) {
    let data = {}
    let url = "getlocationddress";
    if (location_type == "pickup") {
      data = { location: $("#pickup_location_client1").val(), _csrf: $('#csrfToken').val() };
      $("#pickup_address,#pickup_city,#pickup_state,#pickup_pin").val("");
    } else if (location_type == "activity") {
      var sourcing_deal = $("#opportunity_name1").val();
      data = { location: $("#activity_location1").val(), sourcing_deal: sourcing_deal, _csrf: $('#csrfToken').val() };
      $("#activity_address,#activity_city,#activity_state,#activity_pincode").val("");
    } else if (location_type == "delivery_internal") {
      url = "warehouse"
      data = { warehouse: $("#delivery_location_internal1").val(), _csrf: $('#csrfToken').val() };
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
    } else if (location_type == "pickup_internal") {
      url = "warehouse"
      data = { warehouse: $("#pickup_location1").val(), _csrf: $('#csrfToken').val() };
    } else if (location_type == "delivery_client") {
      data = { location: $("#delivery_location_client1").val(), _csrf: $('#csrfToken').val() };
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
    } else if (location_type == "billing") {
      data = { location: $("#bill_location1").val(), _csrf: $('#csrfToken').val() };
      $("#bill_address,#city,#state,#pincode").val("");
    }
    startLoading();
    $.ajax({
      type: 'POST',
      url: url,
      data: data,
      success: function (response) {
        stopLoading();
        console.log(response);
        if (response && response.data) {
          if (location_type == "pickup" || location_type == "pickup_internal") {
            $("#pickup_address").val(response.data.address);
            $("#pickup_city").val(response.data.city_name);
            $("#pickup_state").val(response.data.state);
            $("#pickup_pin").val(response.data.pincode);
          } else if (location_type == "activity") {
            $("#activity_address").val(response.data.address);
            $("#activity_city").val(response.data.city_name);
            $("#activity_state").val(response.data.state);
            $("#activity_pincode").val(response.data.pincode);

            //start here
            let hdd_count = response.hdd_count || "";
            let billable_type = response.billable_type || null;
            let bill_to_locations = response.bill_to_locations || "";
            let total_exclusive_gst = response.total_exclusive_gst || "";
            $("#hdd_count").val(hdd_count);
            $("#billable").val(billable_type).trigger("change")
            $("#billing_amount").val(total_exclusive_gst);
            $("#bill_location").attr("data-dynamic-dependent", bill_to_locations);
          } else if (location_type == "delivery_internal" || location_type == "delivery_client") {
            $("#delivery_address").val(response.data.address);
            $("#delivery_city").val(response.data.city_name);
            $("#delivery_state").val(response.data.state);
            $("#delivery_pin").val(response.data.pincode);
          } else if (location_type == "billing") {
            $("#bill_address").val(response.data.address);
            $("#city").val(response.data.city_name);
            $("#state").val(response.data.state);
            $("#pincode").val(response.data.pincode);
            $("#gstin_no").val(response.data.gstin_no_uin)
          }
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        stopLoading();
        alert('Error occured.please try again');
      },
      dataType: 'json'
    });
  }
  function manageAggrementCopy() {
    var aggrement = $("#agreement").val();
    if (aggrement == 2) {
      $(".section-agreement_copy").show();
    } else {
      $(".section-agreement_copy").hide();
      $("#agreement_copy").val("")
    }
  }
  function manageEmailDate() {
    var email_confirmation = $("#email_confirmation").val();
    if (email_confirmation == 2) {
      $(".section-email_date").show();
    } else {
      $(".section-email_date").hide();
      $("#email_date").val("")
    }
  }
  function manageProvisionToExtendTiming() {
    var extend_time_provision = $("#extend_time_provision").val();
    if (extend_time_provision == 1) {
      $("#extension_provision").prop("readonly", false).removeClass("readonly")
    } else {
      $("#extension_provision").val(null).prop("readonly", true).addClass("readonly").trigger("change");
    }
  }
  function manageServiceLift() {
    var service_lift = $("#service_lift").val();
    if (service_lift == 1) {
      $("#lift_timings").prop("readonly", false).removeClass("readonly")
      $("#stairs_area").val(null).prop("readonly", true).addClass("readonly").trigger("change");
    } else if (service_lift == 2) {
      $("#lift_timings").val(null).prop("readonly", true).addClass("readonly").trigger("change");
      $("#stairs_area").prop("readonly", false).removeClass("readonly")
    } else {
      $("#lift_timings").val(null).prop("readonly", true).addClass("readonly").trigger("change");
      $("#stairs_area").val(null).prop("readonly", true).addClass("readonly").trigger("change");
    }
  }
  function manageDeliveryLocation() {
    var delivery_location_type = $("#delivery_location_type").val();
    if (delivery_location_type == 1) {
      $(".section-delivery_location_internal").show()
      $(".section-delivery_location_client,.section-delivery_location_engineer").hide()
      $("#delivery_location_client1,#delivery_location_client,#delivery_location_engineer").val("")
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", true).addClass("readonly");
    } else if (delivery_location_type == 2) {
      $(".section-delivery_location_internal,.section-delivery_location_engineer").hide()
      $("#delivery_location_internal1,#delivery_location_internal,#delivery_location_engineer").val("")
      $(".section-delivery_location_client").show()
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", true).addClass("readonly");
    } else if (delivery_location_type == 3) {
      $(".section-delivery_location_internal,.section-delivery_location_client").hide()
      $(".section-delivery_location_engineer").show()
      $("#delivery_location_internal1,delivery_location_internal,#delivery_location_client1,#delivery_location_client").val("")
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", false).removeClass("readonly")
    } else {
      $(".section-delivery_location_internal,.section-delivery_location_client,.section-delivery_location_engineer").hide()
      $("#delivery_location_internal1,#delivery_location_internal,#delivery_location_engineer,#delivery_location_client1,#delivery_location_client").val("")
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").prop("readonly", true).addClass("readonly").val("");
    }
  }
  function managePickupLocation() {
    var pickup_location_type = $("#pickup_location_type").val();
    if (pickup_location_type == 1) {
      $(".section-pickup_location").show()
      $(".section-pickup_location_client,.section-pickup_location_engineer").hide()
      $("#pickup_location_client1,#pickup_location_client,#pickup_location_engineer").val("")
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", true).addClass("readonly");
    } else if (pickup_location_type == 2) {
      $(".section-pickup_location,.section-pickup_location_engineer").hide()
      $("#pickup_location1,#pickup_location,#pickup_location_engineer").val("")
      $(".section-pickup_location_client").show()
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", true).addClass("readonly");
    } else if (pickup_location_type == 3) {
      $(".section-pickup_location,.section-pickup_location_client").hide()
      $(".section-pickup_location_engineer").show()
      $("#pickup_location1,pickup_locationl,#pickup_location_client1,#pickup_location_client").val("")
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", false).removeClass("readonly")
    } else {
      $(".section-pickup_location,.section-pickup_location_client,.section-pickup_location_engineer").hide()
      $("#pickup_location1,#pickup_location,#pickup_location_engineer,#pickup_location_client1,#pickup_location_client").val("")
      $("#pickup_address,#pickup_state,#pickup_city,#pickup_pin").prop("readonly", true).addClass("readonly").val("");
    }
  }
  function manageFieldsDynamicConditions() {
    var service_to_locations = $("#activity_location1").val()
    var bill_to_locations = $("#bill_location1").val()
    $("#activity_location").attr("data-dynamic-dependent", service_to_locations)
    $("#bill_location").attr("data-dynamic-dependent", bill_to_locations);
  }
  function manageDongleMovement() {
    var hsap_key_require = $("#hsap_key_require").val();
    if (hsap_key_require == 1) {
      $(".blocktitle2610").parents(".titlerow").show()
      $(".blocktitle2611").parents(".titlerow").show()
    } else {
      $("#hsap_count").val("");
      $(".blocktitle2610").parents(".titlerow").hide()
      $(".blockrow2611,.blockrow2610").find("input, select").each(function () {
        if ($(this).is("select")) {
          $(this).val(null).trigger("change");
        } else {
          $(this).val("");
        }
      });
      $(".blocktitle2611").parents(".titlerow").hide()
    }

  }
  function getFeDetails() {
    data = { user: $("#fe_name1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $.ajax({
      type: 'POST',
      url: "getuserdetails",
      data: data,
      success: function (response) {
        stopLoading();
        console.log(response);
        if (response && response.data) {
          $("#fe_number").val(response.data.mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  function getLogistcExeDetails() {
    data = { user: $("#logistic_spoc_name1").val(), _csrf: $('#csrfToken').val() };
    startLoading();
    $.ajax({
      type: 'POST',
      url: "getuserdetails",
      data: data,
      success: function (response) {
        stopLoading();
        console.log(response);
        if (response && response.data) {
          $("#logistic_spoc_number").val(response.data.mobile);
        } else {
          console.log("Invalid response format or missing data");
        }
      },
      error: function (data) { // if error occured
        alert('Error occured.please try again');
        stopLoading();
      },
      dataType: 'json'
    });
  }
  async function getvendor() {
    data = { opportuity_name1: $("#opportunity_name1").val(), _csrf: $('#csrfToken').val() };
    try {
      startLoading();
      let response = await $.ajax({
        type: 'POST',
        url: "getvendor",
        data: data,
        dataType: 'json'
      });
      if (response && response.data) {
        let account = response.data.account || null;
        // let hdd_count = response.data.hdd_count || null;
        // let related = response.data.related || null;
        // let billable_type = response.data.billable_type || null;
        let service_to_locations = response.data.service_to_locations || "";
        if (account) {
          $("#account_name").val(account.acc_name);
          $("#account_name1").val(account.vendor_account_name);
          // if (hdd_count) {
          //   $("#hdd_count").val(hdd_count);
          // }
          $("#billing_type").val(account.billing_type).trigger("change")
          $("#activity_location").attr("data-dynamic-dependent", service_to_locations);

          // $("#productTable2613").find(".remove-row-btn").each(function () {
          //   $(this).trigger("click");
          // });
          resetZipSection();
          laptopSerialNumbers.clear();
          // if (related && Array.isArray(related)) {
          //     for (const item of related) {
          //         await addAssetsDynamicRows(item);
          //     }
          // }
        } else {
          console.log("Invalid response format or missing data");
        }
      }
      stopLoading();
    } catch (error) {
      stopLoading();
      alert('Error occurred. Please try again.');
    }
  }
  // get exchangerate
  $(document).on("change", "#currency", function () {
    data = { currency: $(this).val(), _csrf: $('#csrfToken').val() };
    getexchangerate(data);
  });
  //end exchange rate
  $(document).on("change", "#agreement", function () {
    manageAggrementCopy();
  })
  $(document).on("change", "#email_confirmation", function () {
    manageEmailDate();
  })
  $(document).on("change", "#extend_time_provision", function () {
    manageProvisionToExtendTiming();
  })
  $(document).on("change", "#delivery_location_type", function () {
    manageDeliveryLocation();
  })
  $(document).on("change", "#pickup_location_type", function () {
    managePickupLocation();
  })
  $(document).on("change", "#service_lift", function () {
    manageServiceLift();
  })
  $(document).on("change", "#hsap_key_require", function () {
    manageDongleMovement();
  })

  $(document).on("change", "select[id^='wiping_completed_']", function () {

    let index = $(this).attr("id").match(/wiping_completed_(\d+)/)[1];
    let certField = $(`#certificate_${index}`);
    let helpBlock = certField.closest(".form-group").find(".help-block");
    if ($(this).val() == "1") {
      //certField.prop("required", true);
      // certField.removeClass("F~O").addClass("F~M");
      //helpBlock.text("Uploading a certificate is required.");
    } else {
      //certField.prop("required", false);
      certField.removeClass("F~M").addClass("F~O");
      helpBlock.text("");
    }
  });
  $(document).on("click", ".data-wiping-completed", function () {
    let data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      wiping_completed: "Yes",
    };
    startLoading();
    $.ajax({
      type: "POST",
      url: "datawipingcompleted",
      data: data,
      success: function (data) {
        if (data.status === "success") {
          $(".data-wiping-completed,.add-lead-btn2").remove()
          showSuccessMessage(data.message || "Updated successfully")
          location.reload();
        } else {
          stopLoading();
          showErrorMessage(data.errors || "sometinhg went wrong");
        }
      },
      error: function (data) {
        stopLoading();
        showErrorMessage("Error occured.please try again");
      },
      dataType: "json",
    });

  });

  $(document).on("click", ".data-wiping-client-sign", function () {
    let data = {
      Recordid: $("#Recordid").val(),
      _csrf: $("#csrfToken").val(),
      wiping_asset_details: "Yes",
    };
    startLoading();
    $("#detailViewGeneralLabel").text("Data Wiping Assets")
    $.ajax({
      type: "POST",
      url: "datawipingassets",
      data: data,
      success: function (data) {
        stopLoading();
        if (data.status === "success") {
          let asset_data = data.data || [];
          let dynamic_html = `<table class="table table-bordered text-center">
                                        <thead class="align-middle">
                                            <tr class="table-info">
                                                <th>#</th>
                                                <th>Laptop Serial No</th>
                                                <th>HDD Serial No</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
          if (asset_data) {
            let tabular_data = asset_data.map((ele, index) => `
                        <tr>
                        <td>${++index}</td>
                        <td>${ele.laptop_serial_no}</td>
                        <td>${ele.hdd_sdd_serial_no}</td>
                        </tr>`).join("");
            dynamic_html = dynamic_html + tabular_data;
          }
          dynamic_html = dynamic_html + `<tr><td></td><td>Client Signature</td>
                  <td><div><canvas id="signature-pad"></canvas></div>
                      <div class="text-center"><button class="btn btn-danger clear_image">Clear</button></div>
                    </td></tr>`;
          dynamic_html = dynamic_html + `</tbody></table>`
          $(".modal-dynamic-content").html(dynamic_html);
          $(".clear_image").trigger("click");
        } else {

          $("#detail-view-general-info").modal("hide")
          showErrorMessage(data.errors || "sometinhg went wrong");
        }
      },
      error: function (data) {
        stopLoading();
        $("#detail-view-general-info").modal("hide")
        showErrorMessage("Error occured.please try again");
      },
      dataType: "json",
    });

  });
  $(document).on("click", "#detail-view-general-submit", function (e) {
    e.preventDefault()
    e.preventDefault()

    if (!signaturePad || signaturePad.isEmpty()) {
      $(".detail-view-general-error").text("Please provide a signature first.");
      return;
    }

    var signatureData = signaturePad.toDataURL("image/png");
    console.log(signatureData)
    if (signatureData) {
      let data = {
        Recordid: $("#Recordid").val(),
        _csrf: $("#csrfToken").val(),
        put_client_sign: "Yes",
        image: signatureData
      };
      startLoading();
      $.ajax({
        url: 'datawipingclientsign',
        type: 'POST',
        data: data,
        success: function (data) {
          if (data.status === "success") {
            $(".data-wiping-client-sign,.add-lead-btn2").remove()
            $("#detail-view-general-info").modal("hide")
            showSuccessMessage(data.message || "Updated successfully")
            location.reload();
          } else {
            stopLoading();
            $(".detail-view-general-error").text(data.errors || "sometinhg went wrong");
          }
        },
        error: function (data) {
          stopLoading();
          $(".detail-view-general-error").text(data.errors || "sometinhg went wrong");
        },
        dataType: "json",
      });
    } else {
      $(".detail-view-general-error").text("Please put your signature");
    }
    /*$.ajax({
        type: "POST",
        url: "datawipingcompleted",
        data: data,
        success: function (data) {
            if (data.status === "success") {
              $(".data-wiping-completed,.add-lead-btn2").remove()
              showSuccessMessage(data.message || "Updated successfully")
              location.reload();
            } else {
              stopLoading();
                showErrorMessage(data.errors || "sometinhg went wrong");
            }
        },
        error: function (data) {
            stopLoading();
            showErrorMessage("Error occured.please try again");
        },
        dataType: "json",
    });*/

  });
  $(document).on("click", ".clear_image", function () {
    clearPad()
  })
  function clearPad() {
    var canvas = document.getElementById('signature-pad');
    signaturePad = new SignaturePad(canvas);
    signaturePad.clear();
  }

  manageAggrementCopy()
  manageEmailDate()
  manageProvisionToExtendTiming()
  // manageServiceLift();
  manageDeliveryLocation()
  managePickupLocation()
  manageFieldsDynamicConditions()
  manageDongleMovement()
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // alert(modeInput);
    // initialize currency with INr
    $('#currency').val("1").trigger("change");
    data = { currency: 1, _csrf: $('#csrfToken').val() };

    //end ddepika
    getexchangerate(data);
  }

  // Create a MutationObserver to detect changes to the opportuniy
  var targetNodeOpportunity = document.getElementById('opportunity_name1');
  var observerOpportunity = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        $("#account_name").val("");
        $("#account_name1").val("");
        $("#spoc_name1").val("");
        $("#spoc_name").val("");
        $("#spoc_mobile_number").val("")
        $("#hdd_count").val("")
        $("#billing_amount").val("")
        $("#billable").val(null).trigger("change")
        $("#billing_type").val(null).trigger("change")
        // $("#productTable84").find(".remove-row-btn").each(function () {
        //     $(this).trigger("click");
        // })
        console.log("changed to ", targetNodeOpportunity.value)
        if (targetNodeOpportunity.value !== '') {
          getvendor();
        }
      }
    }
  });
  if (targetNodeOpportunity) {
    observerOpportunity.observe(targetNodeOpportunity, { attributes: true });
  }

  // Create a MutationObserver to detect changes to the activity spoc
  var targetNodeActivitySpoc = document.getElementById('activtiy_spoc1');
  var observerActivitySpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#activtiy_spoc_email").val("");
      $("#activtiy_spoc_mobile").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        fetchActivitySpocDetails();
      }
    }
  });
  // Configuration for the observer for activtiy spoc (observe attribute changes)
  var configActivitySpoc = { attributes: true };
  observerActivitySpoc.observe(targetNodeActivitySpoc, configActivitySpoc);

  //spoc observer
  var targetNodeSpoc = document.getElementById('spoc_name1');
  var observerSpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#spoc_mobile_number").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodeSpoc.value !== '') {
          fetchSpocDetails();
        }
      }
    }
  });
  observerSpoc.observe(targetNodeSpoc, { attributes: true });
  //Billing spoc observer
  var targetNodeBillingSpoc = document.getElementById('bill_spoc1');
  var observerBillingSpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#bill_spoc_number").val("");
      $("#bill_spoc_email").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodeBillingSpoc.value !== '') {
          fetchBillSpocDetails();
        }
      }
    }
  });
  observerBillingSpoc.observe(targetNodeBillingSpoc, { attributes: true });

  //pickup spoc observer
  var targetNodePickupSpoc = document.getElementById('pickup_spoc1');
  var observerPickupSpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#pickup_spoc_number").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodePickupSpoc.value !== '') {
          fetchPickupSpocDetails();
        }
      }
    }
  });
  observerPickupSpoc.observe(targetNodePickupSpoc, { attributes: true });

  //delivery spoc observer
  var targetNodeDeliverySpoc = document.getElementById('receiver_spoc_name1');
  var observerDeliverySpoc = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#receiver_spoc_number").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodeDeliverySpoc.value !== '') {
          fetchDeliverySpocDetails();
        }
      }
    }
  });
  observerDeliverySpoc.observe(targetNodeDeliverySpoc, { attributes: true });
  //Pickup location
  var targetNodePickupLocation = document.getElementById('pickup_location_client1');
  var observerPickupLocation = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#pickup_address,#pickup_city,#pickup_state,#pickup_pin").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        if (targetNodePickupLocation.value !== '') {
          fetchLocation("pickup");
        }
      }
    }
  });
  observerPickupLocation.observe(targetNodePickupLocation, { attributes: true });

  //Pickup Internal location
  var targetNodePickupLocationInternal = document.getElementById('pickup_location1');
  var observerDeliveryLocationInternal = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#pickup_address,#pickup_city,#pickup_state,#pickup_pin").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        fetchLocation("pickup_internal");
      }
    }
  });
  observerDeliveryLocationInternal.observe(targetNodePickupLocationInternal, { attributes: true });
  //Billing location
  var targetNodeBillingLocation = document.getElementById('bill_location1');
  var observerBillingLocation = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#bill_address,#city,#state,#pincode,#gstin_no").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        fetchLocation("billing");
      }
    }
  });
  observerBillingLocation.observe(targetNodeBillingLocation, { attributes: true });
  //Activity location
  var targetNodeActivityLocation = document.getElementById('activity_location1');
  var observerActitvityLocation = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#activity_address,#activity_city,#activity_state,#activity_pincode").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        fetchLocation("activity");
      }
    }
  });
  observerActitvityLocation.observe(targetNodeActivityLocation, { attributes: true });

  //Delivery Internal location
  var targetNodeDeliveryLocationInternal = document.getElementById('delivery_location_internal1');
  var observerDeliveryLocationInternal = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        fetchLocation("delivery_internal");
      }
    }
  });
  observerDeliveryLocationInternal.observe(targetNodeDeliveryLocationInternal, { attributes: true });

  //Delivery client location
  var targetNodeDeliveryLocationClient = document.getElementById('delivery_location_client1');
  var observerDeliveryLocationClient = new MutationObserver(function (mutationsList) {
    for (var mutation of mutationsList) {
      $("#delivery_address,#delivery_state,#delivery_city,#delivery_pin").val("");
      if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
        fetchLocation("delivery_client");
      }
    }
  });
  observerDeliveryLocationClient.observe(targetNodeDeliveryLocationClient, { attributes: true });

  // start for FE
  var targetNodeFE = document.getElementById('fe_name1');
  if (targetNodeFE) {
    var observerFE = new MutationObserver(function (mutationsList) {
      for (var mutation of mutationsList) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
          $("#fe_number").val('');
          if (targetNodeFE.value !== '') {
            getFeDetails();
          }
        }
      }
    });
    observerFE.observe(targetNodeFE, { attributes: true });
  }
  //end for FE

  // start for Logistics Exe
  var targetNodeLogisticExe = document.getElementById('logistic_spoc_name1');
  if (targetNodeLogisticExe) {
    var observerLogisticExe = new MutationObserver(function (mutationsList) {
      for (var mutation of mutationsList) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
          $("#logistic_spoc_number").val('');
          if (targetNodeLogisticExe.value !== '') {
            getLogistcExeDetails();
          }
        }
      }
    });
    observerLogisticExe.observe(targetNodeLogisticExe, { attributes: true });
  }
  //end for Logistics Exe

  //code added by ptpatel on date 09-05-25
  /////////////create mutation for sourcing deal/////////////////
  // Create a MutationObserver to detect changes to the input vendor account
  function getQueryParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }

  var targetNode = document.getElementById("opportunity_name1");
  if (targetNode.value != '' && getQueryParam('sourcemodule') == 51) //51 is sourcing deal
    getsourcingdetail(targetNode.value)

  /////////get sourcing deal detail///////
  function getsourcingdetail(sourcingdeal) {
    // alert("getsourvingcall");
    if (sourcingdeal) {
      data = {
        sourcingdeal: sourcingdeal,
        _csrf: $("#csrfToken").val(),
      };

      $.ajax({
        type: "POST",
        url: "getsourcingdetail",
        // async:false,
        data: data,
        success: function (response) {

          // Check if the data object exists and contains 'first_name'
          if (response && response.data) {
            $("#account_name").val(response.data.acc_name);
            $("#account_name1").val(response.data.vendoraccid);

          } else {
            console.log("Invalid response format or missing data");
          }
        },
        error: function (data) {
          // if error occured

          alert("Error occured.please try again");
        },
        dataType: "json",
      });
    }

  }
  //end code added by ptpatel on date 09-05-25




  async function getEmptyRowTemplate(blockid, mainmodule) {
    if (cachedEmptyRowHtml) {
      return cachedEmptyRowHtml;
    }

    await addRowBtn(blockid, mainmodule,1);

    const $tbody = $('#productTable' + blockid + ' tbody');
    const $lastRow = $tbody.find('tr.product-row').last();

    cachedEmptyRowHtml = $lastRow.prop('outerHTML');
    $lastRow.remove();

    return cachedEmptyRowHtml;
  }


  const PAGE_SIZE = 30;

  async function buildAllRows() {
    const $tbody = $('#productTable2613 tbody');
    $tbody.empty();

    if (bulkAborted) return;

    const total = allCsvRows.length;
    for (let i = 0; i < total; i++) {
      if (bulkAborted) return;

      const rowData = allCsvRows[i];
      const rowIndex = i + 1;

      const $row = await createProductRowFromTemplate('2613', 'datawiping', rowIndex);

      const pageNum = Math.floor(i / PAGE_SIZE) + 1;
      $row.addClass('page_content_' + pageNum);

      setRowFields($row, rowData);
      if (bulkAborted) return;

      $tbody.append($row);
    }

    const totalPages = Math.ceil(total / PAGE_SIZE);
    $('#totalPages').text(totalPages);
    $('#totalRecords').text(total);

    currentPage = 1;
    showPage(currentPage);
    updatePaginationVisibility();
  }


  $(document).on('change', '#bulk-upload-file', function () {
    bulkAborted = false;
    ensureZipSection();
    $('#loading-overlay').removeAttr('style');
    startLoading();
    showBulkProgress('Reading file...');

    const file = this.files[0];
    if (!file) {
      stopLoading();
      return;
    }

    $('#bulk-upload-file').val('');

    Papa.parse(file, {
      header: true,
      skipEmptyLines: true,
      dynamicTyping: false,
      // worker:true,
      complete: async function (results) {
        try {
          if (bulkAborted) return;
          showBulkProgress('Parsing CSV...');
          const records = results.data || [];

          if (!Array.isArray(records) || records.length === 0) {
            rollbackBulkUpload('NO_VALID_RECORD');
            return;
          }

          let data = records.slice(0, 200);
          if (records.length > 200) {
            rollbackBulkUpload('MORE_THAN_200');
            return;
          }

          const tempSerials = new Set();
          for (let i = 0; i < data.length; i++) {
            if (bulkAborted) return;
            const row = data[i];
            const serial = (row['Laptop Serial No*'] || '').trim();
            const wipingCompleted = (row['Wiping Completed*'] || '').trim();
            const displayIndex = allCsvRows.length + i + 1;

            if (!serial) {
              rollbackBulkUpload('MISSING_LAPTOP_SERIAL_NO', displayIndex);
              return;
            }
            if (!wipingCompleted) {
              rollbackBulkUpload('MISSING_WIPING_COMPLETED', displayIndex);
              return;
            }
            if (laptopSerialNumbers.has(serial) || tempSerials.has(serial)) {
              rollbackBulkUpload('DUPLICATE_LAPTOP_SERIAL_NO', displayIndex, null, serial);
              return;
            }
            tempSerials.add(serial);
          }
          if (bulkAborted) return;
          data.forEach(row => {
            const serial = (row['Laptop Serial No*'] || '').trim();
            if (serial) laptopSerialNumbers.add(serial);
            allCsvRows.push(row);
          });

          await buildAllRows();
          showSuccessMessage('CSV imported successfully.');
        } catch (err) {
          console.error(err);
          showErrorMessage('Error while processing CSV');
          return;
        } finally {
          if (!bulkAborted) {
            $("#upload-zip-section").show();
            stopLoading();
            hideBulkProgress();
          }
        }
      },
      error: function (err) {
        console.error('Papa.parse error', err);
        showErrorMessage('Unable to read CSV file');
        stopLoading();
        hideBulkProgress();
        return;
      }
    });
  });
  var datawipingId = $('#record').val();

  function updateEditModeUI() {
    if (typeof datawipingId !== 'undefined' && datawipingId > 0 && datawipingId != '') {
      $('#paginationBar').hide();

      if (!$('#viewAllAssetsBtn').length) {
        $('#paginationBar').before(
          '<a href="detail?id=' + datawipingId + '"class="btn btn-primary ml-2">Detail View</a>'
        );
      }
    } else {
      updatePaginationVisibility();
    }
  }

  $(function () {
    updateEditModeUI();
  });


  let currentPage = 1;

  function showPage(page) {
    const totalPages = Math.ceil(allCsvRows.length / PAGE_SIZE) || 1;
    if (page < 1 || page > totalPages) return;

    $('#productTable2613 tbody tr.product-row').hide();
    $('#productTable2613 tbody tr.product-row.page_content_' + page).show();
    currentPage = page;
    $('#currentPage').text(page);
    $('#totalPages').text(totalPages);
    $('#totalRecords').text(allCsvRows.length);

    // updatePaginationUI();
    updatePaginationVisibility();
  }


  $('#firstPage').on('click', function (e) {
    e.preventDefault();
    showPage(1);
  });

  $('#prevPage').on('click', function (e) {
    e.preventDefault();
    showPage(currentPage - 1);
  });

  $('#nextPage').on('click', function (e) {
    e.preventDefault();
    const totalPages = Math.ceil(allCsvRows.length / PAGE_SIZE) || 1;
    showPage(currentPage + 1);
  });

  $('#lastPage').on('click', function (e) {
    e.preventDefault();
    const totalPages = Math.ceil(allCsvRows.length / PAGE_SIZE) || 1;
    showPage(totalPages);
  });
  function updatePaginationVisibility() {
    const hasRows = $('#productTable2613 tbody tr.product-row').length > 0;
    if (hasRows) {
      $('#paginationBar').show();
    } else {
      $('#paginationBar').hide();
    }
  }

  async function createProductRowFromTemplate(blockid, mainmodule, rowIndex) {
    const templateHtml = await getEmptyRowTemplate(blockid, mainmodule);

    const htmlWithIndex = templateHtml
      .replace(/(\[)\d+(\]\[)/g, '$1' + rowIndex + '$2')
      .replace(/_(\d+)"/g, '_' + rowIndex + '"')
      .replace(/data-row-index="(\d+)"/g, 'data-row-index="' + rowIndex + '"');

    const $row = $(htmlWithIndex);

    $row.find('.select2-hidden-accessible').select2({
      width: '100%',
      placeholder: 'Select'
    });

    const $dateField = $row.find('.wiping_date');
    if ($dateField.length) {
      flatpickr($dateField[0], {
        dateFormat: 'd-m-Y',
        allowInput: true
      });
    }

    return $row;
  }

  function setRowFields($row, rowData) {
    if (bulkAborted) return;
    const rowIndex = $row.attr("data-row-index") || '';
    const displayIndex = rowIndex !== '' ? parseInt(rowIndex, 10) : '';
    const laptopSerialNo = rowData["Laptop Serial No*"]?.trim() || '';
    const wipingCompleted = rowData["Wiping Completed*"]?.trim() || '';

    if (!laptopSerialNo) {
      rollbackBulkUpload("MISSING_LAPTOP_SERIAL_NO", displayIndex);
      return;
    }
    if (!wipingCompleted) {
      rollbackBulkUpload("MISSING_WIPING_COMPLETED", displayIndex);
      return;
    }

    $row.find('input[name*="[laptop_serial_no]"]').val(rowData["Laptop Serial No*"] || '');
    $row.find('input[name*="[hdd_sdd_serial_no]"]').val(rowData["HDD/SDD Serial No"] || '');


    if (!setDropdownValue($row, 'make', rowData["Make"], "Make", displayIndex)) {
      rollbackBulkUpload("INVALID_DROPDOWN", displayIndex, "Make", rowData["Make"]);
      return false;
    }
    if (!setDropdownValue($row, 'type', rowData["Type"], "Type", displayIndex)) {
      rollbackBulkUpload("INVALID_DROPDOWN", displayIndex, "Type", rowData["Type"]);
      return false;
    }
    if (!setDropdownValue($row, 'capacity', rowData["Capacity"], "Capacity", displayIndex)) {
      rollbackBulkUpload("INVALID_DROPDOWN", displayIndex, "Capacity", rowData["Capacity"]);
      return false;
    }
    if (!setDropdownValue($row, 'software_name', rowData["Software Name"], "Software Name", displayIndex)) {
      rollbackBulkUpload("INVALID_DROPDOWN", displayIndex, "Software Name", rowData["Software Name"]);
      return false;
    }
    if (!setDropdownValue($row, 'wiping_completed', rowData["Wiping Completed*"], "Wiping Completed", displayIndex)) {
      rollbackBulkUpload("INVALID_DROPDOWN", displayIndex, "Wiping Completed", rowData["Wiping Completed*"]);
      return false;
    }

    if (bulkAborted) return;
    let dateField = $row.find('input[name*="[wiping_date]"]');

    if (dateField.length) {

      let csvDate = rowData["Wiping Date"]?.trim() || "";

      let parsedDate = null;

      if (csvDate) {
        let parts = csvDate.includes('-') ? csvDate.split('-') :
          csvDate.includes('/') ? csvDate.split('/') : [];

        if (parts.length === 3) {
          if (parts[0].length === 4) {
            parsedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
          } else {
            parsedDate = `${parts[0]}-${parts[1]}-${parts[2]}`;
          }
        }
      }

      if (dateField[0]._flatpickr) {
        dateField[0]._flatpickr.destroy();
      }

      dateField.val(parsedDate || "");

      flatpickr(dateField[0], {
        dateFormat: "d-m-Y",
        defaultDate: parsedDate || null,
        allowInput: true,
      });
    }

    const certName = rowData["Certificate"] || rowData._certExpected || '';
    const certInput = $row.find('input[name*="[certificate]"]');

    certInput.attr(
      'placeholder',
      certName ? "Please upload " + certName : "Please upload certificate"
    );

    if ($row.find('.certificate-path-info').length === 0) {
      certInput.after(
        '<span class="certificate-path-info" ' +
        'style="font-size:smaller;color:#888;margin-left:4px;"></span>'
      );
    }
    const $info = $row.find('.certificate-path-info');

    if ($row.find('.certificate_path_hidden').length === 0) {
      $row.append(
        `<input type="hidden" class="certificate_path_hidden" name="certificate_path_hidden[]" value="${certName}">`
      );
    } else {
      $row.find('.certificate_path_hidden').val(certName);
    }

    if ($row.find('.certificate_attachment_id').length === 0) {
      $row.append(
        `<input type="hidden"
              name="certificate_attachment_id[${rowIndex}]"
              class="certificate_attachment_id"
              value="">`
      );
    }
    const $attach = $row.find('.certificate_attachment_id');

    if (rowData._certStatus === "mapped" && rowData._certAttachId) {
      $info.css("color", "green").text("✔ " + certName + " mapped");
      $attach.val(rowData._certAttachId);
      disableCertificateValidation($row);
    } else if (rowData._certStatus === "missing" && certName) {
      $info.css("color", "red").text("✖ Missing: " + certName);
      $attach.val("");
      enableCertificateValidation($row);
    } else {
      $info.css("color", "#888").text(
        certName ? "Expected: " + certName : ""
      );
      $attach.val("");
      enableCertificateValidation($row);
    }

  }

  function setDropdownValue($row, fieldName, textValue, label, displayIndex) {
    // if (bulkAborted) return false;

    let $select = $row.find(`select[name*="[${fieldName}]"]`);
    if (!$select.length) return true;

    let cleaned = (textValue || "").trim().toLowerCase();

    let matchedOption = $select.find("option").filter(function () {
      return $(this).text().trim().toLowerCase() === cleaned;
    });

    if (matchedOption.length === 0) {
      rollbackBulkUpload("INVALID_DROPDOWN", displayIndex, label, textValue);
      return false;
    }

    // if (bulkAborted) return false;

    let matchedValue = matchedOption.val();

    if ($select.hasClass('select2-hidden-accessible')) {
      $select.select2('destroy');
    }

    $select.val(matchedValue).trigger('change');
    $select.select2();

    return true;
  }


  function rollbackBulkUpload(errorType = "GENERIC_ABORT", displayIndex, label, value) {
    let errorMessage = "";
    switch (errorType) {
      case "MISSING_LAPTOP_SERIAL_NO":
        errorMessage = `Row ${displayIndex}: Laptop Serial No* is mandatory but missing. Bulk Upload Aborted!!!`;
        break;
      case "MISSING_WIPING_COMPLETED":
        errorMessage = `Row ${displayIndex}: Wiping Completed* is mandatory but missing. Bulk Upload Aborted!!!`;
        break;
      case "INVALID_DROPDOWN":
        errorMessage = `Row ${displayIndex}: Invalid value "${value}" for field "${label}". Bulk Upload Aborted!!!`;
        break;
      case "DUPLICATE_LAPTOP_SERIAL_NO":
        errorMessage = `Row ${displayIndex}: Duplicate entry denied for Laptop Serial No "${value}". Bulk Upload Aborted!!!`;
        break;
      case "MORE_THAN_200":
        errorMessage = `Record Can't Be more than 200/req. Bulk Upload Aborted!!!`;
        break;
      case "NO_VALID_RECORD":
        errorMessage = ` Invalid Record. Bulk Upload Aborted!!!`;
        break;
      default:
        errorMessage = "Row Operation Aborted";
        if (displayIndex) errorMessage = `Row ${displayIndex}: ${errorMessage} Bulk Upload Aborted!!!`;
    }
    bulkAborted = true;
    $('#productTable2613 tbody').empty();
    $("#upload-zip-section").hide();
    stopLoading();
    resetZipSection();
    laptopSerialNumbers.clear();
    alert(errorMessage);
    updatePaginationVisibility();
    window.location.reload();
    return;
  }

  function resetZipSection() {
    $("#upload-zip-section").remove();
  }

  function ensureZipSection() {
    if ($("#upload-zip-section").length === 0) {
      $("#collapse2613").before(`
        <div id="upload-zip-section" 
            style="margin-bottom:12px; margin-top:12px; margin-left:22px; display:none;">
          <button class="btn btn-warning" id="choose-zip-btn">Choose ZIP</button>
          <input type="file" id="zip-file-input" accept=".zip" style="display:none;">
          <button class="btn btn-success" id="upload-zip-btn" style="display:none;">Upload ZIP</button>
          <span id="zip-status" style="margin-left:10px;font-weight:bold;"></span>
        </div>
      `);
      bindZipEvents();
    }
    $("#upload-zip-section").show();
  }


  function bindZipEvents() {
    $("#choose-zip-btn").on("click", function (e) {
      e.preventDefault();
      $("#zip-file-input").val("");
      $("#upload-zip-btn").text("Upload ZIP").prop("disabled", false).show();
      $("#zip-status").text("").css("color", "");
      $("#choose-zip-btn").text("Choose ZIP");

      $("#productTable2613 tbody tr.product-row").each(function () {
        let $row = $(this);
        $row.find(".certificate-path-info")
          .css("color", "#888")
          .text("");
        $row.find(".certificate_attachment_id").val("");
        $row.find("input[type='file'].certificate")
          .attr("data-pristine-required", "true")
          .attr("required", "true")
          .removeClass("error");
        enableCertificateValidation($row);
      });

      $("#zip-file-input").attr("accept", ".zip");
      $("#zip-file-input").click();
    });

    $("#zip-file-input").on("change", function () {
      let file = this.files[0];
      if (!file) return;
      if (!file.name.toLowerCase().endsWith(".zip")) {
        alert("Only .zip files are allowed.");
        $(this).val("");
        return;
      }
      startLoading();
      $("#upload-zip-btn").show();
      $("#zip-status").text("");
      setTimeout(() => {
        stopLoading();
      }, 300);
    });

    $("#upload-zip-btn").on("click", function () {
      let zip = $("#zip-file-input")[0].files[0];
      if (!zip) return alert("Please choose a ZIP file.");

      let fd = new FormData();
      fd.append("certificate_zip", zip);
      fd.append("_csrf", $("#csrfToken").val());
      startLoading();
      $("#upload-zip-btn").text("Uploading...").prop("disabled", true);

      $.ajax({
        url: "uploadzipfiles",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        success: function (resp) {
          stopLoading();
          $("#upload-zip-btn")
            .text("ZIP Uploaded ✓")
            .prop("disabled", true);

          $("#zip-status").css("color", "green").text("✔ ZIP uploaded");
          $("#choose-zip-btn").text("Change ZIP");

          if (resp.files) applyZipMapping(resp.files);
        },
        error: function () {
          stopLoading();
          $("#upload-zip-btn").text("Upload ZIP").prop("disabled", false);
          $("#zip-status").css("color", "red").text("Failed! Try different file.");
          $("#choose-zip-btn").text("Choose ZIP");
        }
      });
    });
  }
  function enableCertificateValidation($row) {
    let certInput = $row.find("input[type='file'].certificate");
    certInput.attr("data-pristine-required", "true");
    certInput.attr("required", "true");
    let $formGroup = certInput.closest('.form-group');
    let $helpBlock = $formGroup.find(".help-block");
    if ($helpBlock.length === 0) {
      $formGroup.append('<span class="help-block">Certificate required</span>');
    } else {
      $helpBlock.text("Certificate required");
    }
  }


  function applyZipMapping(fileMap) {
    allCsvRows = allCsvRows.map((row) => {
      const expected = (row["Certificate"] || "").trim();
      if (!expected) {
        row._certExpected = "";
        row._certAttachId = null;
        row._certStatus = "";
        return row;
      }

      row._certExpected = expected;
      const attachId = fileMap[expected];

      if (attachId) {
        row._certAttachId = attachId;
        row._certStatus = "mapped";
      } else {
        row._certAttachId = null;
        row._certStatus = "missing";
      }

      return row;
    });

    function updatePaginationUI() {
      const totalRecords = allCsvRows.length;
      const totalPages = Math.ceil(totalRecords / PAGE_SIZE) || 1;
      const page = currentPage > totalPages ? totalPages : currentPage;

      const startRec = totalRecords === 0 ? 0 : (page - 1) * PAGE_SIZE + 1;
      const endRec = Math.min(page * PAGE_SIZE, totalRecords);
      $('#recordInfo').text(`Showing ${startRec}–${endRec} of ${totalRecords}`);

      const $pageNumbers = $('#pageNumbers');
      $pageNumbers.empty();

      const windowSize = 5;
      let startPage = Math.max(1, page - 2);
      let endPage = Math.min(totalPages, startPage + windowSize - 1);
      if (endPage - startPage + 1 < windowSize) {
        startPage = Math.max(1, endPage - windowSize + 1);
      }

      for (let p = startPage; p <= endPage; p++) {
        const $btn = $(`<button class="page-btn">${p}</button>`);
        if (p === page) {
          $btn.prop('disabled', true).addClass('active');
        }
        $btn.on('click', function () {
          showPage(p);
        });
        $pageNumbers.append($btn);
      }

      $('#firstPage').prop('disabled', page <= 1);
      $('#prevPage').prop('disabled', page <= 1);
      $('#nextPage').prop('disabled', page >= totalPages);
      $('#lastPage').prop('disabled', page >= totalPages);
    }

    $('#productTable2613 tbody tr.product-row').each(function () {
      const $row = $(this);
      const rowIndex = $row.attr('data-row-index');

      let expected = ($row.find('.certificate_path_hidden').val() || '').trim();
      if (!expected) {
        const idx = rowIndex ? parseInt(rowIndex, 10) - 1 : -1;
        if (idx >= 0 && allCsvRows[idx]) {
          expected = (allCsvRows[idx]["Certificate"] || "").trim();
        }
      }
      if (!expected) return;

      const attachId = fileMap[expected];

      let $info = $row.find('.certificate-path-info');
      if (!$info.length) {
        const certInput = $row.find('input[name*="[certificate]"]');
        certInput.after(
          '<span class="certificate-path-info" ' +
          'style="font-size:smaller;color:#888;margin-left:4px;"></span>'
        );
        $info = $row.find('.certificate-path-info');
      }

      let $hidden = $row.find('.certificate_path_hidden');
      if (!$hidden.length) {
        $row.append(
          `<input type="hidden" class="certificate_path_hidden" name="certificate_path_hidden[]" value="${expected}">`
        );
        $hidden = $row.find('.certificate_path_hidden');
      } else {
        $hidden.val(expected);
      }

      let $attach = $row.find('.certificate_attachment_id');
      if (!$attach.length) {
        $row.append(
          `<input type="hidden"
                name="certificate_attachment_id[${rowIndex}]"
                class="certificate_attachment_id"
                value="">`
        );
        $attach = $row.find('.certificate_attachment_id');
      }

      if (attachId) {
        $info.css('color', 'green').text('✔ ' + expected + ' mapped');
        $attach.val(attachId);
        disableCertificateValidation($row);
      } else {
        $info.css('color', 'red').text('✖ Missing: ' + expected);
        $attach.val('');
        enableCertificateValidation($row);
      }
    });

    // Keep current page visible as-is
    showPage(currentPage);
  }



  function disableCertificateValidation($row) {

    let certInput = $row.find("input[type='file'].certificate");

    certInput.removeAttr("data-pristine-required");
    certInput.removeAttr("required");
    certInput.removeClass("error");

    $row.find(".help-block").remove();
  }
});
$(document).on('click', '#detail-btn-asset', function () {
  const id = $('#recordid').val();
  if (!id) {
    alert('No datawiping id');
    return;
  }
  window.location.href = 'assetlist?datawiping_id=' + encodeURIComponent(id);
});