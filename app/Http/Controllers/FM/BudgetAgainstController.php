<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BudgetAgainst;
use Illuminate\Http\Request;

class BudgetAgainstController extends Controller
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

            $query = BudgetAgainst::query();

            if ($search) {
                $query->where('budget_against_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'BudgetAgainsts fetched successfully!',
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
            $item = BudgetAgainst::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'BudgetAgainst fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BudgetAgainst not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'budget_against_name' => 'sometimes|required_without:budgetAgainstName|string|max:255',
                'budgetAgainstName' => 'sometimes|required_without:budget_against_name|string|max:255',
            ]);

            $data = [
                'budget_against_name' => $request->input('budget_against_name') ?? $request->input('budgetAgainstName'),
            ];

            $item = BudgetAgainst::create($data);

            return response()->json([
                'status' => true,
                'message' => 'BudgetAgainst created successfully!',
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
                'budget_against_name' => 'sometimes|required_without:budgetAgainstName|string|max:255',
                'budgetAgainstName' => 'sometimes|required_without:budget_against_name|string|max:255',
            ]);

            $item = BudgetAgainst::findOrFail($request->id);
            $data = [
                'budget_against_name' => $request->input('budget_against_name') ?? $request->input('budgetAgainstName'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'BudgetAgainst updated successfully!',
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
            $item = BudgetAgainst::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'BudgetAgainst deleted successfully!',
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
            BudgetAgainst::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'BudgetAgainsts deleted successfully!',
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
