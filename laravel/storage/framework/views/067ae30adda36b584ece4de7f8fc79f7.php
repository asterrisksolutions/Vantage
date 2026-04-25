<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VANTAGE Register</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/register.css')); ?>">
</head>
<body>
  <div class="register-container">
    <p class="system-label">VANTAGE SYSTEM</p>
    <h2>Create Account</h2>
    <p class="subtitle">Register to access the document management system</p>

    <form method="POST" action="<?php echo e(route('register')); ?>" id="registerForm">
      <?php echo csrf_field(); ?>

      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <label for="password_confirmation">Confirm Password</label>
      <input type="password" id="password_confirmation" name="password_confirmation" required>

      <p id="password-error" class="error-msg" style="display:none;">Passwords do not match.</p>

      <?php if($errors->any()): ?>
        <p class="error-msg"><?php echo e($errors->first()); ?></p>
      <?php endif; ?>

      <button type="submit">CREATE ACCOUNT</button>

      <p class="login-link">Already have an account? <a href="/">Login here</a></p>
    </form>
  </div>

  <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirm = document.getElementById('password_confirmation').value;
      const errorMsg = document.getElementById('password-error');

      if (password !== confirm) {
        e.preventDefault();
        errorMsg.style.display = 'block';
      } else {
        errorMsg.style.display = 'none';
      }
    });
  </script>
</body>
</html><?php /**PATH C:\Users\Velasco Ralph\OneDrive\Desktop\Vantage-Demo1 - Copy (2)\resources\views/register/register.blade.php ENDPATH**/ ?>