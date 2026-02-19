<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function books(Request $request)
    {
        $books = Book::query()
            ->when($request->title, fn ($q) => $q->where('title', 'like', '%'.$request->title.'%'))
            ->when($request->isbn, fn ($q) => $q->where('isbn', 'like', '%'.$request->isbn.'%'))
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = filter_var($request->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if (!is_null($status)) {
                    $q->where('status', $status);
                }
            })
            ->orderBy('id')
            ->get();

        return BookResource::collection($books);
    }
// FILTROS
    public function storeLoan(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
            'borrower_name' => 'required|string|max:255',
            'loaned_at' => 'nullable|date',
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->available_copies < 1) {
            return response()->json(['message' => 'Ya no hay mas libros para hacer un prestamo.'], 422);
        }

        $loan = Loan::create([
            'book_id' => $book->id,
            'borrower_name' => $data['borrower_name'],
            'loaned_at' => $data['loaned_at'] ?? now(),
        ]);

        $book->decrement('available_copies');
        $book->status = $book->available_copies > 0;
        $book->save();

        return response()->json(['message' => 'Prestamo creado.', 'loan' => $loan], 201);
    }
// LOAN
    public function returnLoan($loan_id)
    {
        $loan = Loan::findOrFail($loan_id);

        if ($loan->returned_at) {
            return response()->json(['message' => 'Este libro ya lo devolvieron.'], 422);
        }

        $book = Book::findOrFail($loan->book_id);

        $loan->update(['returned_at' => now()]);
        $book->increment('available_copies');
        $book->update(['status' => true]);

        return response()->json(['message' => 'Libro devuelto.'], 200);
    }
}

// ya con esto todo bien, no change pls