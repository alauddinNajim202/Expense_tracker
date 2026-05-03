<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'amount' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'transaction_date'   => 'required|date',
            'description' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
        ]);

        // Create a new expense record
        $expense = Transaction::create([
            'amount' => $request->input('amount'),
            'transaction_date'   => $request->input('transaction_date'),
            'user_id' => auth('api')->id(),
            'category_id' => $request->input('category_id'),
            'type' => 'expense',
            'payment_method' => $request->input('payment_method'),
            'description' => $request->input('description'),
        ]);




        $budget = $expense->category ? $expense->category->budgets()
            ->where('period', 'monthly')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->first() : null;

        // decrease the budget amount if budget exists
        if ($budget && $budget->amount > 0) {
            $budget->amount -= $expense->amount;
            $budget->save();
        }

        // if the expense is created and budget exists and budget amount is less than or equal to 0, return warning message
        if ($expense && $budget && $budget->amount <= 0) {
            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully, but you have exceeded your budget for this category this month.',
                'data'    => $expense,
                'status'  => 201,
            ], 201);
        }


        // Return a success response with the created expense
        return response()->json([
            'success' => true,
            'message' => 'Expense recorded successfully',
            'data'    => $expense,
            'status'  => 201,
        ], 201);
    }
}
