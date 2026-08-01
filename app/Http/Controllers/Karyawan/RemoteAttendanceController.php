<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\RemoteAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RemoteAttendanceController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $remoteAttendances = $employee
            ? $employee->remoteAttendances()->latest('date')->latest('time')->paginate(10)
            : collect();

        return view('karyawan.remote-attendances.index', compact('remoteAttendances'));
    }

    public function create()
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akun kamu belum terhubung ke data karyawan. Hubungi admin.');
        }

        // Tentukan jenis absen yang disarankan: kalau hari ini sudah ada pengajuan
        // "masuk" yang pending/approved, form otomatis diarahkan ke "pulang".
        $today = now()->toDateString();
        $hasMasukToday = $employee->remoteAttendances()
            ->whereDate('date', $today)
            ->where('punch_type', 'masuk')
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        $suggestedPunchType = $hasMasukToday ? 'pulang' : 'masuk';

        return view('karyawan.remote-attendances.create', compact('suggestedPunchType'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akun kamu belum terhubung ke data karyawan. Hubungi admin.');
        }

        $validated = $request->validate([
            'punch_type' => ['required', 'in:masuk,pulang'],
            'category' => ['required', 'in:dinas,lapangan,wfh'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['required', 'image', 'max:3072'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'lat.required' => 'Lokasi GPS wajib diaktifkan sebelum mengirim pengajuan.',
            'lng.required' => 'Lokasi GPS wajib diaktifkan sebelum mengirim pengajuan.',
            'photo.required' => 'Foto bukti wajib dilampirkan.',
        ]);

        $photoPath = $request->file('photo')->store('remote-attendance-photos', 'public');

        $employee->remoteAttendances()->create([
            'date' => now()->toDateString(),
            'punch_type' => $validated['punch_type'],
            'category' => $validated['category'],
            'time' => now()->format('H:i:s'),
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'photo' => $photoPath,
            'note' => $validated['note'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.remote-attendances.index')
            ->with('success', 'Pengajuan absen luar kantor terkirim. Menunggu persetujuan admin.');
    }
}
