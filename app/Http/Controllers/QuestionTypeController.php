<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuestionTypePost;
use App\Models\QuestionType;

class QuestionTypeController extends Controller
{
    public function create(QuestionTypePost $request)
    {
        $questionType = new QuestionType();
        $questionType->question_type = $request->question_type;
        $questionType->save();
        return response()->json(['message' => 'QuestionType saved successfully'], 201);
    }

    public function update(QuestionTypePost $request, $id)
    {
        $questionType = QuestionType::find($id);
        $questionType->question_type = $request->question_type;
        $questionType->save();
        return response()->json(['message' => 'QuestionType Updated successfully'], 200);
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