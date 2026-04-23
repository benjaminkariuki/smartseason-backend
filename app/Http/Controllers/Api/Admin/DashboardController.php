<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Fetch all fields into memory to leverage Collections and Accessors
        $allFields = Field::all();
        $totalFields = $allFields->count();
        
        // 2. Status breakdown (Active, At Risk, Completed)
        $statusBreakdown = [
            'Active' => $allFields->filter(fn($f) => strtolower($f->status) === 'active')->count(),
            'At Risk' => $allFields->filter(fn($f) => strtolower($f->status) === 'at risk')->count(),
            'Completed' => $allFields->filter(fn($f) => strtolower($f->status) === 'completed')->count(),
        ];

        // 3. Stage breakdown
        $stages = ['preparation', 'planted', 'growing', 'ready', 'harvested'];
        $stageBreakdown = [];
        foreach ($stages as $stage) {
            $stageBreakdown[$stage] = $allFields->where('current_stage', $stage)->count();
        }

        // Top 5 most stagnant fields
        $stagnantFields = Field::where('updated_at', '<', now()->subDays(14))
            ->where('current_stage', '!=', 'harvested')
            ->orderBy('updated_at', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'kpi' => [
                'total_fields' => $totalFields,
                'status_breakdown' => $statusBreakdown,
                'stage_breakdown' => $stageBreakdown,
            ],
            'risk_table' => $stagnantFields,
        ]);
    }
}