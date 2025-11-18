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
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="color-dark fw-500 d-flex justify-content-between mt-15 mx-4">
                                <div class="input-container icon-left icon-right position-relative">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                    <span class="input-icon icon-right" onclick="clearSearch()" style="cursor: pointer;">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="search" class="form-control form-control-default" 
                                           placeholder="Search Permissions" 
                                           style="width: 300px;" maxlength="255" autocomplete="off">
                                </div>
                                <div class="action-btn">
                                    <a href="{{ route('permissions.create') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i> Create Permission
                                    </a>
                                </div>
                            </div>
                            <div class="table4 p-25 bg-white mb-30">
                                <div class="table-responsive">
                                    <table id="datatable" class="table mb-0 datatable">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Name</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Description</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Group</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Assigned Roles</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Status</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Actions</span>
                                                </th>
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
    </div>

    <div id="loadingIndicator"
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgb(0 0 0 / 32%); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="spinner-border text-danger" role="status"></div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('vendor_assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/sweetalert2/sweetalert2.all.min.js') }}"></script>
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
                <i class="fas fa-shield-alt fa-2x mb-2"></i><br>
                <span style="font-size: 1.1em;">No permissions found.</span>
            </div>`,
        },
        ajax: {
            url: "{{ route('permissions.index') }}",
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
            { data: 'group', name: 'group', className: 'text-center' },
            { data: 'assigned_roles', name: 'assigned_roles', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', className: 'text-center', searchable: false }
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
        let form = this;
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to delete this permission? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('permission_created_success'))
            toastr.success('{{ session('permission_created_success') }}');
        @endif
    });
</script>
<script src="{{ asset('vendor_assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
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
</style>
@endpush
