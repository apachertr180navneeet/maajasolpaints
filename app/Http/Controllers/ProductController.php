<?php



namespace App\Http\Controllers;



use App\Models\Product;

use App\Models\Transaction;

use Illuminate\Http\Request;



class ProductController extends Controller

{

    /**

     * Display a listing of the resource.

     */

   public function index()

   {

    $products = Product::orderBy('created_at', 'desc')->whereNull('deleted_at')->get();

    return view('admin.products.index', compact('products'));

   }





    /**

     * Show the form for creating a new resource.

     */

    public function create()

    {

        return view('admin.products.edit');

    }



    /**

     * Store a newly created resource in storage.

     */

    public function store(Request $request)

    {

        $validatedData = $request->validate([

            'name' => 'required|max:255',

            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

            'points' => 'required|numeric',

            'worth_price' => 'required|numeric',

            // 'description' => 'required',

            'expiry_date' => 'required|date'

            

        ]);

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $imagePath =  uniqid() . '_' . $image->getClientOriginalName();

            $image->move(public_path('images/products'), $imagePath);

            $validatedData['image'] = $imagePath;

        }



        $product = Product::create($validatedData);



        return redirect()->route('admin.products')

            ->with('success', 'Product created successfully.');

    }



    /**

     * Display the specified resource.

     */

    public function show($id)

    {

        $product = Product::findOrFail($id);

        $product->expiry_date = date('Y-m-d', strtotime($product->expiry_date));

        $disabled = true;

        return view('admin.products.edit', compact('product', 'disabled'));

    }



    /**

     * Show the form for editing the specified resource.

     */

    public function edit($id)

    {

        $product = Product::findOrFail($id);

        $product->expiry_date = date('Y-m-d', strtotime($product->expiry_date));

        return view('admin.products.edit', compact('product'));

    }



    /**

     * Update the specified resource in storage.

     */

    public function update(Request $request, $id)

    {

        $validatedData = $request->validate([

            'name' => 'required|max:255',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'points' => 'required|numeric',

            'worth_price' => 'required|numeric',

            'expiry_date' => 'required|date'

        ]);



        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {

            // Delete old image

            if ($product->image) {

                $oldImagePath = public_path('images/products/' . $product->image);

                if (file_exists($oldImagePath)) {

                    unlink($oldImagePath);

                }

            }



            // Upload new image

            $image = $request->file('image');

            $imagePath = uniqid() . '_' . $image->getClientOriginalName();

            $image->move(public_path('images/products'), $imagePath);

            $validatedData['image'] = $imagePath;

        }

        $product->update($validatedData);



        return redirect()->route('admin.products')

            ->with('success', 'Product updated successfully.');

    }



    /**

     * Remove the specified resource from storage.

     */

   public function destroy($id)

    {

        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->back()->with('success', 'Product soft deleted successfully!');

    }





    /**

     * Get list of products via API

     * @return \Illuminate\Http\JsonResponse

     */

    public function getProducts()

    {

       $products = Product::latest()->get();

        $products->transform(function ($product) {

            // Skip expired products

            if (date('Y-m-d', strtotime($product->expiry_date)) < date('Y-m-d')) {

                return null;

            }

            $product->image_url = urlencode(asset('images/products/' . basename($product->image))); 

           $product->earn_points = (int)Transaction::where('product_id', $product->id)

                ->where('is_expire', 0)

                ->where('user_id', auth()->id())

                //->where('status', 'completed')

                ->where('status', '!=','rejected')

                ->where('transaction_type', 'product')

                ->selectRaw('COALESCE(SUM(CASE WHEN type = "credit" THEN points WHEN type = "debit" THEN -points ELSE 0 END), 0) as total_points')

                ->value('total_points');

            $product->remaining_points = max(0, (int)($product->points - $product->earn_points));

            return $product;

        });

        $products = $products->filter();

        return response()->json([

            'status' => 'success',

            'data' => $products->values()->toArray()

        ]);

    }

}

