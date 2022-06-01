<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YoutubeApiController extends Controller
{
    public function index(Request $request)
    {

        return view('Developer.index');
    }

    public function test(Request $request)
    {

        return view('test');
    }
}
