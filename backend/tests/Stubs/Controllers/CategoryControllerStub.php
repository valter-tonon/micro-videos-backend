<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\BasicCrudController;
use Tests\Stubs\Model\CategoryStub;

class CategoryControllerStub extends BasicCrudController
{

    protected function model(): string
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable',
        ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable',
        ];
    }
}
