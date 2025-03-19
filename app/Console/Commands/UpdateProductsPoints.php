<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateProductsPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-products-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $products = Product::where('expiry_date', '<', Carbon::now())->get();

        foreach ($products as $product) {
            $transactions = Transaction::where('product_id', $product->id)
                ->where('is_expire', false)
                ->get();

            $userPoints = [];

            foreach ($transactions as $transaction) {
                $transaction->is_expire = true;
                $transaction->save();

                if (!isset($userPoints[$transaction->user_id])) {
                    $userPoints[$transaction->user_id] = ['credit' => 0, 'debit' => 0];
                }

                if ($transaction->type === 'credit') {
                    $userPoints[$transaction->user_id]['credit'] += $transaction->points;
                } elseif ($transaction->type === 'debit') {
                    $userPoints[$transaction->user_id]['debit'] += $transaction->points;
                }
            }

            foreach ($userPoints as $userId => $points) {
                $remainingPoints = $points['credit'] - $points['debit'];

                if ($remainingPoints > 0) {
                    $user = User::find($userId);
                    if ($user) {
                        $user->gift_balance += $remainingPoints;
                        $user->save();

                        Transaction::create([
                            'user_id' => $user->id,
                            'product_id' => null,
                            'points' => $remainingPoints,
                            'is_expire' => false,
                            'transaction_type' => 'gift',
                            'status' => 'completed',
                            'type' => 'credit',
                            'message' => 'Transferred remaining balance to gift balance'
                        ]);
                    }
                }
            }
        }

        $this->info('Transactions and user balances updated successfully.');
    }
}
