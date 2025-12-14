<!DOCTYPE html>
<html>
<head>
    <title>Subscription #{{ $subscription->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui; background: #f5f7fa; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin: 20px 0; }
        .label { font-weight: 600; }
        .actions { margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px; }
        .btn-danger { background: #fc8181; color: white; }
        .btn-secondary { background: #cbd5e0; color: #2d3748; }
        .success { background: #c6f6d5; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subscription #{{ $subscription->id }}</h1>
        @if(session('success'))<div class="success">{{ session('success') }}</div>@endif
        <div class="grid">
            <div class="label">User:</div><div>{{ $subscription->user->name }} ({{ $subscription->user->email }})</div>
            <div class="label">Plan:</div><div>{{ $subscription->plan->name }} - ${{ $subscription->plan->price }}/month</div>
            <div class="label">Status:</div><div>{{ ucfirst($subscription->status) }}</div>
            <div class="label">Type:</div><div>{{ ucfirst($subscription->type) }}</div>
            <div class="label">Trial End:</div><div>{{ $subscription->trial_end_at?->format('M d, Y g:i A') ?? 'N/A' }}</div>
            <div class="label">Started:</div><div>{{ $subscription->started_at?->format('M d, Y g:i A') ?? 'N/A' }}</div>
            <div class="label">Ended:</div><div>{{ $subscription->ended_at?->format('M d, Y g:i A') ?? 'N/A' }}</div>
            <div class="label">Created:</div><div>{{ $subscription->created_at->format('M d, Y g:i A') }}</div>
        </div>
        <div class="actions">
            @can('cancel', $subscription)
                @if($subscription->canBeCancelled())
                    <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" style="display: inline;">
                        @csrf
                        <button class="btn btn-danger" onclick="return confirm('Cancel subscription?')">Cancel Subscription</button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">← Back</a>
        </div>
    </div>
</body>
</html>
