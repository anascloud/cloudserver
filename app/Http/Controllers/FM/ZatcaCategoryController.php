<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\ZatcaCategory;
use Illuminate\Http\Request;

class ZatcaCategoryController extends Controller
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

            $query = ZatcaCategory::query();

            if ($search) {
                $query->where('zatca_category_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'ZatcaCategorys fetched successfully!',
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
            $item = ZatcaCategory::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'ZatcaCategory fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ZatcaCategory not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'zatca_category_name' => 'sometimes|required_without:zatcaCategoryName|string|max:255',
                'zatcaCategoryName' => 'sometimes|required_without:zatca_category_name|string|max:255',
            ]);

            $data = [
                'zatca_category_name' => $request->input('zatca_category_name') ?? $request->input('zatcaCategoryName'),
            ];

            $item = ZatcaCategory::create($data);

            return response()->json([
                'status' => true,
                'message' => 'ZatcaCategory created successfully!',
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
                'zatca_category_name' => 'sometimes|required_without:zatcaCategoryName|string|max:255',
                'zatcaCategoryName' => 'sometimes|required_without:zatca_category_name|string|max:255',
            ]);

            $item = ZatcaCategory::findOrFail($request->id);
            $data = [
                'zatca_category_name' => $request->input('zatca_category_name') ?? $request->input('zatcaCategoryName'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'ZatcaCategory updated successfully!',
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
            $item = ZatcaCategory::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'ZatcaCategory deleted successfully!',
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
            ZatcaCategory::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'ZatcaCategorys deleted successfully!',
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
