<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tutor = Auth::user();
        
        $stats = [
            'total_courses' => 0, // We'll add this in course module
            'total_students' => 0,
            'pending_assignments' => 0,
        ];

        return view('tutor.dashboard', compact('stats'));
    }
}