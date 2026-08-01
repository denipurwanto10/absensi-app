<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $leaves = $employee
            ? $employee->leaves()->latest()->paginate(10)
            : collect();

        $cutiQuota = $employee?->leave_quota ?? 0;
        $cutiUsed = $employee?->cutiUsed() ?? 0;
        $cutiRemaining = $employee?->cutiRemaining() ?? 0;

        return view('karyawan.leaves.index', compact('leaves', 'cutiQuota', 'cutiUsed', 'cutiRemaining'));
    }

    public function create()
    {
        $employee = Auth::user()->employee;

        $cutiQuota = $employee?->leave_quota ?? 0;
        $cutiRemaining = $employee?->cutiRemaining() ?? 0;

        return view('karyawan.leaves.create', compact('cutiQuota', 'cutiRemaining'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akun kamu belum terhubung ke data karyawan. Hubungi admin.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:izin,sakit,cuti'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'submit_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'submit_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        // Kuota cuti hanya berlaku untuk jenis "cuti" (izin & sakit tidak dibatasi kuota).
        if ($validated['type'] === 'cuti') {
            $duration = \Illuminate\Support\Carbon::parse($validated['start_date'])
                ->diffInDays(\Illuminate\Support\Carbon::parse($validated['end_date'])) + 1;
            $remaining = $employee->cutiRemaining();

            if ($duration > $remaining) {
                $year = now()->year;
                throw ValidationException::withMessages([
                    'end_date' => "Pengajuan {$duration} hari melebihi sisa kuota cuti kamu ({$remaining} hari tahun {$year}).",
                ]);
            }
        }

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $employee->leaves()->create([
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment' => $validated['attachment'] ?? null,
            'submit_lat' => $validated['submit_lat'] ?? null,
            'submit_lng' => $validated['submit_lng'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.leaves.index')
            ->with('success', 'Pengajuan berhasil dikirim. Menunggu persetujuan admin.');
    }
}
