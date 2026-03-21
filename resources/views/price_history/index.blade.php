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
                            <div class="color-dark fw-500 d-flex justify-content-between mt-15 mx-4">
                                <div class="input-container icon-left icon-right position-relative">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                        <span class="input-icon icon-right" onclick="clearSearch()" style="cursor: pointer;">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="search" class="form-control form-control-default" 
                                           placeholder="Search Price History" style="width: 300px;" 
                                           maxlength="255" autocomplete="off">
                                </div>
                            </div>
                            <div class="table4 p-25 bg-white mb-30">
                                <div class="table-responsive">
                                    <table id="datatable" class="table mb-0 datatable">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Date</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Product Name</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Old Price</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">New Price</span>
                                                </th>
                                                <th class="text-center align-middle">
                                                    <span class="userDatatable-title">Performed By</span>
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

                $('.price-history-table-count-info').html(countText);
            }
            
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                dom: 'rt<"bottom d-flex justify-content-between align-items-center flex-wrap gap-2"l<"price-history-table-count-info text-center flex-grow-1 small fw-semibold text-primary">p><"clear">',
                language: {
                    emptyTable: `<div class="py-4 text-center text-muted">
                        <i class="fas fa-history fa-2x mb-2"></i><br>
                        <span style="font-size: 1.1em;">No price history found.</span>
                    </div>`,
                },
                ajax: {
                    url: "{{ route('price_history.list') }}",
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
                    { data: 'date', name: 'created_at', className: 'text-center', width: '150px' },
                    { data: 'product_name', name: 'product.name', className: 'text-center product-name-wrap', width: '250px' },
                    { data: 'price_old', name: 'price_old', className: 'text-center', width: '120px' },
                    { data: 'price_new', name: 'price_new', className: 'text-center', width: '120px' },
                    { data: 'performed_by', name: 'user.name', className: 'text-center', width: '150px' },
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

    // Auto-focus search input on page load
            $('#search').focus();
                });
    </script>
@endsection
