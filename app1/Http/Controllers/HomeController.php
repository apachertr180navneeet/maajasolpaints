<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\QR;
use App\Models\Slider;
use App\Models\Transaction;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $activeSection = 'dashboard';
        $admin = auth()->user();
        $userActivityData = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn($item) => $item->count);

        $dates = collect(range(1, now()->daysInMonth))->map(fn($day) => now()->startOfMonth()->addDays($day - 1)->format('Y-m-d'))->toArray();
        $activityCounts = array_map(fn($date) => $userActivityData[$date] ?? 0, $dates);

        $sliderCount = Slider::count();
        $qrCount = QR::count();
        $usedQrCount = QR::where('is_used', 1)->count();
        $activeQrCount = QR::where('is_used', 0)->count();
        $giftCount = Gift::count();
        $pendingCount = Transaction::where('status', 'pending')->count();
        $approvedCount = Transaction::where('status', 'approved')->count();
        $completedCount = Transaction::where('status', 'completed')->count();
        $rejectedCount = Transaction::where('status', 'rejected')->count();
        $totalUserCount = User::count();
        $activeUserCount = User::where('status', 'active')->count();
        $inactiveUserCount = User::where('status', 'inactive')->count();
        $blockedUserCount = User::where('status', 'blocked')->count();
        $pendingUserCount = User::where('status', 'pending')->count();

        return view('admin.dashboard', compact('activeSection', 'dates', 'activityCounts', 'sliderCount',  'qrCount', 'giftCount', 'pendingCount', 'approvedCount', 'completedCount', 'rejectedCount', 'totalUserCount', 'activeUserCount', 'inactiveUserCount', 'blockedUserCount', 'pendingUserCount', 'activeQrCount', 'usedQrCount'));
    }
}
