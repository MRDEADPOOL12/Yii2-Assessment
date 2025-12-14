<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Subscription - Subscription Management</title>
    <link rel="stylesheet" href="{{ asset('css/subscriptions.css') }}">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <h1>Create Subscription</h1>
        
        <div class="info-box">
            <strong>Info:</strong> Trial subscriptions automatically expire after 7 days and convert to paid subscriptions.
        </div>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <form action="{{ route('subscriptions.store') }}" method="POST">
            @csrf
            
            <label>Plan</label>
            <select name="plan_id" required>
                <option value="">Select a Plan</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month
                    </option>
                @endforeach
            </select>
            @error('plan_id')<div class="error">{{ $message }}</div>@enderror
            
            <label>Subscription Type</label>
            <select name="type" required>
                <option value="trial" {{ old('type') == 'trial' ? 'selected' : '' }}>Trial (7 days free)</option>
                <option value="paid" {{ old('type') == 'paid' ? 'selected' : '' }}>Paid (Immediate billing)</option>
            </select>
            @error('type')<div class="error">{{ $message }}</div>@enderror
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                Create Subscription
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('subscriptions.index') }}" class="link">← Cancel and go back</a>
        </div>
    </div>
</body>
</html>