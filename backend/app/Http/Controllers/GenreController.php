<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GenreController extends BasicCrudController
{

    protected $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];


    protected function model(): string
    {
        return Genre::class;
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
