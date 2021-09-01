<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\UserHasTask;
use Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->user()->id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3',
            'file' => 'mimes:pdf,doc,docx,jpg,jpeg,png,ppt,pptx',
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $task = Task::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'start' => $request->start,
            'end' => $request->end,
            'user_id' => auth()->user()->id
        ]);

        if ($request->hasFile('file')) {
            $filename = $request->file->getClientOriginalName();
            $path = $request->file->storeAs('task', $filename);
            $task->file = '/storage/'.$path;
            $task->save();
        }

        return response()->json([
            'message' => 'Task successfully created',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task, $id)
    {
        if(!is_numeric($id)){
            return response()->json([
                'message' => 'Params id must be a number'
            ], 400);
        }

        $task = Task::find($id);
        
        if(!$task){
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Task found',
            'data' => $task
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!is_numeric($id)){
            return response()->json([
                'message' => 'Params id must be a number'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3',
            'file' => 'mimes:pdf,doc,docx,jpg,jpeg,png,ppt,pptx',
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        if($task->user_id != auth()->user()->id){
            return response()->json([
                'message' => 'You can only update self-created task'
            ], 405);
        }

        $task->title = $request->title;
        $task->desc = $request->desc;
        $task->start = $request->start;
        $task->end = $request->end;
        if ($request->hasFile('file')) {
            $filename = $request->file->getClientOriginalName();
            $path = $request->file->storeAs('task', $filename);
            $task->file = '/storage/'.$path;
            $task->save();
        }
        $task->user_id = auth()->user()->id;

        return response()->json([
            'message' => 'Task successfully updated',
            'data' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task, $id)
    {
        if(!is_numeric($id)){
            return response()->json([
                'message' => 'Params id must be a number'
            ], 400);
        }

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        if($task->user_id != auth()->user()->id){
            return response()->json([
                'message' => 'You can only delete self-created task'
            ], 405);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task successfully deleted'
        ]);
    }

    public function taskme(){
        $taskme = UserHasTask::where('user_id', auth()->user()->id)->with('task')->get();
        return response()->json([
            'message' => 'Success',
            'data' => $taskme
        ]);
    }
}
