<?php



namespace App\Http\Controllers;



use App\Models\Gift;

use App\Models\Product;

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

            'redemption_type' => 'required|in:gift,cash,upi,account,product',

            'points' => 'nullable|numeric|min:0',

            'remark' => 'nullable|string',

            'product_id' => 'nullable|numeric',

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

        // Check for existing pending requests based on redemption type
        $redemptionType = $request->input('redemption_type');
        if ($redemptionType === 'gift') {
            $pendingGiftPoints = Transaction::where('user_id', $user->id)
                ->where('transaction_type', 'gift')
                ->where('status', 'pending')
                ->sum('points');

            $totalGiftCreditPoints = Transaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->where('transaction_type', 'gift')
                ->sum('points');
            $totalGiftDebitPoints = Transaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->where('status', '!=', 'rejected')
                ->where('transaction_type', 'gift')
                ->sum('points');
            $effectiveGiftBal = max((float)$user->gift_balance, (float)($totalGiftCreditPoints - $totalGiftDebitPoints));

            if ($pendingGiftPoints > 0 && $pendingGiftPoints >= $effectiveGiftBal) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You already have pending requests that exceed or match your gift balance. Please wait for approval.',
                ], 400);
            }
        } elseif (in_array($redemptionType, ['cash', 'upi', 'account'])) {
            $pendingCashPoints = Transaction::where('user_id', $user->id)
                ->whereIn('transaction_type', ['cash', 'upi', 'account'])
                ->where('status', 'pending')
                ->sum('points');

            if ($pendingCashPoints > 0 && $pendingCashPoints >= $user->balance) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You already have pending requests that exceed or match your balance. Please wait for approval.',
                ], 400);
            }
        }

        // if ($request->input('redemption_type') === 'gift') {
        //     if ($pendingPoints >= $user->gift_balance) {

        //         return response()->json([

        //             'status' => 0,

        //             'message' => 'You already have pending requests that exceed or match your gift balance. Please wait for approval.',

        //         ], 400);

        //     }

        // } elseif ($request->input('redemption_type') === 'cash') {

        //     if ($pendingPoints >= $user->balance) {

        //         return response()->json([

        //             'status' => 0,

        //             'message' => 'You already have pending requests that exceed or match your cash balance. Please wait for approval.',

        //         ], 400);

        //     }

        // } elseif ($request->input('redemption_type') === 'product') {

        //     $product = Product::find($request->input('product_id')); // Assuming `product_id` is provided in the request.

        //     if ($product) {

        //         $product->earn_points = (int) Transaction::where('product_id', $product->id)

        //             ->where('is_expire', 0)

        //             ->where('user_id', auth()->id())

        //             ->where('type', 'credit')

        //             ->where('status', 'completed')

        //             ->where('transaction_type', 'product')

        //             ->sum('points');



        //         $product->remaining_points = (int) ($product->points - $product->earn_points);



        //         if ($product->remaining_points <= 0) {

        //             return response()->json([

        //                 'status' => 0,

        //                 'message' => 'You do not have enough points available to redeem this product.',

        //             ], 400);

        //         }

        //     } else {

        //         return response()->json([

        //             'status' => 0,

        //             'message' => 'Product not found.',

        //         ], 404);

        //     }

        // } else {

        //     return response()->json([

        //         'status' => 0,

        //         'message' => 'Invalid redemption type.',

        //     ], 400);

        // }



       //dd($product);

        DB::beginTransaction();

        

        try {

            if ($request->input('redemption_type') === 'gift') {

                $gift = Gift::find($request->input('gift_id'));

                if (!$gift) {

                    return response()->json(['status' => 0, 'message' => 'Gift not found.'], 404);

                }

                $points = $gift->points;

                $totalGiftCreditPoints = Transaction::where('user_id', $user->id)
                    ->where('type', 'credit')
                    ->where('transaction_type', 'gift')
                    ->sum('points');
                $totalGiftDebitPoints = Transaction::where('user_id', $user->id)
                    ->where('type', 'debit')
                    ->where('status', '!=', 'rejected')
                    ->where('transaction_type', 'gift')
                    ->sum('points');
                $effectiveGiftBal = max((float)$user->gift_balance, (float)($totalGiftCreditPoints - $totalGiftDebitPoints));

                if ($effectiveGiftBal < $gift->points) {
                    return response()->json(['status' => 0, 'message' => 'Insufficient balance.'], 400);
                }

                $transactionId = 'TX-' . strtoupper($user->id) . '-' . strtoupper(uniqid());

                $message = "Points redeemed for gift {$gift->name}";

                Transaction::create([

                    'user_id' => $user->id,

                    'points' => $points,

                    'type' => 'debit',

                    'message' => $message,

                    'remark' => $request->remark ?? null,

                    'transaction_id' => $transactionId,

                    'transaction_type' => 'gift',

                    'gift_id' => $gift->id,

                    'status' => 'pending',

                ]);

                $user->gift_balance = max(0, $effectiveGiftBal - $points);

            } else if ($request->input('redemption_type') === 'product') {

                $debitproductPoints = Transaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->where('product_id', $request->input('product_id'))
                ->where('transaction_type', 'product')
                ->where('status', '!=','rejected')
                ->sum('points');

                $creditproductPoints = Transaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->where('product_id', $request->input('product_id'))
                ->where('transaction_type', 'product')
                ->sum('points');

                $productpoint = $creditproductPoints - $debitproductPoints;

                $product = Product::find($request->input('product_id'));

                if (!$product) {

                    return response()->json(['status' => 0, 'message' => 'Product not found.'], 404);

                }

                $points = $product->points;

                if ($productpoint < $product->points) {

                    return response()->json(['status' => 0, 'message' => 'Insufficient balance.'], 400);

                }

                $transactionId = 'TX-' . strtoupper($user->id) . '-' . strtoupper(uniqid());

                $message = "Points redeemed for product {$product->name}";

                Transaction::create([

                    'user_id' => $user->id,

                    'points' => $points,

                    'type' => 'debit',

                    'message' => $message,

                    'remark' => $request->input('remark'),

                    'transaction_id' => $transactionId,

                    'transaction_type' => 'product',

                    'product_id' => $product->id,

                    'status' => 'pending',

                ]);



                //$user->balance -= $points;

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

                    'message' => 'Points redeemed for ' . $request->input('redemption_type'),

                    'transaction_id' => $transactionId,

                    'transaction_type' => $request->input('redemption_type'),

                    'status' => 'pending',

                     'remark' => $request->input('remark'),

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
            'product'
        ])->where('transaction_type', '!=', 'qr');

        if ($status === 'pending') {
            $title = "Pending Requests";
            $query->where('status', 'pending');
        } elseif ($status === 'approved') {
            $title = "Approved Requests";
            $query->where('status', 'approved');
        } elseif ($status === 'completed') {
            $title = "Completed Requests";
            $query->where('status', 'completed');
        } elseif ($status === 'rejected') {
            $title = "Rejected Requests";
            $query->where('status', 'rejected');
        } else {
            $title = "All Redemption Requests";
        }

        $redeemRequests = $query->orderBy('id', 'desc')->get();

        $counts = [
            'all' => Transaction::where('transaction_type', '!=', 'qr')->count(),
            'pending' => Transaction::where('transaction_type', '!=', 'qr')->where('status', 'pending')->count(),
            'approved' => Transaction::where('transaction_type', '!=', 'qr')->where('status', 'approved')->count(),
            'rejected' => Transaction::where('transaction_type', '!=', 'qr')->where('status', 'rejected')->count(),
            'completed' => Transaction::where('transaction_type', '!=', 'qr')->where('status', 'completed')->count(),
        ];

        return view('admin.redemption.index', [
            'title' => $title,
            'redeemRequests' => $redeemRequests,
            'status' => $status,
            'counts' => $counts,
            'activeSection' => 'redemption'
        ]);
    }

    public function show(Request $request, $id)
    {
        $transaction = Transaction::with([
            'gift' => function ($query) {
                $query->withTrashed();
            },
            'user' => function ($query) {
                $query->withTrashed();
            },
            'product'
        ])->where('id', $id)->firstOrFail();

        // Find next pending request if current transaction is pending
        $nextPending = null;
        if ($transaction->status === 'pending') {
            $nextPending = Transaction::where('transaction_type', '!=', 'qr')
                ->where('status', 'pending')
                ->where('id', '!=', $transaction->id)
                ->orderBy('id', 'desc')
                ->first();
        }

        $redirectTo = $request->input('redirect_to', route('admin.redemption', ['status' => 'pending']));

        return view('admin.redemption.show', [
            'transaction' => $transaction,
            'nextPending' => $nextPending,
            'redirectTo' => $redirectTo,
            'activeSection' => 'redemption',
            'title' => 'Request Details #' . $transaction->id
        ]);
    }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            if (in_array($transaction->status, ['approved', 'completed'])) {
                $redirectTo = $request->input('redirect_to', route('admin.redemption', ['status' => 'pending']));
                return redirect($redirectTo)->with('error', 'This redemption request has already been processed.');
            }

            if (in_array($transaction->transaction_type, ['upi', 'account'])) {
                $request->validate([
                    'transaction_id' => 'required|string|max:255',
                ]);
            }

            $transaction->transaction_id = $request->transaction_id;

            if (in_array($transaction->transaction_type, ['cash', 'gift'])) {
                $prefix = "TX-CH";
                $uniqueId = strtoupper(uniqid());
                $transaction->transaction_id = $prefix . $uniqueId;
            }

            $transaction->status = 'approved';
            $transaction->approved_at = now();
            $transaction->remark = $request->filled('remark') ? $request->remark : $transaction->remark;

            $user = User::findOrFail($transaction->user_id);
            $transaction->save();

            if ($user) {
                if ($user->balance < 0) {
                    throw new \Exception('Insufficient balance to approve this redemption request.');
                }
                $user->save();
            }

            DB::commit();

            $redirectTo = $request->input('redirect_to');
            if (!$redirectTo) {
                $redirectTo = route('admin.redemption', ['status' => 'pending']);
            }

            return redirect($redirectTo)->with('success', 'Redeem request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)->firstOrFail();

            if (in_array($transaction->status, ['rejected', 'completed'])) {
                $redirectTo = $request->input('redirect_to', route('admin.redemption', ['status' => 'pending']));
                return redirect($redirectTo)->with('error', 'This redemption request has already been processed.');
            }

            $transaction->status = 'rejected';
            $transaction->approved_at = null;
            if ($request->filled('remark')) {
                $transaction->remark = $request->remark;
            }
            $transaction->save();

            if ($transaction->transaction_type == 'product') {
                // No refund points for product
            } elseif ($transaction->transaction_type == 'gift') {
                $user = User::findOrFail($transaction->user_id);
                $user->gift_balance += $transaction->points;
                $user->save();
            } else {
                $user = User::findOrFail($transaction->user_id);
                $user->balance += $transaction->points;
                $user->save();
            }

            DB::commit();

            $redirectTo = $request->input('redirect_to');
            if (!$redirectTo) {
                $redirectTo = route('admin.redemption', ['status' => 'pending']);
            }

            return redirect($redirectTo)->with('success', 'Redeem request rejected successfully and points refunded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->delete();

            $redirectTo = $request->input('redirect_to');
            if (!$redirectTo) {
                $redirectTo = route('admin.redemption', ['status' => 'pending']);
            }

            return redirect($redirectTo)->with('success', 'Redemption request deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }

}

