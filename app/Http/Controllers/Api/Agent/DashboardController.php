<?php
namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agentId = $request->user()->id;

        // Fetch ALL fields into memory first to avoid query builder mutation
        $myFields = Field::where('agent_id', $agentId)->get();

        // Calculate Stage Breakdown
        $stages = ['preparation', 'planted', 'growing', 'ready', 'harvested'];
        $stageBreakdown = [];
        foreach ($stages as $stage) {
            $stageBreakdown[$stage] = $myFields->where('current_stage', $stage)->count();
        }

        // Calculate Status Breakdown via your model accessors
        $statusBreakdown = [
            'Active' => $myFields->filter(fn($f) => strtolower($f->status) === 'active')->count(),
            'At Risk' => $myFields->filter(fn($f) => strtolower($f->status) === 'at risk')->count(),
            'Completed' => $myFields->filter(fn($f) => strtolower($f->status) === 'completed')->count(),
        ];

        return response()->json([
            'work_queue' => [
                'total_assigned' => $myFields->count(),
                'ready_to_harvest' => $myFields->where('current_stage', 'ready')->count(),
            ],
            'priority_alerts' => $myFields->filter(fn($f) => strtolower($f->status) === 'at risk')->values(),
            'stage_breakdown' => $stageBreakdown,
            'status_breakdown' => $statusBreakdown,
        ]);
    }
}