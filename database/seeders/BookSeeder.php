<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::insert([
            [
                'category_id' => 1,
                'code' => 'NV-01',
                'title' => 'Home Sweet Loan',
                'author' => 'Almira Bastari'
            ],
            [
                'category_id' => 2,
                'code' => 'BF-01',
                'title' => 'Mohammad Hatta, Untuk Negeriku',
                'author' => 'Taufik Abdullah'
            ],
            [
                'category_id' => 1,
                'code' => 'NV-02',
                'title' => 'Keajaiban Toko Kelontong Namiya',
                'author' => 'Keigo Higashino'
            ],
        ]);
    }
}
