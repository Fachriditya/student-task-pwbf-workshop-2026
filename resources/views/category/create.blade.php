@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-shape"></i>
            </span> Add Category
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('category.index') }}">Categories</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Add Category</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Category Form</h4>
                    <p class="card-description">Add new category</p>
                    
                    <form action="{{ route('category.store') }}" method="POST" class="forms-sample">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Category Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                placeholder="Enter category name"
                                value="{{ old('name') }}"
                                required
                                autofocus>
                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <small class="form-text text-muted">Category name must be unique</small>
                        </div>

                        <button type="submit" class="btn btn-gradient-primary me-2">
                            <i class="mdi mdi-content-save"></i> Save
                        </button>
                        <a href="{{ route('category.index') }}" class="btn btn-light">
                            <i class="mdi mdi-cancel"></i> Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="mdi mdi-information-outline"></i> Information
                    </h4>
                    <p class="card-description">Category guidelines</p>
                    
                    <div class="alert alert-info" role="alert">
                        <strong>Tips:</strong>
                        <ul class="mb-0 ps-3">
                            <li>Use clear and descriptive names</li>
                            <li>Category names must be unique</li>
                            <li>Example: Fiction, Non-Fiction, Science, etc.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection