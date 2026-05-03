<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $user_id = auth('api')->id();

        $total_balance = Transaction::where('user_id', $user_id)
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income')
            ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense')
            ->first();

        $total_income  = $total_balance->total_income ?? 0;
        $total_expense = $total_balance->total_expense ?? 0;
        $net_balance   = $total_income - $total_expense;

        $transactions = Transaction::with('category')
            ->where('user_id', $user_id)
            ->orderBy('transaction_date', 'desc')
            ->get();



        $data = [
            'total_income'  => (string) $total_income ?? '0.00',
            'total_expense' => (string) $total_expense ?? '0.00',
            'net_balance'   => (string) $net_balance ?? '0.00',
            'transactions'  => $transactions,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Transactions retrieved successfully',
            'data'    => $data,
            'status'  => 200,
        ], 200);
    }

}
