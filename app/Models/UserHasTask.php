<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasTask extends Model
{
    use HasFactory;

    protected $table = 'user_has_task';
    protected $guarded = [];

    public function task(){
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
