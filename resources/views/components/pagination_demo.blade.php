@extends('layouts.app')
@section('content')
    <div class="contents">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>{{ $pageTitle }}</h4>
                            <p class="text-muted">{{ $pageDescription }}</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h5>Sample Data (Items {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }})</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($paginator as $item)
                                                    <tr>
                                                        <td>{{ $item }}</td>
                                                        <td>Sample Item {{ $item }}</td>
                                                        <td>This is a sample item for pagination demonstration</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <div class="text-muted">
                                            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries
                                        </div>
                                        <div>
                                            {{ $paginator->links('components.pagination') }}
                                        </div>
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

@push('styles')
<style>
.pagination {
    margin: 0;
    gap: 2px;
}

.pagination .page-link {
    border-radius: 6px;
    border: 1px solid #e9ecef;
    color: #495057;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease-in-out;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #495057;
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}
</style>
@endpush 