<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'file_path',
        'file_type',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
