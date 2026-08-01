<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    /**
     * Tampilkan kalender bulanan dengan tanggal merah otomatis:
     * - Hari Minggu selalu ditandai merah.
     * - Tanggal yang tercatat di tabel holidays (hasil sinkronisasi API
     *   libur nasional ATAU input manual admin) juga ditandai merah/amber.
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

        return view('admin.holidays.index', [
            'cursor' => $cursor,
            'holidaysInMonth' => $holidaysInMonth,
            'holidaysThisYear' => $holidaysThisYear,
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * Tambah hari libur/cuti bersama secara manual (mis. cuti bersama internal
     * perusahaan yang tidak ada di kalender nasional).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:150'],
        ]);

        Holiday::updateOrCreate(
            ['date' => $validated['date']],
            ['name' => $validated['name'], 'source' => 'manual', 'is_national' => false]
        );

        return back()->with('success', 'Hari libur berhasil ditambahkan ke kalender.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Hari libur dihapus dari kalender.');
    }

    /**
     * Sinkronkan daftar hari libur nasional Indonesia untuk satu tahun dari
     * API publik (api-hari-libur.vercel.app), lalu simpan/perbarui ke tabel
     * holidays supaya kalender terisi otomatis tanpa input manual satu-satu.
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'min:2020', 'max:2035'],
        ]);
        $year = $validated['tahun'];

        try {
            $response = Http::timeout(15)->get('https://api-hari-libur.vercel.app/api', [
                'year' => $year,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghubungi layanan kalender libur. Coba lagi nanti atau tambahkan manual.');
        }

        $payload = $response->json();
        $items = $payload['data'] ?? null;

        if (! $response->successful() || ! is_array($items)) {
            return back()->with('error', 'Gagal mengambil data hari libur nasional untuk tahun '.$year.'.');
        }

        $count = 0;

        foreach ($items as $item) {
            $date = $item['date'] ?? null;
            $name = $item['description'] ?? null;

            if (! $date || ! $name) {
                continue;
            }

            Holiday::updateOrCreate(
                ['date' => $date],
                [
                    'name' => $name,
                    'source' => 'api',
                    'is_national' => ! str_contains(strtolower($name), 'cuti bersama'),
                ]
            );
            $count++;
        }

        return back()->with('success', "Berhasil menyinkronkan {$count} hari libur nasional tahun {$year}.");
    }
}
