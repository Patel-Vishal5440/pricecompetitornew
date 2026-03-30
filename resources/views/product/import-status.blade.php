@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/dataTables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable-common.css') }}">
@endsection

@section('content')
<div class="contents">
    <div class="">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="color-dark fw-500 d-flex justify-content-between align-items-center flex-wrap gap-2 mt-15 mx-4 import-status-filters">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="input-container icon-left icon-right position-relative import-search-wrap">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                    <span class="input-icon icon-right" onclick="clearImportStatusSearch()" style="cursor: pointer;">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="importStatusSearch" class="form-control form-control-default filter-control"
                                           placeholder="Search Import Status"
                                           maxlength="255" autocomplete="off">
                                </div>
                                <select id="importTypeFilter" class="form-select filter-control import-type-filter">
                                    <option value="">All Types</option>
                                    <option value="products">Product Import</option>
                                    <option value="price">Product Price Update</option>
                                </select>
                            </div>
                        </div>
                        <div class="table4 p-25 bg-white mb-30">
                            <div class="table-responsive">
                                <table id="datatable" class="table mb-0 datatable import-status-table">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center align-middle"><span class="userDatatable-title">Job</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Type</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Status</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Processed</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Success</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Failed</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Progress</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Message</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Updated</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Actions</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulkImportQueueTableBody">
                                        <tr>
                                            <td colspan="10" class="text-muted text-center">No import jobs found.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor_assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function updateTableCountInfo(tableApi) {
        var info = tableApi.page.info();
        var onCurrentPage = Math.max(0, info.end - info.start);
        var filteredCount = info.recordsDisplay || 0;
        var totalCount = info.recordsTotal || 0;

        var countText = 'On this page: <strong>' + onCurrentPage + '</strong> | Total: <strong>' + totalCount + '</strong>';
        if (filteredCount !== totalCount) {
            countText = 'On this page: <strong>' + onCurrentPage + '</strong> | Filtered: <strong>' + filteredCount + '</strong> | Total: <strong>' + totalCount + '</strong>';
        }

        $('.import-status-table-count-info').html(countText);
    }

    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        autoWidth: false,
        dom: 'rt<"bottom d-flex justify-content-between align-items-center flex-wrap gap-2"l<"import-status-table-count-info text-center flex-grow-1 small fw-semibold text-primary">p><"clear">',
        language: {
            emptyTable: `<div class="py-4 text-center text-muted">
                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                <span style="font-size: 1.1em;">No import jobs found.</span>
            </div>`,
        },
        ajax: {
            url: "{{ route('products.importStatusJobs') }}",
            data: function(data) {
                data.searchData = $('#importStatusSearch').val();
                data.import_type = $('#importTypeFilter').val();
            }
        },
        columns: [
            {
                data: 'id',
                className: 'text-center align-middle',
                render: function(data) {
                    return '<strong>Job #' + Number(data || 0) + '</strong>';
                }
            },
            {
                data: 'type_label',
                className: 'text-center align-middle',
                render: function(data, type, row) {
                    const label = data || (row.type === 'price' ? 'Product Price Update' : 'Product Import');
                    return escapeHtml(label);
                }
            },
            {
                data: 'status',
                className: 'text-center align-middle',
                render: function(data) {
                    const statusText = String(data || 'queued')
                        .replace(/_/g, ' ')
                        .toLowerCase()
                        .replace(/\b\w/g, function(char) { return char.toUpperCase(); });
                    return escapeHtml(statusText);
                }
            },
            {
                data: null,
                className: 'text-center align-middle',
                render: function(data, type, row) {
                    return Number(row.processed_rows || 0) + ' / ' + Number(row.total_rows || 0);
                }
            },
            { data: 'success_count', className: 'text-center align-middle' },
            {
                data: 'failed_count',
                className: 'text-center align-middle',
                render: function(data) {
                    const value = Number(data || 0);
                    return value > 0 ? '<span class="text-danger fw-semibold">' + value + '</span>' : value;
                }
            },
            {
                data: 'progress_percent',
                render: function(data) {
                    const progress = Number(data || 0);
                    return `<div class="d-flex align-items-center gap-2" style="min-width: 180px;">
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span>${progress}%</span>
                    </div>`;
                }
            },
            {
                data: 'message',
                className: 'align-middle',
                render: function(data) {
                    return escapeHtml(data || '-');
                }
            },
            {
                data: 'updated_at',
                className: 'text-center align-middle',
                render: function(data, type, row) {
                    return escapeHtml(data || row.created_at || '-');
                }
            },
            {
                data: null,
                className: 'text-center align-middle',
                orderable: false,
                render: function(data, type, row) {
                    const errors = Array.isArray(row.errors) ? row.errors : [];
                    const failed = Number(row.failed_count || 0);
                    const hasErrors = errors.length > 0 || failed > 0;
                    if (!hasErrors) {
                        return '<div class="d-flex justify-content-center"><span class="text-muted">-</span></div>';
                    }
                    const payload = encodeURIComponent(JSON.stringify(errors));
                    return '<div class="d-flex justify-content-center"><button type="button" class="btn btn-xs btn-outline-danger px-2 py-1 view-errors" data-job-id="' + Number(row.id || 0) + '" data-errors="' + payload + '" title="View error details">Errors</button></div>';
                }
            },
        ],
        columnDefs: [
            { targets: 0, width: '90px' },   // Job
            { targets: 1, width: '170px' },  // Type
            { targets: 2, width: '110px' },  // Status
            { targets: 3, width: '110px' },  // Processed
            { targets: 4, width: '90px' },   // Success
            { targets: 5, width: '90px' },   // Failed
            { targets: 6, width: '210px' },  // Progress
            { targets: 7, width: '320px' },  // Message
            { targets: 8, width: '170px' },  // Updated
            { targets: 9, width: '100px', className: 'text-center align-middle' },  // Actions
        ],
        drawCallback: function() {
            updateTableCountInfo(this.api());
        }
    });

    let reloadTimer = null;
    function debounceReload() {
        if (reloadTimer) {
            clearTimeout(reloadTimer);
        }
        reloadTimer = setTimeout(function() {
            table.ajax.reload();
        }, 250);
    }

    $('#importStatusSearch').on('keyup', debounceReload);
    $('#importTypeFilter').on('change', function() {
        table.ajax.reload();
    });

    window.clearImportStatusSearch = function() {
        $('#importStatusSearch').val('');
        table.ajax.reload();
    };

    // Error details modal handling
    $(document).on('click', '.view-errors', function() {
        try {
            // Ensure modal exists in DOM (layout may not render a separate modals section)
            (function ensureErrorsModalExists() {
                if (!document.getElementById('importErrorsModal')) {
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = '' +
                        '<div class="modal fade" id="importErrorsModal" tabindex="-1" aria-hidden="true">' +
                        '  <div class="modal-dialog modal-lg modal-dialog-scrollable">' +
                        '    <div class="modal-content">' +
                        '      <div class="modal-header">' +
                        '        <h6 class="modal-title mb-0">Import Error Details</h6>' +
                        '        <button type="button" class="btn btn-sm border-0 bg-transparent p-1" data-import-errors-close aria-label="Close" title="Close">' +
                        '          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">' +
                        '            <line x1="18" y1="6" x2="6" y2="18"></line>' +
                        '            <line x1="6" y1="6" x2="18" y2="18"></line>' +
                        '          </svg>' +
                        '        </button>' +
                        '      </div>' +
                        '      <div class="modal-body">' +
                        '        <div id="importErrorsContent" class="small"></div>' +
                        '      </div>' +
                        '      <div class="modal-footer">' +
                        '        <button type="button" class="btn btn-secondary btn-sm" data-import-errors-close>Close</button>' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
                        '</div>';
                    document.body.appendChild(wrapper.firstChild);
                }
            })();

            const encoded = $(this).data('errors') || '';
            const decoded = decodeURIComponent(String(encoded));
            let errors = [];
            if (decoded) {
                errors = JSON.parse(decoded);
            }

            let contentHtml = '';
            const buildItem = function(item) {
                if (item == null) {
                    return '';
                }
                if (typeof item === 'string') {
                    return '<li class="mb-1"><code>' + escapeHtml(item) + '</code></li>';
                }
                if (typeof item === 'object') {
                    // Try common shapes: {row: n, message: '...'} or {index: n, error: '...'}
                    const rowNum = item.row ?? item.index ?? '';
                    const message = item.message ?? item.error ?? JSON.stringify(item);
                    const prefix = rowNum !== '' ? ('Row ' + escapeHtml(String(rowNum)) + ': ') : '';
                    return '<li class="mb-1"><code>' + prefix + escapeHtml(String(message)) + '</code></li>';
                }
                return '<li class="mb-1"><code>' + escapeHtml(String(item)) + '</code></li>';
            };

            if (Array.isArray(errors) && errors.length > 0) {
                contentHtml = '<ol class="mb-0 ps-3">' + errors.map(buildItem).join('') + '</ol>';
            } else {
                contentHtml = '<div class="text-muted">No error details available.</div>';
            }

            $('#importErrorsContent').html(contentHtml);
            const modalEl = document.getElementById('importErrorsModal');
            // Initialize modal with backward-compatible pattern (Bootstrap <5.2 lacks getOrCreateInstance)
            var modal = modalEl.__importErrorsModalInstance;
            if (!modal) {
                try {
                    // Try existing instance getter first
                    modal = (bootstrap.Modal.getInstance && bootstrap.Modal.getInstance(modalEl)) || null;
                } catch (_) {
                    modal = null;
                }
                if (!modal) {
                    modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
                }
                modalEl.__importErrorsModalInstance = modal;
            }

            // Ensure close buttons work even if data API fails
            $(modalEl).off('click.importErrorsClose').on('click.importErrorsClose', '[data-import-errors-close]', function() {
                var inst = modalEl.__importErrorsModalInstance;
                if (!inst && bootstrap.Modal && bootstrap.Modal.getInstance) {
                    inst = bootstrap.Modal.getInstance(modalEl);
                }
                if (!inst) {
                    try {
                        inst = new bootstrap.Modal(modalEl);
                    } catch (_) {}
                }
                if (inst && typeof inst.hide === 'function') {
                    inst.hide();
                }
            });

            // Clean up modal element after hidden to avoid duplicates
            $(modalEl).off('hidden.bs.modal.importErrors').on('hidden.bs.modal.importErrors', function() {
                // Remove from DOM so a fresh one is created next time
                this.parentNode && this.parentNode.removeChild(this);
            });

            modal.show();
        } catch (e) {
            $('#importErrorsContent').html('<div class="text-danger">Failed to load error details.</div>');
            const modalEl = document.getElementById('importErrorsModal');
            var modal;
            try {
                modal = (bootstrap.Modal.getInstance && bootstrap.Modal.getInstance(modalEl)) || null;
            } catch (_) {
                modal = null;
            }
            if (!modal) {
                modal = new bootstrap.Modal(modalEl);
            }
            modal.show();
        }
    });
});
</script>
<style>
.import-status-filters .filter-control {
    height: 38px;
}
.import-status-table th,
.import-status-table td {
    max-width: none;
}
.import-status-table td:nth-child(8),
.import-status-table th:nth-child(8) {
    white-space: normal !important;
    word-break: break-word;
}
.import-status-filters .import-search-wrap {
    width: 320px;
}
.import-status-filters .import-type-filter {
    min-width: 220px;
    margin-left: 6px;
}
@media (max-width: 768px) {
    .import-status-filters .import-search-wrap,
    .import-status-filters .import-type-filter {
        width: 100%;
        min-width: 100%;
    }
}
</style>
@endsection

