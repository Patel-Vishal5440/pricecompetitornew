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
                                            <th class="text-center">Last Run</th>
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
    <div id="loadingIndicator"
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgb(0 0 0 / 32%); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="spinner-border text-danger" role="status"></div>
    </div>

{{-- Modal for editing schedule times --}}
<div class="modal fade" id="editScheduleModal" tabindex="-1" role="dialog" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock me-2"></i>Edit Schedule Times
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">Schedule Configuration</strong>
                        <small class="text-muted">
                            Configure multiple schedule times for this cron job. The cron will run at each specified time daily in the selected timezone. 
                            <strong>Default schedule:</strong> 4 times a day at 12:00 PM, 3:00 PM, 6:00 PM, and 9:00 PM (UTC). 
                            Times should be in 24-hour format (HH:MM). You can add, remove, or modify times as needed.
                        </small>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="scheduleTimezone" class="form-label fw-semibold">
                        <i class="fas fa-globe me-1"></i>Timezone <span class="text-danger">*</span>
                    </label>
                    <select class="form-select form-select-lg" id="scheduleTimezone" required>
                        <option value="UTC" selected>UTC (Coordinated Universal Time)</option>
                        <option value="America/New_York">Eastern Time (EST/EDT)</option>
                        <option value="America/Chicago">Central Time (CST/CDT)</option>
                        <option value="America/Denver">Mountain Time (MST/MDT)</option>
                        <option value="America/Los_Angeles">Pacific Time (PST/PDT)</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>All schedule times will use this timezone.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-clock me-1"></i>Schedule Times <span class="text-danger">*</span>
                    </label>
                    <div id="scheduleTimesContainer" class="border rounded p-3 bg-light" style="min-height: 150px; max-height: 300px; overflow-y: auto;">
                        <!-- Schedule times will be added here dynamically -->
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addScheduleTime">
                                <i class="fas fa-plus me-1"></i>Add Time
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>At least one time required (24-hour format)
                        </small>
                    </div>
                    <div id="scheduleTimesError" class="alert alert-danger mt-2" style="display:none;"></div>
                </div>

                <input type="hidden" id="editCronJobId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveScheduleTimes">
                    Save Schedule
                </button>
            </div>
        </div>
    </div>
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
    
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        dom: 'rt<"bottom d-flex justify-content-between align-items-center flex-wrap gap-2"l<"cron-jobs-table-count-info text-center flex-grow-1 small fw-semibold text-primary">p><"clear">',
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
            { data: 'last_run', name: 'last_run', className: 'text-center' },
            { data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false },
        ],
        drawCallback: function() {
            var info = this.api().page.info();
            var onCurrentPage = Math.max(0, info.end - info.start);
            var filteredCount = info.recordsDisplay || 0;
            var totalCount = info.recordsTotal || 0;
            var countText = 'On this page: <strong>' + onCurrentPage + '</strong> | Total: <strong>' + totalCount + '</strong>';
            if (filteredCount !== totalCount) {
                countText = 'On this page: <strong>' + onCurrentPage + '</strong> | Filtered: <strong>' + filteredCount + '</strong> | Total: <strong>' + totalCount + '</strong>';
            }
            $('.cron-jobs-table-count-info').html(countText);
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

    // Delete confirmation
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this cron job? This action cannot be undone.')) {
            this.submit();
        }
    });

    // Edit schedule button click
    $(document).on('click', '.edit-schedule-btn', function() {
        const cronJobId = $(this).data('cron-job-id');
        let scheduleTimes = $(this).data('schedule-times') || [];
        const timezone = $(this).data('timezone') || 'UTC';
        
        // Ensure scheduleTimes is an array
        if (typeof scheduleTimes === 'string') {
            try {
                scheduleTimes = JSON.parse(scheduleTimes);
            } catch(e) {
                scheduleTimes = [];
            }
        }
        
        // Reset modal
        $('#editCronJobId').val(cronJobId);
        $('#scheduleTimezone').val(timezone);
        $('#scheduleTimesContainer').empty();
        $('#scheduleTimesError').hide().html('');
        
        // If there are existing schedule times, show those; otherwise show defaults
        if (scheduleTimes.length > 0) {
            // Display existing schedule times
            scheduleTimes.forEach(function(time) {
                if (time) {
                    addScheduleTimeInput(time);
                }
            });
        } else {
            // Display default times: 12:00, 15:00, 18:00, 21:00 (UTC)
            addScheduleTimeInput('12:00');
            addScheduleTimeInput('15:00');
            addScheduleTimeInput('18:00');
            addScheduleTimeInput('21:00');
        }
        
        $('#editScheduleModal').modal('show');
    });

    // Reset modal when closed
    $('#editScheduleModal').on('hidden.bs.modal', function() {
        $('#editCronJobId').val('');
        $('#scheduleTimezone').val('UTC');
        $('#scheduleTimesContainer').empty();
        $('#scheduleTimesError').hide().html('');
    });

    // Add schedule time input
    function addScheduleTimeInput(time = '') {
        const timeId = 'scheduleTime_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const timeInput = `
            <div class="input-group mb-2 schedule-time-row" data-time-id="${timeId}">
                <span class="input-group-text bg-white">
                    <i class="fas fa-clock text-primary"></i>
                </span>
                <input type="time" class="form-control schedule-time-input" 
                       value="${time}" 
                       required 
                       pattern="[0-9]{2}:[0-9]{2}"
                       placeholder="HH:MM">
                <button type="button" class="btn btn-outline-danger remove-schedule-time" title="Remove this time">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        $('#scheduleTimesContainer').append(timeInput);
    }

    // Add new schedule time
    $('#addScheduleTime').on('click', function() {
        addScheduleTimeInput();
    });

    // Remove schedule time
    $(document).on('click', '.remove-schedule-time', function() {
        $(this).closest('.schedule-time-row').remove();
    });

    // Save schedule times
    $('#saveScheduleTimes').on('click', function() {
        const cronJobId = $('#editCronJobId').val();
        const timezone = $('#scheduleTimezone').val();
        let scheduleTimes = [];
        
        // Collect all schedule times
        $('.schedule-time-input').each(function() {
            const time = $(this).val().trim();
            if (time) {
                scheduleTimes.push(time);
            }
        });

        // Validate
        if (scheduleTimes.length === 0) {
            $('#scheduleTimesError').html('<i class="fas fa-exclamation-circle me-1"></i>At least one schedule time is required.').show();
            return;
        }

        // Remove duplicates
        scheduleTimes = [...new Set(scheduleTimes)];

        // Validate time format
        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
        for (let i = 0; i < scheduleTimes.length; i++) {
            if (!timeRegex.test(scheduleTimes[i])) {
                $('#scheduleTimesError').html('<i class="fas fa-exclamation-circle me-1"></i>Invalid time format. Please use HH:MM format (24-hour).').show();
                return;
            }
        }

        // Check for duplicate times
        if (scheduleTimes.length !== [...new Set(scheduleTimes)].length) {
            $('#scheduleTimesError').html('<i class="fas fa-exclamation-circle me-1"></i>Duplicate times are not allowed.').show();
            return;
        }

        $('#scheduleTimesError').hide().html('');
        
        // Disable button and show loading
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
        showPageLoading();

        // Send AJAX request
        $.ajax({
            url: `{{ url('cron-jobs') }}/${cronJobId}/update-schedule`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                schedule_times: scheduleTimes,
                timezone: timezone
            },
            success: function(response) {
                hidePageLoading();
                $btn.prop('disabled', false).html('Save Schedule');
                
                if (response.success) {
                    toastr.success(response.message || 'Schedule times updated successfully!');
                    $('#editScheduleModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Failed to update schedule times');
                    if (response.errors) {
                        let errorMsg = '';
                        $.each(response.errors, function(key, value) {
                            errorMsg += value[0] + '<br>';
                        });
                        $('#scheduleTimesError').html(errorMsg).show();
                    }
                }
            },
            error: function(xhr) {
                hidePageLoading();
                $btn.prop('disabled', false).html('Save Schedule');
                
                let errorMessage = 'Failed to update schedule times';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMsg = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMsg += value[0] + '<br>';
                    });
                    $('#scheduleTimesError').html(errorMsg).show();
                }
            }
        });
    });

    // Initialize toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
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

