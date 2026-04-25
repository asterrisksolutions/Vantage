<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/manager.css') }}">
</head>
<body>
  <header class="top-bar">
    <h1>VANTAGE</h1>
    <div class="user-info">
      Manager
      <a href="{{ route('logout') }}">
        <button id="logoutBtn">Logout</button>
      </a>
    </div>
  </header>

  <main class="dashboard">

    <div class="page-header">
      <div>
        <p class="page-label">VANTAGE DASHBOARD</p>
        <h2 class="page-title">Document Review</h2>
        <p class="page-subtitle">Review and approve documents submitted by users</p>
      </div>
    </div>

    <div class="document-section">
      <div class="section-header">
        <p class="section-label">PENDING DOCUMENTS</p>
        <h3 class="section-title">Submitted Documents</h3>
      </div>

      <table class="doc-table">
        <thead>
          <tr>
            <th>USER NAME</th>
            <th>ROLE</th>
            <th>FILE NAME</th>
            <th>DATE UPLOADED</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          {{-- Backend will loop documents here later --}}
          <tr>
            <td>John Doe</td>
            <td>Staff</td>
            <td>Sample Document.pdf</td>
            <td>Apr 25, 2026 10:00 AM</td>
            <td class="action-btns">
              <button class="view-btn">View</button>
              <button class="download-btn">Download</button>
              <button class="approve-btn">Approve</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </main>
</body>
</html>