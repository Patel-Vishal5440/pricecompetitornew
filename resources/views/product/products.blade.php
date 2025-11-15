@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/dataTables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ mix('css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable-common.css') }}">
    <style>
        .removeuppercase {
            text-transform: none !important;
        }
        
        /* Product Create Form Theme Styling */
        #addProductModal .modal-header {
            background: linear-gradient(135deg, #5f63f2 0%, #4347d9 100%);
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }
        
        #addProductModal .modal-header .modal-title {
            font-weight: 600;
            font-size: 1.125rem;
            letter-spacing: 0.3px;
        }
        
        #addProductModal .modal-body {
            background: #f8f9fb;
        }
        
        #addProductModal .alert-info {
            background: linear-gradient(135deg, rgba(44, 153, 255, 0.1) 0%, rgba(44, 153, 255, 0.05) 100%);
            border: 1px solid rgba(44, 153, 255, 0.2);
            border-left: 4px solid #2c99ff;
            color: #1a73e8;
            border-radius: 8px;
        }
        
        #addProductModal .alert-info i {
            color: #2c99ff;
        }
        
        #addProductModal .alert-info strong {
            color: #1a73e8;
        }
        
        #addProductModal h6.text-primary {
            color: #5f63f2 !important;
            font-weight: 600;
            font-size: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(95, 99, 242, 0.2);
            margin-bottom: 1.25rem;
        }
        
        #addProductModal h6.text-primary i {
            color: #5f63f2;
            background: rgba(95, 99, 242, 0.1);
            padding: 0.5rem;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        #addProductModal .form-label {
            color: #272b41;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        #addProductModal .form-control,
        #addProductModal .form-select {
            border: 1px solid #e3e6ef;
            border-radius: 6px;
            padding: 0.625rem 0.875rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        #addProductModal .form-control:focus,
        #addProductModal .form-select:focus {
            border-color: #5f63f2;
            box-shadow: 0 0 0 0.2rem rgba(95, 99, 242, 0.15);
            background: #ffffff;
        }
        
        #addProductModal .input-group-text {
            background: linear-gradient(135deg, #f8f9fb 0%, #f4f5f7 100%);
            border: 1px solid #e3e6ef;
            color: #5f63f2;
            font-weight: 600;
            border-right: none;
        }
        
        #addProductModal .input-group .form-control {
            border-left: none;
        }
        
        #addProductModal .input-group:focus-within .input-group-text {
            border-color: #5f63f2;
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.05) 0%, rgba(95, 99, 242, 0.02) 100%);
        }
        
        #addProductModal .form-text {
            color: #666d92;
            font-size: 0.8125rem;
            margin-top: 0.375rem;
        }
        
        #addProductModal .form-text i {
            color: #2c99ff;
        }
        
        #addProductModal .text-danger {
            color: #ff4d4f !important;
        }
        
        #addProductModal .invalid-feedback {
            color: #ff4d4f;
            font-size: 0.8125rem;
            margin-top: 0.25rem;
        }
        
        #addProductModal .form-control.is-invalid,
        #addProductModal .form-select.is-invalid {
            border-color: #ff4d4f;
        }
        
        #addProductModal .form-control.is-invalid:focus,
        #addProductModal .form-select.is-invalid:focus {
            border-color: #ff4d4f;
            box-shadow: 0 0 0 0.2rem rgba(255, 77, 79, 0.15);
        }
        
        #addProductModal hr {
            border-color: #e3e6ef;
            opacity: 1;
            margin: 1.5rem 0;
        }
        
        #addProductModal .modal-footer {
            background: #ffffff;
            border-top: 1px solid #e3e6ef;
            padding: 1rem 1.5rem;
        }
        
        #addProductModal .btn-primary {
            background: linear-gradient(135deg, #5f63f2 0%, #4347d9 100%);
            border: none;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.3);
        }
        
        #addProductModal .btn-primary:hover {
            background: linear-gradient(135deg, #4347d9 0%, #3639c4 100%);
            box-shadow: 0 6px 16px rgba(95, 99, 242, 0.4);
            transform: translateY(-1px);
        }
        
        #addProductModal .btn-secondary {
            background: #f8f9fb;
            border: 1px solid #e3e6ef;
            color: #666d92;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        #addProductModal .btn-secondary:hover {
            background: #f4f5f7;
            border-color: #c6d0dc;
            color: #272b41;
        }
        
        #addProductModal .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        #addProductModal .mb-4 {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e3e6ef;
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
                            <div class="col-12 col-md-2 col-lg-2">
                                <label class="form-label small text-muted mb-1">Search Product</label>
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
                                        placeholder="Search by name or SKU">
                                </div>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="col-12 col-md-2 col-lg-2">
                                <label class="form-label small text-muted mb-1">Category</label>
                                <select id="filterCategory" class="form-control form-control-solid">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    @foreach($legacyCategories as $legacyCategory)
                                        <option value="{{ $legacyCategory }}">{{ $legacyCategory }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Competitor Filter -->
                            <div class="col-12 col-md-2 col-lg-2">
                                <label class="form-label small text-muted mb-1">Competitor</label>
                                <select id="filterCompetitor" class="form-control form-control-solid">
                                    <option value="">All Competitors</option>
                                    @foreach($competitors as $competitor)
                                        <option value="{{ $competitor->id }}">{{ $competitor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Price Sort Filter -->
                            <div class="col-12 col-md-2 col-lg-2">
                                <label class="form-label small text-muted mb-1">Sort Price</label>
                                <select id="filterPriceSort" class="form-control form-control-solid" title="Select a competitor first to sort by price">
                                    <option value="">Sort by Price</option>
                                    <option value="low_to_high">Price: Low to High</option>
                                    <option value="high_to_low">Price: High to Low</option>
                                </select>
                            </div>
                            
                            <!-- Price Comparison Filter -->
                            <div class="col-12 col-md-2 col-lg-2">
                                <label class="form-label small text-muted mb-1">Price Comparison</label>
                                <select id="filterPriceComparison" class="form-control form-control-solid" title="Select a competitor first to compare prices">
                                    <option value="">All Prices</option>
                                    <option value="higher">Competitor Higher</option>
                                    <option value="lower">Competitor Lower</option>
                                </select>
                            </div>
                            
                            <!-- Add Product Button -->
                            <div class="col-12 col-md-2 col-lg-2 d-flex align-items-end justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm" id="addProductBtn">
                                    <i class="fas fa-plus me-1"></i> Add Product
                                </button>
                            </div>
                        </div>
                        <div class="table4 p-25 bg-white mb-30">
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table id="datatable" class="table mb-0 datatable">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            {{-- <th class="text-center">Id</th> --}}
                                            <th class="text-start">Product Name</th>
                                            <th class="text-center">Sku</th>
                                            <th class="text-center">Price</th>
                                            @foreach ($competitors as $competitor)
                                                <th class="text-center">{{ $competitor->shortname }} Link</th>
                                                <th class="text-center">Price</th>
                                            @endforeach
                                            <th class="text-center">Actions</th>
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

{{-- Modal for assigning competitor link --}}
<div class="modal fade com_Link" id="competitorLinkModal" tabindex="-1" role="dialog" aria-labelledby="competitorLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Competitor Link</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span data-feather="x"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="mb-2"><span class="fw-semibold text-dark">Competitor Website :</span> <span id="ciWebsite"></span></div>
                </div>
                <div class="mb-3">
                    <label for="modalCompetitorLink" class="form-label">Competitor Link</label>
                    <input type="text" class="form-control mb-3" id="modalCompetitorLink" placeholder="Paste competitor product link here">
                    <div id="modalCompetitorLinkError" class="invalid-feedback" style="display:none;"></div>
                </div>
                <input type="hidden" id="modalCompetitorId">
                <input type="hidden" id="modalProductId">
                <input type="hidden" id="modalCompetitorWebsite">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCompetitorLink">Save Link</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for adding product --}}
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Odoo Product Entry Info -->
                <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">Odoo Product Entry</strong>
                        <small class="text-muted">Product data will be fetched from Odoo using the Odoo ID.</small>
                    </div>
                </div>
                
                <!-- Product Information Section -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-box me-2"></i>Product Information
                    </h6>
                    
                    <div class="mb-3">
                        <label for="addProductOdooId" class="form-label fw-semibold">
                            Odoo ID <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="addProductOdooId" placeholder="Enter Odoo Product ID" required>
                        <div id="addProductOdooIdError" class="invalid-feedback" style="display:none;"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Product data will be automatically fetched from Odoo
                        </small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Category & Competitor Section -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-tags me-2"></i>Category & Competitor
                    </h6>
                    
                    <div class="mb-3">
                        <label for="addProductCategory" class="form-label fw-semibold">
                            Category
                        </label>
                        <select class="form-select" id="addProductCategory">
                            <option value="">Select Category (Optional)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div id="addProductCategoryError" class="invalid-feedback" style="display:none;"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Can't find your category? Contact administrator to add a new category.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Competitor URLs
                        </label>
                        <small class="form-text text-muted d-block mb-2">
                            <i class="fas fa-info-circle me-1"></i>Add competitor URLs (optional). Price will be automatically scraped from these URLs.
                        </small>
                        <div id="competitorUrlsContainer">
                            @foreach($competitors as $competitor)
                            <div class="mb-2 competitor-url-row" data-competitor-id="{{ $competitor->id }}" data-competitor-website="{{ $competitor->website }}">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" title="{{ $competitor->name }}">
                                        <i class="fas fa-link"></i>
                                        <span class="ms-1">{{ $competitor->shortname ?? substr($competitor->name, 0, 3) }}</span>
                                    </span>
                                    <input type="url" 
                                           class="form-control competitor-url-input" 
                                           id="competitorUrl_{{ $competitor->id }}" 
                                           data-competitor-id="{{ $competitor->id }}"
                                           placeholder="Paste {{ $competitor->shortname ?? $competitor->name }} link">
                                </div>
                                <div class="invalid-feedback competitor-url-error" id="competitorUrlError_{{ $competitor->id }}" style="display:none;"></div>
                            </div>
                            @endforeach
                        </div>
                        <div id="competitorUrlsError" class="invalid-feedback" style="display:none;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveAddProduct">
                    <i class="fas fa-save me-2"></i>Add Product
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for editing price --}}
<div class="modal fade" id="priceEditModal" tabindex="-1" role="dialog" aria-labelledby="priceEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Price</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span data-feather="x"></span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="modalPrice" placeholder="Enter Price">
                <input type="hidden" id="modalPriceProductId">
                <div id="modalPriceError" class="text-danger mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary savePriceBtn" id="savePrice">Save</button>
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
                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                <span style="font-size: 1.1em;">No products found.</span>
            </div>`,
        },
        ajax: {
            url: "{{ route('products.list') }}",
            data: function(data) {
                hidePageLoading();
                data.searchData = $('#search').val();
                data.category = $('#filterCategory').val();
                data.competitor_id = $('#filterCompetitor').val();
                data.price_sort = $('#filterPriceSort').val();
                data.price_comparison = $('#filterPriceComparison').val();
            },
            complete: function() {
                $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        },
        columns: [
            // { data: 'odoo_id', name: 'id', className: 'text-center', width: '60px' },
            { data: 'name', name: 'name', className: 'text-start', width: '250px' },
            { data: 'default_code', name: 'default_code', className: 'text-center', width: '120px' },
            {
                data: 'list_price',
                name: 'list_price',
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <span class="mx-2">${data}</span>
                            <a href="javascript:void(0)" class="mx-2 text-light edit-price-btn"
                               data-product-id="${row.odoo_id}" data-current-price="${data}">
                                <i class="fas fa-edit fs-6"></i>
                            </a>
                        </div>`;
                }
            },
            @foreach ($competitors as $competitor)
            {
                data: 'competitor_link_{{ $competitor->id }}',
                name: 'competitor_link_{{ $competitor->id }}',
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex justify-content-center align-items-center">
                            <a href="javascript:void(0)" class="m-2 text-light add-link-btn"
                               data-row-id="{{ $competitor->id }}" 
                               data-product-id="${row.id}" 
                               data-current-link="${data || ''}"
                               data-competitor-name="{{ $competitor->name }}"
                               data-competitor-shortname="{{ $competitor->shortname }}"
                               data-competitor-website="{{ $competitor->website }}">
                                <i class="fas fa-link fs-6"></i>
                            </a>
                        </div>`;
                }
            },
            {
                data: 'competitor_price_{{ $competitor->id }}',
                name: 'competitor_price_{{ $competitor->id }}',
                className: 'text-center',
                render: function(data) {
                    return `<span>${data}</span>`;
                }
            },
            @endforeach
            {
                data: 'action',
                name: 'action',
                className: 'text-center',
                searchable: false,
                width: '60px'
            },
        ]
    });

    $('#search').on('keyup', function() {
        table.ajax.reload();
    });

    // Filter change handlers
    $('#filterCategory').on('change', function() {
        table.ajax.reload();
    });

    $('#filterCompetitor').on('change', function() {
        // Clear price sort and price comparison if competitor is cleared
        if (!$(this).val()) {
            $('#filterPriceSort').val('');
            $('#filterPriceComparison').val('');
        }
        table.ajax.reload();
    });

    $('#filterPriceSort').on('change', function() {
        var competitorId = $('#filterCompetitor').val();
        if ($(this).val() && !competitorId) {
            toastr.warning('Please select a competitor first to sort by price');
            $(this).val('');
            return;
        }
        table.ajax.reload();
    });

    $('#filterPriceComparison').on('change', function() {
        var competitorId = $('#filterCompetitor').val();
        if ($(this).val() && !competitorId) {
            toastr.warning('Please select a competitor first to compare prices');
            $(this).val('');
            return;
        }
        table.ajax.reload();
    });

    // Clear search function
    window.clearSearch = function() {
        $('#search').val('');
        table.ajax.reload();
    };

    // Add Product Modal
    $(document).on('click', '#addProductBtn', function() {
        // Reset form fields
        $('#addProductOdooId').val('');
        $('#addProductCategory').val('');
        $('.competitor-url-input').val('');
        // Reset errors
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');
        $('#addProductModal').modal('show');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    // Initialize on modal show
    $(document).on('show.bs.modal', '#addProductModal', function() {
        // Reset form
        $('#addProductModal form')[0]?.reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
    });

    // Clear Odoo ID error when user starts typing
    $(document).on('input', '#addProductOdooId', function() {
        $('#addProductOdooIdError').hide();
        $(this).removeClass('is-invalid');
    });

    $(document).on('click', '#saveAddProduct', function() {
        var $btn = $(this);
        var odooId = $('#addProductOdooId').val();
        var categoryId = $('#addProductCategory').val();

        // Reset errors
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var hasError = false;

        // Validate Odoo ID (required)
        if (!odooId || odooId.trim() === '' || isNaN(odooId)) {
            $('#addProductOdooIdError').text('Odoo ID is required and must be a number').show();
            $('#addProductOdooId').addClass('is-invalid');
            hasError = true;
        }

        // Collect and validate competitor URLs (optional)
        var competitorUrls = [];
        
        $('.competitor-url-input').each(function() {
            var $input = $(this);
            var url = $input.val().trim();
            var competitorId = $input.data('competitor-id');
            var $row = $input.closest('.competitor-url-row');
            var competitorWebsite = $row.data('competitor-website');
            var $errorDiv = $('#competitorUrlError_' + competitorId);

            // Clear previous errors
            $errorDiv.hide();
            $input.removeClass('is-invalid');

            // If URL is provided, validate it
            if (url) {
                // Validate URL format
                try {
                    new URL(url);
                } catch (e) {
                    $errorDiv.text('Invalid URL format').show();
                    $input.addClass('is-invalid');
                    hasError = true;
                    return;
                }

                // Validate URL domain if competitor website is set
                if (competitorWebsite) {
                    try {
                        const providedDomain = new URL(url).hostname.replace(/^www\./, '').toLowerCase();
                        const competitorDomain = new URL(competitorWebsite).hostname.replace(/^www\./, '').toLowerCase();
                        if (providedDomain !== competitorDomain) {
                            $errorDiv.text(`URL domain does not match competitor's website. Expected: ${competitorDomain}`).show();
                            $input.addClass('is-invalid');
                            hasError = true;
                            return;
                        }
                    } catch (e) {
                        $errorDiv.text('Invalid URL format').show();
                        $input.addClass('is-invalid');
                        hasError = true;
                        return;
                    }
                }

                competitorUrls.push({
                    competitor_id: competitorId,
                    competitor_url: url
                });
            }
        });

        if (hasError) {
            return;
        }

        $btn.prop('disabled', true).text('Adding...');
        showPageLoading();

        var postData = {
            _token: "{{ csrf_token() }}",
            odoo_id: odooId
        };

        // Add optional fields only if provided
        if (categoryId) {
            postData.category_id = categoryId;
        }
        if (competitorUrls.length > 0) {
            postData.competitor_urls = competitorUrls;
        }

        $.post("{{ route('products.store') }}", postData).done(function(response) {
            hidePageLoading();
            $btn.prop('disabled', false).text('Add Product');
            if (response.success) {
                var message = response.message;
                if (response.product && response.product.odoo_id) {
                    message += ' (Odoo ID: ' + response.product.odoo_id + ')';
                }
                toastr.success(message);
                $('#addProductModal').modal('hide');
                // Reset form
                $('#addProductModal form')[0]?.reset();
                $('.competitor-url-input').val('');
                table.ajax.reload();
            } else {
                toastr.error(response.message);
                // Show error inline next to Odoo ID field if it's about Odoo ID
                if (response.message && (response.message.toLowerCase().includes('not found') || response.message.toLowerCase().includes('odoo'))) {
                    $('#addProductOdooIdError').text(response.message).show();
                    $('#addProductOdooId').addClass('is-invalid');
                }
            }
        }).fail(function(xhr) {
            hidePageLoading();
            $btn.prop('disabled', false).text('Add Product');
            var errorMessage = 'Failed to add product';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
                toastr.error(errorMessage);
                // Show error inline next to Odoo ID field if it's about Odoo ID
                if (errorMessage.toLowerCase().includes('not found') || errorMessage.toLowerCase().includes('odoo')) {
                    $('#addProductOdooIdError').text(errorMessage).show();
                    $('#addProductOdooId').addClass('is-invalid');
                }
            } else {
                toastr.error(errorMessage);
            }
        });
    });

    $(document).on("click", ".edit-price-btn", function() {
        $('#modalPriceProductId').val($(this).data("product-id"));
        $('#modalPrice').val($(this).data("current-price"));
        // For edit price modal
        $('#modalPriceError').hide();
        $('#modalPrice').removeClass('is-invalid');
        $('#priceEditModal').modal('show');
        // Reinitialize feather icons for the close button
        if (typeof feather !== 'undefined') {
            feather.replace();
        } else if (typeof window.reloadFeatherIcons === 'function') {
            window.reloadFeatherIcons();
        }
    });

    $(document).on('click', '.add-link-btn', function() {
        $('#modalCompetitorId').val($(this).data('row-id'));
        $('#modalProductId').val($(this).data('product-id'));
        $('#modalCompetitorLink').val($(this).data('current-link'));
        $('#modalCompetitorWebsite').val($(this).data('competitor-website'));

        // Display all competitor info
        const competitorShortname = $(this).data('competitor-shortname');
        const competitorWebsite = $(this).data('competitor-website');
        $('#ciShortname').text(competitorShortname);
        if (competitorWebsite) {
            $('#ciWebsite').html(`<a href='${competitorWebsite}' target='_blank' class='text-primary text-decoration-underline fw-semibold'>${competitorWebsite}</a>`);
        } else {
            $('#ciWebsite').html('<span class="text-secondary">N/A</span>');
        }

        // Show expected domain info
        if (competitorWebsite) {
            try {
                const expectedDomain = new URL(competitorWebsite).hostname.replace(/^www\./, '').toLowerCase();
                $('#expectedDomain').text(expectedDomain);
            } catch (e) {
                $('#expectedDomain').text('Invalid competitor website');
            }
        } else {
            $('#expectedDomain').text('No website configured for this competitor');
        }
        $('#expectedDomainAlert').show();

        // Reset error
        $('#modalCompetitorLinkError').hide().text("");
        $('#modalCompetitorLink').removeClass('is-invalid');
        $('#competitorLinkModal').modal('show');
        // Reinitialize feather icons for the close button
        if (typeof feather !== 'undefined') {
            feather.replace();
        } else if (typeof window.reloadFeatherIcons === 'function') {
            window.reloadFeatherIcons();
        }
    });

    $(document).on('click', '#saveCompetitorLink', function() {
        showPageLoading();
        $('#modalCompetitorLinkError').hide().text("");
        $('#modalCompetitorLink').removeClass('is-invalid');
        let competitorId = $('#modalCompetitorId').val();
        let productId = $('#modalProductId').val();
        let link = $('#modalCompetitorLink').val();
        let competitorWebsite = $('#modalCompetitorWebsite').val();

        if (!link.trim()) {
            $('#modalCompetitorLinkError').text('Please enter a URL').show();
            $('#modalCompetitorLink').addClass('is-invalid');
            hidePageLoading();
            return;
        }

        // Client-side domain validation
        if (competitorWebsite) {
            try {
                const providedDomain = new URL(link).hostname.replace(/^www\./, '').toLowerCase();
                const competitorDomain = new URL(competitorWebsite).hostname.replace(/^www\./, '').toLowerCase();
                if (providedDomain !== competitorDomain) {
                    $('#modalCompetitorLinkError').text(`URL domain does not match competitor's website. Expected: ${competitorDomain}, provided: ${providedDomain}`).show();
                    $('#modalCompetitorLink').addClass('is-invalid');
                    hidePageLoading();
                    return;
                }
            } catch (e) {
                $('#modalCompetitorLinkError').text('Invalid URL format').show();
                $('#modalCompetitorLink').addClass('is-invalid');
                hidePageLoading();
                return;
            }
        }

        $('#modalCompetitorLinkError').hide().text("");
        $('#modalCompetitorLink').removeClass('is-invalid');

        $.post("{{ route('products.addLink') }}", {
            _token: "{{ csrf_token() }}",
            competitor_id: competitorId,
            competitor_url: link,
            product_id: productId
        }).done(function(response) {
            hidePageLoading();
            if (response.success) {
                toastr.success(response.message);
                $('#competitorLinkModal').modal('hide');
                table.ajax.reload(null, false);
            } else {
                toastr.error(response.message);
            }
        }).fail(function(xhr) {
            hidePageLoading();
            if (xhr.responseJSON && xhr.responseJSON.message) {
                $('#modalCompetitorLinkError').text(xhr.responseJSON.message).show();
                $('#modalCompetitorLink').addClass('is-invalid');
            } else {
                toastr.error('Request failed');
            }
        });
    });

    $(document).on('click', '.savePriceBtn', function() {
        var $btn = $(this);
        $('#modalPriceError').hide();
        $('#modalPrice').removeClass('is-invalid');
        $btn.prop('disabled', true).text('Saving...');
        showPageLoading();
        var price = $('#modalPrice').val();
        if (!price.trim() || isNaN(price) || Number(price) < 0) {
            $('#modalPriceError').text('Please enter a valid price').show();
            $('#modalPrice').addClass('is-invalid');
            hidePageLoading();
            $btn.prop('disabled', false).text('Save');
            return;
        } else {
            $('#modalPriceError').hide();
            $('#modalPrice').removeClass('is-invalid');
        }
        $.post("{{ route('products.updatePrice') }}", {
            _token: "{{ csrf_token() }}",
            id: $('#modalPriceProductId').val(),
            list_price: price
        }).done(function(response) {
            hidePageLoading();
            $btn.prop('disabled', false).text('Save');
            if (response.success) {
                toastr.success('Price updated successfully');
                $('#priceEditModal').modal('hide');
                table.ajax.reload(null, false);
            } else {
                toastr.error('Error updating price');
            }
        }).fail(function() {
            hidePageLoading();
            $btn.prop('disabled', false).text('Save');
            toastr.error('Error updating price');
        });
    });

    $(document).on('click', '.sync-product', function() {
        showPageLoading();
        $.get("{{ route('products.sync-specific') }}", {
            _token: "{{ csrf_token() }}",
            odoo_id: $(this).data('product-id')
        }).done(function(response) {
            hidePageLoading();
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false);
            } else {
                toastr.error(response.message);
            }
        }).fail(function() {
            hidePageLoading();
            toastr.error('Error syncing product');
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
        @if(session('product_created_success'))
            toastr.success('{{ session('product_created_success') }}');
        @endif
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
