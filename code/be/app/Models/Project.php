<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    
    // CRUD functions
    // ----------------------------------------------------
    // get project list
    public static function list($params){

        // set current page
        $page = empty($params["page"]) ? 1 : $params["page"];
        
        // initialize base object
        $projects = Project::orderBy("id", "desc")->paginate(2, ["*"], "", $page);

        return $projects;
    }

    // // get project detail
    public static function detail($params){

        // get data
        $projects = Project::find($params["id"]);

        return $projects;
    }

    // create new project
    public static function store($params){
        
        // insert data
        $project = new Project;
        $project->name = $params["name"];
        $project->status = "OPEN";
        $project->total_task = 0;
        $project->total_open_task = 0;
        $project->total_closed_task = 0;
        $project->save();

        // refresh project object
        $project->refresh();

        return $project;
    }
    
    // edit existing project
    public static function modify($params){
        
        // get existing project data into object
        $project = Project::find($params["id"]);

        // set new values
        $project->name = $params["name"];
        $project->save();

        // refresh project object
        $project->refresh();

        return $project;
    }
    
    // delete existing project
    public static function remove($params){
        
        // get existing project data into object
        $project = Project::find($params["id"]);
        $project->delete();

        return $project;
    }

    // get all project
    public static function getAll(){
        // get all client
        $projects = Project::get();

        return $projects;
    }
    
    public static function summary($type){
        
        // get existing project data into object
        $project = DB::table("projects")
            ->selectRaw("count(*) as 'total'")
            ->where("status", "OPEN")
            ->first();

        return $project;
    }
}
