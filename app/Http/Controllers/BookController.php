<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['authors', 'category'])->get();

        return response()->json([
            'success' => true,
            'books' => $books
        ], 200);
    }

    public function show($id)
    {
        $book = Book::with(['authors', 'category'])->find($id);
        
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
            'author_ids' => 'required|array', 
            'author_ids.*' => 'exists:authors,id', 
            'year' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string'
        ]);

        $book = Book::create([
            'title' => $request->title,
            'year' => $request->year,
            'category_id' => $request->category_id,
            'description' => $request->description
        ]);

        $book->authors()->attach($request->author_ids);

        return response()->json([
            'success' => true,
            'book' => $book->load('authors', 'category') 
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
            'author_ids' => 'sometimes|array',
            'author_ids.*' => 'exists:authors,id',
            'year' => 'sometimes|integer',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'nullable|string'
        ]);

        $book->update($request->only(['title', 'year', 'category_id', 'description']));
        if ($request->has('author_ids')) {
            $book->authors()->sync($request->author_ids);
        }

        return response()->json([
            'success' => true,
            'book' => $book->load('authors', 'category')
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
        $book->authors()->detach();
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livre supprimé avec succès'
        ], 200);
    }
}
