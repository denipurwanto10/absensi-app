<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\RemoteAttendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemoteAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $remoteAttendances = RemoteAttendance::with('employee.user')
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->latest('date')->latest('time')
            ->paginate(15)
            ->withQueryString();

        $pendingCount = RemoteAttendance::where('status', 'pending')->count();

        return view('admin.remote-attendances.index', compact('remoteAttendances', 'status', 'pendingCount'));
    }

    /**
     * Tandai pengajuan sedang ditinjau (tahap "Diproses" di timeline), sebelum
     * keputusan akhir disetujui/ditolak.
     */
    public function process(Request $request, RemoteAttendance $remoteAttendance)
    {
        $remoteAttendance->update([
            'status' => 'processing',
            'admin_note' => $request->input('admin_note'),
        ]);

        return back()->with('success', 'Pengajuan absen luar kantor sedang diproses.');
    }

    /**
     * Setujui pengajuan absen luar kantor. Menerapkan jam & foto yang diajukan ke
     * baris absensi (dibuat kalau belum ada) dan menandai sumbernya sebagai 'remote'
     * supaya tampil beda dari absen QR biasa di laporan.
     */
    public function approve(Request $request, RemoteAttendance $remoteAttendance)
    {
        $attendance = Attendance::firstOrNew([
            'employee_id' => $remoteAttendance->employee_id,
            'date' => $remoteAttendance->date->toDateString(),
        ]);

        if ($remoteAttendance->punch_type === 'masuk') {
            $batasTelat = Setting::current()->batas_telat;
            $attendance->fill([
                'check_in' => $remoteAttendance->time,
                'check_in_lat' => $remoteAttendance->lat,
                'check_in_lng' => $remoteAttendance->lng,
                'check_in_photo' => $remoteAttendance->photo,
                'status' => $remoteAttendance->time > $batasTelat ? 'telat' : 'hadir',
            ]);
        } else {
            $attendance->fill([
                'check_out' => $remoteAttendance->time,
                'check_out_lat' => $remoteAttendance->lat,
                'check_out_lng' => $remoteAttendance->lng,
                'check_out_photo' => $remoteAttendance->photo,
            ]);
            if (! $attendance->status) {
                $attendance->status = 'hadir';
            }
        }

        $attendance->source = 'remote';
        $attendance->remote_category = $remoteAttendance->category;
        $attendance->save();

        $remoteAttendance->update([
            'attendance_id' => $attendance->id,
            'status' => 'approved',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Absen luar kantor '.$remoteAttendance->employee->user->name.' disetujui dan tercatat di laporan absensi.');
    }

    public function reject(Request $request, RemoteAttendance $remoteAttendance)
    {
        $request->validate(['admin_note' => ['required', 'string', 'max:500']]);

        $remoteAttendance->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan absen luar kantor ditolak.');
    }
}
