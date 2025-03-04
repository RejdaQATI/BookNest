<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;

class BookControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_can_list_books()
  {
    $books = Book::factory(3)->create();

    $response = $this->get('/api/books');

    $response->assertStatus(200)
      ->assertJsonCount(3, 'books')
      ->assertJsonStructure([
        'books' => [
          '*' => ['id', 'title', 'description', 'authors', 'category']
        ]
      ]);
  }

  public function test_can_show_a_book()
  {
  
    $book = Book::factory()->create();
    $category = Category::factory()->create();
    $authors = Author::factory(2)->create();

    $book->category()->associate($category);
    $book->save();
    $book->authors()->attach($authors);

    $response = $this->get("/api/books/{$book->id}");

    $expectedAuthors = $book->authors->map(fn($author) => ['id' => $author->id, 'name' => $author->name])->toArray(); // Get names only
    $expectedCategory = ['id' => $book->category->id, 'name' => $book->category->name]; // Get category name only

    // Assert response
    $response->assertStatus(200)
        ->assertJson([
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'authors' => $expectedAuthors,
                'category' => $expectedCategory
            ]
        ]);
  }

  public function test_show_returns_404_when_book_not_found()
  {
    $response = $this->get('/api/books/1');

    $response->assertStatus(404);
  }

  public function test_can_create_a_book()
  {
    $category = Category::factory()->create();
        $authors = Author::factory(2)->create();

        $response = $this->postJson('/api/books', [
            'title' => 'Test Book',
            'year' => 2023,
            'category_id' => $category->id,
            'author_ids' => $authors->pluck('id')->toArray(),
            'description' => 'A test description'
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['book']);
  }

  public function test_create_book_validation_error()
  {
    $response = $this->postJson('/api/books', []);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['title', 'year', 'category_id', 'author_ids']);
  }

  public function test_can_update_book()
  {
    $book = Book::factory()->create();
    $category = Category::factory()->create();
    $authors = Author::factory(2)->create();

    $response = $this->putJson("/api/books/{$book->id}", [
      'title' => 'Updated Book',
      'year' => 2023,
      'category_id' => $category->id,
      'author_ids' => $authors->pluck('id')->toArray(),
      'description' => 'An updated description'
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);
  }

  public function test_update_returns_404_when_book_not_found()
  {
    $response = $this->putJson('/api/books/1', []);

    $response->assertStatus(404);
  }

  public function test_can_delete_a_book()
  {
    $book = Book::factory()->create();

    $response = $this->delete("/api/books/{$book->id}");

    $response->assertStatus(200)
      ->assertJson(['success' => true]);
  }

  public function test_delete_returns_404_when_book_not_found()
  {
    $response = $this->delete('/api/books/1');

    $response->assertStatus(404);
  }
}
