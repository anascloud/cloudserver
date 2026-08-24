<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
    }

    public function indexAll(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize', $request->input('per_page', 10));
            $search = $request->input('search', '');

            $query = Budget::query();

            if ($search) {
                $query->where('budget_name', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Budgets fetched successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $items->currentPage(),
                    'data' => $items->items(),
                    'first_page_url' => $items->url(1),
                    'from' => $items->firstItem(),
                    'last_page' => $items->lastPage(),
                    'last_page_url' => $items->url($items->lastPage()),
                    'next_page_url' => $items->nextPageUrl(),
                    'path' => $request->url(),
                    'per_page' => $items->perPage(),
                    'prev_page_url' => $items->previousPageUrl(),
                    'to' => $items->lastItem(),
                    'total' => $items->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    public function index(Request $request)
    {
        return $this->indexAll($request);
    }

    public function show($id)
    {
        try {
            $item = Budget::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Budget fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Budget not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'budget_name' => 'sometimes|required_without:budgetName|string|max:255',
                'budgetName' => 'sometimes|required_without:budget_name|string|max:255',
                'budget_against_id' => 'sometimes|nullable|integer',
                'budgetAgainstId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'fiscal_year_id' => 'sometimes|nullable|integer',
                'fiscalYearId' => 'sometimes|nullable|integer',
                'budget_distribution_id' => 'sometimes|nullable|integer',
                'budgetDistributionId' => 'sometimes|nullable|integer',
                'cost_center_id' => 'sometimes|nullable|integer',
                'costCenterId' => 'sometimes|nullable|integer',
            ]);

            $data = [
                'budget_name' => $request->input('budget_name') ?? $request->input('budgetName'),
                'budget_against_id' => $request->input('budget_against_id') ?? $request->input('budgetAgainstId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'fiscal_year_id' => $request->input('fiscal_year_id') ?? $request->input('fiscalYearId'),
                'budget_distribution_id' => $request->input('budget_distribution_id') ?? $request->input('budgetDistributionId'),
                'cost_center_id' => $request->input('cost_center_id') ?? $request->input('costCenterId'),
            ];

            $item = Budget::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['budget_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Budget created successfully!',
                'errors' => null,
                'data' => $item,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'budget_name' => 'sometimes|required_without:budgetName|string|max:255',
                'budgetName' => 'sometimes|required_without:budget_name|string|max:255',
                'budget_against_id' => 'sometimes|nullable|integer',
                'budgetAgainstId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'fiscal_year_id' => 'sometimes|nullable|integer',
                'fiscalYearId' => 'sometimes|nullable|integer',
                'budget_distribution_id' => 'sometimes|nullable|integer',
                'budgetDistributionId' => 'sometimes|nullable|integer',
                'cost_center_id' => 'sometimes|nullable|integer',
                'costCenterId' => 'sometimes|nullable|integer',
            ]);

            $item = Budget::findOrFail($request->id);
            $data = [
                'budget_name' => $request->input('budget_name') ?? $request->input('budgetName'),
                'budget_against_id' => $request->input('budget_against_id') ?? $request->input('budgetAgainstId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'fiscal_year_id' => $request->input('fiscal_year_id') ?? $request->input('fiscalYearId'),
                'budget_distribution_id' => $request->input('budget_distribution_id') ?? $request->input('budgetDistributionId'),
                'cost_center_id' => $request->input('cost_center_id') ?? $request->input('costCenterId'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['budget_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Budget updated successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $item = Budget::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Budget deleted successfully!',
                'errors' => null,
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->all();
            Budget::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Budgets deleted successfully!',
                'errors' => null,
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }
}
