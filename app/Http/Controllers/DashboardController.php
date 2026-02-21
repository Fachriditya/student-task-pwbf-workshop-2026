<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get total counts
        $totalBooks = Book::count();
        $totalCategories = Category::count();

        // Get recent books (latest 5)
        $recentBooks = Book::with('category')
            ->latest()
            ->limit(5)
            ->get();

        // Get categories with book counts
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