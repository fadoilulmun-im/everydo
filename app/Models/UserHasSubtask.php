<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasSubtask extends Model
{
    use HasFactory;
    protected $table = 'user_has_subtask';
    protected $guarded = [];

}
