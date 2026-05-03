<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'amount' => 'required|numeric',
            'source' => 'required|string|max:255',
            'transaction_date'   => 'required|date',
        ]);

        // Create a new income record
        $income = Transaction::create([
            'amount' => $request->input('amount'),
            'income_source' => $request->input('source'),
            'transaction_date'   => $request->input('transaction_date'),
            'user_id' => auth('api')->id(),
            'type' => 'income'
        ]);

        // Return a success response with the created income
        return response()->json([
            'success' => true,
            'message' => 'Income recorded successfully',
            'data'    => $income,
            'status'  => 201,
        ], 201);
    }
}
