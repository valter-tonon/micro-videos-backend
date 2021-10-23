<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class BasicCrudController extends Controller
{

    protected abstract function model();

    protected abstract function rulesStore();

    public function index()
    {
         return $this->model()::all();
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($data);
        $obj->refresh();
        return $obj;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id)
    {
        return $this->findOrFail($id);
    }

}
