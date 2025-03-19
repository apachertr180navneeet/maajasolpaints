<?php



namespace App\Http\Controllers;



use App\Models\Product;

use App\Models\QR;

use App\Models\Transaction;

use App\Models\User;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

use Illuminate\Support\Str;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

use PDF;





class QrController extends Controller

{

    public function index()

    {

        $qrs = QR::where('is_used', 0)->get();

        return view('admin.qr.index', compact('qrs'));

    }

    public function getUsedQrCode()

    {

        $qrs = QR::where('is_used', 1)

            ->orderBy('updated_at', 'desc')

            ->get();

        return view('admin.qr.used', compact('qrs'));

    }



    public function create()

    {

        $products = Product::all();

        return view('admin.qr.add', compact('products'));

    }

    public function storeBatch(Request $request)

    {

        $validated = $request->validate([

            'number_of_qrs' => 'required|integer|min:1',

            'points' => 'required|integer|min:0',

            'qr_type' => 'required|string|in:cash,gift,product',

            'product_id' => [

                Rule::when(fn($input) => $input->qr_type === 'product', [

                    'required',

                    'exists:products,id'

                ], ['nullable'])

            ]

        ]);



       

        $numberOfQrs = $validated['number_of_qrs'];

        $points = $validated['points'];



        for ($i = 0; $i < $numberOfQrs; $i++) {

            $qrId = 'QR' . $this->generateUniqueQrId();



            $qrCodeImage = QrCode::format('png')

                ->size(200)

                ->generate($qrId);



            $filename = "qr_{$qrId}.png";

            $path = public_path("images/qrcodes/{$filename}");

            file_put_contents($path, $qrCodeImage);



            Qr::create([

                'qr_id' => $qrId,

                'points' => $points,

                'remark' => null,

                'is_used' => false,

                'used_by' => null,

                'qr_image' => $filename,

                'qr_type' => $validated['qr_type'],

                'is_product' => $validated['qr_type'] === 'product',

                'product_id' => $validated['qr_type'] === 'product' ? $validated['product_id'] : null,

            ]);

        }



        return redirect()->route('admin.qr')->with('success', 'QR Codes generated successfully.');

    }



    public function storeAndExportPdf(Request $request)

    {

        $validated = $request->validate([

            'number_of_qrs' => 'required|integer|min:1',

            'points' => 'required|integer|min:0',

            'qr_type' => 'required|string|in:cash,gift,product',

            'product_id' => [

                Rule::when(fn($input) => $input->qr_type === 'product', [

                    'required',

                    'exists:products,id'

                ], ['nullable'])

            ]

        ]);



        $numberOfQrs = $validated['number_of_qrs'];

        $points = $validated['points'];

        $qrs = [];

        for ($i = 0; $i < $numberOfQrs; $i++) {

            $qrId = 'QR' . $this->generateUniqueQrId();

            $qrCodeImage = QrCode::format('png')

                ->size(200)

                ->generate($qrId);



            $filename = "qr_{$qrId}.png";

            $path = public_path("images/qrcodes/{$filename}");

            file_put_contents($path, $qrCodeImage);



            $qr = Qr::create([

                'qr_id' => $qrId,

                'points' => $points,

                'remark' => null,

                'is_used' => false,

                'used_by' => null,

                'qr_image' => $filename,

                'qr_type' => $validated['qr_type'],

                'is_product' => $validated['qr_type'] === 'product',

                'product_id' => $validated['qr_type'] === 'product' ? $validated['product_id'] : null,

            ]);

            $qrs[] = $qr;

        }



        $timestamp = now()->format('Y-m-d_H-i-s');

        $additionalData = [

            'generatedAt' => now()->toDateTimeString(),

            'count' => $numberOfQrs,

        ];

        $pdf = PDF::loadView('admin.qr.pdf', array_merge(compact('qrs'), $additionalData));

        return $pdf->download('qr_records_' . $numberOfQrs . '_' . $timestamp . '.pdf');

    }







    private function generateUniqueQrId()

    {

        do {

            $qr_id = strtoupper(Str::random(8)); // Generates an 8-character alphanumeric string

        } while (QR::where('qr_id', $qr_id)->exists());



        return $qr_id;

    }



    // Show the form for editing the specified QR

    public function edit(QR $qr)

