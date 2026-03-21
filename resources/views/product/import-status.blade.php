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
                                <span class="input-icon icon-right" onclick="clearImportStatusSearch()" style="cursor: pointer;">
                                    <i data-feather="x" class="text-muted"></i>
                                </span>
                                <input type="text" id="importStatusSearch" class="form-control form-control-default"
                                       placeholder="Search Import Status" style="width: 300px;"
                                       maxlength="255" autocomplete="off">
                            </div>
                        </div>
                        <div class="table4 p-25 bg-white mb-30">
                            <div class="table-responsive">
                                <table class="table mb-0 datatable">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th class="text-center align-middle"><span class="userDatatable-title">Job</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Status</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Processed</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Success</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Failed</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Progress</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Message</span></th>
                                            <th class="text-center align-middle"><span class="userDatatable-title">Updated</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulkImportQueueTableBody">
                                        <tr>
                                            <td colspan="8" class="text-muted text-center">No import jobs found.</td>
                                        </tr>
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
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let bulkImportListRefreshTimer = null;
    let allJobs = [];

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderImportJobRow(job) {
        const statusText = String(job.status || 'queued')
            .replace(/_/g, ' ')
            .toLowerCase()
            .replace(/\b\w/g, function(char) { return char.toUpperCase(); });
        const progress = Number(job.progress_percent || 0);
        const updatedAt = job.updated_at || job.created_at || '-';

        return `
            <tr>
                <td class="text-center align-middle"><strong>Job #${job.id}</strong></td>
                <td class="text-center align-middle">${escapeHtml(statusText)}</td>
                <td class="text-center align-middle">${Number(job.processed_rows || 0)} / ${Number(job.total_rows || 0)}</td>
                <td class="text-center align-middle">${Number(job.success_count || 0)}</td>
                <td class="text-center align-middle ${Number(job.failed_count || 0) > 0 ? 'text-danger fw-semibold' : ''}">${Number(job.failed_count || 0)}</td>
                <td style="min-width: 180px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span>${progress}%</span>
                    </div>
                </td>
                <td class="align-middle">${escapeHtml(job.message || '-')}</td>
                <td class="text-center align-middle">${escapeHtml(updatedAt)}</td>
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

    function jobMatchesSearch(job, keyword) {
        if (!keyword) {
            return true;
        }

        const haystack = [
            `job #${job.id || ''}`,
            job.status || '',
            `${Number(job.processed_rows || 0)} / ${Number(job.total_rows || 0)}`,
            `${Number(job.success_count || 0)}`,
            `${Number(job.failed_count || 0)}`,
            `${Number(job.progress_percent || 0)}%`,
            job.message || '',
            job.updated_at || job.created_at || ''
        ].join(' ').toLowerCase();

        return haystack.includes(keyword);
    }

    function renderFilteredJobs() {
        const keyword = ($('#importStatusSearch').val() || '').trim().toLowerCase();
        const filteredJobs = allJobs.filter(job => jobMatchesSearch(job, keyword));

        if (filteredJobs.length === 0) {
            $('#bulkImportQueueTableBody').html(renderBulkImportEmptyRow('No import jobs found.'));
            return;
        }

        const html = filteredJobs.map(renderImportJobRow).join('');
        $('#bulkImportQueueTableBody').html(html);
    }

    function loadBulkImportJobs() {
        $.ajax({
            url: "{{ route('products.bulkImportJobs') }}",
            type: 'GET',
            data: {
                all: 1
            },
            success: function(response) {
                if (!response.success || !Array.isArray(response.jobs) || response.jobs.length === 0) {
                    allJobs = [];
                    $('#bulkImportQueueTableBody').html(renderBulkImportEmptyRow('No import jobs found.'));
                    return;
                }

                allJobs = response.jobs;
                renderFilteredJobs();
            }
        });
    }

    $('#importStatusSearch').on('keyup', function() {
        renderFilteredJobs();
    });

    window.clearImportStatusSearch = function() {
        $('#importStatusSearch').val('');
        renderFilteredJobs();
    };

    loadBulkImportJobs();
    bulkImportListRefreshTimer = setInterval(loadBulkImportJobs, 10000);
});
</script>
@endsection
