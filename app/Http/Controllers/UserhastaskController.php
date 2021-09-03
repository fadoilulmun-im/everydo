<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Subtask;
use App\Models\UserHasTask;
use App\Models\UserHasSubtask;
use Validator;
use Illuminate\Support\Facades\Storage;


class UserhastaskController extends Controller
{
    public function tasktome($second_id){

        $task = Task::where('second_id', $second_id)->first();
        
        if(!$task){
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        $cek = UserHasTask::where('user_id', auth()->user()->id)
            ->where('task_id', $task->id)
            ->get();
        if((sizeof($cek)) > 0){
            return response()->json([
                'message' => 'the task has already in your account',
            ], 400);
        }

        $usertask = UserHasTask::create([
            'user_id' => auth()->user()->id,
            'task_id' => $task->id,
        ]);

        return response()->json([
            'message' => 'the task was successfully added to your account',
            'task' => $task
        ], 201);
    }

    public function collect(Request $request, $subtask_id){

        $subtask = Subtask::find($subtask_id);
        if(!$subtask){
            return response()->json([
                'message' => 'Subtask not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'mimes:pdf,doc,docx,jpg,jpeg,png,ppt,pptx',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $cek = UserHasSubtask::where('user_id', auth()->user()->id)
                ->where('subtask_id', $subtask_id)->first();
        if($cek){
            $usertask = $cek;
        }else{
            $usertask = new UserHasSubtask();
        }
        $usertask->user_id = auth()->user()->id;
        $usertask->subtask_id = $subtask_id;
        $usertask->notes = $request->notes;

        if ($request->hasFile('file')) {
            $filename = 'assigment-'.time().$request->file->getClientOriginalName();
            $request->file('file')->storeAs($filename, '' , 'google');
            $usertask->file = Storage::disk('google')->url($filename);
        }
        $usertask->save();

        return response()->json([
            'message' => 'tugas sudah dikumpulkan',
            'data' => $usertask
        ], 201);
    }
}
