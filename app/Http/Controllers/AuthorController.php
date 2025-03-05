<?php

/**
 * @param Request $request
 * @property string $title
 * @property int $year
 */

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/authors",
     *     summary="List all authors",
     *     tags={"Authors"},
     * @OA\Response(
     *         response=200,
     *         description="A list of authors",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Author"))
     *     )
     * )
     */
    public function index()
    {
        $authors = Author::all();
        return response()->json(['success' => true, 'authors' => $authors], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/authors/{id}",
     *     summary="Get a single author",
     *     tags={"Authors"},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the author",
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Author details",
     * @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     * @OA\Response(response=404,                         description="Author not found")
     * )
     */
    public function show($id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response()->json(['success' => false, 'message' => 'Auteur non trouvé'], 404);
        }
        return response()->json(['success' => true, 'author' => $author], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/authors",
     *     summary="Add a new author",
     *     tags={"Authors"},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     * @OA\Response(
     *         response=201,
     *         description="Author created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     * @OA\Response(response=400,                         description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'bio' => 'nullable|string']);

        $author = Author::create($request->only(['name', 'bio']));

        return response()->json(['success' => true, 'author' => $author], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/authors/{id}",
     *     summary="Update an author",
     *     tags={"Authors"},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     * @OA\Response(response=200,                         description="Author updated successfully"),
     * @OA\Response(response=404,                         description="Author not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response()->json(['success' => false, 'message' => 'Auteur non trouvé'], 404);
        }

        $request->validate(['name' => 'sometimes|string|max:255', 'bio' => 'nullable|string']);
        $author->update($request->only(['name', 'bio']));

        return response()->json(['success' => true, 'author' => $author], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/authors/{id}",
     *     summary="Delete an author",
     *     tags={"Authors"},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(response=200, description="Author deleted successfully"),
     * @OA\Response(response=404, description="Author not found")
     * )
     */
    public function destroy($id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response()->json(['success' => false, 'message' => 'Auteur non trouvé'], 404);
        }

        $author->delete();

        return response()->json(['success' => true, 'message' => 'Auteur supprimé avec succès'], 200);
    }
}
