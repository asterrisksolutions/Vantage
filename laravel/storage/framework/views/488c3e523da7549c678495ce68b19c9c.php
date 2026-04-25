<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VANTAGE Login</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
</head>
<body>
  <div class="login-container">
    <p class="system-label">VANTAGE SYSTEM</p>
    <h2>Secure Login</h2>
    <p class="subtitle">Access the document management system</p>
    <form method="POST" action="<?php echo e(route('login')); ?>">
      <?php echo csrf_field(); ?>
      <label for="username">Email</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <?php if($errors->any()): ?>
        <p style="color:#fc8181; font-size:0.9em;"><?php echo e($errors->first()); ?></p>
      <?php endif; ?>

      <p class="forgot">Forgot your password?</p>

      <p class="register-link">Don't have an account? <a href="<?php echo e(route('register')); ?>">Register</a></p>

      <button type="submit">LOG IN</button>
    </form>
  </div>
</body>
</html><?php /**PATH C:\Users\Velasco Ralph\OneDrive\Desktop\Vantage-Demo1 - Copy (2)\resources\views/welcome.blade.php ENDPATH**/ ?>