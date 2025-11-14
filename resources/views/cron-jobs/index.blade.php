@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/dataTables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable-common.css') }}">

@endsection

@section('content')
<div class="contents">
    <div class="">
        <div class="row">
            <div class="col-12">
                <div class="card" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); width: 100%;">
                    <div class="card-body p-3">
                                                    <div class="color-dark fw-500 d-flex justify-content-between mt-15 mx-4">
                                <div class="input-container icon-left icon-right position-relative">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                    <span class="input-icon icon-right" onclick="clearSearch()">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="search" name="search" data-table="datatable"
                                        autocomplete="off"
                                        class="form-control form-control-solid w-250px ps-12 table_search"
                                        placeholder="Search Cron Jobs">
                                </div>
                            </div>
                        <div class="table4 p-25 bg-white mb-30">
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table id="datatable" class="table mb-0 datatable">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Description</th>
                                            <th class="text-center">Schedule</th>
                                            <th class="text-center">Command</th>
                                            <th class="text-center">Last Run</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loadingIndicator"
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgb(0 0 0 / 32%); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="spinner-border text-danger" role="status"></div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    function showPageLoading() {
        document.getElementById("loadingIndicator").style.display = "flex";
    }
    function hidePageLoading() {
        document.getElementById("loadingIndicator").style.display = "none";
    }
    
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        dom: 'rt<"bottom"lp><"clear">',
        language: {
            emptyTable: `<div class="py-4 text-center text-muted">
                <i class="fas fa-clock fa-2x mb-2"></i><br>
                <span style="font-size: 1.1em;">No cron jobs found.</span>
            </div>`,
            paginate: {
                previous: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><polyline points="12 4 6 9 12 14"></polyline></svg>`,
                next: `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><polyline points="6 4 12 9 6 14"></polyline></svg>`
            }
        },
        ajax: {
            url: "{{ route('cron-jobs.index') }}",
            data: function(data) {
                hidePageLoading();
                data.searchData = $('#search').val();
            },
            complete: function() {
                $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        },
        columns: [
            { data: 'name', name: 'name', className: 'text-center' },
            { data: 'description', name: 'description', className: 'text-center' },
            { data: 'schedule', name: 'schedule', className: 'text-center' },
            { data: 'command', name: 'command', className: 'text-center' },
            { data: 'last_run', name: 'last_run', className: 'text-center' },
        ]
    });

    $('#search').on('keyup', function() {
        table.ajax.reload();
    });

    // Clear search function
    window.clearSearch = function() {
        $('#search').val('');
        table.ajax.reload();
    };

    // Delete confirmation
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this cron job? This action cannot be undone.')) {
            this.submit();
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@push('styles')
<style>
.input-container {
    position: relative;
}

.input-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
}

.input-icon.icon-left {
    left: 10px;
}

.input-icon.icon-right {
    right: 10px;
}

.form-control-solid {
    padding-left: 35px;
    padding-right: 35px;
}

.badge-lg {
    font-size: 0.75em;
    font-weight: 500;
}

code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.875em;
    color: #495057;
}

        .userDatatable-content small {
            font-size: 0.75em;
            color: #6c757d;
        }

        .dataTables_wrapper .dataTable tbody tr:hover {
            background-color: #f8f9fa;
        }
</style>
@endpush 