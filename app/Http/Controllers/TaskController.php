<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => false
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'sometimes|required|max:255',
        ]);

        $task->update($request->all());

        return response()->json($task);
    }
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}