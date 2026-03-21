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
                                        </tr>
                                    </thead>
                                    <tbody id="bulkImportQueueTableBody">
                                        <tr>
                                            <td colspan="9" class="text-muted text-center">No import jobs found.</td>
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
