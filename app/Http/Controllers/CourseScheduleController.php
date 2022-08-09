<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseScheduleController extends Controller
{
    public function index()
    {
        return view("courseschedules.index");
    }

    public function show(Course $course)
    {
        return view("courseschedules.show", compact("course"));
    }

    public function showLicNot()
    {
        return view("courseschedules.showLicNot");
    }
}
