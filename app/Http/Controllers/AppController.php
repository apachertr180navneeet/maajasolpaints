<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\Settings;
use App\Models\Slider;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
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

        // Retrieve all transactions of type 'product' for the given user
        $allTransactions = Transaction::where('user_id', $user->id)
        ->where('transaction_type', 'product')
        ->get();

        foreach ($allTransactions as $transaction) {
            // Ensure transaction has a valid product_id and type is 'credit'
            if (!empty($transaction->product_id) && $transaction->type === "credit") {
                
                
                // Fetch the associated product
                $product = Product::find($transaction->product_id);
                
                if ($product) { // Proceed only if product exists
                    $expiryDate = Carbon::parse($product->expiry_date);
                    $currentDate = Carbon::now();

                    // Check if the product is expired
                    if ($expiryDate->lt($currentDate)) {
                        
                        // Fetch the user associated with the transaction
                        $userToUpdate = User::find($transaction->user_id);
                        
                        if ($userToUpdate) {
                            // Calculate the total points earned from this expired product
                            $totalPoints = Transaction::where('product_id', $transaction->product_id)
                                ->where('user_id', $transaction->user_id)
                                ->where('type', 'credit')
                                ->sum('points');

                            // Add the total points to the user's gift balance
                            $userToUpdate->increment('gift_balance', $totalPoints);

                            // Prepare message for transaction update
                            $msg = "Product expired: {$product->name}. Points credited to gift balance.";

                            // Update all related transactions to reflect expiration (change type to 'gift')
                            Transaction::where('product_id', $transaction->product_id)
                                ->where('user_id', $transaction->user_id)
                                ->where('type', 'credit')
                                ->update([
                                    'transaction_type' => 'gift',
                                    'remark' => $msg
                                ]);
                        }
                    }
                }
            }
        }

        $user->aadhaar_image = $user->aadhaar_image ? asset($user->aadhaar_image) : null;
        $user->profile_image = $user->profile_image ? asset($user->profile_image) : null;

        $latestTransaction = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        $totalCreditPoints = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')->where('transaction_type', 'cash')
            ->sum('points');
        $totalGiftCreditPoints = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('transaction_type', 'gift')
            ->sum('points');

        $totalGiftCreditPointsdebit = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', '!=','rejected')
            ->where('transaction_type', 'gift')
            ->sum('points');

        $latestTransactionDetails = $latestTransaction ? [
            'id' => $latestTransaction->id,
            'points' => $latestTransaction->points,
            'type' => $latestTransaction->type,
            'message' => $latestTransaction->message,
            'remark' => $latestTransaction->remark,
            'created_at' => $latestTransaction->created_at->toDateTimeString(),
        ] : null;
        $giftlifetimeblance = Transaction::where('user_id', $user->id)
        ->where('type', 'debit')
        ->where('transaction_type', 'gift')
        ->where('status', '!=','rejected')
        ->sum('points');
        return response()->json([
            'message' => 'Dashboard fetched successfully.',
            'status' => 1,
            'data' => [
                'sliders' => $sliders,
                'user' => $user,
                'last_transaction' => $latestTransactionDetails,
                'lifetime_balance' => $totalCreditPoints,
                'gift_balance' => $totalGiftCreditPoints - $totalGiftCreditPointsdebit,
                'gift_lifetime_balance' => $giftlifetimeblance + ($totalGiftCreditPoints - $totalGiftCreditPointsdebit) ,
                'settings' => $settings
            ]
        ], 200);
    }
}
