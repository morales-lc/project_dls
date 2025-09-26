<div id="dashboardWrapper" class="d-flex position-relative">
    @include('components.admin-sidebar')
    <div class="flex-grow-1">
        <head>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        </head>
        @include('navbar')
        <div class="container py-5">
    <h2 class="fw-bold mb-4">Category Control Panel</h2>
        @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <button type="button" class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-lg"></i> Add Category
        </button>
        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('mides.categories.add') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category/Program Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <form method="POST" action="{{ route('mides.categories.update', $cat->id) }}">
                        @csrf
                        @method('PUT')
                        <td>
                            <select name="type" class="form-select form-select-sm">
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ $cat->type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $cat->name }}">
                        </td>
                        <td>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                    <form method="POST" action="{{ route('mides.categories.delete', $cat->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                        </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-center">
        <a href="{{ route('mides.management') }}" class="btn btn-secondary">Back to Repository Management</a>
    </div>
</div>
