<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription #{{ $subscription->id }} - Subscription Management</title>
    <link rel="stylesheet" href="{{ asset('css/subscriptions.css') }}">
</head>
<body>
    <div class="container" style="max-width: 800px;">
        <h1>Subscription #{{ $subscription->id }}</h1>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <div class="grid">
            <div class="label">User:</div>
            <div>{{ $subscription->user->name }} ({{ $subscription->user->email }})</div>
            
            <div class="label">Plan:</div>
            <div>{{ $subscription->plan->name }} - ${{ number_format($subscription->plan->price, 2) }}/month</div>
            
            <div class="label">Status:</div>
            <div>{{ ucfirst($subscription->status) }}</div>
            
            <div class="label">Type:</div>
            <div>{{ ucfirst($subscription->type) }}</div>
            
            <div class="label">Trial End:</div>
            <div>{{ $subscription->trial_end_at?->format('M d, Y g:i A') ?? 'N/A' }}</div>
            
            <div class="label">Started:</div>
            <div>{{ $subscription->started_at?->format('M d, Y g:i A') ?? 'N/A' }}</div>
            
            <div class="label">Ended:</div>
            <div>{{ $subscription->ended_at?->format('M d, Y g:i A') ?? 'N/A' }}</div>
            
            <div class="label">Created:</div>
            <div>{{ $subscription->created_at->format('M d, Y g:i A') }}</div>
        </div>
        
        <div class="actions">
            @can('cancel', $subscription)
                @if($subscription->canBeCancelled())
                    <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" style="display: inline;">
                        @csrf
                        <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you want to cancel this subscription?')">
                            Cancel Subscription
                        </button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">← Back to List</a>
        </div>
    </div>
</body>
</html>