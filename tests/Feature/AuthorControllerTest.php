<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testListAllAuthors()
    {
        Author::factory()->count(3)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'authors' => [
                         '*' => [
                             'id',
                             'name',
                             'bio',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);
    }

    public function testShowSingleAuthor()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'author' => [
                         'id' => $author->id,
                         'name' => $author->name,
                         'bio' => $author->bio
                     ]
                 ]);
    }

    public function testShowNonExistingAuthor()
    {
        $response = $this->getJson('/api/authors/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Auteur non trouvé'
                 ]);
    }

    public function testCreateNewAuthor()
    {
        $data = [
            'name' => 'George Orwell',
            'bio' => 'English novelist and essayist'
        ];

        $response = $this->postJson('/api/authors', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'author' => [
                         'name' => 'George Orwell',
                         'bio' => 'English novelist and essayist'
                     ]
                 ]);

        $this->assertDatabaseHas('authors', $data);
    }

    public function testCreateAuthorWithoutName()
    {
        $response = $this->postJson('/api/authors', ['bio' => 'An unknown author']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function testUpdateAuthor()
    {
        $author = Author::factory()->create();

        $data = [
            'name' => 'George Orwell Updated',
            'bio' => 'Updated bio'
        ];

        $response = $this->putJson("/api/authors/{$author->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'author' => $data
                 ]);

        $this->assertDatabaseHas('authors', $data);
    }

    public function testUpdateNonExistingAuthor()
    {
        $data = [
            'name' => 'New Author',
            'bio' => 'Updated bio'
        ];

        $response = $this->putJson('/api/authors/9999', $data);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Auteur non trouvé'
                 ]);
    }

    public function testDeleteAuthor()
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Auteur supprimé avec succès'
                 ]);

        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }

    public function testDeleteNonExistingAuthor()
    {
        $response = $this->deleteJson('/api/authors/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Auteur non trouvé'
                 ]);
    }
}