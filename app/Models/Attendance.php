<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'training_session_id'
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
    public function session()
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }
}
