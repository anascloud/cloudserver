<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AssetRepair;
use Illuminate\Http\Request;

class AssetRepairController extends Controller
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

            $query = AssetRepair::query();

            if ($search) {
                $query->where('asset_repair_serial_number', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'AssetRepairs fetched successfully!',
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
            $item = AssetRepair::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'AssetRepair fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'AssetRepair not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_repair_serial_number' => 'sometimes|required_without:assetRepairSerialNumber|string|max:255',
                'assetRepairSerialNumber' => 'sometimes|required_without:asset_repair_serial_number|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'failure_date' => 'sometimes|nullable|date',
                'failureDate' => 'sometimes|nullable|date',
                'completion_date' => 'sometimes|nullable|date',
                'completionDate' => 'sometimes|nullable|date',
                'repair_date' => 'sometimes|nullable|date',
                'repairDate' => 'sometimes|nullable|date',
                'purchase_invoice_no' => 'sometimes|nullable|string|max:255',
                'purchaseInvoiceNo' => 'sometimes|nullable|string|max:255',
                'expense_account_id' => 'sometimes|nullable|integer',
                'expenseAccountId' => 'sometimes|nullable|integer',
                'repair_cost' => 'sometimes|nullable|numeric',
                'repairCost' => 'sometimes|nullable|numeric',
                'repair_description' => 'sometimes|nullable|string|max:255',
                'repairDescription' => 'sometimes|nullable|string|max:255',
                'repair_status' => 'sometimes|nullable|string|max:255',
                'repairStatus' => 'sometimes|nullable|string|max:255',
            ]);

            $data = [
                'asset_repair_serial_number' => $request->input('asset_repair_serial_number') ?? $request->input('assetRepairSerialNumber'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'failure_date' => $request->input('failure_date') ?? $request->input('failureDate'),
                'completion_date' => $request->input('completion_date') ?? $request->input('completionDate'),
                'repair_date' => $request->input('repair_date') ?? $request->input('repairDate'),
                'purchase_invoice_no' => $request->input('purchase_invoice_no') ?? $request->input('purchaseInvoiceNo'),
                'expense_account_id' => $request->input('expense_account_id') ?? $request->input('expenseAccountId'),
                'repair_cost' => $request->input('repair_cost') ?? $request->input('repairCost'),
                'repair_description' => $request->input('repair_description') ?? $request->input('repairDescription'),
                'repair_status' => $request->input('repair_status') ?? $request->input('repairStatus'),
            ];

            $item = AssetRepair::create($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetRepair created successfully!',
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
                'asset_repair_serial_number' => 'sometimes|required_without:assetRepairSerialNumber|string|max:255',
                'assetRepairSerialNumber' => 'sometimes|required_without:asset_repair_serial_number|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'failure_date' => 'sometimes|nullable|date',
                'failureDate' => 'sometimes|nullable|date',
                'completion_date' => 'sometimes|nullable|date',
                'completionDate' => 'sometimes|nullable|date',
                'repair_date' => 'sometimes|nullable|date',
                'repairDate' => 'sometimes|nullable|date',
                'purchase_invoice_no' => 'sometimes|nullable|string|max:255',
                'purchaseInvoiceNo' => 'sometimes|nullable|string|max:255',
                'expense_account_id' => 'sometimes|nullable|integer',
                'expenseAccountId' => 'sometimes|nullable|integer',
                'repair_cost' => 'sometimes|nullable|numeric',
                'repairCost' => 'sometimes|nullable|numeric',
                'repair_description' => 'sometimes|nullable|string|max:255',
                'repairDescription' => 'sometimes|nullable|string|max:255',
                'repair_status' => 'sometimes|nullable|string|max:255',
                'repairStatus' => 'sometimes|nullable|string|max:255',
            ]);

            $item = AssetRepair::findOrFail($request->id);
            $data = [
                'asset_repair_serial_number' => $request->input('asset_repair_serial_number') ?? $request->input('assetRepairSerialNumber'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'failure_date' => $request->input('failure_date') ?? $request->input('failureDate'),
                'completion_date' => $request->input('completion_date') ?? $request->input('completionDate'),
                'repair_date' => $request->input('repair_date') ?? $request->input('repairDate'),
                'purchase_invoice_no' => $request->input('purchase_invoice_no') ?? $request->input('purchaseInvoiceNo'),
                'expense_account_id' => $request->input('expense_account_id') ?? $request->input('expenseAccountId'),
                'repair_cost' => $request->input('repair_cost') ?? $request->input('repairCost'),
                'repair_description' => $request->input('repair_description') ?? $request->input('repairDescription'),
                'repair_status' => $request->input('repair_status') ?? $request->input('repairStatus'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetRepair updated successfully!',
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
            $item = AssetRepair::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetRepair deleted successfully!',
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
            AssetRepair::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetRepairs deleted successfully!',
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
