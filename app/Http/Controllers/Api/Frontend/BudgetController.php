<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $user_id = auth('api')->id();

        $budgets = Budget::with('category')->where('user_id', $user_id)->get();

        $current_month = now()->month;
        $current_year  = now()->year;

        $total_monthly_budget = Budget::where('user_id', $user_id)
                                ->where('period', 'monthly')
                                ->whereMonth('start_date', $current_month)
                                ->whereYear('start_date', $current_year)
                                ->sum('amount');




        $budgets->transform(function ($budget) {


            $monthly_expenses = Transaction::where('user_id', $budget->user_id)
                ->where('type', 'expense')
                ->whereHas('category', function ($query) use ($budget) {
                    $query->where('id', $budget->category_id);
                })
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount');

            $remining_budget = $budget->amount - $monthly_expenses;


            $user_budger_percentage = $budget->amount > 0 ? ($monthly_expenses / $budget->amount) * 100 : 0;



            return [
                'id'         => $budget->id,
                'category'   => $budget->category ? $budget->category->name : null,
                'amount'     => $budget->amount ?? '0.00',
                'remaining_budget' => (string) $remining_budget ?? '0.00',
                'monthly_expenses' => (string) $monthly_expenses ?? '0.00',
                'budget_percentage_used' => round($user_budger_percentage, 2) . '%',
                'period'     => $budget->period,
                'start_date' => $budget->start_date->toDateString() ?? null,
                'end_date'   => $budget->end_date ? $budget->end_date->toDateString() : null,
            ];
        });

        $data = [
            'total_monthly_budget' => $total_monthly_budget ?? '0.00',
            'budgets'              => $budgets,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Budgets retrieved successfully',
            'data'    => $data,
            'status'  => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric',
            'period'      => 'required|in:daily,weekly,monthly,yearly',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $user_id = auth('api')->id();

        $budget = Budget::create([
            'user_id'     => $user_id,
            'category_id' => $request->input('category_id'),
            'amount'      => $request->input('amount'),
            'period'      => $request->input('period'),
            'start_date'  => $request->input('start_date'),
            'end_date'    => $request->input('end_date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Budget created successfully',
            'data'    => $budget,
            'status'  => 201,
        ], 201);
    }

    // Additional methods for show, update, delete can be added here as needed
    public function show($id)
    {
        $user_id = auth('api')->id();

        $budget = Budget::with('category')->where('user_id', $user_id)->find($id);

        if (! $budget) {
            return response()->json([
                'success' => false,
                'message' => 'Budget not found',
                'status'  => 404,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Budget retrieved successfully',
            'data'    => $budget,
            'status'  => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric',
            'period'      => 'required|in:daily,weekly,monthly,yearly',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $user_id = auth('api')->id();

        $budget = Budget::where('user_id', $user_id)->find($id);

        if (! $budget) {
            return response()->json([
                'success' => false,
                'message' => 'Budget not found',
                'status'  => 404,
            ], 404);
        }

        $budget->update([
            'category_id' => $request->input('category_id'),
            'amount'      => $request->input('amount'),
            'period'      => $request->input('period'),
            'start_date'  => $request->input('start_date'),
            'end_date'    => $request->input('end_date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Budget updated successfully',
            'data'    => $budget,
            'status'  => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $user_id = auth('api')->id();

        $budget = Budget::where('user_id', $user_id)->find($id);

        if (! $budget) {
            return response()->json([
                'success' => false,
                'message' => 'Budget not found',
                'status'  => 404,
            ], 404);
        }

        $budget->delete();

        return response()->json([
            'success' => true,
            'message' => 'Budget deleted successfully',
            'status'  => 200,
        ], 200);
    }

}
