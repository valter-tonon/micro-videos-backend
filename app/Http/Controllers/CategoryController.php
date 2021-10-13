<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'description' => 'nullable'
    ];

    public function index()
    {
        return Category::all();
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rules);
        $category = Category::create($data);
        $category->refresh();
        return $category;
    }

    public function show(Category $category): Category
    {
        return $category;
    }

    public function update(Request $request, Category $category): Category
    {
        $data = $this->validate($request, $this->rules);
        $category->update($data);
        return $category;
    }

    public function destroy(Category $category): \Illuminate\Http\Response
    {
        $category->delete();
        return response()->noContent();
    }
}
