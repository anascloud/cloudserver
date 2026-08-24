<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
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

            $query = CostCenter::query();

            if ($search) {
                $query->where('cost_center_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'CostCenters fetched successfully!',
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
            $item = CostCenter::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'CostCenter fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'CostCenter not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cost_center_name' => 'sometimes|required_without:costCenterName|string|max:255',
                'costCenterName' => 'sometimes|required_without:cost_center_name|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $data = [
                'cost_center_name' => $request->input('cost_center_name') ?? $request->input('costCenterName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item = CostCenter::create($data);

            return response()->json([
                'status' => true,
                'message' => 'CostCenter created successfully!',
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
                'cost_center_name' => 'sometimes|required_without:costCenterName|string|max:255',
                'costCenterName' => 'sometimes|required_without:cost_center_name|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $item = CostCenter::findOrFail($request->id);
            $data = [
                'cost_center_name' => $request->input('cost_center_name') ?? $request->input('costCenterName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'CostCenter updated successfully!',
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
            $item = CostCenter::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'CostCenter deleted successfully!',
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
            CostCenter::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'CostCenters deleted successfully!',
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
