<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Validator;

use App\Enums\TaskStatus;
use App\Enums\TaskLevel;
use App\Models\Task;
use App\Models\Project;

class TaskController extends Controller
{
    public function list(Request $request){
        $data = Task::list($request);
        return response()->json($data, 200);
    }

    public function detail(Request $request){
        $data = [
            "task" => Task::detail($request->all()),
        ];
        return response()->json($data, 200);
    }

    public function prepareStore(Request $request){
        $data = [
            "projects" => Project::getAll(),
            "task_level" => TaskLevel::cases()
        ];
        return response()->json($data, 200);
    }
    
    public function store(Request $request){
        // validate request
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "project_id" => "integer",
            "level" => [new Enum(TaskLevel::class)],
            "deadline" => "date_format:Y-m-d",
            "reminder" => "date_format:Y-m-d"

        ],[
            "required" => "Required."
        ]);
        
        if ($validator->fails()) {
            $error_message = [];

            // get all error message
            $messages = $validator->messages();
            foreach ($messages->all() as $message){
                $error_message[] = $message;
            }

            return response()->json($validator->messages(), 400);
        } else {
            $task = Task::store($request);
            return response()->json($task, 200);
        }
    }

    public function prepareModify(Request $request){
        $data = [
            "task" => Task::detail($request->all()),
            "projects" => Project::getAll(),
            "task_level" => TaskLevel::cases()
        ];
        return response()->json($data, 200);
    }
    
    public function modify(Request $request){
        // validate request
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
            "name" => "required",
            "project_id" => "integer",
            "level" => [new Enum(TaskLevel::class)],
            "deadline" => "date_format:Y-m-d",
            "reminder" => "date_format:Y-m-d"

        ]);
        
        if ($validator->fails()) {
            $error_message = [];

            // get all error message
            $messages = $validator->messages();
            foreach ($messages->all() as $message){
                $error_message[] = $message;
            }

            return response()->json($validator->messages(), 400);
        } else {
            $task = Task::modify($request);
            return response()->json($task, 200);
        }
    }
    
    public function remove(Request $request){
        // validate request
        $validator = Validator::make($request->all(), [
            "id" => "required|integer"
        ]);
        
        if ($validator->fails()) {
            $error_message = [];

            // get all error message
            $messages = $validator->messages();
            foreach ($messages->all() as $message){
                $error_message[] = $message;
            }

            return response()->json($error_message, 400);
        } else {
            $task = Task::remove($request);
            return response()->json($task, 200);
        }
    }
    
    public function close(Request $request){
        // validate request
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
        ]);
        
        if ($validator->fails()) {
            $error_message = [];

            // get all error message
            $messages = $validator->messages();
            foreach ($messages->all() as $message){
                $error_message[] = $message;
            }

            return response()->json($validator->messages(), 400);
        } else {
            $task = Task::close($request);
            return response()->json($task, 200);
        }
    }

    public function summary(Request $request){
        $data = [
            "all" => Task::summary("all"),
            "unlimited" => Task::summary("unlimited"),
            "limited" => Task::summary("limited"),
            "expired" => Task::summary("expired")
        ];
        return response()->json($data, 200);
    }
}