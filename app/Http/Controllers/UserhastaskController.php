<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\UserHasTask;

class UserhastaskController extends Controller
{
    public function tasktome($task_id){

        $task = Task::find($task_id);
        
        if(!$task){
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        $cek = UserHasTask::where('user_id', auth()->user()->id)
            ->where('task_id', $task_id)
            ->get();
        if((sizeof($cek)) > 0){
            return response()->json([
                'message' => 'the task has already in your account',
            ], 400);
        }

        $usertask = UserHasTask::create([
            'user_id' => auth()->user()->id,
            'task_id' => $task_id,
        ]);

        return response()->json([
            'message' => 'the task was successfully added to your account',
            'data' => $usertask
        ], 201);
    }
}
