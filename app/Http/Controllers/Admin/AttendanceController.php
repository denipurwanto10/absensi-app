<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    /**
     * Query dasar laporan absensi dengan filter dari request.
     * Dipakai bersama oleh index() dan export (Excel/PDF) supaya
     * hasil export selalu sesuai filter yang sedang aktif.
     *
     * Prioritas filter: bulan (laporan bulanan) > tanggal spesifik > hari ini (default).
     */
    protected function filteredQuery(Request $request)
    {
        $query = Attendance::with('employee.user');

        if ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->bulan.'-01');
            $query->whereYear('date', $bulan->year)->whereMonth('date', $bulan->month);
        } elseif ($request->filled('date')) {
            $query->whereDate('date', $request->date('date'));
        } else {
            $query->whereDate('date', now()->toDateString());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return $query->orderBy('date')->orderBy('check_in');
    }

    public function index(Request $request)
    {
        $attendances = (clone $this->filteredQuery($request))
            ->latest('date')->latest('check_in')
            ->paginate(20)->withQueryString();
        $employees = Employee::with('user')->orderBy('nip')->get();

        return view('admin.attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Export laporan absensi ke Excel (.csv, langsung terbuka rapi di Excel).
     */
    public function exportExcel(Request $request)
    {
        $attendances = $this->filteredQuery($request)->get();
        $filename = 'laporan-absensi-'.now()->format('Y-m-d_His').'.csv';

        $periode = $request->filled('bulan')
            ? Carbon::parse($request->bulan.'-01')->translatedFormat('F Y')
            : ($request->filled('date')
                ? Carbon::parse($request->date)->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y'));

        $recap = $attendances->countBy('status');

        $callback = function () use ($attendances, $periode, $recap) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM supaya karakter tampil benar di Excel

            fputcsv($handle, ['Laporan Absensi']);
            fputcsv($handle, ['Periode', $periode]);
            fputcsv($handle, ['Diexport', now()->translatedFormat('d F Y, H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Ringkasan',
                'Hadir: '.($recap['hadir'] ?? 0),
                'Telat: '.($recap['telat'] ?? 0),
                'Izin: '.($recap['izin'] ?? 0),
                'Sakit: '.($recap['sakit'] ?? 0),
                'Cuti: '.($recap['cuti'] ?? 0),
                'Alpha: '.($recap['alpha'] ?? 0),
            ]);
            fputcsv($handle, []);

            fputcsv($handle, ['Tanggal', 'NIP', 'Nama', 'Jabatan', 'Masuk', 'Pulang', 'Sumber', 'Status']);

            foreach ($attendances as $a) {
                fputcsv($handle, [
                    $a->date->format('d-m-Y'),
                    $a->employee->nip,
                    $a->employee->user->name,
                    $a->employee->position ?? '-',
                    $a->check_in ? Carbon::parse($a->check_in)->format('H:i') : '-',
                    $a->check_out ? Carbon::parse($a->check_out)->format('H:i') : '-',
                    $a->is_remote ? ($a->remote_category_label.' (Luar Kantor)') : 'QR (Kantor)',
                    $a->status_label,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export laporan absensi ke PDF (butuh package barryvdh/laravel-dompdf,
     * lihat README bagian instalasi).
     */
    public function exportPdf(Request $request)
    {
        $attendances = $this->filteredQuery($request)->get();

        $periode = $request->filled('bulan')
            ? Carbon::parse($request->bulan.'-01')->translatedFormat('F Y')
            : ($request->filled('date')
                ? Carbon::parse($request->date)->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y'));

        $pdf = Pdf::loadView('admin.attendances.pdf', compact('attendances', 'periode'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-absensi-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function scanner()
    {
        return view('admin.attendances.scanner');
    }

    /**
     * Endpoint dipanggil via AJAX dari halaman scanner setelah kamera berhasil membaca QR.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $employee = Employee::with('user')->where('qr_token', $request->qr_token)->first();

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak dikenali.',
            ], 404);
        }

        $today = now()->toDateString();
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $today,
        ]);

        if (! $attendance->exists) {
            // Absen masuk
            $now = now();
            $status = $now->format('H:i:s') > Setting::current()->batas_telat ? 'telat' : 'hadir';

            $attendance->fill([
                'check_in' => $now->format('H:i:s'),
                'status' => $status,
            ])->save();

            return response()->json([
                'success' => true,
                'message' => "Absen masuk berhasil untuk {$employee->user->name}",
                'type' => 'check_in',
                'name' => $employee->user->name,
                'time' => $now->format('H:i'),
                'status' => $status,
            ]);
        }

        if (! $attendance->check_out) {
            // Absen pulang
            $now = now();
            $attendance->update(['check_out' => $now->format('H:i:s')]);

            return response()->json([
                'success' => true,
                'message' => "Absen pulang berhasil untuk {$employee->user->name}",
                'type' => 'check_out',
                'name' => $employee->user->name,
                'time' => $now->format('H:i'),
                'status' => $attendance->status,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "{$employee->user->name} sudah absen masuk & pulang hari ini.",
        ], 409);
    }
}
