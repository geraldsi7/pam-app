<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AdminActivityLog::query()
            ->with('actor:id,name,email')
            ->latest();

        if ($action = $request->string('action')->toString()) {
            $query->where('action', $action);
        }

        return Inertia::render('Admin/Activity/Index', [
            'activity' => $query->paginate(20)->withQueryString(),
            'filters' => ['action' => $request->input('action')],
        ]);
    }
}
