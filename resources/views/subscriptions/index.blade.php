<!DOCTYPE html>
<html>
<head>
    <title>Subscriptions</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { margin-bottom: 20px; }
        .nav { display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0; }
        .nav a { background: #4299e1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #edf2f7; font-weight: 600; }
        .link { color: #4299e1; text-decoration: none; }
        .success { background: #c6f6d5; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 2px solid #e2e8f0; display: flex; justify-content: space-between; }
        button { background: #e53e3e; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subscriptions</h1>
        @if(session('success'))<div class="success">{{ session('success') }}</div>@endif
        <div class="nav">
            <a href="{{ route('subscriptions.create') }}">+ Create</a>
            @if(auth()->user()->isAdmin())<span style="color: #e53e3e; font-weight: bold;">ADMIN</span>@endif
        </div>
        <table>
            <tr><th>ID</th><th>User</th><th>Plan</th><th>Status</th><th>Type</th><th>Trial End</th><th>Actions</th></tr>
            @forelse($subscriptions as $s)
            <tr>
                <td>#{{ $s->id }}</td>
                <td>{{ $s->user->name }}</td>
                <td>{{ $s->plan->name }} - ${{ $s->plan->price }}</td>
                <td>{{ $s->status }}</td>
                <td>{{ $s->type }}</td>
                <td>{{ $s->trial_end_at?->format('M d, Y') ?? '-' }}</td>
                <td><a href="{{ route('subscriptions.show', $s) }}" class="link">View</a></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; padding: 40px;">No subscriptions found</td></tr>
            @endforelse
        </table>
        <div class="footer">
            <div>Total: {{ $totalCount }}</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button>Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
