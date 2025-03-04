<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     required={"name"},
 * @OA\Property(property="id",          type="integer", example=1),
 * @OA\Property(property="name",        type="string", example="Fiction"),
 * @OA\Property(property="description", type="string", example="Fictional books category"),
 * @OA\Property(property="created_at",  type="string", format="date-time"),
 * @OA\Property(property="updated_at",  type="string", format="date-time")
 * )
 */
class Category extends Model
{
    protected $fillable = ['name', 'description'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
