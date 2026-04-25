<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/manager.css')); ?>">
</head>
<body>


  <header class="top-bar">
    <h1>VANTAGE Manager Dashboard</h1>
    <div class="user-info">
      Manager
      <a href="<?php echo e(route('logout')); ?>">
        <button id="logoutBtn">Logout</button>
      </a>
    </div>
  </header>


  <main class="dashboard">
    <section class="summary">
      <div class="card">Pending Approvals</div>
      <div class="card">Change Requests</div>
    </section>



    <div class="content">
      <section class="approvals">
        <h2>Documents Pending Approval</h2>
        <ul>
          <li class="approval_info">Document <br>
            <button class="view-btn">View</button>
            <button class="approve-btn">Approve</button>
          </li>
          <li class="approval_info">Document <br>
            <button class="view-btn">View</button>
            <button class="approve-btn">Approve</button>
          </li>
        </ul>
      </section>



      <section class="changes">
        <h2>Change Requests</h2>
        <ul>
          <li class="changes_info">Request<br><button class="approve-change-btn">Approve</button></li>
          <li class="changes_info">Request<br><button class="approve-change-btn">Approve</button></li>
        </ul>
      </section>
    </div>
  </main>

  
</body>
</html><?php /**PATH C:\Users\Velasco Ralph\OneDrive\Desktop\Vantage-Demo1 - Copy (2)\resources\views/manager/manager.blade.php ENDPATH**/ ?>