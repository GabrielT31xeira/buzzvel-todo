<?php

namespace App\Http\Controllers\api;

use App\Models\File;
use App\Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $tasks = Task::with('creator', 'updater', 'files')->get();
        if(count($tasks) === 0) {
            return response()->json(['message' => 'No tasks found']);
        }

        return response()->json(['message' => 'Task list', 'tasks' => $tasks], 200);
    }

    public function store(Request $req): \Illuminate\Http\JsonResponse
    {
        $validate = Validator::make($req->all(),[
            'title' => 'required|unique:task|max:255',
            'description' => 'required|max:255',
            'pdf.*' => ['max:20000']
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $task = new Task;
            $task->title = $req->input('title');
            $task->description = $req->input('description');
            $task->completed = false;
            $task->created_by = $user->id;
            $task->save();
            $file = $req->input('pdf');
            foreach ($file as $files){
                $newFile = new File();
                $newFile->pdf = $files;
                $newFile->task_id = $task->id;
                $newFile->save();
            }
            DB::commit();

            return response()->json(['message' => 'task created'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $task = Task::with('creator', 'updater', 'files')->find($id);
            if (!$task) {
                return response()->json(['message' => 'task not found'], 404);
            }
            return response()->json(['task' => $task],200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update($id, Request $req): \Illuminate\Http\JsonResponse
    {
        $validate = Validator::make($req->all(),[
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'pdf.*' => ['max:20000']
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $task = Task::with('creator', 'updater', 'files')->find($id);
            if (!$task) {
                return response()->json(['message' => 'task not found'], 404);
            }

            $user = Auth::user();
            $task->title = $req->input('title');
            $task->description = $req->input('description');
            if ($req->input('completed'))
            {
                $task->completed = $req->input('completed');
                $data = new DateTime();
                $task->completed_at = $data->format('Y-m-d');
            } else {
                $task->completed = false;
            }
            $task->updated_by = $user->id;
            $task->save();

            $file = $req->input('pdf');

            foreach ($file as $files){
                $newFile = new File();
                $newFile->pdf = $files;
                $newFile->task_id = $task->id;
                $newFile->save();
            }
            DB::commit();

            return response()->json(['message' => 'Task updated'],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'task not found'], 404);
        }

        $task->files()->delete();

        $task->delete();

        return response()->json(['message' => 'task deleted successfully'],200);
    }
}
