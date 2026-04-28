<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VANTAGE Landing Page</title>
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
  <style>
    .alert {
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }
    .alert-success {
      background: rgba(40, 167, 69, 0.2);
      color: #86efac;
      border: 1px solid rgba(40, 167, 69, 0.3);
    }
    .alert-error {
      background: rgba(220, 53, 69, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(220, 53, 69, 0.3);
    }
    .file-type-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      background: rgba(59, 130, 246, 0.3);
      color: #93c5fd;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
    }
    .modal-content {
      background: #1e293b;
      margin: 10% auto;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      border: 1px solid rgba(255,255,255,0.1);
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .modal-header h3 {
      margin: 0;
      color: white;
      font-size: 18px;
    }
    .close {
      color: #94a3b8;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover {
      color: white;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #94a3b8;
      font-size: 14px;
    }
    .form-group input[type="file"],
    .form-group textarea {
      width: 100%;
      padding: 12px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 8px;
      color: #cbd5e0;
      font-size: 14px;
      box-sizing: border-box;
    }
    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }
    .form-group input[type="file"]::file-selector-button {
      background: rgba(124, 58, 237, 0.3);
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      color: #c4b5fd;
      cursor: pointer;
      margin-right: 12px;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
    }
    .btn-primary {
      background: rgba(124, 58, 237, 0.3);
      color: #c4b5fd;
    }
    .btn-primary:hover {
      background: rgba(124, 58, 237, 0.5);
    }
    .empty-state {
      text-align: center;
      padding: 40px;
      color: #94a3b8;
    }
  </style>
</head>
<body>
  <header class="top-bar">
    <h1>VANTAGE</h1>
    <div class="user-info">
      <a href="{{ route('user.profile') }}" class="user-name-link">{{ Auth::user()->name }}</a>
      <a href="{{ route('logout') }}">
        <button id="logoutBtn">Logout</button>
      </a>
    </div>
  </header>

  <main class="dashboard">

    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-error">
        {{ session('error') }}
      </div>
    @endif

    <div class="page-header">
      <div>
        <p class="page-label">VANTAGE DASHBOARD</p>
        <h2 class="page-title">Document Management</h2>
        <p class="page-subtitle">View and manage your uploaded documents</p>
      </div>
      <button class="upload-btn" onclick="document.getElementById('uploadModal').style.display='block'">Upload Document</button>
    </div>

    <div class="document-section">
      <div class="section-header">
        <p class="section-label">REPOSITORY</p>
        <h3 class="section-title">Stored Documents</h3>
      </div>

      @if($documents->isEmpty())
        <div class="empty-state">
          <p>No documents uploaded yet. Click "Upload Document" to add your first file.</p>
        </div>
      @else
        <table class="doc-table">
          <thead>
            <tr>
              <th>TITLE</th>
              <th>TYPE</th>
              <th>SIZE</th>
              <th>DATE UPLOADED</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            @foreach($documents as $document)
              <tr>
                <td>{{ $document->name }}</td>
                <td><span class="file-type-badge">{{ $document->file_type }}</span></td>
                <td>{{ $document->formatted_size }}</td>
                <td>{{ $document->created_at->format('M d, Y h:i A') }}</td>
                <td class="action-btns">
                  <a href="{{ route('documents.download', $document) }}" class="download-btn">Download</a>
                  <form action="{{ route('documents.destroy', $document) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

  </main>

  <!-- Upload Modal -->
  <div id="uploadModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Upload Document</h3>
        <span class="close" onclick="document.getElementById('uploadModal').style.display='none'">&times;</span>
      </div>
      <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label for="file">Select File</label>
          <input type="file" name="file" id="file" required>
        </div>
        <div class="form-group">
          <label for="description">Description (optional)</label>
          <textarea name="description" id="description" placeholder="Add a description for this document..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
      </form>
    </div>
  </div>

  <script>
    // Close modal when clicking outside
    window.onclick = function(event) {
      var modal = document.getElementById('uploadModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>