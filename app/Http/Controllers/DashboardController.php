<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalCategories = Category::count();

        $recentBooks = Book::with('category')
            ->latest()
            ->limit(5)
            ->get();

        $categoriesWithCount = Category::withCount('books')
            ->having('books_count', '>', 0)
            ->orderBy('books_count', 'desc')
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'totalCategories',
            'recentBooks',
            'categoriesWithCount'
        ));
    }
}