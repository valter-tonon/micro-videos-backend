<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Feature\Traits\TestSaves;
use Tests\Feature\Traits\TestValidations;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;


    /**
     * @var Collection|Model|mixed
     */
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testInvalidateData()
    {
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreActions($data, 'required', []);
        $this->assertInvalidationInUpdateActions($data, 'required', []);

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreActions($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateActions($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreActions($data, 'boolean', []);
        $this->assertInvalidationInUpdateActions($data, 'boolean', []);
    }
    /**
     * @param TestResponse $response
     */
    protected function getAssertJsonFragment(TestResponse $response): void
    {
        $this->assertInvalidationFields($response, ['name'], 'required', []);
    }

    /**
     * @param TestResponse $response
     */
    protected function assertValidationMax(TestResponse $response): void
    {
        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max'=>255]);
        $this->assertInvalidationFields($response, ['is_active'], 'boolean', []);
    }

    /**
     * @throws \Exception
     */
    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];

        $response = $this->assertStore(
            $data, $data + [
                'is_active' => true,
                'deleted_at' => null
            ]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = [
            'name' => 'test',
            'is_active' => false
        ];

        $this->assertStore($data, $data + ['is_active' => false]);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $data = [
            'name' => 'test',
            'is_active' => false,
        ];

        $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $data = [
            'name' => 'test',
            'is_active' => true
        ];
        $this->assertUpdate($data, array_merge($data , ['is_active' => true]));

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE',route('genres.destroy', ['genre' => $this->genre->id]) );
        $response->assertNoContent($response->getStatusCode());
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function routeStore(): string
    {
        return route('genres.store');
    }

    protected function routeUpdate(): string
    {
        return route('genres.update' , ['genre' => $this->genre->id]);
    }

    protected function model(): string
    {
        return Genre::class;
    }
}
