<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile_number' => 'required|string|max:15|unique:users', // Mobile number is required
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20|unique:users',
            'ifsc_code' => 'nullable|string|max:11',
            'balance' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'dob' => 'nullable',
            'aadhaar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'bank_cheque_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'status' => 0,
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $otp = 1111;

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'bank_name' => $request->bank_name,
            'password' => Hash::make('2IXIMMEWXG'),
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'balance' => 0.00,
            'address' => $request->address,
            'dob' => $request->dob,
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'status' => 'pending',
        ];

        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            $profileImagePath = 'images/profile/' . uniqid() . '_' . $profileImage->getClientOriginalName();
            $profileImage->move(public_path('images/profile'), $profileImagePath);
            $userData['profile_image'] = $profileImagePath;
        }

        if ($request->hasFile('aadhaar_image')) {
            $aadhaarImage = $request->file('aadhaar_image');
            $aadhaarImagePath = 'images/aadhaar/' . uniqid() . '_' . $aadhaarImage->getClientOriginalName();
            $aadhaarImage->move(public_path('images/aadhaar'), $aadhaarImagePath);
            $userData['aadhaar_image'] = $aadhaarImagePath;
        }
        if ($request->hasFile('bank_cheque_image')) {
            $bankChequeImage = $request->file('bank_cheque_image');
            $bankChequeImagePath = 'images/cheque/' . uniqid() . '_' . $bankChequeImage->getClientOriginalName();
            $bankChequeImage->move(public_path('images/cheque'), $bankChequeImagePath);
            $userData['bank_cheque_image'] = $bankChequeImagePath;
        }

        $user = User::create($userData);

        return response()->json([
            'message' => 'User registered successfully. Please Verify OTP.',
            'status' => 1,
            'data' => null
        ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|max:15',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'status' => 0,
                'data' => null
            ], 404);
        }
        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Your account is pending approval by the admin.',
                'status' => 0,
                'data' => null
            ], 403);
        }
        if ($user->status === 'blocked') {
            return response()->json([
                'message' => 'Your account is blocked. Please contact admin.',
                'status' => 0,
                'data' => null
            ], 403);
        }
        if ($user->status === 'inactive') {
            return response()->json([
                'message' => 'Your account is inactive. Please contact support.',
                'status' => 1,
                'data' => null
            ], 403);
        }
        $otp = 1111;
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // SMSService::send($user->mobile_number, "Your OTP is: $otp");

        return response()->json([
            'message' => 'OTP sent successfully.',
            'status' => 1,
            'data' => null
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $user->aadhaar_image = $user->aadhaar_image ? asset($user->aadhaar_image) : null;
        $user->profile_image = $user->profile_image ? asset($user->profile_image) : null;
        $user->bank_cheque_image = $user->bank_cheque_image ? asset($user->bank_cheque_image) : null;

        return response()->json([
            'message' => 'User data retrieved successfully.',
            'status' => 1,
            'data' => $user,
        ], 200);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|max:15',
            'otp' => 'required|string|max:6',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();
        if (!$user || $user->otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
                'status' => 0,
                'data' => null
            ], 400);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            // 'status' => 'active',
            'email_verified_at' => now(),
        ]);
        if ($user->status === 'inactive') {
            return response()->json([
                'message' => 'OTP verified successfully. Your account is inactive. Please log in after verification or contact support for assistance.',
                'status' => 1,
                'data' => null
            ], 403);
        }
        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Your account is pending approval by the admin.',
                'status' => 0,
                'data' => null
            ], 403);
        }

        $user->aadhaar_image = $user->aadhaar_image ? asset($user->aadhaar_image) : null;
        $user->profile_image = $user->profile_image ? asset($user->profile_image) : null;
        $user->bank_cheque_image = $user->bank_cheque_image ? asset($user->bank_cheque_image) : null;

        $token = $user->createToken(config('app.key'))->plainTextToken;
        return response()->json([
            'message' => 'OTP verified successfully.',
            'status' => 1,
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 200);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|max:15',
        ]);

        // Retrieve the user by mobile number
        $user = User::where('mobile_number', $request->mobile_number)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'status' => 0,
                'data' => null
            ], 404);
        }

        $otp = 1111;
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // SMSService::send($user->mobile_number, "Your new OTP is: $otp");

        return response()->json([
            'message' => 'OTP resent successfully.',
            'status' => 1,
            'data' => null
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Old password is incorrect.',
                'status' => 0,
                'data' => null
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.',
            'status' => 1,
            'data' => null
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . auth()->id(),
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20|unique:users,account_number,' . auth()->id(),
            'ifsc_code' => 'nullable|string|max:11',
            'balance' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'dob' => 'nullable',
            'aadhaar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'bank_cheque_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
        ]);

        $user = auth()->user();
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->bank_name = $request->input('bank_name', $user->bank_name);
        $user->account_number = $request->input('account_number', $user->account_number);
        $user->ifsc_code = $request->input('ifsc_code', $user->ifsc_code);
        $user->balance = $request->input('balance', $user->balance);  // Uncomment if you want to update balance
        $user->address = $request->input('address', $user->address);
        $user->dob = $request->input('dob', $user->dob);

        if ($request->hasFile('aadhaar_image')) {
            if ($user->aadhaar_image && file_exists(public_path('images/aadhaar/' . $user->aadhaar_image))) {
                unlink(public_path('images/aadhaar/' . $user->aadhaar_image));
            }
            $aadhaarImage = $request->file('aadhaar_image');
            $aadhaarImagePath = 'images/aadhaar/' . uniqid() . '_' . $aadhaarImage->getClientOriginalName();
            $aadhaarImage->move(public_path('images/aadhaar'), $aadhaarImagePath);
            $user->aadhaar_image = $aadhaarImagePath;
        }
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && file_exists(public_path('images/profile/' . $user->profile_image))) {
                unlink(public_path('images/profile/' . $user->profile_image));
            }

            $profileImage = $request->file('profile_image');
            $profileImagePath = 'images/profile/' . uniqid() . '_' . $profileImage->getClientOriginalName();
            $profileImage->move(public_path('images/profile'), $profileImagePath);
            $user->profile_image = $profileImagePath;
        }

        if ($request->hasFile('bank_cheque_image')) {
            if ($user->bank_cheque_image && file_exists(public_path('images/cheque/' . $user->bank_cheque_image))) {
                unlink(public_path('images/cheque/' . $user->bank_cheque_image));
            }

            $chequeImage = $request->file('bank_cheque_image');
            $chequeImagePath = 'images/cheque/' . uniqid() . '_' . $chequeImage->getClientOriginalName();
            $chequeImage->move(public_path('images/cheque'), $chequeImagePath);
            $user->bank_cheque_image = $chequeImagePath;
        }
        $user->save();
        $user->aadhaar_image = $user->aadhaar_image ? (asset($user->aadhaar_image)) : null;
        $user->profile_image = $user->profile_image ? (asset($user->profile_image)) : null;
        $user->bank_cheque_image = $user->bank_cheque_image ? (asset($user->bank_cheque_image)) : null;
        return response()->json([
            'message' => 'Profile updated successfully.',
            'status' => 1,
            'data' => $user
        ]);
    }

    public function logout(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'No user is logged in',
                    'status' => 0
                ], 400);
            }
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'Logout Successful',
                'status' => 1
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout Failed',
                'status' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|size:10',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }
        $mobileNumber = $request->mobile_number;
        $user = User::where('mobile_number', $mobileNumber)->first();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
        if ($user->mobile_number !== $request->mobile_number) {
            return response()->json(['message' => 'Mobile number does not match.'], 403);
        }
        // $user->delete();
        return response()->json(['message' => 'Account deleted successfully.'], 200);
    }
}
