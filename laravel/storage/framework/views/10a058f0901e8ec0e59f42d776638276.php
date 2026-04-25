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
    <h2>Login</h2>
    <form method="POST" action="<?php echo e(route('login')); ?>">
      <?php echo csrf_field(); ?>
      <label for="username">Email / Username</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <?php if($errors->any()): ?>
        <p style="color:red; font-size:0.9em;"><?php echo e($errors->first()); ?></p>
      <?php endif; ?>

      <button type="submit">Login</button>
      <p class="forgot">Forgot Password?</p>
    </form>
  </div>
</body>
</html><?php /**PATH C:\Users\Velasco Ralph\OneDrive\Desktop\Vantage-Demo1\resources\views/welcome.blade.php ENDPATH**/ ?>