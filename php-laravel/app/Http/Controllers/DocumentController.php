<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Display a listing of the user's documents with search and filters.
     */
    public function index(Request $request)
    {
        $query = Document::where('user_id', Auth::id());

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('unique_id', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('agency', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%")
                  ->orWhere('document_reference', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by file type
        if ($request->has('file_type') && !empty($request->file_type)) {
            $query->where('file_type', 'like', "%{$request->file_type}%");
        }

        // Filter by classification
        if ($request->has('classification') && !empty($request->classification)) {
            $query->where('classification', $request->classification);
        }

        // Filter by agency
        if ($request->has('agency') && !empty($request->agency)) {
            $query->where('agency', 'like', "%{$request->agency}%");
        }

        // Filter by subject
        if ($request->has('subject') && !empty($request->subject)) {
            $query->where('subject', 'like', "%{$request->subject}%");
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return view('documents.index', compact('documents'));
    }

    /**
     * Upload a new document with metadata.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'description' => 'nullable|string|max:500',
            // Metadata validation
            'document_origin' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:500',
            'document_reference' => 'nullable|string|max:100',
            'software_used' => 'nullable|string|max:255',
            'retention_expiry_date' => 'nullable|date',
            'copyright' => 'nullable|string|max:500',
            'gps_location' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        
        // Store the file
        $path = $file->store('documents', 'private');
        
        // Generate integrity hash
        $hash = hash_file('sha256', storage_path('app/private/' . $path));

        // Create document record with all metadata
        $document = Document::create([
            'user_id' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
            'version' => '1.0',
            'integrity_hash' => $hash,
            // Metadata fields
            'document_origin' => $request->input('document_origin'),
            'subject' => $request->input('subject'),
            'agency' => $request->input('agency'),
            'classification' => $request->input('classification') ?? 'Unclassified',
            'author' => $request->input('author'),
            'keywords' => $request->input('keywords'),
            'document_reference' => $request->input('document_reference'),
            'software_used' => $request->input('software_used'),
            'retention_expiry_date' => $request->input('retention_expiry_date'),
            'copyright' => $request->input('copyright'),
            'gps_location' => $request->input('gps_location'),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Upload a new version of an existing document.
     */
    public function storeVersion(Request $request, Document $parentDocument)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        // Verify ownership
        if ($parentDocument->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $file = $request->file('file');
        $path = $file->store('documents', 'private');
        $hash = hash_file('sha256', storage_path('app/private/' . $path));

        // Increment version
        $versionParts = explode('.', $parentDocument->version);
        $newVersion = $versionParts[0] . '.' . ($versionParts[1] ?? 0 + 1);

        $document = Document::create([
            'user_id' => Auth::id(),
            'parent_document_id' => $parentDocument->id,
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
            'version' => $newVersion,
            'integrity_hash' => $hash,
            // Copy metadata from parent
            'document_origin' => $parentDocument->document_origin,
            'subject' => $parentDocument->subject,
            'agency' => $parentDocument->agency,
            'classification' => $parentDocument->classification,
            'author' => $parentDocument->author,
            'keywords' => $parentDocument->keywords,
            'document_reference' => $parentDocument->document_reference,
            'software_used' => $request->software_used,
            'copyright' => $parentDocument->copyright,
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'New version uploaded successfully.');
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        // Ensure user can only download their own documents
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return Storage::disk('private')->download($document->file_path, $document->name);
    }

    /**
     * Delete a document.
     */
    public function destroy(Document $document)
    {
        // Ensure user can only delete their own documents
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file from storage
        Storage::disk('private')->delete($document->file_path);
        
        // Delete database record
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Verify document integrity.
     */
    public function verify(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $currentHash = hash_file('sha256', storage_path('app/private/' . $document->file_path));
        $isValid = $currentHash === $document->integrity_hash;

        return redirect()->route('documents.index')
            ->with($isValid ? 'success' : 'error', 
                $isValid ? 'Document integrity verified successfully.' : 'Document integrity check FAILED - file may have been tampered with.');
    }
}