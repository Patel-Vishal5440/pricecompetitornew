@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/dataTables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable-common.css') }}">
    <style>
        /* Category Modal Theme Styling */
        #categoryModal .modal-header {
            background: linear-gradient(135deg, #5f63f2 0%, #4347d9 100%);
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }
        
        #categoryModal .modal-header .modal-title {
            font-weight: 600;
            font-size: 1.125rem;
            letter-spacing: 0.3px;
            color: #ffffff;
        }
        
        #categoryModal .modal-body {
            background: #f8f9fb;
        }
        
        #categoryModal .form-label {
            color: #272b41;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        #categoryModal .form-control,
        #categoryModal .form-select {
            border: 1px solid #e3e6ef;
            border-radius: 6px;
            padding: 0.625rem 0.875rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        #categoryModal .form-control:focus,
        #categoryModal .form-select:focus {
            border-color: #5f63f2;
            box-shadow: 0 0 0 0.2rem rgba(95, 99, 242, 0.15);
            background: #ffffff;
        }
        
        #categoryModal .text-danger {
            color: #ff4d4f !important;
        }
        
        #categoryModal .invalid-feedback {
            color: #ff4d4f;
            font-size: 0.8125rem;
            margin-top: 0.25rem;
        }
        
        #categoryModal .form-control.is-invalid,
        #categoryModal .form-select.is-invalid {
            border-color: #ff4d4f;
        }
        
        #categoryModal .form-control.is-invalid:focus,
        #categoryModal .form-select.is-invalid:focus {
            border-color: #ff4d4f;
            box-shadow: 0 0 0 0.2rem rgba(255, 77, 79, 0.15);
        }
        
        #categoryModal .modal-footer {
            background: #ffffff;
            border-top: 1px solid #e3e6ef;
            padding: 1rem 1.5rem;
        }
        
        #categoryModal .btn-primary {
            background: linear-gradient(135deg, #5f63f2 0%, #4347d9 100%);
            border: none;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.3);
        }
        
        #categoryModal .btn-primary:hover {
            background: linear-gradient(135deg, #4347d9 0%, #3639c4 100%);
            box-shadow: 0 6px 16px rgba(95, 99, 242, 0.4);
            transform: translateY(-1px);
        }
        
        #categoryModal .btn-secondary {
            background: #f8f9fb;
            border: 1px solid #e3e6ef;
            color: #666d92;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        #categoryModal .btn-secondary:hover {
            background: #f4f5f7;
            border-color: #c6d0dc;
            color: #272b41;
        }
        
        #categoryModal .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        /* Table Styling Improvements */
        .table-hover tbody tr:hover {
            background-color: #f8f9fb;
            transition: background-color 0.2s ease;
        }
        
        .table th {
            background-color: #f8f9fb;
            border-bottom: 2px solid #e3e6ef;
            font-weight: 600;
            color: #272b41;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6ef;
        }
        
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            border: none;
            color: #fff;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #5f63f2 0%, #4347d9 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #4347d9 0%, #3639c4 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(95, 99, 242, 0.4);
        }
        
        .form-control-solid {
            background-color: #f8f9fb;
            border: 1px solid #e3e6ef;
        }
        
        .form-control-solid:focus {
            background-color: #ffffff;
            border-color: #5f63f2;
            box-shadow: 0 0 0 0.2rem rgba(95, 99, 242, 0.15);
        }
    </style>
@endsection

@section('content')
<div class="contents">
    <div class="">
        <div class="row">
            <div class="col-12">
                <div class="card" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); width: 100%;">
                    <div class="card-body p-3">
                        <!-- Filter and Action Bar -->
                        <div class="row g-3 align-items-end mb-3">
                            <!-- Search Column -->
                            <div class="col-12 col-md-4 col-lg-4">
                                <label class="form-label small text-muted mb-1">Search Category</label>
                                <div class="input-container icon-left icon-right position-relative">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                    <span class="input-icon icon-right" onclick="clearSearch()" style="cursor: pointer;">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="search" name="search" data-table="datatable"
                                        autocomplete="off"
                                        class="form-control form-control-solid ps-12 pe-12 table_search"
                                        placeholder="Search by name or description"
                                        style="max-width: 100%;"
                                        maxlength="255">
                                </div>
                            </div>
                            
                            <!-- Add Category Button -->
                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('category.create'))
                            <div class="col-12 col-md-8 col-lg-8 d-flex align-items-end justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm" id="addCategoryBtn">
                                    <i class="fas fa-plus me-1"></i> Add Category
                                </button>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Table Section -->
                        <div class="table4 p-25 bg-white mb-30">
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table id="datatable" class="table mb-0 table-hover">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-start align-middle">
                                                <span class="userDatatable-title">Name</span>
                                            </th>
                                            <th class="text-start align-middle">
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
                                    <tbody>
                                        <!-- DataTables will populate this -->
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

<div id="loadingIndicator"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgb(0 0 0 / 32%); z-index: 9999; display: flex; align-items: center; justify-content: center;">
    <div class="spinner-border text-danger" role="status"></div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
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
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="fas fa-save me-2"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            
            // Display session messages
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

            function showPageLoading() {
                document.getElementById("loadingIndicator").style.display = "flex";
            }

            function hidePageLoading() {
                document.getElementById("loadingIndicator").style.display = "none";
            }

            hidePageLoading();

            // Initialize feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Initialize DataTable
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                dom: 'rt<"bottom"lp><"clear">',
                language: {
                    emptyTable: `<div class="py-5 text-center text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                        <p class="mb-0">No categories found</p>
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
                        // Reinitialize feather icons after table load
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name',
                        className: 'text-start',
                        width: '250px'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        className: 'text-start',
                        width: '300px'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        width: '120px',
                        orderable: false
                    },
                    {
                        data: 'products_count',
                        name: 'products_count',
                        className: 'text-center',
                        width: '150px',
                        orderable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        className: 'text-center',
                        width: '120px',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Search functionality
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
                // Reinitialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });

            // Edit Category - Use event delegation for dynamically loaded content
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
                // Reinitialize feather icons
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
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save');
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
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save');
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors
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

            // Delete Category - Use event delegation
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
        });
    </script>
@endsection
