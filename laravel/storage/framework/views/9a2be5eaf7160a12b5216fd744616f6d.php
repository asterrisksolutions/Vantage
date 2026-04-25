<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/profile.css')); ?>">
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

  <main class="profile-container">
    <div class="profile-card">
      <h2>User Profile</h2>

      <div class="profile-field">
        <span class="label">Name</span>
        <span class="value">John Doe</span>
      </div>

      <div class="profile-field">
        <span class="label">Email</span>
        <span class="value">johndoe@vantage.com</span>
      </div>

      <div class="profile-field">
        <span class="label">Role</span>
        <span class="value">Staff</span>
      </div>

      <div class="profile-field">
        <span class="label">Status</span>
        <span class="value active-status">Active</span>
      </div>

      <div class="profile-actions">
        <button class="change-pass-btn">Change Password</button>
        <a href="<?php echo e(route('user.landing')); ?>">
          <button class="back-btn">Back to Dashboard</button>
        </a>
      </div>
    </div>
  </main>
</body>
</html><?php /**PATH C:\Users\Velasco Ralph\OneDrive\Desktop\Vantage-Demo1 - Copy (2)\resources\views/user/profile.blade.php ENDPATH**/ ?>