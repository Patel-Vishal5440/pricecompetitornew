@extends('layouts.app')
@section('title', $pageTitle ?? 'Category Management')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/dataTables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable-common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-modals.css') }}">
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
                                       placeholder="Search Categories" 
                                       style="width: 300px;" maxlength="255" autocomplete="off">
                            </div>
                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('category.create'))
                            <div class="action-btn">
                                <button type="button" class="btn btn-outline-primary" id="addCategoryBtn">
                                    <i class="fas fa-plus"></i> Create Category
                                </button>
                            </div>
                            @endif
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
                                                <span class="userDatatable-title">Status</span>
                                            </th>
                                            <th class="text-center align-middle">
                                                <span class="userDatatable-title">Products Count</span>
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

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">
                    <i class="fas fa-plus-circle me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="categoryName" placeholder="Enter category name" required>
                        <div id="categoryNameError" class="invalid-feedback" style="display:none;"></div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="categoryDescription" rows="3" placeholder="Enter category description (optional)"></textarea>
                        <div id="categoryDescriptionError" class="invalid-feedback" style="display:none;"></div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="categoryStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div id="categoryStatusError" class="invalid-feedback" style="display:none;"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    Save
                </button>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                        <span style="font-size: 1.1em;">No categories found.</span>
                    </div>`,
                },
                ajax: {
                    url: "{{ route('categories.index') }}",
                    data: function(data) {
                        hidePageLoading();
                        data.searchData = $('#search').val();
                    },
                    complete: function() {
                        hidePageLoading();
                        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                        $('[data-bs-toggle="tooltip"]').tooltip();
                        // Reinitialize feather icons after table load
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                },
                columns: [
                    { data: 'name', name: 'name', className: 'text-center', width: '250px' },
                    { data: 'description', name: 'description', className: 'text-center', width: '300px' },
                    { data: 'status', name: 'status', className: 'text-center', width: '120px', orderable: false },
                    { data: 'products_count', name: 'products_count', className: 'text-center', width: '150px', orderable: false },
                    { data: 'actions', name: 'actions', className: 'text-center', searchable: false, width: '120px' },
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

            // Get Bootstrap modal instance
            const categoryModalElement = document.getElementById('categoryModal');
            let categoryModal = null;
            if (categoryModalElement) {
                categoryModal = new bootstrap.Modal(categoryModalElement);
            }

            // Reset form and clear errors
            function resetCategoryForm() {
                $('#categoryForm')[0].reset();
                $('#categoryId').val('');
                $('#categoryNameError, #categoryDescriptionError, #categoryStatusError').hide();
                $('#categoryName, #categoryDescription, #categoryStatus').removeClass('is-invalid');
            }

            // Add Category
            $(document).on('click', '#addCategoryBtn', function() {
                resetCategoryForm();
                $('#categoryModalTitle').html('<i class="fas fa-plus-circle me-2"></i>Add New Category');
                if (categoryModal) {
                    categoryModal.show();
                }
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });

            // Edit Category
            $(document).on('click', '.edit-category-btn', function() {
                resetCategoryForm();
                const categoryId = $(this).data('id');
                const categoryName = $(this).data('name');
                const categoryDescription = $(this).data('description') || '';
                const categoryStatus = $(this).data('status');
                
                $('#categoryModalTitle').html('<i class="fas fa-edit me-2"></i>Edit Category');
                $('#categoryId').val(categoryId);
                $('#categoryName').val(categoryName);
                $('#categoryDescription').val(categoryDescription);
                $('#categoryStatus').val(categoryStatus);
                
                if (categoryModal) {
                    categoryModal.show();
                }
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });

            // Save Category
            $(document).on('click', '#saveCategoryBtn', function() {
                const $btn = $(this);
                const categoryId = $('#categoryId').val();
                const name = $('#categoryName').val().trim();
                const description = $('#categoryDescription').val().trim();
                const status = $('#categoryStatus').val();

                // Reset errors
                $('#categoryNameError, #categoryDescriptionError, #categoryStatusError').hide();
                $('#categoryName, #categoryDescription, #categoryStatus').removeClass('is-invalid');

                let hasError = false;

                // Validate name
                if (!name) {
                    $('#categoryNameError').text('Category name is required').show();
                    $('#categoryName').addClass('is-invalid');
                    hasError = true;
                }

                if (hasError) {
                    return;
                }

                const data = {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    description: description,
                    status: status
                };

                const url = categoryId ? '{{ route("categories.update", ":id") }}'.replace(':id', categoryId) : '{{ route("categories.store") }}';
                const method = categoryId ? 'PUT' : 'POST';

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function(response) {
                        $btn.prop('disabled', false).html('Save');
                        if (response.success) {
                            toastr.success(response.message);
                            if (categoryModal) {
                                categoryModal.hide();
                            }
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('Save');
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#categoryNameError').text(errors.name[0]).show();
                                $('#categoryName').addClass('is-invalid');
                            }
                            if (errors.description) {
                                $('#categoryDescriptionError').text(errors.description[0]).show();
                                $('#categoryDescription').addClass('is-invalid');
                            }
                            if (errors.status) {
                                $('#categoryStatusError').text(errors.status[0]).show();
                                $('#categoryStatus').addClass('is-invalid');
                            }
                        } else {
                            const message = xhr.responseJSON?.message || 'An error occurred';
                            toastr.error(message);
                        }
                    }
                });
            });

            // Delete Category
            $(document).on('click', '.delete-category-btn', function() {
                const categoryId = $(this).data('id');
                const categoryName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete "${categoryName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("categories.destroy", ":id") }}'.replace(':id', categoryId),
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message || 'Cannot delete category');
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message || 'An error occurred';
                                toastr.error(message);
                            }
                        });
                    }
                });
            });

            // Reset form when modal is hidden
            if (categoryModalElement) {
                categoryModalElement.addEventListener('hidden.bs.modal', function() {
                    resetCategoryForm();
                });
            }

            // Auto-focus search input on page load
            $('#search').focus();

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if(session('create'))
                toastr.success("{{ session('create') }}");
            @endif
            @if(session('update'))
                toastr.success("{{ session('update') }}");
            @endif
            @if(session('delete'))
                toastr.success("{{ session('delete') }}");
            @endif
            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
@endsection
