<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CorrectionController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $corrections = $employee
            ? $employee->corrections()->latest()->paginate(10)
            : collect();

        return view('karyawan.corrections.index', compact('corrections'));
    }

    public function create()
    {
        return view('karyawan.corrections.create');
    }

    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akun kamu belum terhubung ke data karyawan. Hubungi admin.');
        }

        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'requested_check_in' => ['nullable', 'date_format:H:i'],
            'requested_check_out' => ['nullable', 'date_format:H:i'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'submit_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'submit_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if (empty($validated['requested_check_in']) && empty($validated['requested_check_out'])) {
            return back()->withErrors([
                'requested_check_in' => 'Isi minimal salah satu: jam masuk atau jam pulang yang benar.',
            ])->withInput();
        }

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('correction-attachments', 'public');
        }

        $existingAttendance = $employee->attendances()->whereDate('date', $validated['date'])->first();

        $employee->corrections()->create([
            'attendance_id' => $existingAttendance?->id,
            'date' => $validated['date'],
            'requested_check_in' => $validated['requested_check_in'] ?? null,
            'requested_check_out' => $validated['requested_check_out'] ?? null,
            'reason' => $validated['reason'],
            'attachment' => $validated['attachment'] ?? null,
            'submit_lat' => $validated['submit_lat'] ?? null,
            'submit_lng' => $validated['submit_lng'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.corrections.index')
            ->with('success', 'Pengajuan koreksi absensi berhasil dikirim. Menunggu persetujuan admin.');
    }
}
