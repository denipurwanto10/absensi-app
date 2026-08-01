<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;
        $today = $employee?->todayAttendance();

        $histories = $employee
            ? $employee->attendances()->latest('date')->paginate(10)
            : collect();

        $isHolidayToday = Holiday::isRedDate(now());
        $holidayNameToday = Holiday::nameFor(now()) ?? (now()->isSunday() ? 'Libur Mingguan' : null);

        $cutiQuota = $employee?->leave_quota ?? 0;
        $cutiUsed = $employee?->cutiUsed() ?? 0;
        $cutiRemaining = $employee?->cutiRemaining() ?? 0;

        $monthAttendances = $employee
            ? $employee->attendances()->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->get()
            : collect();

        $hadirCount = $monthAttendances->whereIn('status', ['hadir', 'telat'])->count();
        $telatCount = $monthAttendances->where('status', 'telat')->count();
        $izinCount = $monthAttendances->whereIn('status', ['izin', 'sakit', 'cuti'])->count();
        $alphaCount = $monthAttendances->where('status', 'alpha')->count();

        return view('karyawan.dashboard', compact(
            'employee',
            'today',
            'histories',
            'isHolidayToday',
            'holidayNameToday',
            'cutiQuota',
            'cutiUsed',
            'cutiRemaining',
            'hadirCount',
            'telatCount',
            'izinCount',
            'alphaCount'
        ));
    }
}
