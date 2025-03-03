<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'success' => true,
            'books' => $books
        ], 200);
    }

    public function show($id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'book' => $book
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'year' => 'required|integer',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $book = Book::create($request->all());

        return response()->json([
            'success' => true,
            'book' => $book
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé'
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer',
            'category' => 'sometimes|string|max:255',
            'description' => 'nullable|string'
        ]);

        $book->update($request->all());

        return response()->json([
            'success' => true,
            'book' => $book
        ], 200);
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé'
            ], 404);
        }
        
        $book->delete();
        return response()->json([
            'success' => true,
            'message' => 'Livre supprimé avec succès'
        ], 200);
    }
}