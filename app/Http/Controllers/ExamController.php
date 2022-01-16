<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ExamPost;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamController extends Controller
{
    public function create(ExamPost $request)
    {   
        $question_id_arr = $this->getQuestionForExam($request);

        try
        { 
            DB::beginTransaction();
            $exam = new Exam();
            $exam->title = $request->title;
            $exam->publish_date = $request->publish_date;
            $exam->duration = $request->duration;
            $exam->num_ques = $request->num_ques;
            $exam->published = 0;
            $exam->save();

            $exam_questions_arr = [];
            $i = 0;
            foreach($question_id_arr as $question_id){
                $exam_questions_arr[$i]['exam_id'] = $exam->id;
                $exam_questions_arr[$i]['question_id'] = $question_id;
                $exam_questions_arr[$i]['created_at'] = date('Y-m-d H:i:s');
                $i++;
            }
            ExamQuestion::insert($exam_questions_arr);
            DB::commit();
            return response()->json(['message' => 'Exam saved successfully'], 200);
        }
        catch (\Exception $e)
        {   
            DB::rollback();
            return response()->json(['message' => 'Something went wrong'], 500);
        }

        
    }

    public function update(ExamPost $request, $id)
    {
        $question_id_arr = $this->getQuestionForExam($request);
        try
        { 
            DB::beginTransaction();
            $exam = Exam::find($id);
            if($exam->published == 1){
                throw new HttpResponseException(response()->json(['error'=>'exam is already published'], 422));
            }
            $exam->title = $request->title;
            $exam->publish_date = $request->publish_date;
            $exam->duration = $request->duration;
            $exam->num_ques = $request->num_ques;
            $exam->save();

            $exam_questions_arr = [];
            $i = 0;
            ExamQuestion::where('exam_id', $exam->id)->delete();
            foreach($question_id_arr as $question_id){
                $exam_questions_arr[$i]['exam_id'] = $exam->id;
                $exam_questions_arr[$i]['question_id'] = $question_id;
                $exam_questions_arr[$i]['created_at'] = date('Y-m-d H:i:s');
                $i++;
            }
            ExamQuestion::insert($exam_questions_arr);
            DB::commit();
            return response()->json(['message' => 'Exam updated successfully'], 200);
        }
        catch (\Exception $e)
        {   
            DB::rollback();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }


    public function delete(Request $request, $id)
    {   
        $exam = Exam::find($id);
        if(is_null($exam)){
            return response()->json(['message' => 'Exam not found'], 400);
        }
        else{
            try
            { 
                DB::beginTransaction();
                $exam->delete();
                ExamQuestion::where('exam_id',$id)->delete();
                DB::commit();
                return response()->json(['message' => 'Exam Deleted successfully'], 200);
            }
            catch (\Exception $e)
            {   
                DB::rollback();
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }
    }

    public function getAllExam(Request $request)
    {
        $exams =  Exam::orderBy('updated_at', 'DESC');
        if($request->updated_at){
            $exams->where('updated_at', $request->updated_at);
        }
        $data = $exams->get();
        return response()->json(['data' => $data], 200);
    }


    public function getExamQuestion(Request $request, $id)
    {
        $exam_questions = Question::select('questions.id', 'exam_questions.exam_id', 'questions.question', 'questions.option1', 'questions.option2', 'questions.option3', 'questions.option4')->join('exam_questions', 'questions.id', 'exam_questions.question_id')->where('exam_questions.exam_id', $id)->get();
        if(is_null($exam_questions)){
            throw new HttpResponseException(response()->json(['error'=>'No exam found'], 422));
        }
        return response()->json(['data' => $exam_questions], 200);
    }

    public function getQuestionForExam($request){

        $count = Question::count();
        // setting number of question with max value 20 and randomly take question id
        $num_ques = $request->num_ques;
        $all_quetsion_id = Question::select('id')->get()->toArray();
        $num_of_total_ques = count($all_quetsion_id);
        if($num_ques > $num_of_total_ques){
            throw new HttpResponseException(response()->json(['num_ques' => "Not enough number of questions in question bank"], 422));
        }
        $all_quetsion_id = array_column($all_quetsion_id, 'id');
        $exam_question_id =  array_rand($all_quetsion_id,$num_ques);
        
        $question_id_arr = [];
        foreach ($exam_question_id as $key => $value) {
            array_push($question_id_arr, $all_quetsion_id[$value]);
        }
        return $question_id_arr;
    }


    public function sendMail(Request $request)
    {   
        echo "sending mails.....";
        $emails = ['ishmam.ekaf@gmail.com', 'ishmam.bhuiyan01@northsouth.edu'];
        $exams = Exam::where('published', 0)->where('publish_date', date('Y-m-d'))->get();

        foreach($exams as $exam){
            $questions = Question::join('exam_questions', 'questions.id', 'exam_questions.question_id')->where('exam_id', $exam->id)->get()->toArray();
            foreach($emails as $email){
                dispatch((new \App\Jobs\SendEmailJob($email, $questions))->delay(now()->addSeconds(1)));
                // dispatch(new \App\Jobs\SendEmailJob($email, $questions));
            }
            $exam->published = 1;
            $exam->save();
        }
    }
}