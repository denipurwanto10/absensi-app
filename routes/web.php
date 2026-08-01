<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CorrectionController as AdminCorrectionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeDocumentController;
use App\Http\Controllers\Admin\HolidayController as AdminHolidayController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\RemoteAttendanceController as AdminRemoteAttendanceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Karyawan\AnnouncementController as KaryawanAnnouncementController;
use App\Http\Controllers\Karyawan\AttendanceController as KaryawanAttendanceController;
use App\Http\Controllers\Karyawan\CorrectionController as KaryawanCorrectionController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\DocumentController as KaryawanDocumentController;
use App\Http\Controllers\Karyawan\HolidayController as KaryawanHolidayController;
use App\Http\Controllers\Karyawan\LeaveController as KaryawanLeaveController;
use App\Http\Controllers\Karyawan\ProfileController as KaryawanProfileController;
use App\Http\Controllers\Karyawan\RemoteAttendanceController as KaryawanRemoteAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/karyawan', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/karyawan/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/karyawan', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/karyawan/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/karyawan/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/karyawan/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/karyawan/{employee}/qrcode', [EmployeeController::class, 'showQr'])->name('employees.qrcode');
    Route::post('/karyawan/{employee}/regenerate-qr', [EmployeeController::class, 'regenerateQr'])->name('employees.regenerate-qr');

    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/absensi/scanner', [AttendanceController::class, 'scanner'])->name('attendances.scanner');
    Route::post('/absensi/scan', [AttendanceController::class, 'scan'])->name('attendances.scan');
    Route::get('/absensi/export/excel', [AttendanceController::class, 'exportExcel'])->name('attendances.export-excel');
    Route::get('/absensi/export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendances.export-pdf');

    Route::get('/izin', [AdminLeaveController::class, 'index'])->name('leaves.index');
    Route::post('/izin/{leave}/process', [AdminLeaveController::class, 'process'])->name('leaves.process');
    Route::post('/izin/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/izin/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');

    Route::get('/koreksi', [AdminCorrectionController::class, 'index'])->name('corrections.index');
    Route::post('/koreksi/{correction}/process', [AdminCorrectionController::class, 'process'])->name('corrections.process');
    Route::post('/koreksi/{correction}/approve', [AdminCorrectionController::class, 'approve'])->name('corrections.approve');
    Route::post('/koreksi/{correction}/reject', [AdminCorrectionController::class, 'reject'])->name('corrections.reject');

    Route::get('/absen-luar', [AdminRemoteAttendanceController::class, 'index'])->name('remote-attendances.index');
    Route::post('/absen-luar/{remoteAttendance}/process', [AdminRemoteAttendanceController::class, 'process'])->name('remote-attendances.process');
    Route::post('/absen-luar/{remoteAttendance}/approve', [AdminRemoteAttendanceController::class, 'approve'])->name('remote-attendances.approve');
    Route::post('/absen-luar/{remoteAttendance}/reject', [AdminRemoteAttendanceController::class, 'reject'])->name('remote-attendances.reject');

    Route::get('/dokumen-karyawan', [EmployeeDocumentController::class, 'index'])->name('documents.index');
    Route::get('/dokumen-karyawan/{employee}', [EmployeeDocumentController::class, 'show'])->name('documents.show');
    Route::post('/dokumen-karyawan/{employee}', [EmployeeDocumentController::class, 'store'])->name('documents.store');
    Route::delete('/dokumen-karyawan/{employee}/{document}', [EmployeeDocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/pengumuman', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/pengumuman', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/pengumuman/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/kalender', [AdminHolidayController::class, 'index'])->name('holidays.index');
    Route::post('/kalender', [AdminHolidayController::class, 'store'])->name('holidays.store');
    Route::delete('/kalender/{holiday}', [AdminHolidayController::class, 'destroy'])->name('holidays.destroy');
    Route::post('/kalender/sinkronkan', [AdminHolidayController::class, 'sync'])->name('holidays.sync');

    Route::get('/pengaturan', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/scan', [KaryawanAttendanceController::class, 'scanner'])->name('attendances.scanner');
    Route::post('/scan', [KaryawanAttendanceController::class, 'scan'])->name('attendances.scan');

    Route::get('/profil', [KaryawanProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profil/foto', [KaryawanProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profil/password', [KaryawanProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/izin', [KaryawanLeaveController::class, 'index'])->name('leaves.index');
    Route::get('/izin/ajukan', [KaryawanLeaveController::class, 'create'])->name('leaves.create');
    Route::post('/izin', [KaryawanLeaveController::class, 'store'])->name('leaves.store');

    Route::get('/koreksi', [KaryawanCorrectionController::class, 'index'])->name('corrections.index');
    Route::get('/koreksi/ajukan', [KaryawanCorrectionController::class, 'create'])->name('corrections.create');
    Route::post('/koreksi', [KaryawanCorrectionController::class, 'store'])->name('corrections.store');

    Route::get('/absen-luar', [KaryawanRemoteAttendanceController::class, 'index'])->name('remote-attendances.index');
    Route::get('/absen-luar/ajukan', [KaryawanRemoteAttendanceController::class, 'create'])->name('remote-attendances.create');
    Route::post('/absen-luar', [KaryawanRemoteAttendanceController::class, 'store'])->name('remote-attendances.store');

    Route::get('/dokumen', [KaryawanDocumentController::class, 'index'])->name('documents.index');
    Route::post('/dokumen', [KaryawanDocumentController::class, 'store'])->name('documents.store');
    Route::delete('/dokumen/{document}', [KaryawanDocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/pengumuman', [KaryawanAnnouncementController::class, 'index'])->name('announcements.index');

    Route::get('/kalender', [KaryawanHolidayController::class, 'index'])->name('holidays.index');
});
