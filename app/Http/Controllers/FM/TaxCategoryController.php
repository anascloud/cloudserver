<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\TaxCategory;
use Illuminate\Http\Request;

class TaxCategoryController extends Controller
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

            $query = TaxCategory::query();

            if ($search) {
                $query->where('tax_category_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'TaxCategorys fetched successfully!',
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
            $item = TaxCategory::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'TaxCategory fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'TaxCategory not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tax_category_name' => 'sometimes|required_without:taxCategoryName|string|max:255',
                'taxCategoryName' => 'sometimes|required_without:tax_category_name|string|max:255',
                'zatca_category_id' => 'sometimes|nullable|integer',
                'zatcaCategoryId' => 'sometimes|nullable|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $data = [
                'tax_category_name' => $request->input('tax_category_name') ?? $request->input('taxCategoryName'),
                'zatca_category_id' => $request->input('zatca_category_id') ?? $request->input('zatcaCategoryId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item = TaxCategory::create($data);

            return response()->json([
                'status' => true,
                'message' => 'TaxCategory created successfully!',
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
                'tax_category_name' => 'sometimes|required_without:taxCategoryName|string|max:255',
                'taxCategoryName' => 'sometimes|required_without:tax_category_name|string|max:255',
                'zatca_category_id' => 'sometimes|nullable|integer',
                'zatcaCategoryId' => 'sometimes|nullable|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $item = TaxCategory::findOrFail($request->id);
            $data = [
                'tax_category_name' => $request->input('tax_category_name') ?? $request->input('taxCategoryName'),
                'zatca_category_id' => $request->input('zatca_category_id') ?? $request->input('zatcaCategoryId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'TaxCategory updated successfully!',
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
            $item = TaxCategory::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'TaxCategory deleted successfully!',
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
            TaxCategory::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'TaxCategorys deleted successfully!',
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
