<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\Redemption;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RedemptionController extends Controller
{
    public function redeemRequestSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gift_id' => 'nullable|exists:gifts,id',
            'redemption_type' => 'required|in:gift,cash,upi,account',
            'points' => 'nullable|numeric|min:0',
        ]);
        $points = null;

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation failed.',
                    'status' => 0,
                    'data' => ['errors' => $validator->errors()],
                ],
                422,
            );
        }

        $user = Auth::user();
        // Check for existing pending requests
        $pendingPoints = Transaction::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('points');

        if ($pendingPoints >= $user->balance) {
            return response()->json([
                'status' => 0,
                'message' => 'You already have pending requests that exceed or match your balance. Please wait for approval.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($request->input('redemption_type') === 'gift') {
                $gift = Gift::find($request->input('gift_id'));
                if (!$gift) {
                    return response()->json(['status' => 0, 'message' => 'Gift not found.'], 404);
                }
                $points = $gift->points;
                if ($user->balance < $gift->points) {
                    return response()->json(['status' => 0, 'message' => 'Insufficient balance.'], 400);
                }
                $transactionId = 'TX-' . strtoupper($user->id) . '-' . strtoupper(uniqid());
                $remark = "Points redeemed for gift {$gift->name}";
                Transaction::create([
                    'user_id' => $user->id,
                    'points' => $points,
                    'type' => 'debit',
                    'remark' => $remark,
                    'transaction_id' => $transactionId,
                    'transaction_type' => 'gift',
                    'gift_id' => $gift->id,
                    'status' => 'pending',
                ]);

                $user->balance -= $points;
            } else {
                $points = $request->input('points');

                if ($points <= 0 || $points > $user->balance) {
                    return response()->json(['status' => 0, 'message' => 'Invalid or insufficient points.'], 400);
                }

                $transactionId = in_array($request->input('redemption_type'), ['account', 'upi'])
                    ? 'TX-' . strtoupper($user->id) . strtoupper(substr(uniqid(), -6)) // Using substr to get the last 5 characters
                    : null;

                Transaction::create([
                    'user_id' => $user->id,
                    'points' => $points,
                    'type' => 'debit',
                    'remark' => 'Points redeemed for ' . $request->input('redemption_type'),
                    'transaction_id' => $transactionId,
                    'transaction_type' => $request->input('redemption_type'),
                    'status' => 'pending',
                ]);
                $user->balance -= $points;
            }

            $user->save();
            DB::commit();

            return response()->json(
                [
                    'status' => 1,
                    'message' => 'Redemption request created successfully.',
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'An error occurred.', 'dev_message' => $e->getMessage()], 500);
        }
    }

    public function showRedeemRequest(Request $request)
    {
        $user = auth()->user();
        $query = Redemption::where('user_id', $user->id)->with('gift');
        if ($request->has('type') && !empty($request->input('type'))) {
            $type = $request->input('type');
            $query->where('redemption_type', $type);
        }
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->whereHas('gift', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('points', 'like', "%{$search}%");
            });
        }
        $redemptions = $query->get();
        return response()->json([
            'success' => true,
            'data' => $redemptions,
        ]);
    }

    public function index($status = 'all')
    {
        $query = Transaction::with([
            'gift' => function ($query) {
                $query->withTrashed();
            },
            'user' => function ($query) {
                $query->withTrashed();
            },
        ])->where('transaction_type', '!=', 'qr');

        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected');
        }

        $redeemRequests = $query->orderBy('id', 'desc')->get();
        return view('admin.redemption.index', compact('redeemRequests'));
    }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            if (in_array($transaction->status, ['approved', 'completed'])) {
                return redirect()->route('admin.redemption')->with('error', 'This redemption request has already been processed.');
            }

            if (in_array($transaction->transaction_type, ['upi', 'account'])) {
                $request->validate([
                    'transaction_id' => 'required|string|max:255',
                ]);
            }
            $transaction->transaction_id = $request->transaction_id;
            if (in_array($transaction->transaction_type, ['cash'])) {
                $prefix = "TX-CH";
                $uniqueId = strtoupper(uniqid());
                $transaction->transaction_id = $prefix . $uniqueId;
            }
            $transaction->status = 'approved';
            $user = User::findOrFail($transaction->user_id); // Retrieve the user associated with the transaction
            // $user->balance -= $transaction->points; // Deduct the points
            $transaction->save();
            if ($user) {
                if ($user->balance < 0) {
                    throw new \Exception('Insufficient balance to approve this redemption request.');
                }
                $user->save();
            }

            DB::commit();

            return redirect()->route('admin.redemption')->with('success', 'Redeem request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.redemption')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            if (in_array($transaction->status, ['rejected', 'completed'])) {
                return redirect()->route('admin.redemption')->with('error', 'This redemption request has already been processed.');
            }
            $transaction->status = 'rejected';
            $transaction->save();

            $user = User::findOrFail($transaction->user_id);
            $user->balance += $transaction->points;
            $user->save();

            Transaction::create([
                'user_id' => $transaction->user_id,
                'points' => $transaction->points,
                'type' => 'credit',
                'remark' => 'Points refunded due to redemption rejection',
                'transaction_id' => 'TX-' . strtoupper($transaction->user_id) . '-' . strtoupper(uniqid()),
                'redemption_id' => $transaction->redemption_id,
                'status' => 'completed'
            ]);

            DB::commit();

            return redirect()->route('admin.redemption')->with('success', 'Redeem request rejected successfully and points refunded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.redemption')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
