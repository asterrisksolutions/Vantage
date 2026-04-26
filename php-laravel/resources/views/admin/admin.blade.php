<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - User Management</title>
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>


  <header class="top-bar">
    <h1>VANTAGE Admin Dashboard</h1>
    <div class="user-info">
      Admin
      <a href="{{ route('logout') }}">
        <button id="logoutBtn">Logout</button>
      </a>
    </div>
  </header>


  <div class="main-container">
    <aside class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li>sidebar1</li>
        <li>sidebar2</li>
        <li class="active">User Management</li>
        <li>sidebar3</li>
        <li>sidebar4</li>
        <li>sidebar5</li>
      </ul>
    </aside>



    <main class="content">
      <div class="section-header">
        <h2>User Management</h2>
        <a href="{{ route('register') }}">
        <button class="create-btn">+ Add User</button>
        </a>
      </div>
      <section class="user-table-section">
        <table>
          <thead>
            <tr>
              <th>User Name</th>
              <th>Role</th>
              <th>Lock</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>John Doe</td>
              <td>Staff</td>
              <td><button class="lock-btn" onclick="toggleLock(this)">Lock</button></td>
            </tr>
            <tr>
              <td>Manager</td>
              <td>Manager</td>
              <td><button class="lock-btn" onclick="toggleLock(this)">Lock</button></td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>


  <script>
    function toggleLock(button) {
      if (button.textContent === "Lock") {
        button.textContent = "Unlock";
        button.classList.add("locked");
        alert("User has been locked.");
      } else {
        button.textContent = "Lock";
        button.classList.remove("locked");
        alert("User has been unlocked.");
      }
    }

    document.getElementById("logoutBtn").addEventListener("click", function() {
      window.location.href = "{{ route('logout') }}";
    });
  </script>
</body>
</html>