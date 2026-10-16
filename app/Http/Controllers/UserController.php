<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index($status = 'all') // 'all' is default
    {
        // Query logic based on the passed status
        $query = User::where('is_admin', 0);

        if ($status == 'active') {
            $title = "Active Users";
            $query->where('status', 'active');
        } elseif ($status == 'pending') {
            $title = "Pending Users";
            $query->where('status', 'pending');
        } elseif ($status == 'blocked') {
            $title = "Blocked Users";
            $query->where('status', 'blocked');
        } elseif ($status == 'inactive') {
            $title = "Inactive Users";
            $query->where('status', 'inactive');
        } else {
            $title = "All Users";
        }

        $users = $query->orderBy('name', 'asc')->get();

        // Calculate lifetime balance for each user
        foreach ($users as $user) {
            $lifetime_balance = Transaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->sum('points');
            $user->lifetime_balance = $lifetime_balance;
        }

        // Calculate counts for each status
        $counts = [
            'all' => User::where('is_admin', 0)->count(),
            'pending' => User::where('is_admin', 0)->where('status', 'pending')->count(),
            'active' => User::where('is_admin', 0)->where('status', 'active')->count(),
            'inactive' => User::where('is_admin', 0)->where('status', 'inactive')->count(),
            'blocked' => User::where('is_admin', 0)->where('status', 'blocked')->count(),
        ];

        // Determine active section for UI highlighting
        $activeSection = 'users';

        return view('admin/users.index', [
            'title' => $title,
            'users' => $users,
            'status' => $status,
            'counts' => $counts,
            'activeSection' => $activeSection
        ]);
    }

    public function show(Request $request, User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function approve(Request $request, User $user)
    {
        try {
            $user->status = 'active';
            $user->save();

            $redirectTo = $request->input('redirect_to');
            if (!$redirectTo) {
                $redirectTo = route('admin.users', ['status' => 'pending']);
            }

            return redirect($redirectTo)->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to activate user: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,pending,inactive,blocked',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the user by ID and update the status
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => 'User status updated successfully', 'user' => $user], 200);
    }

    public function edit(Request $request, User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'required|string|max:15|unique:users,mobile_number,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20|unique:users,account_number,' . $user->id,
            'ifsc_code' => 'nullable|string|max:11',
            'balance' => 'nullable|numeric|min:0',
            'gift_balance' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'dob' => 'nullable',
            'aadhaar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
            'bank_cheque_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
            'status' => 'nullable',
        ]);

        // Prepare data to update
        $userData = $request->only([
            'name',
            'email',
            'mobile_number',
            'bank_name',
            'account_number',
            'ifsc_code',
            'balance',
            'address',
            'dob',
            'status',
            'gift_balance'
        ]);

        if ($request->hasFile('aadhaar_image')) {
            // Delete existing image if it exists
            if ($user->aadhaar_image && file_exists(public_path($user->aadhaar_image))) {
                unlink(public_path($user->aadhaar_image));
            }
            $aadhaarImage = $request->file('aadhaar_image');
            $aadhaarImagePath = 'images/aadhaar/' . uniqid() . '_' . $aadhaarImage->getClientOriginalName();
            $aadhaarImage->move(public_path('images/aadhaar'), $aadhaarImagePath);
            $userData['aadhaar_image'] = $aadhaarImagePath;
        }
        if ($request->hasFile('bank_cheque_image')) {
            // Delete existing image if it exists
            if ($user->bank_cheque_image && file_exists(public_path($user->bank_cheque_image))) {
                unlink(public_path($user->bank_cheque_image));
            }
            $bankChequeImage = $request->file('bank_cheque_image');
            $bankChequeImagePath = 'images/cheque/' . uniqid() . '_' . $bankChequeImage->getClientOriginalName();
            $bankChequeImage->move(public_path('images/cheque'), $bankChequeImagePath);
            $userData['bank_cheque_image'] = $bankChequeImagePath;
        }

        $originalStatus = $user->status;
        $user->update($userData);
        if ($request->status != 'active') {
            $user->tokens()->delete();
        }

        $message = ($request->status == 'active' && $originalStatus != 'active')
            ? 'User activated successfully.'
            : 'User updated successfully.';

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo) {
            return redirect($redirectTo)->with('success', $message);
        }

        return redirect()->route('admin.users', ['status' => $user->status ?? 'all'])->with('success', $message);
    }
    public function destroy(Request $request, User $user)
    {
        try {
            $user->delete();
            $redirectTo = $request->input('redirect_to');
            if ($redirectTo) {
                return redirect($redirectTo)->with('success', 'User deleted successfully.');
            }
            return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
    public function showPoints()
    {
        
        $transactions = Transaction::where('transaction_type', 'product')
        ->where('status', '!=', 'rejected')
        ->select([
            'user_id',
            'product_id',
            \DB::raw('SUM(CASE WHEN type = "credit" THEN points ELSE 0 END) as points_earned'),
            \DB::raw('SUM(CASE WHEN type = "credit" THEN points ELSE 0 END) - SUM(CASE WHEN type = "debit" THEN points ELSE 0 END) as points_balance'),
            \DB::raw('SUM(CASE WHEN type = "credit" THEN points ELSE 0 END) as total_points'),
            \DB::raw('MAX(created_at) as latest_date'),
        ])
        ->with(['user', 'product'])
        ->groupBy('user_id', 'product_id')
        ->orderByDesc('latest_date')
        ->get();
            // foreach ($transactions as $transaction){
            //     echo "<pre>";
            //     print_r($transaction->product->points);
            // }
            // die;


        return view('admin.users.points', [
            'transactions' => $transactions,
            'title' => 'User Product Points Summary'
        ]);
    }

}
