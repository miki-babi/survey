<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'question_id', 'next_question_id'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
