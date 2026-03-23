<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamenController extends Controller
{
    public function index()
    {
        return view('examens.index');
    }

    public function create()
    {
        return view('examens.create');
    }

    public function results()
    {
        return view('examens.results');
    }
}