.dataTables_wrapper .dataTable tbody tr:hover {
    background-color: #f8f9fa;
}

/* Schedule times container styling */
#scheduleTimesContainer {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6 !important;
}

.schedule-time-row {
    transition: all 0.2s ease;
}

.schedule-time-row:hover {
    background-color: #ffffff;
    border-radius: 4px;
    padding: 2px;
}

.schedule-time-input {
    border-left: none;
}

.schedule-time-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.remove-schedule-time {
    border-left: none;
}

.remove-schedule-time:hover {
    background-color: #dc3545;
    color: #fff;
    border-color: #dc3545;
}

/* Modal improvements */
#editScheduleModal .modal-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: #fff;
}

#editScheduleModal .modal-title {
    color: #fff;
}

#editScheduleModal .btn-close-white {
    filter: invert(1);
}

/* Badge improvements */
.badge.rounded-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

/* Actions column styling */
.edit-schedule-btn {
    border: none;
    background: transparent;
    padding: 0;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.edit-schedule-btn:hover {
    opacity: 0.8;
}

.edit-schedule-btn:focus {
    outline: none;
}

.edit-schedule-btn i {
    font-size: 16px;
}

/* Actions column alignment */
#datatable thead th:last-child,
#datatable tbody td:last-child {
    text-align: center;
    vertical-align: middle;
}
</style>
@endpush 