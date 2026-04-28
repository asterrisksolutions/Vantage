<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - VANTAGE</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .page-header {
            margin-bottom: 30px;
        }
        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .page-header p {
            color: #94a3b8;
            margin: 0;
            font-size: 14px;
        }
        .card {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 25px;
            margin-bottom: 25px;
        }
        .card h2 {
            margin-top: 0;
            font-size: 18px;
            color: white;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .table th {
            background: rgba(255,255,255,0.03);
            font-weight: 600;
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .table td {
            color: #cbd5e0;
            font-size: 14px;
        }
        .table tr:hover {
            background: rgba(255,255,255,0.02);
        }
        .role-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-badge.admin {
            background: rgba(220, 53, 69, 0.3);
            color: #fca5a5;
        }
        .role-badge.manager {
            background: rgba(255, 193, 7, 0.3);
            color: #fcd34d;
        }
        .role-badge.user {
            background: rgba(40, 167, 69, 0.3);
            color: #86efac;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <h1>VANTAGE Admin Dashboard</h1>
        <div class="user-info">
            <span>{{ Auth::user()->name }}</span>
            <button id="logoutBtn" onclick="document.getElementById('logout-form').submit()">Logout</button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <h2>MENU</h2>
            <ul>
                <li onclick="window.location='{{ route('admin.dashboard') }}'">Dashboard</li>
                <li onclick="window.location='{{ route('admin.users') }}'" style="background: rgba(124, 58, 237, 0.2); color: #a78bfa;">User Management</li>
                <li onclick="window.location='{{ route('admin.audit-logs') }}'">Audit Logs</li>
                <li onclick="window.location='{{ route('admin.password.reset.form') }}'">Password Reset</li>
            </ul>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1>User Management</h1>
                <p>Manage system users and their roles.</p>
            </div>

            <div class="card">
                <h2>All Users</h2>
                <p style="color: #94a3b8;">User management functionality coming soon.</p>
            </div>
        </div>
    </div>
</body>
</html>