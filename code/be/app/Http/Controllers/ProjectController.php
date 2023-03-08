<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Validator;

use App\Models\Project;

class ProjectController extends Controller
{
    public function list(Request $request){
        $data = Project::list($request);
        return response()->json($data, 200);
    }

    public function detail(Request $request){
        $data = [
            "project" => Project::detail($request->all()),
        ];
        return response()->json($data, 200);
    }

    public function store(Request $request){
        // validate request
        $validator = Validator::make($request->all(), [
            "name" => "required"

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
            $project = Project::store($request);
            return response()->json($project, 200);
        }
    }

    public function prepareModify(Request $request){
        $data = [
            "project" => Project::detail($request->all())
        ];
        return response()->json($data, 200);
    }
    
    public function modify(Request $request){
        // validate request
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
            "name" => "required"

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
            $project = Project::modify($request);
            return response()->json($project, 200);
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
            $project = Project::remove($request);
            return response()->json($project, 200);
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
            $project = Project::close($request);
            return response()->json($project, 200);
        }
    }

    public function summary(Request $request){
        $data = [
            "all" => Project::summary("all"),
        ];
        return response()->json($data, 200);
    }
}