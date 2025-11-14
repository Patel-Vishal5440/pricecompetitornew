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
    </style>
@endsection

@section('content')
<div class="contents">
    <div class="">
        <div class="row">
            <div class="col-12">
                <div class="card" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); width: 100%;">
                    <div class="card-body p-3">
                        <div class="color-dark fw-500 d-flex justify-content-start mt-15 mx-4">
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
                                    placeholder="Search Product">
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

    // Clear search function
    window.clearSearch = function() {
        $('#search').val('');
        table.ajax.reload();
    };

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
