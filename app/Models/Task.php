<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function students(){
        return $this->belongsToMany(User::class, 'user_has_task', 'task_id', 'user_id');
    }
}
