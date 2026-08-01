<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    /**
     * Ringkasan dokumen semua karyawan — pencarian cepat & indikator kelengkapan
     * dokumen wajib (KTP, Kontrak Kerja), sebelum admin masuk ke detail per karyawan.
     */
    public function index(Request $request)
    {
        $search = $request->get('q');

        $employees = Employee::with(['user', 'documents'])
            ->when($search, function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            })
            ->orderBy(function ($q) {
                $q->select('name')->from('users')->whereColumn('users.id', 'employees.user_id');
            })
            ->paginate(12)
            ->withQueryString();

        return view('admin.documents.index', compact('employees', 'search'));
    }

    /**
     * Halaman kelola dokumen milik satu karyawan — admin bisa melihat, mengunggah
     * dokumen baru, dan menghapus dokumen yang sudah ada.
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'documents.uploader']);

        return view('admin.documents.show', compact('employee'));
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:ktp,kontrak_kerja,sertifikat,lainnya'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'expires_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $request->file('file');
        $path = $file->store('employee-documents', 'public');

        $employee->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'expires_at' => $validated['expires_at'] ?? null,
            'note' => $validated['note'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        abort_if($document->employee_id !== $employee->id, 404);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
