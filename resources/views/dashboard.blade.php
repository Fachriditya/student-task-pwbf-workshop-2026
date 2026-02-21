@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                </li>
            </ul>
        </nav>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        {{-- Total Books --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Books 
                        <i class="mdi mdi-book-open-page-variant mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $totalBooks }}</h2>
                    <h6 class="card-text">
                        <a href="{{ route('book.index') }}" class="text-white">View all books →</a>
                    </h6>
                </div>
            </div>
        </div>

        {{-- Total Categories --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Categories 
                        <i class="mdi mdi-shape mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $totalCategories }}</h2>
                    <h6 class="card-text">
                        <a href="{{ route('category.index') }}" class="text-white">View all categories →</a>
                    </h6>
                </div>
            </div>
        </div>

        {{-- Welcome Message --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Welcome Back 
                        <i class="mdi mdi-hand-wave mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ Auth::user()->name }}</h2>
                    <h6 class="card-text">Have a great day!</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- Middle Section: Recent Books & Books by Category --}}
    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-start">
                            <i class="mdi mdi-book-multiple text-info"></i> Recent Books
                        </h4>
                        <a href="{{ route('book.index') }}" class="btn btn-sm btn-outline-primary float-end">
                            View All <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBooks as $book)
                                <tr>
                                    <td><label class="badge badge-gradient-primary">{{ $book->code }}</label></td>
                                    <td>{{ Str::limit($book->title, 25) }}</td>
                                    <td>{{ Str::limit($book->author, 20) }}</td>
                                    <td><label class="badge badge-gradient-success">{{ $book->category->name }}</label></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="mdi mdi-book-open-variant mdi-36px d-block mb-2"></i>
                                        No books yet. <a href="{{ route('book.create') }}">Add your first book</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="mdi mdi-chart-pie text-success"></i> Books by Category
                    </h4>
                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Books</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoriesWithCount as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-end">
                                        <label class="badge badge-gradient-info">{{ $category->books_count }}</label>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        <i class="mdi mdi-shape-outline mdi-36px d-block mb-2"></i>
                                        No categories yet. <a href="{{ route('category.create') }}">Add your first category</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($categoriesWithCount->count() > 0)
                    <div class="mt-3 text-center">
                        <a href="{{ route('category.index') }}" class="btn btn-outline-success btn-sm btn-block">
                            View All Categories <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="mdi mdi-flash text-warning"></i> Quick Actions
                    </h4>
                    <div class="row mt-3">
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <a href="{{ route('book.create') }}" class="btn btn-gradient-primary btn-lg btn-block">
                                <i class="mdi mdi-book-plus"></i> Add Book
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <a href="{{ route('category.create') }}" class="btn btn-gradient-success btn-lg btn-block">
                                <i class="mdi mdi-plus-circle"></i> Add Category
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('book.index') }}" class="btn btn-gradient-info btn-lg btn-block">
                                <i class="mdi mdi-book-search"></i> Browse Books
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('category.index') }}" class="btn btn-gradient-warning btn-lg btn-block">
                                <i class="mdi mdi-view-list"></i> Categories
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection