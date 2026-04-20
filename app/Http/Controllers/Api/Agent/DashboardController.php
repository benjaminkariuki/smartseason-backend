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

        // My assigned fields overview
        $myFields = Field::where('agent_id', $agentId);

        return response()->json([
            'work_queue' => [
                'total_assigned' => $myFields->count(),
                'ready_to_harvest' => $myFields->where('current_stage', 'ready')->count(),
            ],
            'priority_alerts' => $myFields->get()->filter(fn($f) => $f->status === 'At Risk')->values(),
        ]);
    }
}