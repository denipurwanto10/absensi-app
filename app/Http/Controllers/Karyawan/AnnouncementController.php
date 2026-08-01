<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')->ordered()->paginate(10);

        return view('karyawan.announcements.index', compact('announcements'));
    }
}
