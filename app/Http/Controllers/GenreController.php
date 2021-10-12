<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{

    public function index()
    {
        return Genre::all();
    }

    public function store(Request $request)
    {
       return Genre::create($request->all());
    }

    public function show(Genre $genre): Genre
    {
        return $genre;
    }

    public function update(Request $request, Genre $genre): Genre
    {
        $genre->update($request->all());
        return $genre;
    }

    public function destroy(Genre $genre): \Illuminate\Http\Response
    {
        $genre->delete();
        return response()->noContent();
    }
}
