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
            if(!is_array($request->correct_ans)){
                $error['correct_ans'][] = 'Invalid Correct Answer';
            }
            else{
                $correct_ans = array_unique($request->correct_ans);
                if($questionType->multiple_ans == 0 && count($correct_ans)>1){
                    $error['correct_ans'][] = 'You cannot select multiple correct answer for this question type';
                }
                else if((($questionType->multiple_ans == 0 && count($correct_ans) > 1) || count($correct_ans) >4) || (empty(array_intersect($correct_ans, [1,2,3,4])))){
                    $error['correct_ans'][] = 'Invalid Correct Answer';
                }
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

    public function update(QuestionPost $request, $id)
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
        $question = Question::find($id);
        if(is_null($question)){
            return response()->json(['message' => 'Question not found'], 400);
        }
        else{
            $question->delete();
            return response()->json(['message' => 'Question Deleted successfully'], 200);
        }
    }

    public function getAllQuestionType(Request $request)
    {
        $question =  Question::orderBy('updated_at', 'DESC');
        if($request->updated_at){
            $question->where('updated_at', $request->updated_at);
        }
        if($request->question_type_id){
            $question->where('question_type_id', $request->question_type_id);
        }
        $data = $question->get();
        return response()->json(['data' => $data], 200);
    }

}