<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class Task extends Model
{
    use HasFactory;

    
    // relationships
    // ----------------------------------------------------
    public function project(){
        return $this->belongsTo(Project::class);
    }

    // CRUD functions
    // ----------------------------------------------------
    // get task list
    public static function list($params){

        // set current page
        $page = empty($params["page"]) ? 1 : $params["page"];
        
        // initialize base object
        $tasks = Task::with("project")->orderBy("id", "desc")->paginate(2, ["*"], "", $page);

        return $tasks;
    }

    // // get task detail
    public static function detail($params){

        // get data
        $tasks = Task::with("project")->find($params["id"]);

        return $tasks;
    }

    // create new task
    public static function store($params){
        
        // insert data
        $task = new Task;
        $task->name = $params["name"];
        $task->project_id = ($params["project_id"] > 0) ? $params["project_id"] : null;
        $task->level = $params["level"];
        $task->description = $params["description"];
        $task->status = "OPEN";
        $task->reminder = $params["reminder"];
        $task->deadline = $params["deadline"];
        $task->save();

        if(!empty($params["project_id"]) && $params["project_id"] > 0){
            $project = Project::find($params["project_id"]);
            $project->total_task = $project->total_task + 1;
            $project->total_open_task = $project->total_open_task + 1;
            $project->save();
        }

        // refresh task object
        $task->refresh();

        return $task;
    }
    
    // edit existing task
    public static function modify($params){
        
        // get existing task data into object
        $task = Task::find($params["id"]);

        // set new values
        $task->name = $params["name"];
        $task->project_id = ($params["project_id"] > 0) ? $params["project_id"] : null;
        $task->level = $params["level"];
        $task->description = $params["description"];
        $task->reminder = $params["reminder"];
        $task->deadline = $params["deadline"];
        $task->save();

        // refresh task object
        $task->refresh();

        return $task;
    }
    
    // delete existing task
    public static function remove($params){
        
        // get existing task data into object
        $task = Task::find($params["id"]);
        $task->delete();

        return $task;
    }
    
    // close task
    public static function close($params){
        
        // get existing task data into object
        $task = Task::find($params["id"]);

        // set new values
        $task->status = "CLOSED";
        $task->save();

        if(!empty($task->project_id) && $task->project_id > 0){
            $project = Project::find($task->project_id);
            $project->total_open_task = $project->total_open_task - 1;
            $project->total_closed_task = $project->total_closed_task + 1;
            $project->save();
        }

        // refresh task object
        $task->refresh();

        return $task;
    }
    
    // task summary
    public static function summary($type){
        
        // get existing task data into object
        $task = DB::table("tasks")
            ->selectRaw("level, count(*) as 'total'")
            ->where("status", "OPEN")
            ->groupBy("level");

        switch ($type) {
            case "unlimited":
                $task->whereNull("deadline");
                break;
            case "limited":
                $task->whereNotNull("deadline")->where("deadline", "<=", date("Y-m-d"));
                break;
            case "expired":
                $task->where("deadline", ">", date("Y-m-d"));
                break;
        }

        return $task->get();
    }
}
