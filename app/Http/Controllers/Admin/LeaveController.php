<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $leaves = Leave::with('employee.user')
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = Leave::where('status', 'pending')->count();

        return view('admin.leaves.index', compact('leaves', 'status', 'pendingCount'));
    }

    /**
     * Tandai pengajuan sedang ditinjau (tahap "Diproses" di timeline), sebelum
     * keputusan akhir disetujui/ditolak. Opsional — admin boleh langsung
     * approve/reject tanpa lewat tahap ini.
     */
    public function process(Request $request, Leave $leave)
    {
        $leave->update([
            'status' => 'processing',
            'admin_note' => $request->input('admin_note'),
        ]);

        return back()->with('success', 'Pengajuan '.$leave->type_label.' sedang diproses.');
    }

    /**
     * Setujui pengajuan. Otomatis membuat/menimpa baris absensi untuk setiap
     * tanggal dalam rentang izin, supaya tidak tercatat "alpha" di laporan.
     */
    public function approve(Request $request, Leave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

        foreach ($period as $date) {
            Attendance::updateOrCreate(
                ['employee_id' => $leave->employee_id, 'date' => $date->toDateString()],
                ['status' => $leave->type, 'leave_id' => $leave->id]
            );
        }

        return back()->with('success', 'Pengajuan '.$leave->type_label.' disetujui.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate(['admin_note' => ['required', 'string', 'max:500']]);

        $leave->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan '.$leave->type_label.' ditolak.');
    }
}
