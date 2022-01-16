<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $table = "exam_questions";

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
