<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'nip' => ['required', 'string', 'unique:employees,nip'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'leave_quota' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('employees', 'public')
            : null;

        DB::transaction(function () use ($data, $photoPath) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'karyawan',
            ]);

            Employee::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'position' => $data['position'] ?? null,
                'phone' => $data['phone'] ?? null,
                'photo' => $photoPath,
                'qr_token' => 'EMP-'.Str::upper(Str::random(10)),
                'leave_quota' => $data['leave_quota'] ?? 12,
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');

        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$employee->user_id],
            'password' => ['nullable', 'string', 'min:6'],
            'nip' => ['required', 'string', 'unique:employees,nip,'.$employee->id],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'leave_quota' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $employee->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ? Hash::make($data['password']) : $employee->user->password,
        ]);

        $photoPath = $employee->photo;

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        $employee->update([
            'nip' => $data['nip'],
            'position' => $data['position'] ?? null,
            'phone' => $data['phone'] ?? null,
            'photo' => $photoPath,
            'leave_quota' => $data['leave_quota'],
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->user()->delete(); // employees terhapus otomatis (cascade)

        return back()->with('success', 'Karyawan berhasil dihapus.');
    }

    public function regenerateQr(Employee $employee)
    {
        $employee->update([
            'qr_token' => 'EMP-'.Str::upper(Str::random(10)),
        ]);

        return back()->with('success', 'QR Code karyawan berhasil diperbarui.');
    }

    public function showQr(Employee $employee)
    {
        $employee->load('user');

        return view('admin.employees.qrcode', compact('employee'));
    }
}
