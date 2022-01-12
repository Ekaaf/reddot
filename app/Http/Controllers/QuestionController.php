<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuestionPost;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionController extends Controller
{   

    public function validateQuestionType($request)
    {
        $questionType = QuestionType::find($request->question_type_id);
        $error = [];
        if(is_null($questionType)){
            $error['question_type_id'][] = 'Invalid QuestionType';
        }
        else{
            $correct_ans = array_unique($request->correct_ans);
            if(($questionType->multiple_ans == 0 && count($correct_ans) > 1) || count($correct_ans) >4){
                $error['question_type_id'][] = 'Invalid QuestionType';
            }
            if(empty(array_intersect($correct_ans, [1,2,3,4]))){
                $error['question_type_id'][] = 'Invalid Correct Answer';
                
            }
        }
        
        if(count($error)>0){
            throw new HttpResponseException(response()->json($error, 422));
        }
    }

    public function create(QuestionPost $request)
    {   
        $this->validateQuestionType($request);
        
        $question = new Question();
        $question->question_type_id = $request->question_type_id;
        $question->question = $request->question;
        $question->option1 = $request->option1;
        $question->option2 = $request->option2;
        $question->option3 = $request->option3;
        $question->option4 = $request->option4;
        $question->correct_ans = implode(",", array_unique($request->correct_ans));
        $question->save();
        return response()->json(['message' => 'Question saved successfully'], 201);
    }

    public function update(QuestionTypePost $request, $id)
    {   
        $this->validateQuestionType($request);

        $question = Question::find($id);
        $question->question_type_id = $request->question_type_id;
        $question->question = $request->question;
        $question->option1 = $request->option1;
        $question->option2 = $request->option2;
        $question->option3 = $request->option3;
        $question->option4 = $request->option4;
        $question->correct_ans = implode(",", array_unique($request->correct_ans));
        $question->save();
        return response()->json(['message' => 'Question updated successfully'], 200);
    }


    public function delete(Request $request, $id)
    {   
        $questionType = QuestionType::find($id);
        if(is_null($questionType)){
            return response()->json(['message' => 'QuestionType not found'], 400);
        }
        else{
            return response()->json(['message' => 'QuestionType Deleted successfully'], 200);
        }
    }

    public function getAllQuestionType(Request $request)
    {
        $questionType =  QuestionType::orderBy('updated_at', 'DESC');
        if($request->updated_at){
            $questionType->where('updated_at', $request->updated_at);
        }
        $data = $questionType->get();
        return response()->json(['data' => $data], 200);
    }

}