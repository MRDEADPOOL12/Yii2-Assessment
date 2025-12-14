<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

final class SubscriptionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $subscriptions = $this->subscriptionService->getAllSubscriptions();
            $totalCount = Subscription::count();
        } else {
            $subscriptions = $this->subscriptionService->getUserSubscriptions($user);
            $totalCount = Subscription::forUser($user->id)->count();
        }

        return view('subscriptions.index', compact('subscriptions', 'totalCount'));
    }

    public function create(): View
    {
        $plans = Plan::all();
        return view('subscriptions.create', compact('plans'));
    }

    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        try {
            $subscription = $this->subscriptionService->createSubscription($request->validatedData());
            
            return redirect()
                ->route('subscriptions.show', $subscription)
                ->with('success', 'Subscription created successfully');
        } catch (Throwable $e) {
            Log::error('Subscription store failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create subscription. Please try again.');
        }
    }

    public function show(Subscription $subscription): View
    {
        Gate::authorize('view', $subscription);
        
        $subscription->load(['user', 'plan']);
        
        return view('subscriptions.show', compact('subscription'));
    }

    public function cancel(Subscription $subscription): RedirectResponse
    {
        Gate::authorize('cancel', $subscription);

        try {
            $this->subscriptionService->cancelSubscription($subscription);
            
            return redirect()
                ->route('subscriptions.show', $subscription)
                ->with('success', 'Subscription cancelled successfully');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Subscription cancel failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Failed to cancel subscription. Please try again.');
        }
    }
}