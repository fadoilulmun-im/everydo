<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\UserHasTask;
use App\Models\Subtask;
use Validator;

class SubtaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($task_id)
    {
        $subtask = Subtask::where('task_id', $task_id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $subtask
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
            'task_id' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $subtask = Subtask::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'task_id' => $request->task_id,
        ]);

        if ($request->hasFile('file')) {
            $filename = $request->file->getClientOriginalName();
            $path = $request->file->storeAs('subtask', $filename);
            $subtask->file = '/storage/'.$path;
            $subtask->save();
        }

        return response()->json([
            'message' => 'Subtask successfully created',
            'data' => $subtask
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!is_numeric($id)){
            return response()->json([
                'message' => 'Params id must be a number'
            ], 400);
        }

        $subtask = Subtask::find($id);
        if(!$subtask){
            return response()->json([
                'message' => 'Subtask not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Subask found',
            'data' => $subtask
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
            'task_id' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $subtask = Subtask::find($id);
        if(!$subtask){
            return response()->json([
                'message' => 'Subtask not found'
            ], 404);
        }

        $subtask->title = $request->title;
        $subtask->desc = $request->desc;
        $subtask->task_id = $request->task_id;
        if ($request->hasFile('file')) {
            $filename = $request->file->getClientOriginalName();
            $path = $request->file->storeAs('subtask', $filename);
            $subtask->file = '/storage/'.$path;
        }
        $subtask->save();

        return response()->json([
            'message' => 'Subtask successfully updated',
            'data' => $subtask
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!is_numeric($id)){
            return response()->json([
                'message' => 'Params id must be a number'
            ], 400);
        }

        $subtask = Subtask::find($id);

        if(!$subtask){
            return response()->json([
                'message' => 'Subtask not found'
            ], 404);
        }

        if($subtask->task->user_id != auth()->user()->id){
            return response()->json([
                'message' => 'You can only delete self-created subtask'
            ], 405);
        }

        $subtask->delete();

        return response()->json([
            'message' => 'Subtask successfully deleted'
        ]);
    }
}
