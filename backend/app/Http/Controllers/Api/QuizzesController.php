<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class QuizzesController extends Controller
{
    public function index(Request $request)
    {
        $quizs = Quiz::get();

        return response($quizs, 200);
    }
    public function update(Request $request)
    {
        $id = $request['id'];

        $quiz = Quiz::where('id', $id)->first();
        if ($quiz) {
            $quiz->title = $request['title'];
        } else {
            // return 1;
            $quiz = new Quiz();
            $quiz->title = $request['title'];
        }

        $quiz->save();

        return $quiz->id;
    }
    public function addFile(Request $request, $id)
    {
        // $fileName = microtime(true) * 1000 . '.' . $request->file->extension();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($request->file);
        $data = $spreadsheet->getSheet(0)->toArray();
        // return $data;
        unset($data[0]);
        foreach ($data as $key => $value) {
            $question = Question::where('content',  $value[1])->first();
            if (!$question) {

                $question = new Question();
                $question->quiz_id = $id;
                $question->content = $value[1];
                $question->active = 1;
                $question->save();
            }
            for ($i = 0; $i < 4; $i++) {

                $answers = new Answer();
                $answers->question_id = $question->id;
                $answers->content = $value[$i + 2];
                if ($i + 2 == 2) {
                    $answers->is_correct = 1;
                } else {
                    $answers->is_correct = 0;
                }
                $answers->save();
            }
        }
    }
    public function destroy($id)
    {
        Quiz::find($id)->delete();
    }
}
