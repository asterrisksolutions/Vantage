<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VANTAGE Landing Page</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/landing.css')); ?>">
</head>
<body>
  <header class="top-bar">
    <h1>VANTAGE</h1>
    <div class="user-info">
      John Doe
      <a href="<?php echo e(route('logout')); ?>">
        <button id="logoutBtn">Logout</button>
      </a>
    </div>
  </header>

  <main class="dashboard">
    <div class="content single-section">
      <aside class="quick-actions">
        <h2>Quick Actions</h2>
        <button>Upload Document</button>
        <button>View Documents</button>
        <button>View Activity Logs</button>
        <a href="<?php echo e(route('user.profile')); ?>">
        <button>My Profile</button>
        </a>
      </aside>
    </div>
  </main>

  <footer class="system-info">
    <p class="footer-label">User Landing Page</p>
  </footer>
</body>
</html><?php /**PATH C:\Users\Velasco Ralph\OneDrive\Desktop\Vantage-Demo1\resources\views/user/landing.blade.php ENDPATH**/ ?>