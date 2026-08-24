<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AssetLocation;
use Illuminate\Http\Request;

class AssetLocationController extends Controller
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

            $query = AssetLocation::query();

            if ($search) {
                $query->where('asset_location_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'AssetLocations fetched successfully!',
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
            $item = AssetLocation::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'AssetLocation fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'AssetLocation not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_location_name' => 'sometimes|required_without:assetLocationName|string|max:255',
                'assetLocationName' => 'sometimes|required_without:asset_location_name|string|max:255',
            ]);

            $data = [
                'asset_location_name' => $request->input('asset_location_name') ?? $request->input('assetLocationName'),
            ];

            $item = AssetLocation::create($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetLocation created successfully!',
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
                'asset_location_name' => 'sometimes|required_without:assetLocationName|string|max:255',
                'assetLocationName' => 'sometimes|required_without:asset_location_name|string|max:255',
            ]);

            $item = AssetLocation::findOrFail($request->id);
            $data = [
                'asset_location_name' => $request->input('asset_location_name') ?? $request->input('assetLocationName'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetLocation updated successfully!',
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
            $item = AssetLocation::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetLocation deleted successfully!',
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
            AssetLocation::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetLocations deleted successfully!',
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
