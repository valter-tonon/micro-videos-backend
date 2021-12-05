<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends BasicCrudController
{
    protected $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'description' => 'nullable'
    ];


    protected function model(): string
    {
        return Category::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }

}
