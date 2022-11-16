<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id')->select('id', 'question_id', 'content');
    }
}
