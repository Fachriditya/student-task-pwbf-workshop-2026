@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-book-open-page-variant"></i>
            </span> Books
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <a href="{{ route('book.create') }}" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Add Book
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Book List</h4>
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80">No</th>
                                    <th width="120">Code</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th width="150">Category</th>
                                    <th width="150">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $index => $book)
                                <tr>
                                    <td>{{ $books->firstItem() + $index }}</td>
                                    <td>
                                        <label class="badge badge-gradient-primary">{{ $book->code }}</label>
                                    </td>
                                    <td>
                                        <strong>{{ $book->title }}</strong>
                                    </td>
                                    <td>{{ $book->author }}</td>
                                    <td>
                                        <label class="badge badge-gradient-success">
                                            {{ $book->category->name }}
                                        </label>
                                    </td>
                                    <td>{{ $book->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <p class="mt-3 text-muted">No books found. <a href="{{ route('book.create') }}">Add your first book</a></p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($books->hasPages())
                    <div class="mt-4">
                        {{ $books->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection