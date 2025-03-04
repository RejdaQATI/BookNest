<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     title="Book",
 *     required={"title", "author", "category_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="The Great Gatsby"),
 *     @OA\Property(property="author", type="string", example="F. Scott Fitzgerald"),
 *     @OA\Property(property="year", type="integer", example=1925),
 *     @OA\Property(property="category_id", type="integer", example=2),
 *     @OA\Property(property="description", type="string", example="A novel about the American dream."),
 *     @OA\Property(property="available", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */
class Book extends Model
{
    protected $fillable = ['title', 'author_id', 'category_id', 'year', 'description'];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book');
    }    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
