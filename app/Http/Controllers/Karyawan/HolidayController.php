<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HolidayController extends Controller
{
    /**
     * Kalender libur untuk karyawan — hanya lihat, tidak bisa tambah/hapus.
     */
    public function index(Request $request)
    {
        $month = (int) $request->get('bulan', now()->month);
        $year = (int) $request->get('tahun', now()->year);

        $cursor = Carbon::create($year, $month, 1);

        $holidaysInMonth = Holiday::forMonth($year, $month);
        $holidaysThisYear = Holiday::query()
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        return view('karyawan.holidays.index', [
            'cursor' => $cursor,
            'holidaysInMonth' => $holidaysInMonth,
            'holidaysThisYear' => $holidaysThisYear,
            'year' => $year,
            'month' => $month,
        ]);
    }
}
