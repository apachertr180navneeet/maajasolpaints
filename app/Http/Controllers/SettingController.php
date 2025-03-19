<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

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
}
