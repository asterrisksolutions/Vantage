<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Documents - VANTAGE</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .main-content { flex: 1; padding: 30px; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { margin: 0 0 10px 0; font-size: 24px; letter-spacing: 1px; }
        .page-header p { color: #94a3b8; margin: 0; font-size: 14px; }
        .card { background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); padding: 25px; margin-bottom: 25px; }
        .card h2 { margin-top: 0; font-size: 18px; color: white; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #94a3b8; font-size: 14px; }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #cbd5e0; font-size: 14px; box-sizing: border-box; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group input[type="file"]::file-selector-button { background: rgba(124, 58, 237, 0.3); border: none; padding: 8px 16px; border-radius: 6px; color: #c4b5fd; cursor: pointer; margin-right: 12px; }
        .form-control { 
            width: 100%; 
            padding: 12px; 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 8px; 
            color: #cbd5e0; 
            font-size: 14px; 
            box-sizing: border-box; 
        }
        .form-control:focus { 
            outline: none; 
            border-color: rgba(124, 58, 237, 0.5); 
        }
        .form-control::placeholder { 
            color: #64748b; 
        }
        select.form-control { 
            appearance: none; 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); 
            background-repeat: no-repeat; 
            background-position: right 12px center; 
            padding-right: 35px; 
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
        .btn-primary { background: rgba(124, 58, 237, 0.3); color: #c4b5fd; }
        .btn-primary:hover { background: rgba(124, 58, 237, 0.5); }
        .btn-danger { background: rgba(220, 53, 69, 0.3); color: #fca5a5; padding: 6px 12px; font-size: 12px; }
        .btn-danger:hover { background: rgba(220, 53, 69, 0.5); }
        .btn-download { background: rgba(59, 130, 246, 0.3); color: #93c5fd; padding: 6px 12px; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-download:hover { background: rgba(59, 130, 246, 0.5); }
        .btn-verify { background: rgba(40, 167, 69, 0.3); color: #86efac; padding: 6px 12px; font-size: 12px; }
        .btn-verify:hover { background: rgba(40, 167, 69, 0.5); }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: rgba(40, 167, 69, 0.2); color: #86efac; border: 1px solid rgba(40, 167, 69, 0.3); }
        .alert-error { background: rgba(220, 53, 69, 0.2); color: #fca5a5; border: 1px solid rgba(220, 53, 69, 0.3); }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .table th { background: rgba(255,255,255,0.03); font-weight: 600; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .table td { color: #cbd5e0; font-size: 14px; }
        .table tr:hover { background: rgba(255,255,255,0.02); }
        .file-type-badge, .classification-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .classification-badge { background: rgba(124, 58, 237, 0.3); color: #c4b5fd; }
        .classification-badge.confidential { background: rgba(220, 53, 69, 0.3); color: #fca5a5; }
        .classification-badge.secret { background: rgba(220, 38, 12, 0.5); color: #ff9999; }
        .classification-badge.unclassified { background: rgba(40, 167, 69, 0.3); color: #86efac; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .search-bar input { flex: 1; min-width: 200px; }
        .search-bar select { min-width: 150px; }
        .metadata-section { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-top: 10px; }
        .metadata-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .metadata-item { font-size: 13px; }
        .metadata-item strong { color: #94a3b8; display: block; margin-bottom: 4px; }
        .metadata-item span { color: #cbd5e0; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); }
        .modal-content { background: #1e293b; margin: 5% auto; padding: 30px; border-radius: 12px; width: 90%; max-width: 700px; border: 1px solid rgba(255,255,255,0.1); max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { margin: 0; color: white; font-size: 18px; }
        .close { color: #94a3b8; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: white; }
        .tab-buttons { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { padding: 8px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: #94a3b8; cursor: pointer; }
        .tab-btn.active { background: rgba(124, 58, 237, 0.3); color: #c4b5fd; border-color: rgba(124, 58, 237, 0.5); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .search-filters { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
        .search-filters .form-group { margin-bottom: 0; flex: 1; min-width: 150px; }
        .search-filters .form-group:first-child { flex: 2; min-width: 250px; }
        .search-filters .btn { padding: 12px 24px; }
        .filter-row { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; }
        .filter-row .form-group { margin-bottom: 0; flex: 1; min-width: 120px; }
        .form-control { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #cbd5e0; font-size: 14px; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: rgba(124, 58, 237, 0.5); }
        .form-control::placeholder { color: #64748b; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 35px; }
    </style>
</head>
<body>
    <div class="top-bar">
        <h1>VANTAGE Documents</h1>
        <div class="user-info">
            <span>{{ Auth::user()->name }}</span>
            <button id="logoutBtn" onclick="document.getElementById('logout-form').submit()">Logout</button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <h2>MENU</h2>
            <ul>
                <li onclick="window.location='{{ route('user.landing') }}'">Dashboard</li>
                <li onclick="window.location='{{ route('user.profile') }}'">Profile</li>
                <li onclick="window.location='{{ route('documents.index') }}'" style="background: rgba(124, 58, 237, 0.2); color: #a78bfa;">Documents</li>
            </ul>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1>My Documents</h1>
                <p>Upload, view, search, and manage your documents with metadata.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <!-- Search and Filter -->
            <div class="card">
                <h2>Search & Filter</h2>
                <form method="GET" action="{{ route('documents.index') }}">
                    <div class="search-filters">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, ID, subject, keywords..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <select name="file_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="image" {{ request('file_type') == 'image' ? 'selected' : '' }}>Images</option>
                                <option value="word" {{ request('file_type') == 'word' ? 'selected' : '' }}>Word</option>
                                <option value="excel" {{ request('file_type') == 'excel' ? 'selected' : '' }}>Excel</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="classification" class="form-control">
                                <option value="">All Classifications</option>
                                <option value="Unclassified" {{ request('classification') == 'Unclassified' ? 'selected' : '' }}>Unclassified</option>
                                <option value="Confidential" {{ request('classification') == 'Confidential' ? 'selected' : '' }}>Confidential</option>
                                <option value="Secret" {{ request('classification') == 'Secret' ? 'selected' : '' }}>Secret</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('documents.index') }}" class="btn" style="background: rgba(255,255,255,0.1); color: #94a3b8;">Clear</a>
                    </div>
                    <div class="filter-row">
                        <div class="form-group">
                            <input type="text" name="agency" class="form-control" placeholder="Agency" value="{{ request('agency') }}">
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" placeholder="Subject" value="{{ request('subject') }}">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Upload Form -->
            <div class="card">
                <h2>Upload New Document</h2>
                <button class="btn btn-primary" onclick="document.getElementById('uploadModal').style.display='block'">+ Upload Document</button>
            </div>

            <!-- Documents List -->
            <div class="card">
                <h2>My Files ({{ $documents->count() }})</h2>
                @if($documents->isEmpty())
                    <div class="empty-state">
                        <p>No documents found. Upload your first document or adjust your search filters.</p>
                    </div>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Unique ID</th>
                                <th>Name</th>
                                <th>Version</th>
                                <th>Type</th>
                                <th>Classification</th>
                                <th>Subject</th>
                                <th>Agency</th>
                                <th>Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td><code style="font-size: 11px;">{{ $document->unique_id }}</code></td>
                                    <td>{{ $document->name }}</td>
                                    <td>v{{ $document->version }}</td>
                                    <td><span class="file-type-badge">{{ $document->file_type }}</span></td>
                                    <td><span class="classification-badge {{ strtolower($document->classification) }}">{{ $document->classification }}</span></td>
                                    <td>{{ $document->subject ?? '-' }}</td>
                                    <td>{{ $document->agency ?? '-' }}</td>
                                    <td>{{ $document->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-download">Download</a>
                                            <button class="btn" style="background: rgba(124, 58, 237, 0.3); color: #c4b5fd; padding: 6px 12px; font-size: 12px;" onclick="showMetadata({{ $document->id }})">View</button>
                                            <form action="{{ route('documents.verify', $document) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-verify" title="Verify integrity">Verify</button>
                                            </form>
                                            <form action="{{ route('documents.destroy', $document) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this document?')">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload Document with Metadata</h3>
                <span class="close" onclick="document.getElementById('uploadModal').style.display='none'">&times;</span>
            </div>
            
            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    <strong>Validation Errors:</strong>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="tab-buttons">
                    <button type="button" class="tab-btn active" onclick="showTab('basic')">Basic Info</button>
                    <button type="button" class="tab-btn" onclick="showTab('descriptive')">Descriptive</button>
                    <button type="button" class="tab-btn" onclick="showTab('technical')">Technical</button>
                    <button type="button" class="tab-btn" onclick="showTab('admin')">Administrative</button>
                </div>

                <div id="tab-basic" class="tab-content active">
                    <div class="form-group">
                        <label for="file">Select File *</label>
                        <input type="file" name="file" id="file" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Document description..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="document_origin">Document Origin</label>
                            <input type="text" name="document_origin" id="document_origin" class="form-control" placeholder="e.g., Department, Office">
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" placeholder="Document subject">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="agency">Agency</label>
                            <input type="text" name="agency" id="agency" class="form-control" placeholder="Agency name">
                        </div>
                        <div class="form-group">
                            <label for="classification">Classification</label>
                            <select name="classification" id="classification" class="form-control">
                                <option value="Unclassified">Unclassified</option>
                                <option value="Confidential">Confidential</option>
                                <option value="Secret">Secret</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="tab-descriptive" class="tab-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" name="author" id="author" class="form-control" placeholder="Document author">
                        </div>
                        <div class="form-group">
                            <label for="document_reference">Document Reference #</label>
                            <input type="text" name="document_reference" id="document_reference" class="form-control" placeholder="e.g., DOC-2026-001">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keywords">Keywords (comma separated)</label>
                        <input type="text" name="keywords" id="keywords" class="form-control" placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>

                <div id="tab-technical" class="tab-content">
                    <div class="form-group">
                        <label for="software_used">Software Used</label>
                        <input type="text" name="software_used" id="software_used" class="form-control" placeholder="e.g., Microsoft Word 2024">
                    </div>
                    <p style="color: #94a3b8; font-size: 13px;">Integrity hash (SHA-256) will be automatically generated upon upload.</p>
                </div>

                <div id="tab-admin" class="tab-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="retention_expiry_date">Retention Expiry Date</label>
                            <input type="date" name="retention_expiry_date" id="retention_expiry_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="copyright">Copyright Information</label>
                            <input type="text" name="copyright" id="copyright" class="form-control" placeholder="Copyright holder">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gps_location">GPS Location (if applicable)</label>
                        <input type="text" name="gps_location" id="gps_location" class="form-control" placeholder="e.g., 40.7128, -74.0060">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Upload Document</button>
            </form>
        </div>
    </div>

    <!-- Metadata View Modal -->
    <div id="metadataModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Document Metadata</h3>
                <span class="close" onclick="document.getElementById('metadataModal').style.display='none'">&times;</span>
            </div>
            <div id="metadataContent"></div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function showMetadata(id) {
            const docs = @json($documents);
            const doc = docs.find(d => d.id === id);
            if (!doc) return;

            const content = `
                <div class="metadata-section">
                    <h4 style="color: white; margin-top: 0;">Basic Information</h4>
                    <div class="metadata-grid">
                        <div class="metadata-item"><strong>Unique ID</strong><span>${doc.unique_id || '-'}</span></div>
                        <div class="metadata-item"><strong>Version</strong><span>v${doc.version || '1.0'}</span></div>
                        <div class="metadata-item"><strong>File Name</strong><span>${doc.name}</span></div>
                        <div class="metadata-item"><strong>File Type</strong><span>${doc.file_type}</span></div>
                        <div class="metadata-item"><strong>File Size</strong><span>${doc.formatted_size}</span></div>
                        <div class="metadata-item"><strong>Classification</strong><span>${doc.classification || 'Unclassified'}</span></div>
                    </div>
                </div>
                <div class="metadata-section">
                    <h4 style="color: white;">Descriptive Metadata</h4>
                    <div class="metadata-grid">
                        <div class="metadata-item"><strong>Author</strong><span>${doc.author || '-'}</span></div>
                        <div class="metadata-item"><strong>Subject</strong><span>${doc.subject || '-'}</span></div>
                        <div class="metadata-item"><strong>Keywords</strong><span>${doc.keywords || '-'}</span></div>
                        <div class="metadata-item"><strong>Document Reference</strong><span>${doc.document_reference || '-'}</span></div>
                        <div class="metadata-item"><strong>Description</strong><span>${doc.description || '-'}</span></div>
                    </div>
                </div>
                <div class="metadata-section">
                    <h4 style="color: white;">Contextual Metadata</h4>
                    <div class="metadata-grid">
                        <div class="metadata-item"><strong>Document Origin</strong><span>${doc.document_origin || '-'}</span></div>
                        <div class="metadata-item"><strong>Agency</strong><span>${doc.agency || '-'}</span></div>
                        <div class="metadata-item"><strong>Uploaded By</strong><span>${doc.user?.name || '-'}</span></div>
                        <div class="metadata-item"><strong>Created Date</strong><span>${new Date(doc.created_at).toLocaleDateString()}</span></div>
                        <div class="metadata-item"><strong>Modified Date</strong><span>${new Date(doc.updated_at).toLocaleDateString()}</span></div>
                    </div>
                </div>
                <div class="metadata-section">
                    <h4 style="color: white;">Technical & Administrative</h4>
                    <div class="metadata-grid">
                        <div class="metadata-item"><strong>Software Used</strong><span>${doc.software_used || '-'}</span></div>
                        <div class="metadata-item"><strong>Integrity Hash (SHA-256)</strong><span style="font-family: monospace; font-size: 11px;">${doc.integrity_hash || '-'}</span></div>
                        <div class="metadata-item"><strong>Retention Expiry</strong><span>${doc.retention_expiry_date || '-'}</span></div>
                        <div class="metadata-item"><strong>Copyright</strong><span>${doc.copyright || '-'}</span></div>
                        <div class="metadata-item"><strong>GPS Location</strong><span>${doc.gps_location || '-'}</span></div>
                    </div>
                </div>
            `;
            document.getElementById('metadataContent').innerHTML = content;
            document.getElementById('metadataModal').style.display = 'block';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>