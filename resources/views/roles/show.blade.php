@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <div class="contents">
        <div class="">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Role Details</h5>
                                    <p class="text-muted mb-0 mt-1">View user permissions through their role.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <p class="mb-0">Role Information</p>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="30%">ID:</th>
                                                    <td>{{ $role->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name:</th>
                                                    <td>{{ ucfirst($role->name) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Description:</th>
                                                    <td>{{ $role->description ?? 'No description provided' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status:</th>
                                                    <td>
                                                        @if ($role->is_active)
                                                            <span class="badge-lg rounded px-3 py-1"
                                                                style="font-size: 12px; font-weight: 500; color: #198754; background-color: #30ff302b;">Active</span>
                                                        @else
                                                            <span class="badge-lg rounded px-3 py-1"
                                                                style="font-size: 12px; font-weight: 500; color: #dc3545; background-color: #ffcccc85;">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Created:</th>
                                                    <td>{{ $role->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Updated:</th>
                                                    <td>{{ $role->updated_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <p class="mb-0">Assigned Users</p>
                                        </div>
                                        <div class="card-body">
                                            @if ($role->users->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr class="userDatatable-header">
                                                                <th class="text-center align-middle">Name</th>
                                                                <th class="text-center align-middle">Email</th>
                                                                <th class="text-center align-middle">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($role->users as $user)
                                                                <tr>
                                                                    <td class="text-center align-middle">{{ $user->name }}
                                                                    </td>
                                                                    <td class="text-center align-middle">{{ $user->email }}
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        @if ($user->is_active)
                                                                            <span class="badge-lg rounded px-3 py-1"
                                                                                style="font-size: 12px; font-weight: 500; color: #198754; background-color: #30ff302b;">Active</span>
                                                                        @else
                                                                            <span class="badge-lg rounded px-3 py-1"
                                                                                style="font-size: 12px; font-weight: 500; color: #dc3545; background-color: #ffcccc85;">Inactive</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">No users assigned to this role.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Assigned Permissions ({{ $role->permissions->count() }})</h5>
                                        </div>
                                        <div class="card-body">
                                            @if ($role->permissions->count() > 0)
                                                <div class="row">
                                                    @php
                                                        $permissionsByGroup = $role->permissions->groupBy('group');
                                                    @endphp

                                                    @foreach ($permissionsByGroup as $group => $groupPermissions)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border">
                                                                <div class="card-header py-2">
                                                                    <p class="mb-0">{{ $group }}
                                                                        ({{ $groupPermissions->count() }})
                                                                    </p>
                                                                </div>
                                                                <div class="card-body">
                                                                    <ul class="list-unstyled mb-0">
                                                                        @foreach ($groupPermissions as $permission)
                                                                            <li class="mb-2">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center">
                                                                                    <div>
                                                                                        <strong>{{ $permission->description }}</strong>
                                                                                        <br>
                                                                                        <p
                                                                                            class="small">{{ $permission->name }}</p>
                                                                                    </div>
                                                                                    <span class="badge-lg rounded px-3 py-1"
                                                                                style="font-size: 12px; font-weight: 500; color: #17a2b8; background-color: #17a2b826;">Assigned</span>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">No permissions assigned to this role.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('roles.index') }}" class="btn btn-light mx-1">Back
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary mx-1">Edit Role
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
