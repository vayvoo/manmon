<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;

class ResultsController extends Controller
{
    public function index(Request $request, $id)
    {
        $q = [];
        $questions = Question::inRandomOrder()->where("quiz_id", $id)->with(['answers' => function ($q) {
            $q->inRandomOrder();
        }])->get();
        array_push($q, $questions);
        $questions = Question::inRandomOrder()->where("quiz_id", 2)->with(['answers' => function ($q) {
            $q->inRandomOrder();
        }])->get();
        array_push($q, $questions);

        return response($q, 200);
    }
    public function createAttempt(Request $request)
    {
        // $request = $request->all();
        $firstSubject = $request['first'];
        $secondSubject = $request['second'];
        $result = Result::where("user_id", $request->user()->id)->first();
        // return count($result);
        if (!$result) {
            for ($i = 0; $i < 3; $i++) {

                $result = new Result();
                $result->user_id = $request->user()->id;
                if ($i == 0) {

                    $result->quiz_id = $firstSubject;
                }
                if ($i == 1) {

                    $result->quiz_id = $secondSubject;
                }
                if ($i == 2) {

                    $result->quiz_id = 3;
                }
                $result->score = 0;
                $result->started_at = date('Y-m-d H:i:s');
                $result->save();
            }
            return response("success", 200);
        } else {
            if ($result['finished_at'] == null) {
                return response("success", 200);
            } else {
                return response("Old", 404);
            }
        }
    }
}
