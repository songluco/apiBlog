<?php

namespace App\Http\Controllers;

use App\Rules\NameValidate;
use Illuminate\Http\Request;

class NameController extends Controller
{
    //
    public function index()
    {
        return view('name');
    }
    public function addName(Request $request)
    {
        $this->validate($request, [
           'name' => ['required', new NameValidate()]
        ]);
    }



}
