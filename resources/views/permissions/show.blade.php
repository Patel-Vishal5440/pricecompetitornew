@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <div class="contents">
        <div class="">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9 col-12">
                    <div class="card">
                        <div class="card-header p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Permission Details</h5>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Name</dt>
                                <dd class="col-sm-8">{{ $permission->name }}</dd>

                                <dt class="col-sm-4">Description</dt>
                                <dd class="col-sm-8">{{ $permission->description ?? '-' }}</dd>

                                <dt class="col-sm-4">Module</dt>
                                <dd class="col-sm-8">{{ $permission->group ?? '-' }}</dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    @if ($permission->is_active == 'Active')
                                        <span class="badge-lg rounded px-3 py-1 m-1"
                                            style="font-size: 12px; font-weight: 500; color: #198754; background-color: #30ff302b;">Active
                                        </span>
                                    @else
                                        <span class="badge-lg rounded px-3 py-1 m-1"
                                            style="font-size: 12px; font-weight: 500; color: #dc3545; background-color: #ffcccc85;">Inactive
                                        </span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Assigned Roles</dt>
                                <dd class="col-sm-8">
                                    @if ($permission->roles->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($permission->roles as $role)
                                                <span class="badge-lg text-primary rounded px-3 py-1 m-1"
                                                    style="font-size: 12px;font-weight: 500;background-color: #5f63f221;">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('permissions.index') }}" class="btn btn-sm btn-light mx-1">Back</a>
                                <a href="{{ route('permissions.edit', $permission) }}"
                                    class="btn btn-primary px-4 mx-1">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
