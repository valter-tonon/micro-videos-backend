<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $category = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);

    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidateData()
    {
        $response = $this->json('POST',route('genres.store', []));
        $this->getAssertJsonFragment($response);

        $response = $this->json('POST',route('genres.store', [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]));
        $this->assertValidationMax($response);

        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT',route('genres.update', [
            'genre' => $genre->id
        ]),[]);
        $this->getAssertJsonFragment($response);

        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT',route('genres.update', [
            'genre' => $genre->id
        ]),[
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertValidationMax($response);

    }

    /**
     * @param TestResponse $response
     */
    protected function getAssertJsonFragment(TestResponse $response): void
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment(
                [
                    Lang::get('validation.required', ['attribute' => 'name'])
                ]
            );
    }

    /**
     * @param TestResponse $response
     */
    protected function assertValidationMax(TestResponse $response): void
    {
        $response
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment(
                [
                    Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
                ]
            )
            ->assertJsonFragment([
                Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('genres.store'),[
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $category = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genres.store'),[
            'name' => 'test',
            'is_active' => false
        ]);
        $response->assertJsonFragment([
                'is_active' => false
            ]);

    }
    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $response = $this->json('PUT', route('genres.update',['genre' => $genre->id]),[
            'name' => 'test',
            'is_active' => true
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true
            ]);

    }
}
