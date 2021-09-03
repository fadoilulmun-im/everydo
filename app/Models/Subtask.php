<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function task(){
        return $this->belongsTo(Task::class);
    }

    public function assigments(){
        return $this->hasMany(UserHasSubtask::class, 'subtask_id', 'id');
    }
}
