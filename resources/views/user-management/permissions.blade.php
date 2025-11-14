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
                                <h5>User Permission</h5>
                                <p class="text-muted mb-0 mt-1">{{ $pageDescription }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>User Information</p>
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Name:</td>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email:</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Role:</td>
                                        <td>
                                            @if($user->role)
                                                <span class="badge-lg text-primary rounded px-3 py-1" style="font-size: 12px;font-weight: 500;background-color: #5f63f221;">
                                                    {{ $user->role->name }}
                                                </span>
                                            @else
                                                <span class="badge-lg text-danger rounded px-3 py-1" style="font-size: 12px;font-weight: 500;background-color: #ff4d4f21;">Unassigned Role</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Company:</td>
                                        <td>{{ $user->company_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Location:</td>
                                        <td>
                                            @if($user->city && $user->country)
                                                {{ $user->city }}, {{ $user->country }}
                                            @elseif($user->city)
                                                {{ $user->city }}
                                            @elseif($user->country)
                                                {{ $user->country }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <p>Role Permissions</p>
                                @if($user->role && $user->role->permissions->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Permission</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->role->permissions as $permission)
                                                <tr>
                                                    <td>
                                                        {{ $permission->name }}
                                                    </td>
                                                    <td>{{ $permission->description ?? 'No description available' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        @if($user->role)
                                            This role has no specific permissions assigned.
                                        @else
                                            This user has no role assigned, therefore no permissions.
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('user-management.index') }}" class="btn btn-light mx-1">Cancel
                                    </a>
                                    <a href="{{ route('user-management.show', $user) }}" class="btn btn-primary mx-1">View User Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection