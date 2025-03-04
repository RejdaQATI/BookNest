<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="BookNest API", version="1.0")
 */
class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="List all books",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of books",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Book"))
     *     )
     * )
     */
    public function index()
    {
        $books = Book::with(['authors', 'category'])->get();

        return response()->json([
            'success' => true,
            'books' => $books
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Get a single book",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book details",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Add a new book",
     *     tags={"Books"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     summary="Update a book",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(response=200, description="Book updated successfully"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     summary="Delete a book",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Book deleted successfully"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
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