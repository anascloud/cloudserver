<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
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

            $query = Asset::query();

            if ($search) {
                $query->where('asset_name', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Assets fetched successfully!',
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
            $item = Asset::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Asset fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Asset not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_name' => 'sometimes|required_without:assetName|string|max:255',
                'assetName' => 'sometimes|required_without:asset_name|string|max:255',
                'asset_serial_number' => 'sometimes|nullable|string|max:255',
                'assetSerialNumber' => 'sometimes|nullable|string|max:255',
                'product_id' => 'sometimes|nullable|integer',
                'productId' => 'sometimes|nullable|integer',
                'product_name' => 'sometimes|nullable|string|max:255',
                'productName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'asset_category_id' => 'sometimes|nullable|integer',
                'assetCategoryId' => 'sometimes|nullable|integer',
                'asset_location_id' => 'sometimes|nullable|integer',
                'assetLocationId' => 'sometimes|nullable|integer',
                'asset_owner_name' => 'sometimes|nullable|string|max:255',
                'assetOwnerName' => 'sometimes|nullable|string|max:255',
            ]);

            $data = [
                'asset_name' => $request->input('asset_name') ?? $request->input('assetName'),
                'asset_serial_number' => $request->input('asset_serial_number') ?? $request->input('assetSerialNumber'),
                'product_id' => $request->input('product_id') ?? $request->input('productId'),
                'product_name' => $request->input('product_name') ?? $request->input('productName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'asset_category_id' => $request->input('asset_category_id') ?? $request->input('assetCategoryId'),
                'asset_location_id' => $request->input('asset_location_id') ?? $request->input('assetLocationId'),
                'asset_owner_name' => $request->input('asset_owner_name') ?? $request->input('assetOwnerName'),
            ];

            $item = Asset::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Asset created successfully!',
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
                'asset_name' => 'sometimes|required_without:assetName|string|max:255',
                'assetName' => 'sometimes|required_without:asset_name|string|max:255',
                'asset_serial_number' => 'sometimes|nullable|string|max:255',
                'assetSerialNumber' => 'sometimes|nullable|string|max:255',
                'product_id' => 'sometimes|nullable|integer',
                'productId' => 'sometimes|nullable|integer',
                'product_name' => 'sometimes|nullable|string|max:255',
                'productName' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'asset_category_id' => 'sometimes|nullable|integer',
                'assetCategoryId' => 'sometimes|nullable|integer',
                'asset_location_id' => 'sometimes|nullable|integer',
                'assetLocationId' => 'sometimes|nullable|integer',
                'asset_owner_name' => 'sometimes|nullable|string|max:255',
                'assetOwnerName' => 'sometimes|nullable|string|max:255',
            ]);

            $item = Asset::findOrFail($request->id);
            $data = [
                'asset_name' => $request->input('asset_name') ?? $request->input('assetName'),
                'asset_serial_number' => $request->input('asset_serial_number') ?? $request->input('assetSerialNumber'),
                'product_id' => $request->input('product_id') ?? $request->input('productId'),
                'product_name' => $request->input('product_name') ?? $request->input('productName'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'asset_category_id' => $request->input('asset_category_id') ?? $request->input('assetCategoryId'),
                'asset_location_id' => $request->input('asset_location_id') ?? $request->input('assetLocationId'),
                'asset_owner_name' => $request->input('asset_owner_name') ?? $request->input('assetOwnerName'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Asset updated successfully!',
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
            $item = Asset::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Asset deleted successfully!',
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
            Asset::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Assets deleted successfully!',
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
