<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $employee = Auth::user()->employee;

        return view('karyawan.profile', compact('employee'));
    }

    /**
     * Update foto profil. Terpisah dari update password supaya
     * masing-masing form independen (tidak perlu isi password
     * saat hanya ingin ganti foto, begitu juga sebaliknya).
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $employee = Auth::user()->employee;

        if (! $employee) {
            return back()->with('error', 'Akun kamu belum terhubung ke data karyawan.');
        }

        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->update([
            'photo' => $request->file('photo')->store('employees', 'public'),
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
