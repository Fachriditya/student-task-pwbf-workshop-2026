@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-book-open-page-variant"></i>
            </span> Add Book
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('book.index') }}">Books</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Add Book</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Book Form</h4>
                    <p class="card-description">Add new book to library</p>
                    
                    <form action="{{ route('book.store') }}" method="POST" class="forms-sample">
                        @csrf
                        
                        <div class="form-group">
                            <label for="code">Book Code <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('code') is-invalid @enderror" 
                                id="code" 
                                name="code" 
                                placeholder="e.g., BK001"
                                value="{{ old('code') }}"
                                required
                                autofocus>
                            @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <small class="form-text text-muted">Unique identifier for the book</small>
                        </div>

                        <div class="form-group">
                            <label for="title">Book Title <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('title') is-invalid @enderror" 
                                id="title" 
                                name="title" 
                                placeholder="Enter book title"
                                value="{{ old('title') }}"
                                required>
                            @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="author">Author <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('author') is-invalid @enderror" 
                                id="author" 
                                name="author" 
                                placeholder="Enter author name"
                                value="{{ old('author') }}"
                                required>
                            @error('author')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category <span class="text-danger">*</span></label>
                            <select 
                                class="form-control @error('category_id') is-invalid @enderror" 
                                id="category_id" 
                                name="category_id"
                                required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            @if($categories->isEmpty())
                            <small class="form-text text-danger">
                                No categories available. <a href="{{ route('category.create') }}" target="_blank">Create category first</a>
                            </small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-gradient-primary me-2" {{ $categories->isEmpty() ? 'disabled' : '' }}>
                            <i class="mdi mdi-content-save"></i> Save
                        </button>
                        <a href="{{ route('book.index') }}" class="btn btn-light">
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
                    <p class="card-description">Book entry guidelines</p>
                    
                    <div class="alert alert-info" role="alert">
                        <strong>Tips:</strong>
                        <ul class="mb-0 ps-3">
                            <li>Book code must be unique</li>
                            <li>Use consistent code format (e.g., BK001, BK002)</li>
                            <li>Fill all required fields (*)</li>
                            <li>Select appropriate category</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <strong>Note:</strong>
                        <p class="mb-0">Make sure to create categories before adding books.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection