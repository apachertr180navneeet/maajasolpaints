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

                if ($user->gift_balance < $gift->points) {

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



                $user->gift_balance -= $points;

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

            if (in_array($transaction->transaction_type, ['cash','gift'])) {

                $prefix = "TX-CH";

                $uniqueId = strtoupper(uniqid());

                $transaction->transaction_id = $prefix . $uniqueId;

            }

            $transaction->status = 'approved';

            $transaction->approved_at = now();

            $transaction->remark = $request->has('remark') ? $request->remark : $transaction->remark;

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

            $transaction->approved_at = null;

            $transaction->save();

            if($transaction->transaction_type == 'product') {

            }elseif($transaction->transaction_type == 'gift'){
                $user = User::findOrFail($transaction->user_id);
                $user->gift_balance += $transaction->points;
    
                $user->save();

            }else{
                $user = User::findOrFail($transaction->user_id);
    
                $user->balance += $transaction->points;
    
                $user->save();
            }




            // Transaction::create([

            //     'user_id' => $transaction->user_id,

            //     'points' => $transaction->points,

            //     'type' => 'credit',

            //     'gift_id' => $transaction->gift_id,

            //     'product_id' => $transaction->product_id,

            //     'transaction_type' => $transaction->transaction_type, 

            //     'remark' => 'Points refunded due to redemption rejection',

            //     'transaction_id' => 'TX-' . strtoupper($transaction->user_id) . '-' . strtoupper(uniqid()),

            //     'redemption_id' => $transaction->redemption_id,

            //     'status' => 'completed'

            // ]);



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

