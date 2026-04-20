<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;

class DashboardController extends Controller
{
   public function index()
    {
        $totalFields = Field::count();
        
        // Status breakdown (Active, At Risk, Completed)
        // We calculate this dynamically based on our Accessor logic
        $allFields = Field::all();
        $statusBreakdown = [
            'Active' => $allFields->where('status', 'Active')->count(),
            'At Risk' => $allFields->where('status', 'At Risk')->count(),
            'Completed' => $allFields->where('status', 'Completed')->count(),
        ];

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
            ],
            'risk_table' => $stagnantFields,
        ]);
    }
}