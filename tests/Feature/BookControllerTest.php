<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_books()
    {
        $books = Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'books' => [
                         '*' => [
                             'id',
                             'title',
                             'year',
                             'category_id',
                             'description',
                             'authors',
                         ],
                     ],
                 ]);
    }

    /** @test */
    public function it_can_show_a_single_book()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'book' => [
                         'id' => $book->id,
                         'title' => $book->title,
                     ],
                 ]);
    }

    /** @test */
    public function it_returns_404_if_book_not_found()
    {
        $response = $this->getJson('/api/books/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Livre non trouvé',
                 ]);
    }

    /** @test */
    public function it_can_create_a_book()
    {
        $category = Category::factory()->create();
        $authors = Author::factory()->count(2)->create();

        $data = [
            'title' => 'New Book',
            'year' => 2023,
            'category_id' => $category->id,
            'author_ids' => $authors->pluck('id')->toArray(),
            'description' => 'A description of the new book',
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'book' => [
                         'title' => 'New Book',
                         'year' => 2023,
                         'description' => 'A description of the new book',
                     ],
                 ]);

        $this->assertDatabaseHas('books', ['title' => 'New Book']);
    }

    /** @test */
    public function it_can_update_a_book()
    {
        $book = Book::factory()->create();
        $newCategory = Category::factory()->create();

        $data = [
            'title' => 'Updated Title',
            'category_id' => $newCategory->id,
        ];

        $response = $this->putJson("/api/books/{$book->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'book' => [
                         'title' => 'Updated Title',
                         'category_id' => $newCategory->id,
                     ],
                 ]);

        $this->assertDatabaseHas('books', ['title' => 'Updated Title']);
    }

    /** @test */
    public function it_returns_404_if_updating_non_existing_book()
    {
        $response = $this->putJson('/api/books/999', [
            'title' => 'Non-existing book',
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Livre non trouvé',
                 ]);
    }

    /** @test */
    public function it_can_delete_a_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre supprimé avec succès',
                 ]);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /** @test */
    public function it_returns_404_if_deleting_non_existing_book()
    {
        $response = $this->deleteJson('/api/books/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Livre non trouvé',
                 ]);
    }
}
