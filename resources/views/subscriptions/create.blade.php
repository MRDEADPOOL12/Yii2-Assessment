<!DOCTYPE html>
<html>
<head>
    <title>Create Subscription</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui; background: #f5f7fa; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { margin-bottom: 20px; }
        label { display: block; margin: 15px 0 5px; font-weight: 600; }
        select { width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #4299e1; color: white; border: none; border-radius: 4px; margin-top: 20px; cursor: pointer; }
        .link { display: block; text-align: center; margin-top: 15px; color: #718096; text-decoration: none; }
        .info { background: #ebf8ff; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #2c5282; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Subscription</h1>
        <div class="info"><strong>Info:</strong> Trials auto-expire after 7 days and convert to paid.</div>
        <form action="{{ route('subscriptions.store') }}" method="POST">
            @csrf
            <label>Plan</label>
            <select name="plan_id" required>
                <option value="">Select Plan</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month</option>
                @endforeach
            </select>
            <label>Type</label>
            <select name="type" required>
                <option value="trial">Trial (7 days free)</option>
                <option value="paid">Paid (Immediate billing)</option>
            </select>
            <button>Create Subscription</button>
        </form>
        <a href="{{ route('subscriptions.index') }}" class="link">← Cancel</a>
    </div>
</body>
</html>
