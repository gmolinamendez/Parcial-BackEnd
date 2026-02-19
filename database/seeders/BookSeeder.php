<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Aca esta lo del CSV, para que ya lo import se un solo en el seed
        $csvPath = database_path('data/books_classics.csv');

        if (! file_exists($csvPath)) {
            throw new \RuntimeException("No se encontro el archivo CSV en: {$csvPath}");
        }

        $file = fopen($csvPath, 'r');
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 6) {
                continue;
            }

            Book::create([
                'title' => trim((string) $row[0]),
                'description' => trim((string) $row[1]),
                'isbn' => trim((string) $row[2]),
                'total_copies' => (int) $row[3],
                'available_copies' => (int) $row[4],
                'status' => strtolower(trim((string) $row[5])) === 'disponible',
            ]);
        }

        fclose($file);

        Book::factory()->count(90)->create();
    }
}
