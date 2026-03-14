<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Registration;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function __construct(
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): Response
    {
        $query = User::query()
            ->with('registration:id,reference_code,status')
            ->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($type = $request->string('user_type')->toString()) {
            $query->where('user_type', $type);
        }

        if (!is_null($request->input('is_active')) && $request->input('is_active') !== '') {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOL));
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'search' => $request->input('search'),
                'user_type' => $request->input('user_type'),
                'is_active' => $request->input('is_active'),
            ],
            'registrationOptions' => Registration::query()
                ->select(['id', 'reference_code'])
                ->latest()
                ->limit(200)
                ->get(),
            'userTypes' => ['member', 'admin', 'support', 'finance', 'operations'],
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $rawPassword = $validated['password'] ?: Str::password(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($rawPassword),
            'registration_id' => $validated['registration_id'] ?? null,
            'user_type' => $validated['user_type'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $this->activityLogger->log(
            action: 'user.created',
            subject: $user,
            actorId: $request->user()?->id,
            description: 'User created from admin portal.',
            metadata: ['user_type' => $user->user_type]
        );

        return back()->with('success', 'User created successfully.');
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'registration_id' => $validated['registration_id'] ?? null,
            'user_type' => $validated['user_type'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        $this->activityLogger->log(
            action: 'user.updated',
            subject: $user,
            actorId: $request->user()?->id,
            description: 'User updated from admin portal.'
        );

        return back()->with('success', 'User updated successfully.');
    }

    public function toggle(User $user, Request $request): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        $this->activityLogger->log(
            action: 'user.toggled',
            subject: $user,
            actorId: $request->user()?->id,
            description: 'User active status toggled from admin portal.',
            metadata: ['is_active' => $user->is_active]
        );

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user, Request $request): RedirectResponse
    {
        $user->delete();

        $this->activityLogger->log(
            action: 'user.deleted',
            subject: $user,
            actorId: $request->user()?->id,
            description: 'User soft-deleted from admin portal.'
        );

        return back()->with('success', 'User removed successfully.');
    }
}
