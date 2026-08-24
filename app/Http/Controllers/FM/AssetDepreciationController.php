<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AssetDepreciation;
use Illuminate\Http\Request;

class AssetDepreciationController extends Controller
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

            $query = AssetDepreciation::query();

            if ($search) {
                $query->where('asset_depreciation_serial_number', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'AssetDepreciations fetched successfully!',
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
            $item = AssetDepreciation::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'AssetDepreciation fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'AssetDepreciation not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_depreciation_serial_number' => 'sometimes|required_without:assetDepreciationSerialNumber|string|max:255',
                'assetDepreciationSerialNumber' => 'sometimes|required_without:asset_depreciation_serial_number|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'finance_book_id' => 'sometimes|nullable|integer',
                'financeBookId' => 'sometimes|nullable|integer',
                'finance_book_name' => 'sometimes|nullable|string|max:255',
                'financeBookName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'depreciation_method' => 'sometimes|nullable|string|max:255',
                'depreciationMethod' => 'sometimes|nullable|string|max:255',
                'total_depreciation_period' => 'sometimes|nullable|integer',
                'totalDepreciationPeriod' => 'sometimes|nullable|integer',
                'frequency_of_depreciation' => 'sometimes|nullable|string|max:255',
                'frequencyOfDepreciation' => 'sometimes|nullable|string|max:255',
                'expected_value' => 'sometimes|nullable|numeric',
                'expectedValue' => 'sometimes|nullable|numeric',
                'asset_status' => 'sometimes|nullable|string|max:255',
                'assetStatus' => 'sometimes|nullable|string|max:255',
            ]);

            $data = [
                'asset_depreciation_serial_number' => $request->input('asset_depreciation_serial_number') ?? $request->input('assetDepreciationSerialNumber'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'finance_book_id' => $request->input('finance_book_id') ?? $request->input('financeBookId'),
                'finance_book_name' => $request->input('finance_book_name') ?? $request->input('financeBookName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'depreciation_method' => $request->input('depreciation_method') ?? $request->input('depreciationMethod'),
                'total_depreciation_period' => $request->input('total_depreciation_period') ?? $request->input('totalDepreciationPeriod'),
                'frequency_of_depreciation' => $request->input('frequency_of_depreciation') ?? $request->input('frequencyOfDepreciation'),
                'expected_value' => $request->input('expected_value') ?? $request->input('expectedValue'),
                'asset_status' => $request->input('asset_status') ?? $request->input('assetStatus'),
            ];

            $item = AssetDepreciation::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['asset_depreciation_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'AssetDepreciation created successfully!',
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
                'asset_depreciation_serial_number' => 'sometimes|required_without:assetDepreciationSerialNumber|string|max:255',
                'assetDepreciationSerialNumber' => 'sometimes|required_without:asset_depreciation_serial_number|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'finance_book_id' => 'sometimes|nullable|integer',
                'financeBookId' => 'sometimes|nullable|integer',
                'finance_book_name' => 'sometimes|nullable|string|max:255',
                'financeBookName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'depreciation_method' => 'sometimes|nullable|string|max:255',
                'depreciationMethod' => 'sometimes|nullable|string|max:255',
                'total_depreciation_period' => 'sometimes|nullable|integer',
                'totalDepreciationPeriod' => 'sometimes|nullable|integer',
                'frequency_of_depreciation' => 'sometimes|nullable|string|max:255',
                'frequencyOfDepreciation' => 'sometimes|nullable|string|max:255',
                'expected_value' => 'sometimes|nullable|numeric',
                'expectedValue' => 'sometimes|nullable|numeric',
                'asset_status' => 'sometimes|nullable|string|max:255',
                'assetStatus' => 'sometimes|nullable|string|max:255',
            ]);

            $item = AssetDepreciation::findOrFail($request->id);
            $data = [
                'asset_depreciation_serial_number' => $request->input('asset_depreciation_serial_number') ?? $request->input('assetDepreciationSerialNumber'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'finance_book_id' => $request->input('finance_book_id') ?? $request->input('financeBookId'),
                'finance_book_name' => $request->input('finance_book_name') ?? $request->input('financeBookName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'depreciation_method' => $request->input('depreciation_method') ?? $request->input('depreciationMethod'),
                'total_depreciation_period' => $request->input('total_depreciation_period') ?? $request->input('totalDepreciationPeriod'),
                'frequency_of_depreciation' => $request->input('frequency_of_depreciation') ?? $request->input('frequencyOfDepreciation'),
                'expected_value' => $request->input('expected_value') ?? $request->input('expectedValue'),
                'asset_status' => $request->input('asset_status') ?? $request->input('assetStatus'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['asset_depreciation_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'AssetDepreciation updated successfully!',
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
            $item = AssetDepreciation::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetDepreciation deleted successfully!',
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
            AssetDepreciation::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetDepreciations deleted successfully!',
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
