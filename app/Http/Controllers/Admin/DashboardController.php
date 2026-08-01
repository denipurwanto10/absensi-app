<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\RemoteAttendance;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Employee::count();
        $izinPending = Leave::where('status', 'pending')->count();
        $remotePending = RemoteAttendance::where('status', 'pending')->count();
        $hadirHariIni = Attendance::whereDate('date', now()->toDateString())
            ->whereIn('status', ['hadir', 'telat'])
            ->count();
        $telatHariIni = Attendance::whereDate('date', now()->toDateString())
            ->where('status', 'telat')
            ->count();
        $belumAbsen = $totalKaryawan - $hadirHariIni;

        $absensiHariIni = Attendance::with('employee.user')
            ->whereDate('date', now()->toDateString())
            ->latest('check_in')
            ->get();

        $isHolidayToday = Holiday::isRedDate(now());
        $holidayNameToday = Holiday::nameFor(now()) ?? (now()->isSunday() ? 'Libur Mingguan' : null);

        // ---------- Tren kehadiran 14 hari terakhir ----------
        $rangeStart = now()->copy()->subDays(13)->startOfDay();
        $trendRaw = Attendance::selectRaw('date, status, COUNT(*) as total')
            ->whereDate('date', '>=', $rangeStart->toDateString())
            ->groupBy('date', 'status')
            ->get()
            ->groupBy(fn ($row) => $row->date instanceof Carbon ? $row->date->toDateString() : Carbon::parse($row->date)->toDateString());

        $trendLabels = [];
        $trendHadir = [];
        $trendTelat = [];
        $trendIzin = [];
        $trendAlpha = [];

        for ($i = 13; $i >= 0; $i--) {
            $day = now()->copy()->subDays($i);
            $key = $day->toDateString();
            $rows = $trendRaw->get($key, collect());

            $trendLabels[] = $day->translatedFormat('d M');
            $trendHadir[] = (int) $rows->firstWhere('status', 'hadir')?->total;
            $trendTelat[] = (int) $rows->firstWhere('status', 'telat')?->total;
            $trendIzin[] = (int) $rows->whereIn('status', ['izin', 'sakit', 'cuti'])->sum('total');
            $trendAlpha[] = (int) $rows->firstWhere('status', 'alpha')?->total;
        }

        // ---------- Top 5 keterlambatan bulan ini ----------
        $topTelat = Attendance::with('employee.user')
            ->selectRaw('employee_id, COUNT(*) as total')
            ->where('status', 'telat')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->groupBy('employee_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'hadirHariIni',
            'telatHariIni',
            'belumAbsen',
            'absensiHariIni',
            'izinPending',
            'remotePending',
            'isHolidayToday',
            'holidayNameToday',
            'trendLabels',
            'trendHadir',
            'trendTelat',
            'trendIzin',
            'trendAlpha',
            'topTelat'
        ));
    }
}
