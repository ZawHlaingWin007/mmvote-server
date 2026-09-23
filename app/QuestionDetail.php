<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionDetail extends Model
{
    protected $fillable = [
        'image', 'name', 'description','question_id'
    ];

    public function question()
    {
        return $this->belongsTo('App\Question', 'question_id');
    }
}
