<?php

namespace App\Http\Controllers;

class GuideController extends Controller
{
    public function user()
    {
        return view('guides.user');
    }

    public function technical()
    {
        return view('guides.technical');
    }
}
