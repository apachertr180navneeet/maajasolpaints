<?php

namespace App\Http\Controllers;

use App\Models\QR;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
        return view('admin.qr.add');
    }
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'number_of_qrs' => 'required|integer|min:1',
            'points' => 'required|integer|min:0',
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

            $Qr::create([
                'qr_id' => $qrId,
                'points' => $points,
                'remark' => null,
                'is_used' => false,
                'used_by' => null,
                'qr_image' => $filename,
            ]);
        }

        return redirect()->route('admin.qr')->with('success', 'QR Codes generated successfully.');
    }

    public function storeAndExportPdf(Request $request)
    {
        $validated = $request->validate([
            'number_of_qrs' => 'required|integer|min:1',
            'points' => 'required|integer|min:0',
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

            $qrRecord = QR::where('qr_id', $request->input('qr_id'))->first();
            if (!$qrRecord) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid QR code.'
                ], 400);
            }

            if ($qrRecord->is_used) {
                $usedByUser = User::find($qrRecord->used_by);

                return response()->json([
                    'status' => 1,
                    'message' => 'Already used QR code.',
                    'data' => [
                        'user_id' => $usedByUser->id,
                        'name' => $usedByUser->name,
                        'email' => $usedByUser->email,
                        'mobile_number' => $usedByUser->mobile_number
                    ]
                ], 400);
            }

            $qrRecord->is_used = true;
            $qrRecord->used_by = auth()->id();
            $qrRecord->remark = $request->input('remark');
            $qrRecord->save();

            $user = auth()->user();
            $user->balance += $qrRecord->points;
            $user->save();

            $transactionId = 'TX-' . strtoupper($qrRecord->qr_id);
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'points' => $qrRecord->points,
                'type' => 'credit',
                'remark' => 'Points added for QR code scan',
                'transaction_id' => $transactionId,
                'status' => 'completed'
                // 'message' => 'Points added: ' . $qrRecord->points
            ]);

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'QR used successfully, points added.',
                'points' => $qrRecord->points,
                'qr' => $qrRecord,
                'balance' => $user->balance,
                'transaction' => $transaction
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
        $qrs = Qr::where('is_used', 0)->take($count)->get();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $additionalData = [
            'generatedAt' => now()->toDateTimeString(), // Example of passing timestamp
            'count' => $count,
        ];
        $pdf = PDF::loadView('admin.qr.pdf', array_merge(compact('qrs'), $additionalData));
        return $pdf->download('qr_records_' . $count . '_' . $timestamp . '.pdf');
    }
}
