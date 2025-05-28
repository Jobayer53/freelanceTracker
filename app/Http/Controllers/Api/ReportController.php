<?php

namespace App\Http\Controllers\Api;

use App\Models\TimeLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function timeLogSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $query = TimeLog::whereHas('project', function ($q) use ($request) {
            $q->where('user_id', auth()->id());

            if ($request->client_id) {
                $q->where('client_id', $request->client_id);
            }

            if ($request->project_id) {
                $q->where('id', $request->project_id);
            }
        })->whereBetween('start_time', [$request->from, $request->to]);

        $logs = $query->with('project.client')->get();

        $totalHours = $logs->sum('hours');

        $dailyBreakdown = $logs->groupBy(function ($log) {
            return \Carbon\Carbon::parse($log->start_time)->toDateString();
        })->map(function ($group) {
            return round($group->sum('hours'), 2);
        });

        $byProject = $logs->groupBy('project_id')->map(function ($group) {
            return [
                'project' => $group->first()->project->title,
                'client' => $group->first()->project->client->name,
                'hours' => round($group->sum('hours'), 2),
            ];
        });

        return response()->json([
            'total_hours' => round($totalHours, 2),
            'daily_breakdown' => $dailyBreakdown,
            'by_project' => $byProject,
        ]);
    }
}
