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
                                           placeholder="Search Roles" 
                                           style="width: 300px;" maxlength="255" autocomplete="off">
                                </div>
                                <div class="action-btn">
                                    <a href="{{ route('roles.create') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i> Create Role
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
                                                    <span class="userDatatable-title">Permissions</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Users</span>
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
    <script>
        $(document).ready(function() {
            function showPageLoading() {
                document.getElementById("loadingIndicator").style.display = "flex";
            }
            function hidePageLoading() {
                document.getElementById("loadingIndicator").style.display = "none";
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

                $('.roles-table-count-info').html(countText);
            }

            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                dom: 'rt<"bottom d-flex justify-content-between align-items-center flex-wrap gap-2"l<"roles-table-count-info text-center flex-grow-1 small fw-semibold text-primary">p><"clear">',
                language: {
                    emptyTable: `<div class="py-4 text-center text-muted">
                        <i class="fas fa-shield-alt fa-2x mb-2"></i><br>
                        <span style="font-size: 1.1em;">No roles found.</span>
                    </div>`,
                },
                ajax: {
                    url: "{{ route('roles.index') }}",
                    data: function(data) {
                        hidePageLoading();
                        data.searchData = $('#search').val();
                    },
                    complete: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                        $('[data-bs-toggle="tooltip"]').tooltip();
                        // Re-attach delete form event listeners after data load
                        attachDeleteFormListeners();
                    }
                },
                columns: [
                    { data: 'name', name: 'name', className: 'text-center', width: '150px' },
                    { data: 'description', name: 'description', className: 'text-center', width: '250px' },
                    { data: 'permissions', name: 'permissions_count', className: 'text-center', width: '100px' },
                    { data: 'users', name: 'users_count', className: 'text-center', width: '100px' },
                    { data: 'status', name: 'is_active', className: 'text-center', width: '120px' },
                    { data: 'actions', name: 'actions', className: 'text-center', searchable: false, width: '200px' },
                ],
                drawCallback: function() {
                    updateTableCountInfo(this.api());
                }
            });

            $('#search').on('keyup', function() {
                table.ajax.reload();
            });

            // Clear search function
            window.clearSearch = function() {
                $('#search').val('');
                table.ajax.reload();
            };

            // Attach delete form event listeners
            function attachDeleteFormListeners() {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Submit the form
                                const formData = new FormData(this);
                                fetch(this.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => {
                                    if (response.redirected) {
                                        window.location.href = response.url;
                                    } else {
                                        return response.json();
                                    }
                                })
                                .then(data => {
                                    if (data && data.success) {
                                        // Reload the table to reflect changes
                                        table.ajax.reload();
                                        
                                        Swal.fire(
                                            'Deleted!',
                                            data.message,
                                            'success'
                                        );
                                    } else if (data && !data.success) {
                                        Swal.fire(
                                            'Error!',
                                            data.message,
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => {
                                    console.error('Delete error:', error);
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred while deleting the role.',
                                        'error'
                                    );
                                });
                            }
                        });
                    });
                });
            }

            // Auto-focus search input on page load
            $('#search').focus();
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
            @if(session('role_created_success'))
                toastr.success('{{ session('role_created_success') }}');
            @endif
        });
    </script>
@endsection
