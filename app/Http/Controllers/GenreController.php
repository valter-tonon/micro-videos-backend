<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GenreController extends Controller
{

    protected $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];

    public function index()
    {
        return Genre::all();
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rules);
        $genre = Genre::create($data);
        $genre->refresh();
        return $genre;
    }

    public function show(Genre $genre): Genre
    {
        return $genre;
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, Genre $genre): Genre
    {
        $data = $this->validate($request, $this->rules);
        $genre->update($data);
        return $genre;
    }

    public function destroy(Genre $genre): \Illuminate\Http\Response
    {
        $genre->delete();
        return response()->noContent();
    }
}
