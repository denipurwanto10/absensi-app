<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CorrectionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $corrections = AttendanceCorrection::with('employee.user')
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = AttendanceCorrection::where('status', 'pending')->count();

        return view('admin.corrections.index', compact('corrections', 'status', 'pendingCount'));
    }

    /**
     * Tandai koreksi sedang ditinjau (tahap "Diproses" di timeline), sebelum
     * keputusan akhir disetujui/ditolak.
     */
    public function process(Request $request, AttendanceCorrection $correction)
    {
        $correction->update([
            'status' => 'processing',
            'admin_note' => $request->input('admin_note'),
        ]);

        return back()->with('success', 'Koreksi absensi sedang diproses.');
    }

    /**
     * Setujui koreksi. Menerapkan jam yang diminta ke baris absensi (dibuat kalau
     * belum ada), lalu menghitung ulang status hadir/telat berdasarkan jam masuk baru.
     */
    public function approve(Request $request, AttendanceCorrection $correction)
    {
        $attendance = Attendance::firstOrNew([
            'employee_id' => $correction->employee_id,
            'date' => $correction->date->toDateString(),
        ]);

        if ($correction->requested_check_in) {
            $attendance->check_in = $correction->requested_check_in;
            $batasTelat = Setting::current()->batas_telat;
            $attendance->status = $correction->requested_check_in > $batasTelat ? 'telat' : 'hadir';
        }

        if ($correction->requested_check_out) {
            $attendance->check_out = $correction->requested_check_out;
        }

        if (! $attendance->status) {
            $attendance->status = 'hadir';
        }

        $attendance->save();

        $correction->update([
            'attendance_id' => $attendance->id,
            'status' => 'approved',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Koreksi absensi '.$correction->employee->user->name.' disetujui dan data absensi diperbarui.');
    }

    public function reject(Request $request, AttendanceCorrection $correction)
    {
        $request->validate(['admin_note' => ['required', 'string', 'max:500']]);

        $correction->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Koreksi absensi ditolak.');
    }
}
