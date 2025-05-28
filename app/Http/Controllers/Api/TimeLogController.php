<?php

namespace App\Http\Controllers\Api;

use App\Models\TimeLog;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeLog::query()->with('project.client');

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('start_time', [$request->from, $request->to]);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $project = auth()->user()->clients()->with('projects')->get()
            ->pluck('projects')->flatten()->firstWhere('id', $request->project_id);

        if (!$project) {
            return response()->json(['error' => 'Unauthorized project'], 403);
        }

        $hours = 0;
        if ($request->start_time && $request->end_time) {
            $hours = round((strtotime($request->end_time) - strtotime($request->start_time)) / 3600, 2);
        }

        $log = new TimeLog();
        $log->project_id = $request->project_id;
        $log->start_time = $request->start_time;
        $log->end_time = $request->end_time;
        $log->description = $request->description;
        $log->hours = $hours;

        $log->save();



        return response()->json(['message' => 'Time log created successfully', 'log' => $log], 201);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_log_id' => 'required|exists:time_logs,id',
            'start_time' => 'date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $log = TimeLog::find($request->time_log_id);
        if (!$log) return response()->json(['message' => 'Log not found'], 404);
        $project = auth()->user()->clients()->with('projects')->get()
            ->pluck('projects')->flatten()->firstWhere('id', $log->project_id);

        if (!$project) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $log->start_time = $request->start_time;
        $log->end_time = $request->end_time;
        $log->description = $request->description;
        $log->save();

        if ($log->start_time && $log->end_time) {
            $log->hours = round((strtotime($log->end_time) - strtotime($log->start_time)) / 3600, 2);
            $log->save();
        }

        return response()->json(['message' => 'Time log updated successfully', 'log' => $log], 201);
    }

    public function destroy($id)
    {
        $log = TimeLog::find($id);
        if (!$log) return response()->json(['message' => 'Log not found'], 404);
        $project = auth()->user()->clients()->with('projects')->get()
            ->pluck('projects')->flatten()->firstWhere('id', $log->project_id);

        if (!$project) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $log->delete();
        return response()->json(['message' => 'Log deleted'], 200);
    }

    public function start(Request $request)
    {
        // return auth()->user();
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $project = auth()->user()->projects()->find($request->project_id);
        if (!$project) return response()->json(['message' => 'Project not found'], 404);

        $timeLog =  new TimeLog();
        $timeLog->project_id = $project->id;
        $timeLog->start_time = now();
        $timeLog->description = $request->description;
        $timeLog->save();


        return response()->json([
            'message' => 'Time log started.',
            'data' => $timeLog,
        ], 201);
    }
    public function end($id)
    {
        $timeLog = TimeLog::whereHas('project', function ($q) {
            $q->where('user_id', auth()->id());
        })->find($id);
        if (!$timeLog) return response()->json(['message' => 'Time log not found'], 404);
        if ($timeLog->end_time) {
            return response()->json(['message' => 'This time log has already ended.'], 400);
        }

        $endTime = now();
        $start = Carbon::parse($timeLog->start_time);
        $hours = $start->diffInMinutes($endTime) / 60;

        // $timeLog->update([
        //     'end_time' => $endTime,
        //     'hours' => round($hours, 2),
        // ]);
        $timeLog->end_time = $endTime;
        $timeLog->hours = round($hours, 2);
        $timeLog->save();

        return response()->json([
            'message' => 'Time log ended.',
            'data' => $timeLog,
        ], 200);
    }
    public function viewLogs(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $logs = TimeLog::whereHas('project', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->whereBetween('start_time', [$request->from, $request->to])
            ->with('project.client')
            ->orderBy('start_time', 'desc')
            ->get();
        if(!$logs){
            return response()->json(['message' => 'No logs found'], 404);
        }
        return response()->json([
            'message' => 'Logs found.',
            'data' => $logs,
        ], 200);
    }
}
