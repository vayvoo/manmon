<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;

class AnswersController extends Controller
{
    public function index(Request $request)
    {
        $answers = Answer::get();

        return response($answers, 200);
    }
}
