<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::orderBy('created_at', 'desc')->whereNull('deleted_at')->get();
        return view('admin.gifts.index', compact('gifts'));
    }
    public function indexApi()
    {
        $gifts = Gift::where('status', 'active')->get();
        $gifts->transform(function ($gift) {
            $gift->image_url = asset('images/gifts/' . basename($gift->image)); // Adjust if necessary
            return $gift;
        });
        return response()->json([
            'status' => 1,
            'message' => 'Gifts retrieved successfully.',
            'data' => $gifts
        ]);
    }

    public function create()
    {
        return view('admin.gifts.edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points' => 'required|integer',
            'worth_price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        $gift = new Gift();
        $gift->name = $request->input('name');
        $gift->points = $request->input('points');
        $gift->worth_price = $request->input('worth_price');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath =  uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/gifts'), $imagePath);
            $gift->image = $imagePath;
        }

        $gift->save();

        return redirect()->route('admin.gift')->with('success', 'Gift created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'points' => 'sometimes|integer',
            'worth_price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        $gift = Gift::findOrFail($id);
        $gift->name = $request->input('name', $gift->name);
        $gift->points = $request->input('points', $gift->points);
        $gift->worth_price = $request->input('worth_price', $gift->worth_price);

        if ($request->hasFile('image')) {
            if ($gift->image && file_exists(public_path($gift->image))) {
                unlink(public_path($gift->image));
            }
            $image = $request->file('image');
            $imagePath = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/gifts'), $imagePath);
            $gift->image = $imagePath;
        }

        $gift->save();

        return redirect()->route('admin.gift')->with('success', 'Gift created successfully.');
    }

    public function edit($id)
    {
        $gift = Gift::findOrFail($id);
        return view('admin.gifts.edit', compact('gift'));
    }


    public function destroy($id)
    {
        $gift = Gift::findOrFail($id);
        $gift->delete();

        return redirect()->route('admin.gift')->with('success', 'Gift deleted successfully.');
    }
}
