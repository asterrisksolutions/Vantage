<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VANTAGE Landing Page</title>
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
  <header class="top-bar">
    <h1>VANTAGE</h1>
    <div class="user-info">
      <a href="{{ route('user.profile') }}" class="user-name-link">John Doe</a>
      <a href="{{ route('logout') }}">
        <button id="logoutBtn">Logout</button>
      </a>
    </div>
  </header>

  <main class="dashboard">

    <div class="page-header">
      <div>
        <p class="page-label">VANTAGE DASHBOARD</p>
        <h2 class="page-title">Document Management</h2>
        <p class="page-subtitle">View and manage your uploaded documents</p>
      </div>
      <button class="upload-btn">Upload Document</button>
    </div>

    <div class="document-section">
      <div class="section-header">
        <p class="section-label">REPOSITORY</p>
        <h3 class="section-title">Stored Documents</h3>
      </div>

      <table class="doc-table">
        <thead>
          <tr>
            <th>TITLE</th>
            <th>DATE UPLOADED</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          {{-- Backend will loop documents here later --}}
          {{-- Example placeholder row --}}
          <tr>
            <td>Sample Document</td>
            <td>Apr 25, 2026 10:00 AM</td>
            <td class="action-btns">
              <button class="view-btn">View</button>
              <button class="download-btn">Download</button>
              <button class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </main>
</body>
</html>