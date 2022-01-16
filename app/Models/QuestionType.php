<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    protected $table = "question_types";

    public function questionType()
    {
        return $this->hasMany(Question::class);
    }
}
