<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseClass;
use Illuminate\Http\Request;
use App\User;

class ClothingController extends Controller
{
    public function index()
    {
        $courseClasses = CourseClass::with('students')->paginate(5);
        $courses = Course::all();
        $nonDocents = User::all()->where('isStudent', false)->where('position', '!=', 'formando');
        return view('clothing.index', compact('courseClasses', 'courses', 'nonDocents'));
    }
}
