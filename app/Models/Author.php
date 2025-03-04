<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this line
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *     schema="Author",
 *     type="object",
 *     title="Author",
 *     required={"name"},
 *     @OA\Property(property="id",         type="integer", example=1),
 *     @OA\Property(property="name",       type="string", example="George Orwell"),
 *     @OA\Property(property="bio",        type="string", example="English novelist and essayist"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'bio'];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_book');
    }
}
