<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\MatchRequest;
use App\Models\Industry;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MatchmakingController extends Controller
{
        /**
     * Display the matchmaking directory
     */
    public function index(Request $request): Response
    {
        $query = Business::with(['industries', 'country', 'registration'])
            ->where('is_public', true)
            ->whereHas('registration', function ($q) {
                $q->where('status', 'completed');
            });

        // Filter by industry
        if ($request->filled('industry_id')) {
            $query->whereHas('industries', function ($q) use ($request) {
                $q->where('industry_id', $request->industry_id);
            });
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by business type/origin
        if ($request->filled('origin')) {
            $query->where('origin', $request->origin);
        }

        // Search by company name
        if ($request->filled('search')) {
            $query->where('company_name', 'like', '%' . $request->search . '%');
        }

        $businesses = $query->paginate(12)->through(function ($business) {
            return $this->formatBusinessForDirectory($business);
        });

        // Get filter options
        $industries = Industry::orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $origins = ['China', 'Outside', 'Online'];

        return Inertia::render('Matchmaking/Index', [
            'businesses' => $businesses,
            'filters' => [
                'industries' => $industries,
                'countries' => $countries,
                'origins' => $origins,
            ],
            'currentFilters' => $request->only(['industry_id', 'country_id', 'origin', 'search']),
        ]);
    }

    /**
     * Show detailed business profile
     */
    public function show(Business $business): Response
    {
        $userBusiness = auth()->user()->registration->business;

        // Check if user can view this business
        $canViewDetails = $this->canViewBusinessDetails($userBusiness, $business);

        if (!$canViewDetails) {
            return Inertia::render('Matchmaking/Show', [
                'business' => $this->formatBusinessForPublic($business),
                'canViewDetails' => false,
                'hasRequestPending' => $this->hasPendingRequest($userBusiness, $business),
                'error' => 'Access to detailed information requires a match request approval.',
            ]);
        }

        return Inertia::render('Matchmaking/Show', [
            'business' => $this->formatBusinessForDetail($business),
            'canViewDetails' => true,
        ]);
    }

    /**
     * Request access to business details
     */
    public function requestAccess(Business $business): JsonResponse
    {
        $userBusiness = auth()->user()->registration->business;

        // Can't request access to own business
        if ($userBusiness->id === $business->id) {
            return response()->json(['error' => 'Cannot request access to your own business.'], 400);
        }

        // Check if request already exists
        $existingRequest = MatchRequest::where('requester_business_id', $userBusiness->id)
            ->where('target_business_id', $business->id)
            ->first();

        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                return response()->json(['error' => 'Request already pending.'], 400);
            } elseif ($existingRequest->status === 'approved') {
                return response()->json(['error' => 'Already have access to this business.'], 400);
            }
        }

        MatchRequest::create([
            'requester_business_id' => $userBusiness->id,
            'target_business_id' => $business->id,
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Access request sent successfully.']);
    }

    /**
     * Handle incoming match requests (approve/reject)
     */
    public function handleRequest(MatchRequest $matchRequest, Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $userBusiness = auth()->user()->registration->business;

        // Ensure user owns the target business
        if ($matchRequest->target_business_id !== $userBusiness->id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Update request status
        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        $matchRequest->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => $status === 'approved' ? 'Request approved.' : 'Request rejected.',
        ]);
    }

    /**
     * Show user's match requests
     */
    public function requests(): Response
    {
        $userBusiness = auth()->user()->registration->business;

        $sentRequests = MatchRequest::with(['targetBusiness.industries', 'targetBusiness.country'])
            ->where('requester_business_id', $userBusiness->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $receivedRequests = MatchRequest::with(['requesterBusiness.industries', 'requesterBusiness.country'])
            ->where('target_business_id', $userBusiness->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Matchmaking/Requests', [
            'sentRequests' => $sentRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'business' => $this->formatBusinessForDirectory($request->targetBusiness),
                ];
            }),
            'receivedRequests' => $receivedRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'business' => $this->formatBusinessForDirectory($request->requesterBusiness),
                ];
            }),
        ]);
    }

    /**
     * Check if user can view business details
     */
    private function canViewBusinessDetails(Business $userBusiness, Business $targetBusiness): bool
    {
        // Can always view own business
        if ($userBusiness->id === $targetBusiness->id) {
            return true;
        }

        // Check if there's an approved match request
        return MatchRequest::where(function ($query) use ($userBusiness, $targetBusiness) {
            $query->where('requester_business_id', $userBusiness->id)
                  ->where('target_business_id', $targetBusiness->id)
                  ->where('status', 'approved');
        })->orWhere(function ($query) use ($userBusiness, $targetBusiness) {
            $query->where('requester_business_id', $targetBusiness->id)
                  ->where('target_business_id', $userBusiness->id)
                  ->where('status', 'approved');
        })->exists();
    }

    /**
     * Check if there's a pending request between businesses
     */
    private function hasPendingRequest(Business $userBusiness, Business $targetBusiness): bool
    {
        return MatchRequest::where(function ($query) use ($userBusiness, $targetBusiness) {
            $query->where('requester_business_id', $userBusiness->id)
                  ->where('target_business_id', $targetBusiness->id)
                  ->where('status', 'pending');
        })->exists();
    }

    /**
     * Format business for directory listing (public info only)
     */
    private function formatBusinessForDirectory(Business $business): array
    {
        $userBusiness = auth()->user()->registration->business;

        return [
            'id' => $business->id,
            'company_name' => $business->company_name,
            'origin' => $business->origin,
            'attendance_mode' => $business->attendance_mode,
            'industries' => $business->industries->pluck('name'),
            'country' => $business->country?->name,
            'can_view_details' => $this->canViewBusinessDetails($userBusiness, $business),
            'has_pending_request' => $this->hasPendingRequest($userBusiness, $business),
        ];
    }

    /**
     * Format business for public view (limited info)
     */
    private function formatBusinessForPublic(Business $business): array
    {
        return array_merge($this->formatBusinessForDirectory($business), [
            'website' => $business->business_details['website'] ?? null,
            'company_size' => $business->business_details['company_size'] ?? null,
        ]);
    }

    /**
     * Format business for detailed view (full info)
     */
    private function formatBusinessForDetail(Business $business): array
    {
        $userBusiness = auth()->user()->registration->business;

        return array_merge($this->formatBusinessForPublic($business), [
            'email' => $business->email,
            'phone' => $business->phone,
            'street_address' => $business->business_details['street_address'] ?? null,
            'attendees' => $business->attendees->map(function ($attendee) {
                return [
                    'first_name' => $attendee->first_name,
                    'last_name' => $attendee->last_name,
                    'designation' => $attendee->additional_details['designation'] ?? null,
                    'email' => $attendee->email,
                    'phone' => $attendee->phone,
                ];
            }),
            'can_view_details' => $this->canViewBusinessDetails($userBusiness, $business),
        ]);
    }
}
