<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $announcements = $request->user()->announcements()
            ->with('nguoiGui')
            ->withPivot(['da_doc', 'doc_luc'])
            ->orderByDesc('announcements.created_at')
            ->paginate(15);

        return view('inbox.index', compact('announcements'));
    }

    public function show(Request $request, Announcement $announcement): View
    {
        $attached = $request->user()->announcements()->whereKey($announcement->id)->exists();

        abort_unless($attached, 404);

        $request->user()->announcements()->updateExistingPivot($announcement->id, [
            'da_doc' => true,
            'doc_luc' => now(),
        ]);

        $announcement->load('nguoiGui');

        return view('inbox.show', compact('announcement'));
    }

    public function markAll(Request $request): RedirectResponse
    {
        DB::table('announcement_user')
            ->where('user_id', $request->user()->id)
            ->where('da_doc', false)
            ->update(['da_doc' => true, 'doc_luc' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
