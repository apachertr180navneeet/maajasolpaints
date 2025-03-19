<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\Settings;
use App\Models\Slider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppController extends Controller
{
    public function dashboard()
    {

        $settings = Settings::all()->pluck('value', 'key')->toArray();
        if (isset($settings['PAYMENT_METHOD'])) {
            $settings['PAYMENT_METHOD'] = json_decode($settings['PAYMENT_METHOD'], true);
        }
        if (isset($settings['APP_LOGO'])) {
            $settings['APP_LOGO_URL'] = asset($settings['APP_LOGO']);
        }
        $sliders = Slider::where('status', 'active')
            ->where(function ($query) {
                // Filter sliders that are either not expired or have no expiry date
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', Carbon::now());
            })
            ->orderBy('sequence', 'asc') // Sort by sequence position
            ->select('id', 'title', 'link', 'image')
            ->get();
        $sliders->transform(function ($slider) {
            $slider->image = asset($slider->image);
            return $slider;
        });
        $user = auth()->user();

        $user->aadhaar_image = $user->aadhaar_image ? asset($user->aadhaar_image) : null;
        $user->profile_image = $user->profile_image ? asset($user->profile_image) : null;

        $latestTransaction = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        $totalCreditPoints = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('points');

        $latestTransactionDetails = $latestTransaction ? [
            'id' => $latestTransaction->id,
            'points' => $latestTransaction->points,
            'type' => $latestTransaction->type,
            'remark' => $latestTransaction->remark,
            'created_at' => $latestTransaction->created_at->toDateTimeString(),
        ] : null;

        return response()->json([
            'message' => 'Dashboard fetched successfully.',
            'status' => 1,
            'data' => [
                'sliders' => $sliders,
                'user' => $user,
                'last_transaction' => $latestTransactionDetails,
                'lifetime_balance' => $totalCreditPoints,
                'settings' => $settings
            ]
        ], 200);
    }
}
