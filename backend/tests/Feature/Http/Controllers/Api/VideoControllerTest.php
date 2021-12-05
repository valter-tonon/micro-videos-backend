<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\TestSaves;
use Tests\Feature\Traits\TestValidations;
use Tests\TestCase;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /**
     * @var Collection|Model|mixed
     */
    private $video;
    /**
     * @var array
     */
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video= factory(Video::class)->create();
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'categories_id' => [
                factory(Category::class)->create()->id,
                factory(Category::class)->create()->id,
            ],
            'genres_id' => [
                factory(Genre::class)->create()->id,
                factory(Genre::class)->create()->id,
            ],
        ];
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function testInvalidateData()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];

        $this->assertInvalidationInStoreActions($data, 'required');
        $this->assertInvalidationInUpdateActions($data, 'required');

        $data = [
            'year_launched' => 's',
        ];

        $this->assertInvalidationInStoreActions($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateActions($data, 'date_format', ['format' => 'Y']);

    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a'
        ];

        $this->assertInvalidationInStoreActions($data, 'array');
        $this->assertInvalidationInUpdateActions($data, 'array');

        $data = [
            'categories_id' => [
                'a'
            ]
        ];

        $this->assertInvalidationInStoreActions($data, 'exists');
        $this->assertInvalidationInUpdateActions($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a'
        ];

        $this->assertInvalidationInStoreActions($data, 'array');
        $this->assertInvalidationInUpdateActions($data, 'array');

        $data = [
            'genres_id' => [
                'a'
            ]
        ];

        $this->assertInvalidationInStoreActions($data, 'exists');
        $this->assertInvalidationInUpdateActions($data, 'exists');
    }

    public function testInvalidationIn(): void
    {
        $data = [
            'rating' => '20',
        ];

        $this->assertInvalidationInStoreActions($data, 'in');
        $this->assertInvalidationInUpdateActions($data, 'in');
    }

    public function testInvalidationBoolean(): void
    {
        $data = [
            'opened' => 's',
        ];

        $this->assertInvalidationInStoreActions($data, 'boolean');
        $this->assertInvalidationInUpdateActions($data, 'boolean');

        $this->testInvalidationIn();
    }


    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's',
        ];

        $this->assertInvalidationInStoreActions($data, 'integer');
        $this->assertInvalidationInUpdateActions($data, 'integer');
    }


    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a', 256),
        ];

        $this->assertInvalidationInStoreActions($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateActions($data, 'max.string', ['max' => 255]);
    }


    /**
     * @throws \Exception
     */
    public function testStore()
    {
        $data = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];

        $response = $this->assertStore($data, $data + ['opened' => false]);

        $response->assertJsonStructure([
            'created_at', 'deleted_at'
        ]);

        $this->assertDatabaseHas('videos', $data + ['opened' => false]);
        $this->assertStore($this->sendData + ['opened' => true], $this->sendData + ['opened' => true]);

    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
        $response = $this->assertUpdate($this->sendData + ['opened' => true], $this->sendData + ['opened' => true]);
        $response->assertJsonStructure([
            'created_at', 'deleted_at'
        ]);

        $this->assertUpdate(
            $this->sendData + ['rating' => Video::RATING_LIST[1]],
            $this->sendData + ['rating' => Video::RATING_LIST[1]]
        );

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE',route('videos.destroy', ['video' => $this->video->id]) );
        $response->assertNoContent($response->getStatusCode());
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function routeStore(): string
    {
        return route('videos.store');
    }

    protected function routeUpdate(): string
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model(): string
    {
        return Video::class;
    }


}
