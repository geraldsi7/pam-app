<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AgentStoreRequest;
use App\Http\Requests\Admin\AgentUpdateRequest;
use App\Models\Agent;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgentManagementController extends Controller
{
    public function __construct(
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): Response
    {
        $query = Agent::query()
            ->withCount([
                'registrations',
                'registrations as completed_registrations_count' => fn ($builder) => $builder->where('status', 'completed'),
            ])
            ->latest();

        if (!is_null($request->input('is_active')) && $request->input('is_active') !== '') {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOL));
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Admin/Agents/Index', [
            'agents' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'search' => $request->input('search'),
                'is_active' => $request->input('is_active'),
            ],
        ]);
    }

    public function store(AgentStoreRequest $request): RedirectResponse
    {
        $agent = Agent::create([
            ...$request->validated(),
            'is_active' => (bool) $request->boolean('is_active', true),
        ]);

        $this->activityLogger->log(
            action: 'agent.created',
            subject: $agent,
            actorId: $request->user()?->id,
            description: 'Agent created from admin portal.'
        );

        return back()->with('success', 'Agent created.');
    }

    public function update(AgentUpdateRequest $request, Agent $agent): RedirectResponse
    {
        $agent->update([
            ...$request->validated(),
            'is_active' => (bool) $request->boolean('is_active', true),
        ]);

        $this->activityLogger->log(
            action: 'agent.updated',
            subject: $agent,
            actorId: $request->user()?->id,
            description: 'Agent updated from admin portal.'
        );

        return back()->with('success', 'Agent updated.');
    }

    public function toggle(Agent $agent, Request $request): RedirectResponse
    {
        $agent->update(['is_active' => !$agent->is_active]);

        $this->activityLogger->log(
            action: 'agent.toggled',
            subject: $agent,
            actorId: $request->user()?->id,
            description: 'Agent status toggled from admin portal.',
            metadata: ['is_active' => $agent->is_active]
        );

        return back()->with('success', 'Agent status updated.');
    }
}
