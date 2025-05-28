<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
     public function index()
    {
        $projects = auth()->user()->clients()->with('projects')->get()->pluck('projects')->flatten();
        if($projects->isEmpty()){
            return response()->json(['message' => 'No projects found'], 404);
        }
        return response()->json(['message' => 'Projects found', 'projects' => $projects], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,completed',
            'deadline' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ensure the client belongs to the authenticated user
        $client = auth()->user()->clients()->find($request->client_id);
        if (!$client) {
            return response()->json(['error' => 'Unauthorized client ID'], 403);
        }
        $project = new Project();
        $project->client_id = $client->id;
        $project->user_id = auth()->user()->id;
        $project->title = $request->title;
        $project->description = $request->description;
        $project->status = $request->status;
        $project->deadline = $request->deadline;
        $project->save();

        return response()->json(['message' => 'Project created successfully', 'project' => $project], 201);
    }
     public function show($id)
    {
        $project = Project::find($id);
        if(!$project){
            return response()->json(['message' => 'Project not found'], 404);
        }
        return response()->json(['message' => 'Project found', 'project' => $project], 200);
    }
    public function update(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,completed',
            'deadline' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $project = Project::find($request->project_id);
        if(!$project)return response()->json(['message' => 'Project not found'], 404);
        $client = auth()->user()->clients()->find($project->client_id);
        if (!$client) return response()->json(['error' => 'Unauthorized'], 403);



        $project->title = $request->title;
        $project->description = $request->description;
        $project->status = $request->status;
        $project->deadline = $request->deadline;
        $project->save();

        return response()->json(['message' => 'Project updated successfully', 'project' => $project], 201);
    }
        public function destroy($id)
    {
        $project = Project::find($id);
        if(!$project)return response()->json(['message' => 'Project not found'], 404);
        $client = auth()->user()->clients()->find($project->client_id);

        if (!$client) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted']);
    }

}
