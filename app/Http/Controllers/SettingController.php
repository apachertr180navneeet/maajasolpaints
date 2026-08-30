<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function editSettings()
    {
        $settings = Settings::where('key', '!=', 'PAYMENT_METHOD')->get();
        $paymentMethodSetting = Settings::where('key', 'PAYMENT_METHOD')->first();
        $paymentMethods = [];
        if ($paymentMethodSetting) {
            $decodedMethods = json_decode($paymentMethodSetting->value, true);
            if (is_array($decodedMethods)) {
                $paymentMethods = $decodedMethods;
            }
        }
        return view('admin.settings.edit', compact('settings', 'paymentMethods'));
    }


    public function updateSettings(Request $request)
    {
        // Validate the key and value fields
        $validatedData = $request->validate([
            'key.*' => 'required|string',
            'value.*' => 'required|string',
            'payment_methods' => 'nullable|array', // Allow payment_methods to be nullable or an array
        ]);

        // Update existing settings or create new ones
        $existingSettings = Settings::pluck('value', 'key')->toArray();

        foreach ($request->key as $index => $key) {
            if (array_key_exists($key, $existingSettings)) {
                $setting = Settings::where('key', $key)->first();
                $setting->value = $request->value[$index];
                $setting->save();
            } else {
                Settings::create([
                    'key' => $key,
                    'value' => $request->value[$index],
                ]);
            }
        }

        // Handle the payment methods separately
        $paymentMethods = $request->input('payment_methods', []); // Default to an empty array if no payment methods are selected
        $paymentMethodSetting = Settings::where('key', 'PAYMENT_METHOD')->first();

        if ($paymentMethodSetting) {
            $paymentMethodSetting->value = json_encode($paymentMethods); // Store as JSON string
            $paymentMethodSetting->save();
        } else {
            Settings::create([
                'key' => 'PAYMENT_METHOD',
                'value' => json_encode($paymentMethods),
            ]);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated successfully.');
    }


    public function editChangePassword()
    {
        $user = auth()->user();
        return view('admin.settings.change-password');
    }


    public function updateChangePassword(Request $request)
    {
        // Validate request
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'], 
        ]);

        $user = auth()->user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}
