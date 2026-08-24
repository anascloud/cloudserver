<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
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

            $query = AssetCategory::query();

            if ($search) {
                $query->where('asset_category_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'AssetCategorys fetched successfully!',
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
            $item = AssetCategory::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'AssetCategory fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'AssetCategory not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_category_name' => 'sometimes|required_without:assetCategoryName|string|max:255',
                'assetCategoryName' => 'sometimes|required_without:asset_category_name|string|max:255',
                'fixed_asset_account_id' => 'sometimes|nullable|integer',
                'fixedAssetAccountId' => 'sometimes|nullable|integer',
            ]);

            $data = [
                'asset_category_name' => $request->input('asset_category_name') ?? $request->input('assetCategoryName'),
                'fixed_asset_account_id' => $request->input('fixed_asset_account_id') ?? $request->input('fixedAssetAccountId'),
            ];

            $item = AssetCategory::create($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetCategory created successfully!',
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
                'asset_category_name' => 'sometimes|required_without:assetCategoryName|string|max:255',
                'assetCategoryName' => 'sometimes|required_without:asset_category_name|string|max:255',
                'fixed_asset_account_id' => 'sometimes|nullable|integer',
                'fixedAssetAccountId' => 'sometimes|nullable|integer',
            ]);

            $item = AssetCategory::findOrFail($request->id);
            $data = [
                'asset_category_name' => $request->input('asset_category_name') ?? $request->input('assetCategoryName'),
                'fixed_asset_account_id' => $request->input('fixed_asset_account_id') ?? $request->input('fixedAssetAccountId'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetCategory updated successfully!',
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
            $item = AssetCategory::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetCategory deleted successfully!',
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
            AssetCategory::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetCategorys deleted successfully!',
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
