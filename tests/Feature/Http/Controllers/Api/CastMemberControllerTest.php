<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Feature\Traits\TestSaves;
use Tests\Feature\Traits\TestValidations;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;


    /**
     * @var Collection|Model|mixed
     */
    private $castMembers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMembers = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->castMembers->toArray()]);

    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMembers->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->castMembers->toArray());
    }

    public function testInvalidateData()
    {
        $data = [
            'name' => '',
            'type' => '',
        ];
        $this->assertInvalidationInStoreActions($data, 'required');
        $this->assertInvalidationInUpdateActions($data, 'required');

        $data = [
            'type' => 's',
        ];
        $this->assertInvalidationInStoreActions($data, 'in');
        $this->assertInvalidationInUpdateActions($data, 'in');

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
    }

    /**
     * @throws \Exception
     */
    public function testStore()
    {
        $data = [
            'name' => 'test',
            'type' => 1,
        ];

        $response = $this->assertStore(
            $data, $data + [
                'deleted_at' => null
            ]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
        $this->castMembers = factory(CastMember::class)->create([
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ]);
        $data = [
            'name' => 'test_2',
            'type' => CastMember::TYPE_ACTOR
        ];

        $this->assertUpdate($data, $data);

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE',route('cast_members.destroy', ['cast_member' => $this->castMembers->id]) );
        $response->assertNoContent($response->getStatusCode());
        $this->assertNull(CastMember::find($this->castMembers->id));
    }

    protected function routeStore(): string
    {
        return route('cast_members.store');
    }

    protected function routeUpdate(): string
    {
        return route('cast_members.update' , ['cast_member' => $this->castMembers->id]);
    }

    protected function model(): string
    {
        return CastMember::class;
    }
}
