<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\CategoryController;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\TestSaves;
use Tests\Feature\Traits\TestValidations;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /**
     * @var Collection|Model|mixed
     */
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category= factory(Category::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
    }

    public function testInvalidateData()
    {
        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreActions($data, 'required');
        $this->assertInvalidationInUpdateActions($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreActions($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateActions($data, 'max.string', ['max' => 255]);
        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreActions($data, 'boolean' );
        $this->assertInvalidationInUpdateActions($data, 'boolean' );

    }

    /**
     * @throws \Exception
     */
    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore($data, $data + [
            'description' => null,
                'is_active' => true,
                'deleted_at' => null
            ]);

        $response->assertJsonStructure([
            'created_at', 'deleted_at'
        ]);

        $data = [
            'name' => 'test',
            'description' => 'test_description',
            'is_active' => false
        ];
        $this->assertStore($data, $data + [
            'description' => 'test_description',
                'is_active' => false,
                'deleted_at' => null
            ]);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
         $this->category = factory(Category::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ]);
        $data = [
            'name' => 'test',
            'description' => 'test_description',
            'is_active' => false,
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        $data = [
            'name' => 'test',
            'description' => '',
            'is_active' => false,
        ];
        $this->assertUpdate($data, array_merge($data , ['description' => null]));

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE',route('categories.destroy', ['category' => $this->category->id]) );
        $response->assertNoContent($response->getStatusCode());
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    protected function routeStore(): string
    {
        return route('categories.store');
    }

    protected function routeUpdate(): string
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model(): string
    {
        return Category::class;
    }

}