    {

        $users = User::all(); // Fetch users for the 'used_by' dropdown

        return view('admin.qr.edit', compact('qr', 'users'));

    }



    // Update the specified QR in the database

    public function update(Request $request, QR $qr)

    {

        // Validate the request data

        $validatedData = $request->validate([

            'qr_id' => [

                'required',

                'string',

                'unique:qr_records,qr_id,' . $qr->id,

            ],

            'points' => 'required|integer|min:0',

            'remark' => 'nullable|string',

            'is_used' => 'required|boolean',

            'used_by' => 'nullable|exists:users,id',

        ]);



        $qr->update($validatedData);

        return redirect()->route('admin.qr')->with('success', 'QR code updated successfully!');

    }





    // Remove the specified QR from the database

    public function destroy(QR $qr)

    {

        $qr->delete();

        return redirect()->route('admin.qr')->with('success', 'QR code deleted successfully!');

    }



    public function useQrCode(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $productName = "NO Product";

            // Fetch QR record
            $qrRecord = QR::where('qr_id', $request->input('qr_id'))->first();
            

            if (!$qrRecord) {
                return response()->json(['status' => 0, 'message' => 'Invalid QR code.'], 400);
            }

            // Check if QR is for a product and validate its expiry
            if ($qrRecord->qr_type === 'product') {
                $product = Product::find($qrRecord->product_id);

                

                if (!$product) {
                    return response()->json(['status' => 0, 'message' => 'Product not found.'], 400);
                }

                if (Carbon::parse($product->expiry_date)->isPast()) {
                    return response()->json(['status' => 0, 'message' => 'Product QR has expired.'], 400);
                }
                $productName = $product->name;
            }

            // Check if QR is already used
            if ($qrRecord->is_used) {
                $usedByUser = User::find($qrRecord->used_by);

                return response()->json([
                    'status' => 1,
                    'message' => 'Already used QR code.',
                    'data' => optional($usedByUser)->only(['id', 'name', 'email', 'mobile_number'])
                ], 400);
            }

            // Update QR record as used
            $qrRecord->update([
                'is_used' => true,
                'used_by' => auth()->id(),
                'remark' => $request->input('remark')
            ]);

            $user = auth()->user();
            $points = $qrRecord->points;

            // Update balance based on QR type
            if ($qrRecord->qr_type === 'cash') {
                $user->increment('balance', $points);
            } elseif ($qrRecord->qr_type === 'gift') {
                $user->increment('gift_balance', $points);
            }

            $balance = 0;
            if ($qrRecord->qr_type === 'cash') {
                $balance = $user->balance;
            }elseif($qrRecord->qr_type === 'gift') {
                $balance = $user->gift_balance;
            }else{
                $transactions = Transaction::where('user_id', $user->id)
                    ->where('transaction_type', 'product')
                    ->where('product_id', $qrRecord->product_id)
                    ->get(); // Fetch data from DB

                
                // Sum debit and credit separately
                $debitSum = $transactions->where('type', 'debit')->sum('points');
                $creditSum = $transactions->where('type', 'credit')->sum('points');

                // Calculate remaining balance
                $remainingBalance = $creditSum - $debitSum;
                $balance = $remainingBalance + $points;

        
            }

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'credit',
                'message' => 'Points added for QR code scan',
                'transaction_id' => 'TX-' . strtoupper($qrRecord->qr_id),
                'status' => 'completed',
                'remark' => $request->input('remark'),
                'transaction_type' => $qrRecord->qr_type ?? 'cash',
                'product_id' => $qrRecord->qr_type === 'product' ? $qrRecord->product_id : null
            ]);

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'QR used successfully, points added.',
                'points' => $points,
                'qr' => $qrRecord,
                'balance' => "$balance",
                'transaction' => $transaction,
                'product_name' => $productName
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




   public function exportPdf(Request $request)

    {

        $count = $request->input('count', 5);

        $qrs = Qr::where('is_used', 0)

        ->with('product') 

        ->take($count)

        ->get();

        $timestamp = now()->format('Y-m-d_H-i-s');

        $additionalData = [

            'generatedAt' => now()->toDateTimeString(), // Example of passing timestamp

            'count' => $count,

        ];

        $pdf = PDF::loadView('admin.qr.pdf', array_merge(compact('qrs'), $additionalData));

        return $pdf->download('qr_records_' . $count . '_' . $timestamp . '.pdf');

    }

}

