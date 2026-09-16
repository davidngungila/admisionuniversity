<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = ApplicationDocument::with(['application.applicant'])->latest();
        if ($request->filled('verified')) $q->where('is_verified', $request->verified === 'yes');

        return view('admin.documents.index', ['documents'=>$q->paginate(20)->withQueryString()]);
    }

    public function verify(Request $request, ApplicationDocument $document)
    {
        $d = $request->validate(['is_verified'=>['required','boolean'],'verification_notes'=>['nullable','string']]);
        $document->update([
            'is_verified'=>$request->boolean('is_verified'),
            'verified_by'=>auth()->id(),
            'verified_at'=>now(),
            'verification_notes'=>$d['verification_notes'] ?? null,
        ]);
        return back()->with('success', $request->boolean('is_verified') ? 'Document verified.' : 'Document marked not verified.');
    }

    public function preview(ApplicationDocument $document)
    {
        $path = $document->file_path;
        // Normalize path: stored as storage/... or applications/...
        $storagePath = str_replace('storage/', '', $path);
        // Try public disk first, then local
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
            $fullPath = Storage::disk('public')->path($storagePath);
            $detected = @mime_content_type($fullPath) ?: 'application/octet-stream';
            $storedMime = (string) ($document->mime_type ?? '');
            $mime = (str_contains($storedMime, '/') && $storedMime !== 'application/octet-stream') ? $storedMime : $detected;
            return response()->file($fullPath, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="'.$document->file_name.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }
        if (Storage::exists($storagePath)) {
            return Storage::response($storagePath, $document->file_name, ['Content-Disposition'=>'inline']);
        }
        abort(404, 'File not found.');
    }
}