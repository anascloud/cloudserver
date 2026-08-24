<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AssetMaintenance;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
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

            $query = AssetMaintenance::query();

            if ($search) {
                $query->where('asset_maintenance_serial_number', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'AssetMaintenances fetched successfully!',
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
            $item = AssetMaintenance::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'AssetMaintenance fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'AssetMaintenance not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_maintenance_serial_number' => 'sometimes|required_without:assetMaintenanceSerialNumber|string|max:255',
                'assetMaintenanceSerialNumber' => 'sometimes|required_without:asset_maintenance_serial_number|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
            ]);

            $data = [
                'asset_maintenance_serial_number' => $request->input('asset_maintenance_serial_number') ?? $request->input('assetMaintenanceSerialNumber'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
            ];

            $item = AssetMaintenance::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['asset_maintenance_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'AssetMaintenance created successfully!',
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
                'asset_maintenance_serial_number' => 'sometimes|required_without:assetMaintenanceSerialNumber|string|max:255',
                'assetMaintenanceSerialNumber' => 'sometimes|required_without:asset_maintenance_serial_number|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
            ]);

            $item = AssetMaintenance::findOrFail($request->id);
            $data = [
                'asset_maintenance_serial_number' => $request->input('asset_maintenance_serial_number') ?? $request->input('assetMaintenanceSerialNumber'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['asset_maintenance_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'AssetMaintenance updated successfully!',
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
            $item = AssetMaintenance::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetMaintenance deleted successfully!',
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
            AssetMaintenance::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetMaintenances deleted successfully!',
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
