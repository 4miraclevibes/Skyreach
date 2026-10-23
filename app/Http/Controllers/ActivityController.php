<?php

namespace App\Http\Controllers;

use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['user', 'contract'])->latest()->paginate(12);

        return view('activities.index', [
            'activities' => $activities,
        ]);
    }
}
