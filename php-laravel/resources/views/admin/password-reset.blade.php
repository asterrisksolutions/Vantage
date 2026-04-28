<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Password Reset - VANTAGE</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #cbd5e0;
            font-size: 14px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            background: rgba(255,255,255,0.08);
            color: white;
        }
        .form-group input::placeholder {
            color: #718096;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #a78bfa;
        }
        .btn {
            padding: 12px 24px;
            background: linear-gradient(90deg, #7c3aed, #c026d3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .btn:hover {
            background: linear-gradient(90deg, #6d28d9, #a21caf);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.15);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .user-search {
            position: relative;
        }
        .user-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #1a1a2e;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .user-search-results.show {
            display: block;
        }
        .user-search-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-search-item:hover {
            background: rgba(255,255,255,0.05);
        }
        .user-search-item:last-child {
            border-bottom: none;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-info .name {
            font-weight: 500;
            color: white;
        }
        .user-info .email {
            font-size: 12px;
            color: #718096;
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
        .password-generator {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .password-generator .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .generated-password {
            font-family: monospace;
            font-size: 14px;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            margin-top: 10px;
            display: none;
            color: #a78bfa;
        }
        .generated-password.show {
            display: block;
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
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
        }
        .pagination a,
        .pagination span {
            padding: 8px 14px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            text-decoration: none;
            color: #94a3b8;
            font-size: 13px;
            transition: all 0.2s;
        }
        .pagination a:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        .pagination .active {
            background: linear-gradient(90deg, #7c3aed, #c026d3);
            color: white;
            border-color: transparent;
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
                <li onclick="window.location='{{ route('admin.users') }}'">User Management</li>
                <li onclick="window.location='{{ route('admin.audit-logs') }}'">Audit Logs</li>
                <li onclick="window.location='{{ route('admin.password.reset.form') }}'" style="background: rgba(124, 58, 237, 0.2); color: #a78bfa;">Password Reset</li>
            </ul>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1>Admin Password Reset</h1>
                <p>Reset any user's password. This action will be logged in the audit log.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <h2>Reset User Password</h2>
                
                <form method="POST" action="{{ route('admin.password.reset') }}" id="resetForm">
                    @csrf

                    <div class="form-group">
                        <label for="user_search">Search User</label>
                        <div class="user-search">
                            <input 
                                type="text" 
                                id="user_search" 
                                placeholder="Search by email or name..."
                                autocomplete="off"
                            >
                            <div class="user-search-results" id="searchResults"></div>
                        </div>
                        <input type="hidden" name="user_id" id="user_id" required>
                    </div>

                    <div class="form-group" id="selectedUserInfo" style="display: none;">
                        <label>Selected User</label>
                        <div class="user-info">
                            <span class="name" id="selectedUserName"></span>
                            <span class="email" id="selectedUserEmail"></span>
                            <span class="role-badge" id="selectedUserRole"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="password-generator">
                            <div class="form-group" style="margin-bottom: 0;">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Enter new password"
                                >
                            </div>
                            <button type="button" class="btn btn-secondary" id="generatePassword">
                                Generate
                            </button>
                        </div>
                        <div class="generated-password" id="generatedPassword"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="Confirm new password"
                        >
                    </div>

                    <button type="submit" class="btn">Reset Password</button>
                </form>
            </div>

            <div class="card">
                <h2>User List</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge {{ strtolower($user->role->name ?? 'user') }}">
                                        {{ $user->role->name ?? 'User' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->is_locked)
                                        <span style="color: #fca5a5;">Locked</span>
                                    @else
                                        <span style="color: #86efac;">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn btn-secondary" 
                                        style="padding: 6px 12px; font-size: 12px;"
                                        onclick="selectUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role->name ?? 'User' }}')"
                                    >
                                        Select
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $users->links() }}
            </div>
        </div>
    </div>

    <script>
        // User search functionality
        const searchInput = document.getElementById('user_search');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            
            if (query.length < 2) {
                searchResults.classList.remove('show');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch('{{ route("admin.password.search") }}?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            searchResults.innerHTML = data.map(user => 
                                '<div class="user-search-item" onclick="selectUser(' + user.id + ', \'' + user.name + '\', \'' + user.email + '\', \'' + (user.role_name || 'User') + '\')">' +
                                    '<span class="name">' + user.name + '</span>' +
                                    '<span class="email">' + user.email + '</span>' +
                                '</div>'
                            ).join('');
                            searchResults.classList.add('show');
                        } else {
                            searchResults.classList.remove('show');
                        }
                    });
            }, 300);
        });

        function selectUser(id, name, email, role) {
            document.getElementById('user_id').value = id;
            document.getElementById('selectedUserName').textContent = name;
            document.getElementById('selectedUserEmail').textContent = email;
            document.getElementById('selectedUserRole').textContent = role;
            document.getElementById('selectedUserRole').className = 'role-badge ' + role.toLowerCase();
            document.getElementById('selectedUserInfo').style.display = 'block';
            searchResults.classList.remove('show');
            searchInput.value = email;
        }

        // Password generation
        document.getElementById('generatePassword').addEventListener('click', function() {
            fetch('{{ route("admin.password.generate") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('password').value = data.password;
                    document.getElementById('password_confirmation').value = data.password;
                    const generatedDiv = document.getElementById('generatedPassword');
                    generatedDiv.textContent = 'Generated: ' + data.password;
                    generatedDiv.classList.add('show');
                });
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });
    </script>
</body>
</html>