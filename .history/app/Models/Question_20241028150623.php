<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'is_final', 'type'];

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}
