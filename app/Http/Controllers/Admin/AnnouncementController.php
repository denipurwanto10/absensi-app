<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')->ordered()->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        Announcement::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_pinned' => $request->boolean('is_pinned'),
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
}
