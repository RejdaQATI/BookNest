<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testListAllCategories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'categories' => [
                         '*' => [
                             'id',
                             'name',
                             'description',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);
    }

    public function testShowSingleCategory()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'category' => [
                         'id' => $category->id,
                         'name' => $category->name,
                         'description' => $category->description
                     ]
                 ]);
    }

    public function testShowNonExistingCategory()
    {
        $response = $this->getJson('/api/categories/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Catégorie non trouvée'
                 ]);
    }

    public function testCreateNewCategory()
    {
        $data = [
            'name' => 'Science Fiction',
            'description' => 'Books about science fiction'
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'category' => [
                         'name' => 'Science Fiction',
                         'description' => 'Books about science fiction'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', $data);
    }

    public function testCreateCategoryWithoutName()
    {
        $response = $this->postJson('/api/categories', ['description' => 'An unknown category']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function testUpdateCategory()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Fantasy Updated',
            'description' => 'Updated description'
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'category' => $data
                 ]);

        $this->assertDatabaseHas('categories', $data);
    }

    public function testUpdateNonExistingCategory()
    {
        $data = [
            'name' => 'New Category',
            'description' => 'Updated description'
        ];

        $response = $this->putJson('/api/categories/9999', $data);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Catégorie non trouvée'
                 ]);
    }

    public function testDeleteCategory()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Catégorie supprimée avec succès'
                 ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function testDeleteNonExistingCategory()
    {
        $response = $this->deleteJson('/api/categories/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Catégorie non trouvée'
                 ]);
    }
}
