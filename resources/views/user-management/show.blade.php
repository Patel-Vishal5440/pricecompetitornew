@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <div class="contents">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4">User Details</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $user->email }}</dd>

                            <dt class="col-sm-4">Role</dt>
                            <dd class="col-sm-8">
                                @if ($user->role)
                                    <span class="badge-lg text-primary rounded px-3 py-1"
                                        style="font-size: 12px;font-weight: 500;background-color: #5f63f221;">
                                        {{ strtolower($user->role->name) }}
                                    </span>
                                @else
                                    <span class="badge-lg text-danger rounded px-3 py-1"
                                        style="font-size: 12px;font-weight: 500;background-color: #ff4d4f21;">Unassigned
                                        Role</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Company</dt>
                            <dd class="col-sm-8">{{ $user->company_name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Location</dt>
                            <dd class="col-sm-8">
                                @if ($user->city && $user->country)
                                    {{ $user->city }}, {{ $user->country }}
                                @elseif($user->city)
                                    {{ $user->city }}
                                @elseif($user->country)
                                    {{ $user->country }}
                                @else
                                    N/A
                                @endif
                            </dd>

                            <dt class="col-sm-4">Created</dt>
                            <dd class="col-sm-8">{{ $user->created_at->format('M d, Y') }}</dd>
                        </dl>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('user-management.index') }}" class="btn btn-light mx-1">Back</a>
                            <a href="{{ route('user-management.edit', $user) }}" class="btn btn-primary mx-1">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
