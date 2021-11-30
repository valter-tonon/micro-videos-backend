<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Model\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{
    private $controller;
    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'teste_description']);
        $controller = new CategoryControllerStub();
        $result = $controller->index()->toArray();
        $this->assertEquals($category->toArray(), $result[0]);
    }

    public function testInvalidationData()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid.');
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'teste', 'description' => 'teste_description']);
        $category = $this->controller->store($request);
        $this->assertEquals(CategoryStub::find(1)->toArray(), $category->toArray());
        $this->assertEquals('teste', $category->name);
        $this->assertEquals('teste_description', $category->description);
    }

    public function testFindOrFailFetchModel()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'teste_description']);
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
        $this->assertEquals($category->toArray(), $result->toArray());

    }

    public function testFindOrFailThrowExceptionWhenInvalidateData()
    {
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $this->expectException(ModelNotFoundException::class);
        $reflectionMethod->invokeArgs($this->controller, [0]);

    }

    public function testShow()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'teste_description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testUpdate()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'teste_description']);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'teste_update', 'description' => 'teste_description_update']);
        $result = $this->controller->update($request, $category->id);
        $this->assertEquals(CategoryStub::find(1)->toArray(), $result->toArray());
        $this->assertEquals('teste_update', $result->name);
        $this->assertEquals('teste_description_update', $result->description);
    }

    public function testDelete()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'teste_description']);
        $result = $this->controller->destroy($category->id);
        $this->createTestResponse($result)
            ->assertStatus(204);
        $this->assertNull(CategoryStub::find($category->id));

    }

}
