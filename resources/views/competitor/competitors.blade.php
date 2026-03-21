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
                                    <span class="input-icon icon-right" onclick="clearSearch()">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="search" name="search" data-table="datatable"
                                        autocomplete="off" class="form-control form-control-default"
                                        placeholder="Search Competitors"
                                        style="width: 300px;" maxlength="255">
                                </div>
                                <div class="action-btn">
                                    <a href="{{ route('competitor.create') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i>Add New</a>
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
                                                    <span class="userDatatable-title">Website</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Short Name</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Price Class Name</span>
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

{{-- @push('scripts') --}}
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
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            @if (session('create'))
                toastr.success("{{ session('create') }}");
            @endif
            @if (session('update'))
                toastr.success("{{ session('update') }}");
            @endif
            @if (session('delete'))
                toastr.success("{{ session('delete') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
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

                $('.competitor-table-count-info').html(countText);
            }

            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                dom: 'rt<"bottom d-flex justify-content-between align-items-center flex-wrap gap-2"l<"competitor-table-count-info text-center flex-grow-1 small fw-semibold text-primary">p><"clear">',

                language: {
                    emptyTable: `<div class="py-4 text-center text-muted">
                <i class="fas fa-users fa-2x mb-2"></i><br>
                <span style="font-size: 1.1em;">No competitors found.</span>
            </div>`,
                },
                ajax: {
                    url: "{{ route('competitor.list') }}",
                    data: function(data) {
                        hidePageLoading();
                        data.searchData = $('#search').val();
                    },
                    complete: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                },
                columns: [{
                        data: 'name',
                        name: 'name',
                        className: 'text-center competitor-name-wrap',
                        width: '250px'
                    },
                    {
                        data: 'website_link',
                        name: 'website',
                        className: 'text-center',
                        width: '200px'
                    },
                    {
                        data: 'shortname',
                        name: 'shortname',
                        className: 'text-center',
                        width: '120px'
                    },
                    {
                        data: 'price_class_name',
                        name: 'price_class_name',
                        className: 'text-center',
                        width: '150px'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        className: 'text-center',
                        searchable: false,
                        width: '120px'
                    },
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

            // Handle delete form submission
            $(document).on('submit', 'form[action*="competitor/delete"]', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this competitor!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $(this);
                        const url = form.attr('action');
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            data: form.serialize(),
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function(xhr) {
                                const response = xhr.responseJSON;
                                if (response && response.message) {
                                    toastr.error(response.message);
                                } else {
                                    toastr.error(
                                        'An error occurred while deleting the competitor.'
                                        );
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('vendor_assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
@endsection
