<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function scanner()
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akun kamu belum terhubung ke data karyawan. Hubungi admin.');
        }

        $setting = Setting::current();

        return view('karyawan.scanner', compact('setting'));
    }

    /**
     * Endpoint dipanggil via AJAX dari halaman scanner karyawan setelah kamera
     * berhasil membaca QR. Hanya boleh absen dengan QR milik akun yang sedang login.
     * Kalau geofencing aktif, lokasi HP wajib berada dalam radius kantor.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        $employee = Auth::user()->employee;

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Akun kamu belum terhubung ke data karyawan.',
            ], 422);
        }

        if ($request->qr_token !== $employee->qr_token) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code ini bukan milik akun kamu.',
            ], 403);
        }

        $setting = Setting::current();

        if ($setting->geofence_enabled) {
            if (! $request->filled('lat') || ! $request->filled('lng')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi tidak terdeteksi. Aktifkan izin lokasi di browser lalu coba lagi.',
                ], 422);
            }

            $distance = Setting::distanceInMeters(
                (float) $setting->office_lat,
                (float) $setting->office_lng,
                (float) $request->lat,
                (float) $request->lng
            );

            if ($distance > $setting->radius_meters) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu berada di luar area kantor (jarak '.round($distance).' m). Absen ditolak.',
                ], 422);
            }
        }

        $today = now()->toDateString();
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $today,
        ]);

        if (! $attendance->exists) {
            $now = now();
            $status = $now->format('H:i:s') > $setting->batas_telat ? 'telat' : 'hadir';

            $attendance->fill([
                'check_in' => $now->format('H:i:s'),
                'check_in_lat' => $request->lat,
                'check_in_lng' => $request->lng,
                'status' => $status,
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil pukul '.$now->format('H:i'),
                'type' => 'check_in',
                'time' => $now->format('H:i'),
                'status' => $status,
            ]);
        }

        if (! $attendance->check_out) {
            $now = now();
            $attendance->update([
                'check_out' => $now->format('H:i:s'),
                'check_out_lat' => $request->lat,
                'check_out_lng' => $request->lng,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil pukul '.$now->format('H:i'),
                'type' => 'check_out',
                'time' => $now->format('H:i'),
                'status' => $attendance->status,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kamu sudah absen masuk & pulang hari ini.',
        ], 409);
    }
}
