<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')->paginate(20);
        return response()->json([
            'status' => 1,
            'message' => 'Transactions retrieved successfully.',
            'data' => $transactions
        ]);
    }
    public function indexApi(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $singleDate = $request->input('date');

        $transactions = Transaction::where('user_id', $user->id)
            ->with(['gift:id,name,image'])
            ->orderBy('created_at', 'desc');

        if ($singleDate) {
            $transactions->whereDate('created_at', $singleDate);
        }

        if ($startDate && $endDate) {
            $transactions->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $transactions->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $transactions->whereDate('created_at', '<=', $endDate);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Transactions retrieved successfully.',
            'data' => $transactions->get()
        ]);
    }
}
