<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AssetMovement;
use Illuminate\Http\Request;

class AssetMovementController extends Controller
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

            $query = AssetMovement::query();

            if ($search) {
                $query->where('asset_movement_serial_number', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'AssetMovements fetched successfully!',
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
            $item = AssetMovement::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'AssetMovement fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'AssetMovement not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_movement_serial_number' => 'sometimes|required_without:assetMovementSerialNumber|string|max:255',
                'assetMovementSerialNumber' => 'sometimes|required_without:asset_movement_serial_number|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'transaction_date' => 'sometimes|nullable|date',
                'transactionDate' => 'sometimes|nullable|date',
                'purpose_of_movement' => 'sometimes|nullable|string|max:255',
                'purposeOfMovement' => 'sometimes|nullable|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'asset_location_id' => 'sometimes|nullable|integer',
                'assetLocationId' => 'sometimes|nullable|integer',
            ]);

            $data = [
                'asset_movement_serial_number' => $request->input('asset_movement_serial_number') ?? $request->input('assetMovementSerialNumber'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'transaction_date' => $request->input('transaction_date') ?? $request->input('transactionDate'),
                'purpose_of_movement' => $request->input('purpose_of_movement') ?? $request->input('purposeOfMovement'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'asset_location_id' => $request->input('asset_location_id') ?? $request->input('assetLocationId'),
            ];

            $item = AssetMovement::create($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetMovement created successfully!',
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
                'asset_movement_serial_number' => 'sometimes|required_without:assetMovementSerialNumber|string|max:255',
                'assetMovementSerialNumber' => 'sometimes|required_without:asset_movement_serial_number|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'transaction_date' => 'sometimes|nullable|date',
                'transactionDate' => 'sometimes|nullable|date',
                'purpose_of_movement' => 'sometimes|nullable|string|max:255',
                'purposeOfMovement' => 'sometimes|nullable|string|max:255',
                'asset_id' => 'sometimes|nullable|integer',
                'assetId' => 'sometimes|nullable|integer',
                'asset_location_id' => 'sometimes|nullable|integer',
                'assetLocationId' => 'sometimes|nullable|integer',
            ]);

            $item = AssetMovement::findOrFail($request->id);
            $data = [
                'asset_movement_serial_number' => $request->input('asset_movement_serial_number') ?? $request->input('assetMovementSerialNumber'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'transaction_date' => $request->input('transaction_date') ?? $request->input('transactionDate'),
                'purpose_of_movement' => $request->input('purpose_of_movement') ?? $request->input('purposeOfMovement'),
                'asset_id' => $request->input('asset_id') ?? $request->input('assetId'),
                'asset_location_id' => $request->input('asset_location_id') ?? $request->input('assetLocationId'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'AssetMovement updated successfully!',
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
            $item = AssetMovement::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetMovement deleted successfully!',
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
            AssetMovement::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'AssetMovements deleted successfully!',
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
