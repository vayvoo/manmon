<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;

class QuestionsController extends Controller
{
    public function index(Request $request)
    {
        $q = [];
        $userquiz = Result::where("user_id", $request->user()->id)->where("finished_at", null)->get()->pluck("quiz_id");
        foreach ($userquiz as $quiz) {
            // return $quiz;
            $questions = Question::inRandomOrder()->where("quiz_id", $quiz)->with(['answers' => function ($q) {
                $q->inRandomOrder();
            }])->get();
            array_push($q, $questions);
        }
        // $questions = Question::inRandomOrder()->where("quiz_id", 2)->with(['answers' => function ($q) {
        //     $q->inRandomOrder();
        // }])->get();
        // array_push($q, $questions);

        return response($q, 200);
    }
    public function sendAnswers(Request $request)
    {
        $request = $request->all();
        // return $request;
        $questions = Answer::get();
        // return $questions;
        $userball = 0;
        if ($request) {
            foreach ($request as $answer) {
                foreach ($questions as $q) {
                    if ($answer['answer'] == $q->id && $q->is_correct == 1) {
                        $userball++;
                    }
                }
            }
            return response($userball, 200);
        }
    }
}
