<?php

namespace Tests\Feature\Traits;

use Exception;
use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
    protected function assertStore(array $sendData, array $testDatabase, array $testJsonData = null)
    {
        $response = $this->json('POST', $this->routeStore(), $sendData);

        if ($response->status() !== 201) {
            throw new Exception("Response status must be 201, given {$response->status()}: \n{$response->content()}");
        }

        $this->assertInDatabase($testDatabase, $response);
        $this->assertJsonResponseContent($testJsonData, $testDatabase, $response);

        return $response;
    }

    protected function assertUpdate(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);

        if ($response->status() !== 200) {
            throw new Exception("Response status must be 200, given {$response->status()}: \n{$response->content()}");
        }

        $this->assertInDatabase($testDatabase, $response);
        $this->assertJsonResponseContent($testJsonData, $testDatabase, $response);

        return $response;
    }

    /**
     * @param array $testDatabase
     * @param TestResponse $response
     */
    protected function assertInDatabase(array $testDatabase, TestResponse $response): void
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
    }

    /**
     * @param array|null $testJsonData
     * @param array $testDatabase
     * @param TestResponse $response
     */
    protected function assertJsonResponseContent(?array $testJsonData, array $testDatabase, TestResponse $response): void
    {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    }
}
