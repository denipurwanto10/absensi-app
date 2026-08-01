<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $documents = $employee ? $employee->documents : collect();

        return view('karyawan.documents.index', compact('employee', 'documents'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akun kamu belum terhubung ke data karyawan. Hubungi admin.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:ktp,kontrak_kerja,sertifikat,lainnya'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $request->file('file');
        $path = $file->store('employee-documents', 'public');

        $employee->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'note' => $validated['note'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah. Admin dapat melihatnya di data karyawan.');
    }

    public function destroy(EmployeeDocument $document)
    {
        $employee = Auth::user()->employee;

        abort_if(! $employee || $document->employee_id !== $employee->id, 404);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
