@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ mix('css/dataTables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ mix('css/buttons.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ mix('css/toastr.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatable-common.css') }}">
<link rel="stylesheet" href="{{ asset('css/product-modals.css') }}">
<style>
    /* Page-specific styles only - main styles moved to product-modals.css */
    .edit-price-btn {
        transition: opacity 0.2s, transform 0.2s;
    }

    .edit-price-btn:hover {
        opacity: 1 !important;
        transform: scale(1.1);
    }

    .edit-price-btn:active {
        transform: scale(0.95);
    }

    /* Ensure modal header items are in same row */
    .modal-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
        flex-direction: row !important;
        width: 100% !important;
    }

    .modal-title {
        flex: 1 1 auto;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        line-height: 1.5 !important;
        order: 1;
    }

    /* Ensure close button X icon is visible - no background */
    .btn-close {
        opacity: 1;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-shadow: 0 1px 0 rgba(0, 0, 0, 0.5);
        cursor: pointer;
        background: transparent !important;
        border: none !important;
        padding: 0.5rem !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        margin-left: auto !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        order: 2;
    }

    .btn-close:hover {
        opacity: 0.75;
        color: #fff;
        background: transparent !important;
    }

    .btn-close:focus {
        background: transparent !important;
        box-shadow: none !important;
        outline: none;
    }

    .btn-close span {
        display: inline-block;
        font-size: 1.5rem;
        line-height: 1;
    }

    /* For non-white close buttons */
    .modal-header:not(.bg-dark) .btn-close:not(.btn-close-white) {
        color: #000;
        text-shadow: 0 1px 0 #fff;
    }

    /* Mobilenzo columns styling - bright blue background */
    #datatable thead th.mobilenzo-column,
    table.dataTable thead th.mobilenzo-column {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    #datatable tbody td.mobilenzo-column,
    table.dataTable tbody td.mobilenzo-column {
        background-color: #cfe2ff !important;
    }

    /* Competitor columns styling - bright orange/amber background */
    #datatable thead th.competitor-column,
    table.dataTable thead th.competitor-column {
        background-color: #fd7e14 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    #datatable tbody td.competitor-column,
    table.dataTable tbody td.competitor-column {
        background-color: #ffe5cc !important;
    }

    /* Ensure hover states work properly */
    #datatable tbody tr:hover td.mobilenzo-column,
    table.dataTable tbody tr:hover td.mobilenzo-column {
        background-color: #b6d4fe !important;
    }

    #datatable tbody tr:hover td.competitor-column,
    table.dataTable tbody tr:hover td.competitor-column {
        background-color: #ffd9b3 !important;
    }

    /* Ensure even/odd row striping doesn't override our colors */
    #datatable tbody tr.even td.mobilenzo-column,
    #datatable tbody tr.odd td.mobilenzo-column,
    table.dataTable tbody tr.even td.mobilenzo-column,
    table.dataTable tbody tr.odd td.mobilenzo-column {
        background-color: #cfe2ff !important;
    }

    #datatable tbody tr.even td.competitor-column,
    #datatable tbody tr.odd td.competitor-column,
    table.dataTable tbody tr.even td.competitor-column,
    table.dataTable tbody tr.odd td.competitor-column {
        background-color: #ffe5cc !important;
    }

    /* Add subtle border between sections for better visual separation */
    #datatable thead th.mobilenzo-column:last-of-type,
    #datatable tbody td.mobilenzo-column:last-of-type,
    table.dataTable thead th.mobilenzo-column:last-of-type,
    table.dataTable tbody td.mobilenzo-column:last-of-type {
        border-right: 3px solid #0d6efd !important;
    }

    #datatable thead th.competitor-column:first-of-type,
    #datatable tbody td.competitor-column:first-of-type,
    table.dataTable thead th.competitor-column:first-of-type,
    table.dataTable tbody td.competitor-column:first-of-type {
        border-left: 3px solid #ffc107 !important;
    }

    /* Additional specificity for DataTables */
    table.dataTable.display tbody tr td.mobilenzo-column,
    table.dataTable.display tbody tr.odd td.mobilenzo-column,
    table.dataTable.display tbody tr.even td.mobilenzo-column {
        background-color: #cfe2ff !important;
    }

    table.dataTable.display tbody tr td.competitor-column,
    table.dataTable.display tbody tr.odd td.competitor-column,
    table.dataTable.display tbody tr.even td.competitor-column {
        background-color: #ffe5cc !important;
    }

    /* Keep toolbar buttons visually consistent and readable */
    .toolbar-btn {
        min-width: 122px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
        margin-right: 0.5rem;
    }

    .toolbar-btn:last-child {
        margin-right: 0;
    }

    .filters-row,
    .toolbar-row {
        width: 100%;
        margin-left: 0;
        margin-right: 0;
    }

    .toolbar-row {
        margin-top: 0.75rem;
        margin-bottom: 0.9rem !important;
    }

    .toolbar-inner {
        padding-top: 0.4rem;
        width: 100%;
        justify-content: space-between;
        row-gap: 0.75rem;
    }

    .price-comparison-group {
        margin-right: 0.5rem;
    }

    .bulk-action-bar {
        display: none;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .bulk-action-bar.active {
        display: flex;
    }

    .table4.compact-table-wrap {
        margin-top: 0;
    }

    @media (max-width: 992px) {
        .toolbar-inner {
            gap: 0.75rem !important;
        }

        .toolbar-btn {
            min-width: 112px;
        }
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
                        <div class="row g-3 align-items-end mb-3 filters-row">
                            <!-- Name Filter -->
                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                                <label class="form-label small text-muted mb-1">Name</label>
                                <div class="input-container icon-left icon-right position-relative">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                    <span class="input-icon icon-right" onclick="clearNameFilter()" style="cursor: pointer;">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="filterName" name="filter_name" data-table="datatable"
                                        autocomplete="off"
                                        class="form-control form-control-solid ps-12 pe-12 table_search"
                                        placeholder="Search by name">
                                </div>
                            </div>

                            <!-- SKU Filter -->
                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                                <label class="form-label small text-muted mb-1">SKU</label>
                                <div class="input-container icon-left icon-right position-relative">
                                    <span class="input-icon icon-left">
                                        <span data-feather="search"></span>
                                    </span>
                                    <span class="input-icon icon-right" onclick="clearSkuFilter()" style="cursor: pointer;">
                                        <i data-feather="x" class="text-muted"></i>
                                    </span>
                                    <input type="text" id="filterSku" name="filter_sku" data-table="datatable"
                                        autocomplete="off"
                                        class="form-control form-control-solid ps-12 pe-12 table_search"
                                        placeholder="Search by SKU">
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
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
                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                                <label class="form-label small text-muted mb-1">Competitor</label>
                                <select id="filterCompetitor" class="form-control form-control-solid">
                                    <option value="">All Competitors</option>
                                    @foreach($competitors as $competitor)
                                    <option value="{{ $competitor->id }}">{{ $competitor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Competitor Price Sort Filter -->
                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                                <label class="form-label small text-muted mb-1">Sort Competitor Price</label>
                                <select id="filterPriceSort" class="form-control form-control-solid" title="Select a competitor first to sort by price">
                                    <option value="">Sort by Price</option>
                                    <option value="low_to_high">Price: Low to High</option>
                                    <option value="high_to_low">Price: High to Low</option>
                                </select>
                            </div>

                            <!-- Product Price Sort Filter -->
                            <div class="col-12 col-md-6 col-lg-4 col-xl-2">
                                <label class="form-label small text-muted mb-1">Sort Product Price</label>
                                <select id="filterProductPriceSort" class="form-control form-control-solid">
                                    <option value="">Sort by Product Price</option>
                                    <option value="low_to_high">Price: Low to High</option>
                                    <option value="high_to_low">Price: High to Low</option>
                                </select>
                            </div>

                        </div>

                        <!-- Row 2: Action Controls -->
                        <div class="row mb-2 toolbar-row">
                            <div class="col-12">
                                <div class="d-flex align-items-center flex-wrap gap-3 p-2 toolbar-inner">
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        <div class="d-flex align-items-center gap-2 price-comparison-group">
                                            <label class="form-label small text-muted mb-0">Price Comparison</label>
                                            <select id="filterPriceComparison" class="form-control form-control-solid" style="min-width: 180px;" title="Compare your price with competitors (works with All Competitors or a specific competitor)">
                                                <option value="">All Prices</option>
                                                <option value="higher">Competitor Higher</option>
                                                <option value="lower">Competitor Lower</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-light btn-sm action-btn toolbar-btn border" id="resetFiltersBtn" title="Clear all filters">
                                            <i class="fas fa-undo-alt me-1"></i> Reset Filters
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center flex-wrap gap-3 ms-auto">
                                        <button type="button" class="btn btn-primary btn-sm action-btn toolbar-btn" id="addProductBtn">
                                            Add Product
                                        </button>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-primary btn-sm shadow-sm dropdown-toggle toolbar-btn" id="exportBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-download me-1"></i> Export
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="exportBtn">
                                                <li>
                                                    <h6 class="dropdown-header">Export Format</h6>
                                                </li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" id="exportCsv">
                                                        <i class="fas fa-file-csv me-2 text-primary"></i> Export as CSV
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" id="exportExcel">
                                                        <i class="fas fa-file-excel me-2 text-success"></i> Export as Excel
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" id="exportPdf">
                                                        <i class="fas fa-file-pdf me-2 text-danger"></i> Export as PDF
                                                    </a></li>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm shadow-sm action-btn toolbar-btn" id="importPriceUpdateBtn">
                                            <i class="fas fa-money-bill-wave me-1"></i> Import Price
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm text-white shadow-sm action-btn toolbar-btn" id="importBulkProductsBtn">
                                            <i class="fas fa-file-csv me-1"></i> Import Bulk Products
                                        </button>
                                        <div id="bulkActionBar" class="bulk-action-bar">
                                            <button type="button" class="btn btn-primary btn-sm toolbar-btn" id="bulkSyncPricingBtn">
                                                <i class="fas fa-sync me-1"></i> Get Pricing
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm toolbar-btn" id="bulkDeleteBtn">
                                                <i class="fas fa-trash-alt me-1"></i> Delete
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm toolbar-btn" id="clearBulkSelectionBtn">
                                                Clear Selection
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table4 p-25 bg-white mb-30 compact-table-wrap">
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table id="datatable" class="table mb-0 datatable">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center mobilenzo-column" style="width: 40px;">
                                                <input type="checkbox" id="selectAllProducts" class="form-check-input">
                                            </th>
                                            {{-- <th class="text-center">Id</th> --}}
                                            <th class="text-start mobilenzo-column">Product Name</th>
                                            <th class="text-center mobilenzo-column">Sku</th>
                                            <th class="text-center mobilenzo-column">Category</th>
                                            <th class="text-center mobilenzo-column">Price</th>
                                            <th class="text-center mobilenzo-column">Cost</th>
                                            @foreach ($competitors as $competitor)
                                            <th class="text-center competitor-column">{{ $competitor->shortname ?? $competitor->name }}</th>
                                            @endforeach
                                            <th class="text-center mobilenzo-column">Actions</th>
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
                <h5 class="modal-title">
                    <i class="fas fa-link me-2"></i>Assign Competitor Link
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveCompetitorLink">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for adding product --}}
<div class="modal fade modal-themed" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Odoo Product Entry Info -->
                <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">Odoo Product Entry</strong>
                        <small class="text-muted">Product data will be fetched from Odoo using SKU. At least one is required.</small>
                    </div>
                </div>

                <!-- Product Information Section -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-box me-2"></i>Product Information
                    </h6>

                    <div class="mb-3">
                        <label for="addProductSku" class="form-label fw-semibold">
                            SKU <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="addProductSku" placeholder="Enter Product SKU" required>
                        <div id="addProductSkuError" class="invalid-feedback" style="display:none;"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Enter Product SKU to fetch product data.
                        </small>
                    </div>

                    <div class="alert alert-warning d-flex align-items-start" role="alert" style="margin-top: 1rem;">
                        <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                        <small class="text-muted">Please provide SKU to fetch product from Odoo.</small>
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
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveAddProduct">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for editing product --}}
<div class="modal fade modal-themed" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Product Information Section -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-box me-2"></i>Product Information
                    </h6>

                    <!-- Product Name (Read Only) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Product Name
                        </label>
                        <input type="text" class="form-control" id="editProductName" readonly style="background-color: #f8f9fa;">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Product name is read-only.
                        </small>
                    </div>

                    <!-- SKU (Read Only) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            SKU
                        </label>
                        <input type="text" class="form-control" id="editProductSku" readonly style="background-color: #f8f9fa;">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>SKU is read-only.
                        </small>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label fw-semibold">
                            Price <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" class="form-control" id="editProductPrice" placeholder="Enter Price" required>
                        <div id="editProductPriceError" class="invalid-feedback" style="display:none;"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Update product price. Price will be synced to Odoo if changed.
                        </small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Category & Competitor Section -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-tags me-2"></i>Category & Competitor
                    </h6>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="editProductCategory" class="form-label fw-semibold">
                            Category
                        </label>
                        <select class="form-select" id="editProductCategory">
                            <option value="">Select Category (Optional)</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div id="editProductCategoryError" class="invalid-feedback" style="display:none;"></div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>Can't find your category? Contact administrator to add a new category.
                        </small>
                    </div>

                    <!-- Competitor URLs -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Competitor URLs
                        </label>
                        <small class="form-text text-muted d-block mb-2">
                            <i class="fas fa-info-circle me-1"></i>Update competitor URLs (optional). Price will be automatically scraped from these URLs.
                        </small>
                        <div id="editCompetitorUrlsContainer">
                            @foreach($competitors as $competitor)
                            <div class="mb-2 edit-competitor-url-row" data-competitor-id="{{ $competitor->id }}" data-competitor-website="{{ $competitor->website }}">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" title="{{ $competitor->name }}">
                                        <i class="fas fa-link"></i>
                                        <span class="ms-1">{{ $competitor->shortname ?? substr($competitor->name, 0, 3) }}</span>
                                    </span>
                                    <input type="url"
                                        class="form-control edit-competitor-url-input"
                                        id="editCompetitorUrl_{{ $competitor->id }}"
                                        data-competitor-id="{{ $competitor->id }}"
                                        placeholder="Paste {{ $competitor->shortname ?? $competitor->name }} link">
                                </div>
                                <div class="invalid-feedback edit-competitor-url-error" id="editCompetitorUrlError_{{ $competitor->id }}" style="display:none;"></div>
                            </div>
                            @endforeach
                        </div>
                        <div id="editCompetitorUrlsError" class="invalid-feedback" style="display:none;"></div>
                    </div>
                </div>

                <input type="hidden" id="editProductId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveEditProduct">
                    Save
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
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Price
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="text" class="form-control" id="modalPrice" placeholder="Enter Price">
                <input type="hidden" id="modalPriceProductId">
                <div id="modalPriceError" class="text-danger mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary savePriceBtn" id="savePrice">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Import Price Update --}}
<div class="modal fade" id="importPriceUpdateModal" tabindex="-1" role="dialog" aria-labelledby="importPriceUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Import Price Update
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">CSV Format Required</strong>
                        <small class="text-muted">Your CSV file should have the following columns: <strong>SKU, Price</strong></small>
                        <br><small class="text-muted mt-1 d-block">The first row should be the header row. Each subsequent row should contain SKU and Price values.</small>
                        <br><small class="text-muted mt-2 d-block">
                            <i class="fas fa-download me-1"></i>
                            <a href="{{ route('products.downloadPriceUpdateSample') }}" class="sample-file-link" download>
                                Download Sample File
                            </a>
                        </small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="priceUpdateFile" class="form-label fw-semibold">
                        Select CSV File
                    </label>
                    <input type="file" class="form-control" id="priceUpdateFile" accept=".csv,.txt">
                    <div id="priceUpdateFileError" class="invalid-feedback" style="display:none;"></div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>Maximum file size: 10MB. Supported formats: CSV, TXT
                    </small>
                </div>

                <div id="priceUpdateImportResults" style="display:none;" class="mt-4">
                    <div class="card border-0 shadow-sm" style="background: #f8f9fb;">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                <i class="fas fa-chart-line me-2 text-primary"></i>Import Results
                            </h6>
                            <div id="priceUpdateResultsContent"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitPriceUpdateImport">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Import Bulk Products --}}
<div class="modal fade" id="importBulkProductsModal" tabindex="-1" role="dialog" aria-labelledby="importBulkProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Import Bulk Products
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">CSV Format Required</strong>
                        <small class="text-muted">Your CSV file should have the following columns: <strong>SKU, Category, Competitor URL 1, Competitor URL 2, ...</strong></small>
                        <br><small class="text-muted mt-1 d-block">The first row should be the header row. Columns must be in order: <strong>SKU</strong> (required), <strong>Category</strong> (optional - must exist in system), then any number of <strong>Competitor URLs</strong>. The system will automatically match URLs to competitors based on domain. Products will be fetched from Odoo using the SKU.</small>
                        <br><small class="text-muted mt-2 d-block"><strong>Important:</strong> Categories must be created in the system before importing. If a category doesn't exist, the import will continue but the category won't be assigned to that product.</small>
                        <br><small class="text-muted mt-2 d-block">
                            <i class="fas fa-download me-1"></i>
                            <a href="{{ route('products.downloadBulkProductsSample') }}" class="sample-file-link" download>
                                Download Sample File
                            </a>
                        </small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bulkProductsFile" class="form-label fw-semibold">
                        Select CSV File
                    </label>
                    <input type="file" class="form-control" id="bulkProductsFile" accept=".csv,.txt">
                    <div id="bulkProductsFileError" class="invalid-feedback" style="display:none;"></div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>Maximum file size: 10MB. Supported formats: CSV, TXT
                    </small>
                </div>

                <div id="bulkProductsImportResults" style="display:none;" class="mt-4">
                    <div class="card border-0 shadow-sm" style="background: #f8f9fb;">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                <i class="fas fa-chart-line me-2 text-primary"></i>Import Results
                            </h6>
                            <div id="bulkProductsResultsContent"></div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitBulkProductsImport">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<div id="loadingIndicator"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgb(0 0 0 / 32%); z-index: 1040; display: flex; align-items: center; justify-content: center;">
    <div class="spinner-border text-danger" role="status"></div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor_assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jspdf/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('vendor_assets/js/jspdf/jspdf-autotable.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="{{ asset('vendor_assets/js/toastr/toastr.min.js') }}"></script>
<script>
    $(document).ready(function() {
        function showPageLoading() {
            document.getElementById("loadingIndicator").style.display = "flex";
        }

        function hidePageLoading() {
            document.getElementById("loadingIndicator").style.display = "none";
        }

        let selectedProductIds = new Set();

        function updateBulkActionBar() {
            const count = selectedProductIds.size;
            $('#selectedProductsCount').text(count);
            if (count > 0) {
            $('#bulkActionBar').addClass('active');
            } else {
            $('#bulkActionBar').removeClass('active');
            }
        }

        function syncSelectAllState() {
            const visibleCheckboxes = $('.row-product-checkbox');
            if (!visibleCheckboxes.length) {
                $('#selectAllProducts').prop('checked', false).prop('indeterminate', false);
                return;
            }

            const checkedVisible = visibleCheckboxes.filter(':checked').length;
            const allVisibleChecked = checkedVisible === visibleCheckboxes.length;
            const hasSomeChecked = checkedVisible > 0 && !allVisibleChecked;

            $('#selectAllProducts')
                .prop('checked', allVisibleChecked)
                .prop('indeterminate', hasSomeChecked);
        }

        function getSelectedProductIds() {
            return Array.from(selectedProductIds).map(function(id) {
                return Number(id);
            });
        }

        function reloadPageAfterSuccessToast(delayMs = 1200) {
            setTimeout(function() {
                window.location.reload();
            }, delayMs);
        }

        function updateProductCountInfo(tableApi) {
            var info = tableApi.page.info();
            var onCurrentPage = Math.max(0, info.end - info.start);
            var filteredCount = info.recordsDisplay || 0;
            var totalCount = info.recordsTotal || 0;

            var countText = 'On this page: <strong>' + onCurrentPage + '</strong> | Total: <strong>' + totalCount + '</strong>';
            if (filteredCount !== totalCount) {
                countText = 'On this page: <strong>' + onCurrentPage + '</strong> | Filtered: <strong>' + filteredCount + '</strong> | Total: <strong>' + totalCount + '</strong>';
            }

            $('.product-table-count-info').html(countText);
        }

        let table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            dom: 'rt<"bottom d-flex justify-content-between align-items-center flex-wrap gap-2"l<"product-table-count-info text-center flex-grow-1 small fw-semibold text-primary">p><"clear">',
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
                    data.filter_name = $('#filterName').val();
                    data.filter_sku = $('#filterSku').val();
                    data.category = $('#filterCategory').val();
                    data.competitor_id = $('#filterCompetitor').val();
                    data.price_sort = $('#filterPriceSort').val();
                    data.product_price_sort = $('#filterProductPriceSort').val();
                    data.price_comparison = $('#filterPriceComparison').val();
                },
                complete: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            },
            drawCallback: function() {
                $('.row-product-checkbox').each(function() {
                    const id = $(this).data('product-id');
                    $(this).prop('checked', selectedProductIds.has(String(id)));
                });
                syncSelectAllState();
                updateBulkActionBar();
                updateProductCountInfo(this.api());
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    className: 'text-center mobilenzo-column',
                    searchable: false,
                    width: '40px',
                    render: function(data) {
                        return `<input type="checkbox" class="form-check-input row-product-checkbox" data-product-id="${data}">`;
                    }
                },
                // { data: 'odoo_id', name: 'id', className: 'text-center', width: '60px' },
                {
                    data: 'name',
                    name: 'name',
                    className: 'text-start mobilenzo-column',
                    width: '250px'
                },
                {
                    data: 'default_code',
                    name: 'default_code',
                    className: 'text-center mobilenzo-column',
                    width: '120px'
                },
                {
                    data: 'category_display',
                    name: 'category_display',
                    className: 'text-center mobilenzo-column',
                    width: '150px',
                    render: function(data, type, row) {
                        // Return HTML as-is from server (already formatted as rectangle)
                        return data || '<span style="display: inline-block; padding: 6px 12px; border: 1px solid #6c757d; border-radius: 4px; background-color: transparent; color: #6c757d; font-size: 12px; font-weight: 500; text-align: center; min-width: 80px;">No Category</span>';
                    }
                },
                {
                    data: 'list_price',
                    name: 'list_price',
                    className: 'text-center mobilenzo-column',
                    render: function(data, type, row) {
                        // Format price with proper number formatting
                        const ourPrice = parseFloat(data) || 0;
                        const formattedPrice = ourPrice > 0 ? ourPrice.toFixed(2) : '0.00';

                        // Check all competitor prices to determine if we're winning or losing
                        let priceColorClass = 'text-primary'; // default
                        let hasCompetitorPrices = false;
                        let isLowerThanAny = false;
                        let isHigherThanAny = false;

                        @foreach ($competitors as $competitor)
                        {
                            const competitorPrice{{ $competitor->id }} = parseFloat(row.competitor_price_{{ $competitor->id }}) || 0;
                            if (competitorPrice{{ $competitor->id }} > 0 && ourPrice > 0) {
                                hasCompetitorPrices = true;
                                if (ourPrice < competitorPrice{{ $competitor->id }}) {
                                    isLowerThanAny = true; // We're lower (winning) - Green
                                }
                                if (ourPrice > competitorPrice{{ $competitor->id }}) {
                                    isHigherThanAny = true; // We're higher (losing) - Red
                                }
                            }
                        }
                        @endforeach

                        // Determine color: Green if we're lower than any competitor, Red if we're higher than any
                        if (hasCompetitorPrices) {
                            if (isLowerThanAny) {
                                priceColorClass = 'text-success'; // We're winning (lower than at least one competitor) - Green
                            } else if (isHigherThanAny) {
                                priceColorClass = 'text-danger'; // We're losing (higher than at least one competitor) - Red
                            }
                        }

                        return `
                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                            <span class="fw-bold ${priceColorClass}" style="font-size: 1rem; min-width: 60px;">$${formattedPrice}</span>
                            <a href="javascript:void(0)" class="text-primary edit-price-btn" 
                               data-product-id="${row.odoo_id}" 
                               data-current-price="${data}"
                               title="Edit Price"
                               style="opacity: 0.7; transition: opacity 0.2s; margin-left: 0.5rem;">
                                <i class="fas fa-edit fs-6"></i>
                            </a>
                        </div>`;
                    }
                },
                {
                    data: 'cost',
                    name: 'cost',
                    className: 'text-center mobilenzo-column',
                    render: function(data, type, row) {
                        // Format cost with proper number formatting
                        const cost = parseFloat(data) || 0;
                        const formattedCost = cost > 0 ? cost.toFixed(2) : '0.00';
                        return `<span class="fw-bold" style="font-size: 1rem; min-width: 60px;">$${formattedCost}</span>`;
                    }
                },
                @foreach ($competitors as $competitor)
                {
                    data: 'competitor_link_{{ $competitor->id }}',
                    name: 'competitor_link_{{ $competitor->id }}',
                    className: 'text-center competitor-column',
                    render: function(data, type, row) {
                        const competitorLink = data || '';
                        const competitorPrice = parseFloat(row.competitor_price_{{ $competitor->id }}) || 0;
                        const formattedPrice = competitorPrice > 0 ? competitorPrice.toFixed(2) : '0.00';

                        // Display price and link icon in same row with space - same format as main price column
                        return `
                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                            <span class="fw-bold" style="font-size: 1rem; min-width: 60px;">$${formattedPrice}</span>
                            <a href="javascript:void(0)" class="add-link-btn ${competitorLink ? 'text-primary' : 'text-muted'}"
                               data-row-id="{{ $competitor->id }}" 
                               data-product-id="${row.id}" 
                               data-current-link="${competitorLink}"
                               data-competitor-name="{{ $competitor->name }}"
                               data-competitor-shortname="{{ $competitor->shortname }}"
                               data-competitor-website="{{ $competitor->website }}"
                               title="${competitorLink ? 'Edit {{ $competitor->shortname ?? $competitor->name }} Link (Click to edit, Ctrl+Click to open)' : 'Add {{ $competitor->shortname ?? $competitor->name }} Link'}"
                               style="opacity: 0.7; transition: opacity 0.2s; margin-left: 0.5rem;">
                                <i class="fas fa-link fs-6"></i>
                            </a>
                        </div>`;
                    }
                },
                @endforeach
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center mobilenzo-column',
                    searchable: false,
                    width: '60px'
                },
            ]
        });

        $('#filterName, #filterSku').on('keyup', function() {
            table.ajax.reload();
        });

        $(document).on('change', '.row-product-checkbox', function() {
            const productId = String($(this).data('product-id'));
            if ($(this).is(':checked')) {
                selectedProductIds.add(productId);
            } else {
                selectedProductIds.delete(productId);
            }
            syncSelectAllState();
            updateBulkActionBar();
        });

        $(document).on('change', '#selectAllProducts', function() {
            const isChecked = $(this).is(':checked');
            $('.row-product-checkbox').each(function() {
                const productId = String($(this).data('product-id'));
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    selectedProductIds.add(productId);
                } else {
                    selectedProductIds.delete(productId);
                }
            });
            syncSelectAllState();
            updateBulkActionBar();
        });

        $(document).on('click', '#clearBulkSelectionBtn', function() {
            selectedProductIds.clear();
            $('.row-product-checkbox').prop('checked', false);
            syncSelectAllState();
            updateBulkActionBar();
        });

        $(document).on('click', '#bulkSyncPricingBtn', function() {
            const ids = getSelectedProductIds();
            if (!ids.length) {
                toastr.warning('Please select at least one product.');
                return;
            }

            showPageLoading();
            $.post("{{ route('products.bulkSyncPricing') }}", {
                _token: "{{ csrf_token() }}",
                product_ids: ids
            }).done(function(response) {
                hidePageLoading();
                if (response.success) {
                    const summary = response.summary || {};
                    toastr.success(
                        `Pricing updated. Source: ${summary.source_updated || 0}, Competitors: ${summary.competitor_prices_updated || 0}, Failed: ${summary.failed_products || 0}`
                    );
                    reloadPageAfterSuccessToast();
                } else {
                    toastr.error(response.message || 'Bulk pricing sync failed.');
                }
            }).fail(function(xhr) {
                hidePageLoading();
                toastr.error(xhr.responseJSON?.message || 'Bulk pricing sync failed.');
            });
        });

        $(document).on('click', '#bulkDeleteBtn', function() {
            const ids = getSelectedProductIds();
            if (!ids.length) {
                toastr.warning('Please select at least one product.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `Delete ${ids.length} selected product(s)? This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                reverseButtons: true,
                focusCancel: true
            }).then(function(result) {
                if (!result.isConfirmed) {
                    selectedProductIds.clear();
                    $('.row-product-checkbox').prop('checked', false);
                    syncSelectAllState();
                    updateBulkActionBar();
                    return;
                }

                showPageLoading();
                $.post("{{ route('products.bulkDelete') }}", {
                    _token: "{{ csrf_token() }}",
                    product_ids: ids
                }).done(function(response) {
                    hidePageLoading();
                    if (response.success) {
                        selectedProductIds.clear();
                        updateBulkActionBar();
                        toastr.success(response.message || 'Products deleted successfully.');
                        reloadPageAfterSuccessToast();
                    } else {
                        toastr.error(response.message || 'Bulk delete failed.');
                    }
                }).fail(function(xhr) {
                    hidePageLoading();
                    toastr.error(xhr.responseJSON?.message || 'Bulk delete failed.');
                });
            });
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
                $('#filterProductPriceSort').prop('disabled', false);
                return;
            }
            // Avoid confusion: if competitor price sort is selected, product price sort is ignored
            if ($(this).val()) {
                $('#filterProductPriceSort').val('').prop('disabled', true);
            } else {
                $('#filterProductPriceSort').prop('disabled', false);
            }
            table.ajax.reload();
        });

        $('#filterProductPriceSort').on('change', function() {
            // Avoid confusion: if product price sort is selected, competitor price sort is ignored
            if ($(this).val()) {
                $('#filterPriceSort').val('').prop('disabled', true);
            } else {
                $('#filterPriceSort').prop('disabled', false);
            }
            table.ajax.reload();
        });

        $('#filterPriceComparison').on('change', function() {
            // Price comparison now works with "All Competitors" or a specific competitor
            table.ajax.reload();
        });

        $('#resetFiltersBtn').on('click', function() {
            $('#filterName').val('');
            $('#filterSku').val('');
            $('#filterCategory').val('');
            $('#filterCompetitor').val('');

            $('#filterPriceSort').val('').prop('disabled', false);
            $('#filterProductPriceSort').val('').prop('disabled', false);
            $('#filterPriceComparison').val('');

            // Clear selection state (optional, but keeps UX consistent)
            selectedProductIds.clear();
            $('.row-product-checkbox').prop('checked', false);
            syncSelectAllState();
            updateBulkActionBar();

            table.ajax.reload();
        });

        // Clear name filter function
        window.clearNameFilter = function() {
            $('#filterName').val('');
            table.ajax.reload();
        };

        // Clear SKU filter function
        window.clearSkuFilter = function() {
            $('#filterSku').val('');
            table.ajax.reload();
        };

        // Export functionality - fetch all data and export
        function getExportData(callback) {
            showPageLoading();
            // Get current filter values
            var filters = {
                filter_name: $('#filterName').val(),
                filter_sku: $('#filterSku').val(),
                category: $('#filterCategory').val(),
                competitor_id: $('#filterCompetitor').val(),
                price_sort: $('#filterPriceSort').val(),
                product_price_sort: $('#filterProductPriceSort').val(),
                price_comparison: $('#filterPriceComparison').val(),
                _token: "{{ csrf_token() }}",
                export: true,
                length: -1 // Get all records
            };

            $.ajax({
                url: "{{ route('products.list') }}",
                type: 'GET',
                data: filters,
                success: function(response) {
                    hidePageLoading();
                    if (response.data && response.data.length > 0) {
                        callback(response.data);
                    } else {
                        toastr.warning('No data to export');
                    }
                },
                error: function() {
                    hidePageLoading();
                    toastr.error('Failed to fetch data for export');
                }
            });
        }

        function stripHtml(html) {
            if (!html) return '';
            var tmp = document.createElement('DIV');
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || '';
        }

        function getCategoryForExport(row) {
            var categoryName = (row.category_name || '').toString().trim();
            if (categoryName) {
                return categoryName;
            }

            var categoryFromDisplay = stripHtml(row.category_display || '').replace(/\s+/g, ' ').trim();
            if (categoryFromDisplay) {
                return categoryFromDisplay;
            }

            return 'No Category';
        }

        function exportToCsv(data) {
            var csv = [];
            var headers = ['Product Name', 'SKU', 'Category', 'Price', 'Cost'];

            // Add competitor columns
            @foreach($competitors as $competitor)
            headers.push('{{ $competitor->shortname ?? $competitor->name }}');
            @endforeach

            csv.push(headers.join(','));

            data.forEach(function(row) {
                var values = [
                    '"' + (row.name || '').replace(/"/g, '""') + '"',
                    '"' + (row.default_code || '').replace(/"/g, '""') + '"',
                    '"' + getCategoryForExport(row).replace(/"/g, '""') + '"',
                    row.list_price || '0.00',
                    row.cost || '0.00'
                ];

                @foreach($competitors as $competitor)
                values.push(row.competitor_price_{{ $competitor->id }} || '0.00');
                @endforeach

                csv.push(values.join(','));
            });

            var csvContent = csv.join('\n');
            var blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'products_' + new Date().toISOString().split('T')[0] + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportToExcel(data) {
            if (typeof XLSX === 'undefined') {
                toastr.error('Excel library not loaded. Please refresh and try again.');
                return;
            }

            var headers = ['Product Name', 'SKU', 'Category', 'Price', 'Cost'];
            @foreach($competitors as $competitor)
            headers.push('{{ $competitor->shortname ?? $competitor->name }}');
            @endforeach

            var sheetRows = [headers];
            data.forEach(function(row) {
                var rowValues = [
                    row.name || '',
                    row.default_code || '',
                    getCategoryForExport(row),
                    parseFloat(row.list_price) || 0,
                    parseFloat(row.cost) || 0
                ];

                @foreach($competitors as $competitor)
                rowValues.push(parseFloat(row.competitor_price_{{ $competitor->id }}) || 0);
                @endforeach

                sheetRows.push(rowValues);
            });

            var ws = XLSX.utils.aoa_to_sheet(sheetRows);

            // Column widths in characters for better Excel/LibreOffice display.
            var colWidths = [{
                    wch: 55
                }, // Product Name
                {
                    wch: 28
                }, // SKU
                {
                    wch: 24
                }, // Category
                {
                    wch: 12
                }, // Price
                {
                    wch: 12
                } // Cost
            ];
            @foreach($competitors as $competitor)
            colWidths.push({
                wch: 12
            });
            @endforeach
            ws['!cols'] = colWidths;

            // Center header row text.
            for (var h = 0; h < headers.length; h++) {
                var headerRef = XLSX.utils.encode_col(h) + '1';
                if (ws[headerRef]) {
                    ws[headerRef].s = {
                        alignment: {
                            horizontal: 'center',
                            vertical: 'center'
                        },
                        font: {
                            bold: true
                        }
                    };
                }
            }

            // Apply number format to price/cost and competitor price columns.
            for (var r = 1; r < sheetRows.length; r++) {
                // Price (D) and Cost (E)
                ['D', 'E'].forEach(function(col) {
                    var ref = col + (r + 1);
                    if (ws[ref]) ws[ref].z = '0.00';
                });

                var competitorStartIndex = 5; // 0-based, starts after cost
                var competitorColumnsCount = headers.length - competitorStartIndex;
                for (var c = 0; c < competitorColumnsCount; c++) {
                    var colLetter = XLSX.utils.encode_col(competitorStartIndex + c);
                    var cellRef = colLetter + (r + 1);
                    if (ws[cellRef]) ws[cellRef].z = '0.00';
                }
            }

            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Products');
            XLSX.writeFile(wb, 'products_' + new Date().toISOString().split('T')[0] + '.xlsx', {
                cellStyles: true
            });
        }

        function exportToPdf(data) {
            try {
                // Check if jsPDF is available - UMD version exports to window.jspdf
                let jsPDF;
                if (typeof window.jspdf !== 'undefined') {
                    // UMD version
                    jsPDF = window.jspdf.jsPDF;
                } else if (typeof window.jsPDF !== 'undefined') {
                    // Global version
                    jsPDF = window.jsPDF;
                } else {
                    toastr.error('PDF library not loaded. Please refresh the page and try again.');
                    return;
                }

                // Create PDF in landscape orientation
                const doc = new jsPDF('landscape', 'mm', 'a4');

                // Prepare table data
                var headers = [
                    ['Product Name', 'SKU', 'Category', 'Price', 'Cost']
                ];

                @foreach($competitors as $competitor)
                headers[0].push('{{ $competitor->shortname ?? $competitor->name }}');
                @endforeach

                var tableData = [];
                data.forEach(function(row) {
                    var rowData = [
                        (row.name || '').substring(0, 30), // Limit length
                        row.default_code || '',
                        getCategoryForExport(row).substring(0, 20),
                        '$' + (parseFloat(row.list_price) || 0).toFixed(2),
                        '$' + (parseFloat(row.cost) || 0).toFixed(2)
                    ];

                    @foreach($competitors as $competitor)
                    rowData.push('$' + (parseFloat(row.competitor_price_{{ $competitor->id }}) || 0).toFixed(2));
                    @endforeach

                    tableData.push(rowData);
                });

                const pageWidth = doc.internal.pageSize.getWidth();
                const contentStartY = 15;

                // Add centered title and date without logo.
                doc.setFontSize(16);
                doc.setFont(undefined, 'bold');
                const titleText = 'Products Export';
                const titleWidth = doc.getTextWidth(titleText);
                doc.text(titleText, (pageWidth - titleWidth) / 2, contentStartY);

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');
                const dateText = 'Generated: ' + new Date().toLocaleDateString();
                const dateWidth = doc.getTextWidth(dateText);
                doc.text(dateText, (pageWidth - dateWidth) / 2, contentStartY + 7);

                var tableStartY = contentStartY + 13;

                    // Add table using autoTable if available, otherwise use manual table
                    if (typeof doc.autoTable !== 'undefined') {
                        doc.autoTable({
                            head: headers,
                            body: tableData,
                            startY: tableStartY,
                            styles: {
                                fontSize: 7,
                                cellPadding: 2,
                                halign: 'center',
                                valign: 'middle'
                            },
                            headStyles: {
                                fillColor: [66, 139, 202],
                                textColor: 255,
                                fontStyle: 'bold',
                                halign: 'center'
                            },
                            alternateRowStyles: {
                                fillColor: [245, 245, 245]
                            },
                            margin: {
                                top: tableStartY,
                                left: 14,
                                right: 14
                            },
                            tableWidth: pageWidth - 28
                        });
                    } else {
                        // Manual table creation without autoTable (centered table)
                        doc.setFontSize(8);
                        var y = tableStartY;
                        var availableWidth = pageWidth - 28;
                        var colWidth = Math.min(35, availableWidth / headers[0].length);
                        var tableWidth = colWidth * headers[0].length;
                        var startX = (pageWidth - tableWidth) / 2;

                        // Draw header background
                        doc.setFillColor(66, 139, 202);
                        doc.rect(startX, y - 5, tableWidth, 6, 'F');

                        // Header text
                        doc.setTextColor(255, 255, 255);
                        doc.setFont(undefined, 'bold');
                        headers[0].forEach(function(header, i) {
                            doc.text(header.substring(0, 12), startX + (i * colWidth) + (colWidth / 2), y, {
                                align: 'center'
                            });
                        });

                        // Data rows
                        doc.setTextColor(0, 0, 0);
                        doc.setFont(undefined, 'normal');
                        y += 8;
                        tableData.forEach(function(row, rowIndex) {
                            if (rowIndex % 2 === 0) {
                                doc.setFillColor(245, 245, 245);
                                doc.rect(startX, y - 4, tableWidth, 5, 'F');
                            }

                            row.forEach(function(cell, i) {
                                doc.text(cell.toString().substring(0, 12), startX + (i * colWidth) + (colWidth / 2), y, {
                                    align: 'center'
                                });
                            });
                            y += 6;

                            if (y > 190) {
                                doc.addPage();
                                y = 20;
                            }
                        });
                    }

                // Save PDF
                var filename = 'products_' + new Date().toISOString().split('T')[0] + '.pdf';
                doc.save(filename);
                toastr.success('PDF exported successfully!');
            } catch (error) {
                console.error('PDF export error:', error);
                toastr.error('Failed to generate PDF. Please try again.');
            }
        }

        // Export button handlers
        $('#exportCsv').on('click', function() {
            getExportData(exportToCsv);
        });

        $('#exportExcel').on('click', function() {
            getExportData(exportToExcel);
        });

        $('#exportPdf').on('click', function() {
            getExportData(exportToPdf);
        });

        // Add Product Modal
        $(document).on('click', '#addProductBtn', function() {
            // Reset form fields
            $('#addProductOdooId').val('');
            $('#addProductSku').val('');
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
            $('#addProductOdooId').val('');
            $('#addProductSku').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        });

        // Clear Odoo ID error when user starts typing
        $(document).on('input', '#addProductOdooId', function() {
            $('#addProductOdooIdError').hide();
            $(this).removeClass('is-invalid');
        });

        // Clear SKU error when user starts typing
        $(document).on('input', '#addProductSku', function() {
            $('#addProductSkuError').hide();
            $(this).removeClass('is-invalid');
        });

        $(document).on('click', '#saveAddProduct', function() {
            var $btn = $(this);
            var odooId = $('#addProductOdooId').val() ? $('#addProductOdooId').val().trim() : '';
            var sku = $('#addProductSku').val() ? $('#addProductSku').val().trim() : '';
            var categoryId = $('#addProductCategory').val();

            // Reset errors
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');

            var hasError = false;

            // Validate that at least one of Odoo ID or SKU is provided
            if ((!odooId || odooId === '') && (!sku || sku === '')) {
                $('#addProductOdooIdError').text('Either Odoo ID or SKU is required').show();
                $('#addProductOdooId').addClass('is-invalid');
                $('#addProductSkuError').text('Either Odoo ID or SKU is required').show();
                $('#addProductSku').addClass('is-invalid');
                hasError = true;
            } else {
                // Validate Odoo ID format if provided
                if (odooId && odooId !== '' && isNaN(odooId)) {
                    $('#addProductOdooIdError').text('Odoo ID must be a number').show();
                    $('#addProductOdooId').addClass('is-invalid');
                    hasError = true;
                }
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
                _token: "{{ csrf_token() }}"
            };

            // Add odoo_id or sku (at least one is required)
            if (odooId && odooId !== '') {
                postData.odoo_id = odooId;
            }
            if (sku && sku !== '') {
                postData.sku = sku;
            }

            // Add optional fields only if provided
            if (categoryId) {
                postData.category_id = categoryId;
            }
            if (competitorUrls.length > 0) {
                postData.competitor_urls = competitorUrls;
            }

            $.post("{{ route('products.store') }}", postData).done(function(response) {
                hidePageLoading();
                $btn.prop('disabled', false).text('Save');
                if (response.success) {
                    var message = response.message;
                    if (response.product && response.product.odoo_id) {
                        message += ' (Odoo ID: ' + response.product.odoo_id + ')';
                    }
                    toastr.success(message);
                    $('#addProductModal').modal('hide');
                    // Reset form
                    $('#addProductModal form')[0]?.reset();
                    $('#addProductOdooId').val('');
                    $('#addProductSku').val('');
                    $('.competitor-url-input').val('');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message);
                    // Show error inline next to appropriate field
                    if (response.message && (response.message.toLowerCase().includes('not found') || response.message.toLowerCase().includes('odoo') || response.message.toLowerCase().includes('sku'))) {
                        // Determine which field to show error on based on what was provided
                        if (odooId && odooId !== '') {
                            $('#addProductOdooIdError').text(response.message).show();
                            $('#addProductOdooId').addClass('is-invalid');
                        } else if (sku && sku !== '') {
                            $('#addProductSkuError').text(response.message).show();
                            $('#addProductSku').addClass('is-invalid');
                        } else {
                            // Show on both if neither was provided
                            $('#addProductOdooIdError').text(response.message).show();
                            $('#addProductOdooId').addClass('is-invalid');
                            $('#addProductSkuError').text(response.message).show();
                            $('#addProductSku').addClass('is-invalid');
                        }
                    }
                }
            }).fail(function(xhr) {
                hidePageLoading();
                $btn.prop('disabled', false).text('Add Product');
                var errorMessage = 'Failed to add product';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    toastr.error(errorMessage);
                    // Show error inline next to appropriate field
                    if (errorMessage.toLowerCase().includes('not found') || errorMessage.toLowerCase().includes('odoo') || errorMessage.toLowerCase().includes('sku') || errorMessage.toLowerCase().includes('required')) {
                        // Determine which field to show error on based on what was provided
                        if (odooId && odooId !== '') {
                            $('#addProductOdooIdError').text(errorMessage).show();
                            $('#addProductOdooId').addClass('is-invalid');
                        } else if (sku && sku !== '') {
                            $('#addProductSkuError').text(errorMessage).show();
                            $('#addProductSku').addClass('is-invalid');
                        } else {
                            // Show on both if neither was provided
                            $('#addProductOdooIdError').text(errorMessage).show();
                            $('#addProductOdooId').addClass('is-invalid');
                            $('#addProductSkuError').text(errorMessage).show();
                            $('#addProductSku').addClass('is-invalid');
                        }
                    }
                } else {
                    toastr.error(errorMessage);
                }
            });
        });

        // Prevent double-click on edit button
        let editButtonProcessing = false;

        $(document).on("click", ".edit-product-btn", function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();

            // Prevent double-click
            if (editButtonProcessing) {
                return false;
            }

            const $btn = $(this);

            // Check if modal is already open
            if ($('#editProductModal').hasClass('show')) {
                return false;
            }

            // Disable button temporarily to prevent double-click
            if ($btn.prop('disabled')) {
                return false;
            }

            editButtonProcessing = true;

            const productId = $btn.data('product-id');
            const productName = $btn.data('product-name');
            const productPrice = $btn.data('product-price');
            const productSku = $btn.data('product-sku') || '';
            const categoryId = $btn.data('product-category-id') || '';

            // Set product info immediately
            $('#editProductId').val(productId);
            $('#editProductSku').val(productSku);
            $('#editProductName').val(productName);
            $('#editProductPrice').val(productPrice);
            $('#editProductCategory').val(categoryId);

            // Reset errors
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');
            $('.edit-competitor-url-input').val('');

            // Show modal immediately
            $('#editProductModal').modal('show');

            // Load competitor URLs in background after modal is shown
            $.get("{{ route('products.getCompetitorUrls') }}", {
                product_id: productId,
                _token: "{{ csrf_token() }}"
            }).done(function(response) {
                if (response.success && response.urls) {
                    // Populate competitor URLs
                    response.urls.forEach(function(urlData) {
                        const input = $('#editCompetitorUrl_' + urlData.competitor_id);
                        if (input.length) {
                            input.val(urlData.url || '');
                        }
                    });
                }
            }).fail(function() {
                // Continue even if loading URLs fails
                console.warn('Failed to load competitor URLs');
            }).always(function() {
                // Re-enable button after data is loaded
                editButtonProcessing = false;
                $btn.prop('disabled', false);
            });

            // Refresh feather icons after modal is shown
            if (typeof feather !== 'undefined') {
                setTimeout(function() {
                    feather.replace();
                }, 100);
            }

            return false;
        });

        $(document).on('click', '#saveEditProduct', function() {
            var $btn = $(this);
            var productId = $('#editProductId').val();
            var categoryId = $('#editProductCategory').val();
            var price = $('#editProductPrice').val();

            // Reset errors
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');

            var hasError = false;

            // Validate price
            if (price && (isNaN(price) || Number(price) < 0)) {
                $('#editProductPriceError').text('Please enter a valid price').show();
                $('#editProductPrice').addClass('is-invalid');
                hasError = true;
            }

            // Collect and validate competitor URLs
            var competitorUrls = [];
            $('.edit-competitor-url-input').each(function() {
                var $input = $(this);
                var url = $input.val().trim();
                var competitorId = $input.data('competitor-id');
                var $row = $input.closest('.edit-competitor-url-row');
                var competitorWebsite = $row.data('competitor-website');
                var $errorDiv = $('#editCompetitorUrlError_' + competitorId);

                $errorDiv.hide();
                $input.removeClass('is-invalid');

                if (url) {
                    try {
                        new URL(url);
                    } catch (e) {
                        $errorDiv.text('Invalid URL format').show();
                        $input.addClass('is-invalid');
                        hasError = true;
                        return;
                    }

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

            $btn.prop('disabled', true).text('Saving...');
            showPageLoading();

            var postData = {
                _token: "{{ csrf_token() }}",
                id: productId,
                category_id: categoryId || null,
                list_price: price || null,
                competitor_urls: competitorUrls
            };

            $.post("{{ route('products.update') }}", postData).done(function(response) {
                hidePageLoading();
                $btn.prop('disabled', false).text('Save');
                if (response.success) {
                    toastr.success(response.message || 'Product updated successfully');
                    $('#editProductModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Error updating product');
                }
            }).fail(function(xhr) {
                hidePageLoading();
                $btn.prop('disabled', false).text('Save');
                var errorMessage = 'Error updating product';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
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

        $(document).on('click', '.add-link-btn', function(e) {
            // Allow Ctrl+Click or Cmd+Click to open link in new tab
            if (e.ctrlKey || e.metaKey) {
                const link = $(this).data('current-link');
                if (link) {
                    window.open(link, '_blank');
                }
                return;
            }

            // Normal click opens edit modal
            e.preventDefault();
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
            const $btn = $(this);
            const defaultBtnHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...');
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
                $btn.prop('disabled', false).html(defaultBtnHtml);
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
                        $btn.prop('disabled', false).html(defaultBtnHtml);
                        return;
                    }
                } catch (e) {
                    $('#modalCompetitorLinkError').text('Invalid URL format').show();
                    $('#modalCompetitorLink').addClass('is-invalid');
                    hidePageLoading();
                    $btn.prop('disabled', false).html(defaultBtnHtml);
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
            }).always(function() {
                $btn.prop('disabled', false).html(defaultBtnHtml);
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

        // Import Price Update Modal Handlers
        $(document).on('click', '#importPriceUpdateBtn', function() {
            $('#priceUpdateFile').val('');
            $('#priceUpdateFileError').hide();
            $('#priceUpdateImportResults').hide();
            $('#importPriceUpdateModal').modal('show');
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        $(document).on('click', '#submitPriceUpdateImport', function() {
            const fileInput = $('#priceUpdateFile')[0];
            const file = fileInput.files[0];

            // Reset errors
            $('#priceUpdateFileError').hide();
            $('#priceUpdateFile').removeClass('is-invalid');

            // Validate file
            if (!file) {
                $('#priceUpdateFileError').text('Please select a CSV file').show();
                $('#priceUpdateFile').addClass('is-invalid');
                return;
            }

            // Validate file type
            const validExtensions = ['csv', 'txt'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!validExtensions.includes(fileExtension)) {
                $('#priceUpdateFileError').text('Please select a valid CSV or TXT file').show();
                $('#priceUpdateFile').addClass('is-invalid');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                $('#priceUpdateFileError').text('File size must be less than 10MB').show();
                $('#priceUpdateFile').addClass('is-invalid');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Disable button and show loading
            const $btn = $(this);
            $btn.prop('disabled', true).html('Importing... <i class="fas fa-spinner fa-spin ms-3"></i>');
            showPageLoading();

            // Submit form
            $.ajax({
                url: "{{ route('products.importPriceUpdate') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hidePageLoading();
                    $btn.prop('disabled', false).html('Import Prices');

                    if (response.success) {
                        if (response.results.success > 0) {
                            toastr.success(response.message);
                        } else {
                            toastr.warning(response.message);
                        }

                        // Show results with better UI
                        let resultsHtml = '<div class="row g-3">';

                        // Success count
                        if (response.results.success > 0) {
                            resultsHtml += '<div class="col-12">';
                            resultsHtml += '<div class="alert alert-success d-flex align-items-center mb-0" style="border-left: 4px solid #28a745;">';
                            resultsHtml += '<i class="fas fa-check-circle me-2" style="font-size: 1.2rem;"></i>';
                            resultsHtml += '<div><strong>Success:</strong> ' + response.results.success + ' price(s) updated successfully</div>';
                            resultsHtml += '</div></div>';
                        }

                        // Failed count
                        if (response.results.failed > 0) {
                            resultsHtml += '<div class="col-12">';
                            resultsHtml += '<div class="alert alert-danger d-flex align-items-center mb-0" style="border-left: 4px solid #dc3545;">';
                            resultsHtml += '<i class="fas fa-exclamation-circle me-2" style="font-size: 1.2rem;"></i>';
                            resultsHtml += '<div><strong>Failed:</strong> ' + response.results.failed + ' price(s) failed to update</div>';
                            resultsHtml += '</div></div>';
                        }

                        // Error details
                        if (response.results.errors && response.results.errors.length > 0) {
                            resultsHtml += '<div class="col-12">';
                            resultsHtml += '<div class="alert alert-warning mb-0" style="border-left: 4px solid #ffc107;">';
                            resultsHtml += '<div class="d-flex align-items-center mb-2">';
                            resultsHtml += '<i class="fas fa-exclamation-triangle me-2" style="font-size: 1.1rem;"></i>';
                            resultsHtml += '<strong>Error Details (' + response.results.errors.length + '):</strong>';
                            resultsHtml += '</div>';
                            resultsHtml += '<div style="max-height: 250px; overflow-y: auto; background: #fff; border-radius: 4px; padding: 0.75rem;">';
                            resultsHtml += '<ul class="mb-0" style="list-style: none; padding-left: 0;">';
                            response.results.errors.forEach(function(error, index) {
                                resultsHtml += '<li class="mb-2 pb-2" style="border-bottom: ' + (index < response.results.errors.length - 1 ? '1px solid #e9ecef' : 'none') + ';">';
                                resultsHtml += '<span class="badge bg-secondary me-2" style="font-size: 0.75rem;">Row</span>';
                                resultsHtml += '<span style="font-size: 0.875rem; color: #495057;">' + error + '</span>';
                                resultsHtml += '</li>';
                            });
                            resultsHtml += '</ul></div></div></div>';
                        }

                        resultsHtml += '</div>';

                        $('#priceUpdateResultsContent').html(resultsHtml);
                        $('#priceUpdateImportResults').show();

                        // Reload table
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Import failed');
                        if (response.results && response.results.errors && response.results.errors.length > 0) {
                            let errorHtml = '<div class="alert alert-danger"><strong>Errors:</strong><ul class="mb-0 mt-2">';
                            response.results.errors.forEach(function(error) {
                                errorHtml += '<li>' + error + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            $('#priceUpdateResultsContent').html(errorHtml);
                            $('#priceUpdateImportResults').show();
                        }
                    }
                },
                error: function(xhr) {
                    hidePageLoading();
                    $btn.prop('disabled', false).html('Import Prices');

                    let errorMessage = 'Failed to import prices';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorList = [];
                            for (let field in errors) {
                                if (Array.isArray(errors[field])) {
                                    errorList.push(...errors[field]);
                                } else {
                                    errorList.push(errors[field]);
                                }
                            }
                            errorMessage = errorList.length > 0 ? errorList.join(', ') : errorMessage;
                        }
                    } else if (xhr.responseText) {
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            errorMessage = errorData.message || errorMessage;
                        } catch (e) {
                            if (xhr.status === 0) {
                                errorMessage = 'Network error. Please check your connection and try again.';
                            } else if (xhr.status === 404) {
                                errorMessage = 'Import endpoint not found. Please refresh the page and try again.';
                            } else if (xhr.status === 500) {
                                const responseText = xhr.responseText || '';
                                if (responseText.includes('message') || responseText.includes('error')) {
                                    errorMessage = 'Server error occurred. Please check the file format and try again.';
                                } else {
                                    errorMessage = 'Server error occurred. Please try again later.';
                                }
                            } else if (xhr.status === 422) {
                                errorMessage = 'Validation error. Please check your file format and try again.';
                            } else if (xhr.status === 413) {
                                errorMessage = 'File size too large. Maximum file size is 10MB.';
                            } else if (xhr.status === 415) {
                                errorMessage = 'Invalid file type. Please upload a CSV or TXT file.';
                            } else if (xhr.status >= 400 && xhr.status < 500) {
                                errorMessage = 'Request error. Please check your file and try again.';
                            } else {
                                errorMessage = 'An error occurred while importing. Please try again.';
                            }
                        }
                    } else {
                        if (xhr.status === 0) {
                            errorMessage = 'Network error. Please check your connection and try again.';
                        } else if (xhr.status >= 500) {
                            errorMessage = 'Server error occurred. Please try again later.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Validation error. Please check your file format and try again.';
                        } else if (xhr.status === 413) {
                            errorMessage = 'File size too large. Maximum file size is 10MB.';
                        } else if (xhr.status === 415) {
                            errorMessage = 'Invalid file type. Please upload a CSV or TXT file.';
                        }
                    }

                    toastr.error(errorMessage);

                    $('#priceUpdateResultsContent').html('<div class="alert alert-danger d-flex align-items-center" style="border-left: 4px solid #dc3545;"><i class="fas fa-exclamation-circle me-2" style="font-size: 1.2rem;"></i><div><strong>Error:</strong> ' + errorMessage + '</div></div>');
                    $('#priceUpdateImportResults').show();
                }
            });
        });

        // Import Bulk Products Modal Handlers
        let activeBulkImportJobId = null;
        let bulkImportPollingTimer = null;
        let showAllBulkImportJobs = false;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderImportJobRow(job) {
            const statusBadgeClass = job.status === 'completed' ?
                'bg-success' :
                (job.status === 'failed' ? 'bg-danger' : (job.status === 'processing' ? 'bg-primary' : 'bg-secondary'));
            const progress = Number(job.progress_percent || 0);
            const updatedAt = job.updated_at || job.created_at || '-';

            return `
            <tr>
                <td><strong>Job #${job.id}</strong></td>
                <td><span class="badge ${statusBadgeClass} text-uppercase">${escapeHtml(job.status || 'queued')}</span></td>
                <td>${Number(job.processed_rows || 0)} / ${Number(job.total_rows || 0)}</td>
                <td>${Number(job.success_count || 0)}</td>
                <td class="${Number(job.failed_count || 0) > 0 ? 'text-danger fw-semibold' : ''}">${Number(job.failed_count || 0)}</td>
                <td style="min-width: 180px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span>${progress}%</span>
                    </div>
                </td>
                <td>${escapeHtml(job.message || '-')}</td>
                <td>${escapeHtml(updatedAt)}</td>
            </tr>
        `;
        }

        function renderBulkImportEmptyRow(message) {
            return `
            <tr>
                <td colspan="8" class="text-muted text-center">${escapeHtml(message)}</td>
            </tr>
        `;
        }

        function isFailedBulkImportJob(job) {
            return job.status === 'failed' || Number(job.failed_count) > 0;
        }

        function updateBulkImportQueueToggleLabel() {
            if (!$('#toggleBulkImportQueueView').length) {
                return;
            }
            const iconClass = showAllBulkImportJobs ? 'fa-eye-slash' : 'fa-eye';
            const text = showAllBulkImportJobs ? 'Hide non-failed' : 'Show all';
            $('#toggleBulkImportQueueView i').attr('class', `fas ${iconClass} me-1`);
            $('#toggleBulkImportQueueViewText').text(text);
        }

        function loadBulkImportJobs() {
            if (!$('#bulkImportQueueTableBody').length) {
                return;
            }
            $.ajax({
                url: "{{ route('products.bulkImportJobs') }}",
                type: 'GET',
                success: function(response) {
                    if (!response.success || !Array.isArray(response.jobs) || response.jobs.length === 0) {
                        $('#bulkImportQueueTableBody').html(renderBulkImportEmptyRow('No import jobs yet.'));
                        updateBulkImportQueueToggleLabel();
                        return;
                    }

                    const jobsToRender = showAllBulkImportJobs ?
                        response.jobs :
                        response.jobs.filter(isFailedBulkImportJob);

                    if (jobsToRender.length === 0) {
                        $('#bulkImportQueueTableBody').html(renderBulkImportEmptyRow('No failed jobs found.'));
                        updateBulkImportQueueToggleLabel();
                        return;
                    }

                    const html = jobsToRender.map(renderImportJobRow).join('');
                    $('#bulkImportQueueTableBody').html(html);
                    updateBulkImportQueueToggleLabel();
                }
            });
        }

        function startBulkImportStatusPolling(importJobId) {
            activeBulkImportJobId = importJobId;
            if (bulkImportPollingTimer) {
                clearInterval(bulkImportPollingTimer);
            }

            const fetchStatus = function() {
                $.ajax({
                    url: "{{ url('/products/import-bulk-products/status') }}/" + activeBulkImportJobId,
                    type: 'GET',
                    success: function(response) {
                        if (!response.success || !response.import_job) {
                            return;
                        }

                        const job = response.import_job;
                        loadBulkImportJobs();

                        if (job.status === 'completed' || job.status === 'failed') {
                            clearInterval(bulkImportPollingTimer);
                            bulkImportPollingTimer = null;

                            if (job.status === 'completed') {
                                toastr.success(job.message || 'Bulk import completed');
                                table.ajax.reload(null, false);
                            } else {
                                toastr.error(job.message || 'Bulk import failed');
                            }
                        }
                    }
                });
            };

            fetchStatus();
            bulkImportPollingTimer = setInterval(fetchStatus, 3000);
        }

        $(document).on('click', '#importBulkProductsBtn', function() {
            $('#bulkProductsFile').val('');
            $('#bulkProductsFileError').hide();
            $('#bulkProductsImportResults').hide();
            $('#importBulkProductsModal').modal('show');
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        $(document).on('click', '#submitBulkProductsImport', function() {
            const fileInput = $('#bulkProductsFile')[0];
            const file = fileInput.files[0];

            // Reset errors
            $('#bulkProductsFileError').hide();
            $('#bulkProductsFile').removeClass('is-invalid');

            // Validate file
            if (!file) {
                $('#bulkProductsFileError').text('Please select a CSV file').show();
                $('#bulkProductsFile').addClass('is-invalid');
                return;
            }

            // Validate file type
            const validExtensions = ['csv', 'txt'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!validExtensions.includes(fileExtension)) {
                $('#bulkProductsFileError').text('Please select a valid CSV or TXT file').show();
                $('#bulkProductsFile').addClass('is-invalid');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                $('#bulkProductsFileError').text('File size must be less than 10MB').show();
                $('#bulkProductsFile').addClass('is-invalid');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Disable button and show loading
            const $btn = $(this);
            $btn.prop('disabled', true).html('Importing... <i class="fas fa-spinner fa-spin ms-3"></i>');
            showPageLoading();

            // Submit form
            $.ajax({
                url: "{{ route('products.importBulkProducts') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hidePageLoading();
                    $btn.prop('disabled', false).html('Import Products');

                    if (response.success) {
                        const importMessage = response.message || 'Import queued';
                        toastr.info(importMessage);
                        try {
                            sessionStorage.setItem('bulkImportToast', importMessage);
                        } catch (e) {
                            // no-op if storage is unavailable
                        }
                        $('#importBulkProductsModal').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);
                    } else {
                        toastr.error(response.message || 'Import failed');
                        $('#bulkProductsResultsContent').html('<div class="alert alert-danger mb-0">' + (response.message || 'Import failed') + '</div>');
                        $('#bulkProductsImportResults').show();
                    }
                },
                error: function(xhr) {
                    hidePageLoading();
                    $btn.prop('disabled', false).html('Import Products');

                    let errorMessage = 'Import failed';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            errorMessage = errorData.message || errorMessage;
                        } catch (e) {
                            errorMessage = 'Server error occurred';
                        }
                    }
                    toastr.error(errorMessage);

                    // Show error in results area
                    $('#bulkProductsResultsContent').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    $('#bulkProductsImportResults').show();
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
        try {
            const persistedImportToast = sessionStorage.getItem('bulkImportToast');
            if (persistedImportToast) {
                toastr.success(persistedImportToast);
                sessionStorage.removeItem('bulkImportToast');
            }
        } catch (e) {
            // no-op if storage is unavailable
        }
        @if(session('success'))
        toastr.success("{{ session('success') }}");
        @endif
        @if(session('product_created_success'))
        toastr.success('{{ session('
            product_created_success ') }}');
        @endif

        // Ensure all modal close buttons work properly
        $('.btn-close').on('click', function(e) {
            e.preventDefault();
            var modal = $(this).closest('.modal');
            if (modal.length) {
                // Use Bootstrap 5 modal API
                var bsModal = bootstrap.Modal.getInstance(modal[0]);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    // Fallback to jQuery if Bootstrap modal instance doesn't exist
                    modal.modal('hide');
                }
            }
        });

        // Also handle ESC key to close modals
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.modal.show').each(function() {
                    var bsModal = bootstrap.Modal.getInstance(this);
                    if (bsModal) {
                        bsModal.hide();
                    } else {
                        $(this).modal('hide');
                    }
                });
            }
        });
    });
</script>
<script src="{{ asset('vendor_assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
@endsection