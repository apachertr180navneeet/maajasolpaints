<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{
    public function index()
    {
        $activeSection = 'slider';
        $sliders = Slider::get();
        return view('admin.sliders.index', compact('sliders', 'activeSection'));
    }

    public function create()
    {
        $activeSection = 'slider';
        return view('admin.sliders.edit', ['slider' => null, 'activeSection' => $activeSection]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
            'link' => 'nullable|url',
            'status' => 'required|in:active,inactive',
            'sequence' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after:today',
        ]);


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '_' . $image->getClientOriginalName();
            $imagePath = public_path('images/sliders');
            $image->move($imagePath, $imageName); // Move the file to public/images/sliders
        }

        Slider::create([
            'title' => $request->title,
            'image' => 'images/sliders/' . $imageName,
            'link' => $request->link,
            'status' => $request->status,
            'sequence' => $request->sequence, // Add sequence
            'expiry_date' => $request->expiry_date, // Add expiry date
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'link' => 'nullable|url',
            'status' => 'required|in:active,inactive',
            'sequence' => 'required|integer|min:0', // Add validation for sequence
            'expiry_date' => 'nullable|date|after:today', // Add validation for expiry date
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image && File::exists(public_path($slider->image))) {
                File::delete(public_path($slider->image));
            }

            $imagePath = 'images/sliders/' . uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/sliders'), basename($imagePath));
        } else {
            $imagePath = $slider->image;
        }
        $slider->update([
            'title' => $request->title,
            'image' => $imagePath,
            'link' => $request->link,
            'status' => $request->status,
            'sequence' => $request->sequence,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully.');
    }


    public function destroy(Slider $slider)
    {
        // Delete image from public directory
        if (File::exists(public_path($slider->image))) {
            File::delete(public_path($slider->image));
        }

        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully.');
    }
}
