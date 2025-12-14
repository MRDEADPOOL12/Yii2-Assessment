<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriptions - Subscription Management</title>
    <link rel="stylesheet" href="{{ asset('css/subscriptions.css') }}">
</head>
<body>
    <div class="container">
        <h1>Subscriptions</h1>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <div class="nav">
            <a href="{{ route('subscriptions.create') }}">+ Create Subscription</a>
            @if(auth()->user()->isAdmin())
                <span style="color: #e53e3e; font-weight: bold;">ADMIN MODE</span>
            @endif
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Trial End</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $s)
                <tr>
                    <td>#{{ $s->id }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ $s->plan->name }} - ${{ number_format($s->plan->price, 2) }}</td>
                    <td>{{ ucfirst($s->status) }}</td>
                    <td>{{ ucfirst($s->type) }}</td>
                    <td>{{ $s->trial_end_at?->format('M d, Y') ?? '-' }}</td>
                    <td><a href="{{ route('subscriptions.show', $s) }}" class="link">View</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">No subscriptions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="footer">
            <div>Total Subscriptions: <strong>{{ $totalCount }}</strong></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>