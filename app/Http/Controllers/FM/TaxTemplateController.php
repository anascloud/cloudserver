<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\TaxTemplate;
use Illuminate\Http\Request;

class TaxTemplateController extends Controller
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

            $query = TaxTemplate::query();

            if ($search) {
                $query->where('tax_template_name', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'TaxTemplates fetched successfully!',
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
            $item = TaxTemplate::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'TaxTemplate fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'TaxTemplate not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tax_template_name' => 'sometimes|required_without:taxTemplateName|string|max:255',
                'taxTemplateName' => 'sometimes|required_without:tax_template_name|string|max:255',
                'tax_template_type_id' => 'sometimes|nullable|integer',
                'taxTemplateTypeId' => 'sometimes|nullable|integer',
                'template_type' => 'sometimes|nullable|string|max:255',
                'templateType' => 'sometimes|nullable|string|max:255',
                'tax_category_id' => 'sometimes|nullable|integer',
                'taxCategoryId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $data = [
                'tax_template_name' => $request->input('tax_template_name') ?? $request->input('taxTemplateName'),
                'tax_template_type_id' => $request->input('tax_template_type_id') ?? $request->input('taxTemplateTypeId'),
                'template_type' => $request->input('template_type') ?? $request->input('templateType'),
                'tax_category_id' => $request->input('tax_category_id') ?? $request->input('taxCategoryId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item = TaxTemplate::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['tax_template_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'TaxTemplate created successfully!',
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
                'tax_template_name' => 'sometimes|required_without:taxTemplateName|string|max:255',
                'taxTemplateName' => 'sometimes|required_without:tax_template_name|string|max:255',
                'tax_template_type_id' => 'sometimes|nullable|integer',
                'taxTemplateTypeId' => 'sometimes|nullable|integer',
                'template_type' => 'sometimes|nullable|string|max:255',
                'templateType' => 'sometimes|nullable|string|max:255',
                'tax_category_id' => 'sometimes|nullable|integer',
                'taxCategoryId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $item = TaxTemplate::findOrFail($request->id);
            $data = [
                'tax_template_name' => $request->input('tax_template_name') ?? $request->input('taxTemplateName'),
                'tax_template_type_id' => $request->input('tax_template_type_id') ?? $request->input('taxTemplateTypeId'),
                'template_type' => $request->input('template_type') ?? $request->input('templateType'),
                'tax_category_id' => $request->input('tax_category_id') ?? $request->input('taxCategoryId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['tax_template_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'TaxTemplate updated successfully!',
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
            $item = TaxTemplate::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'TaxTemplate deleted successfully!',
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
            TaxTemplate::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'TaxTemplates deleted successfully!',
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
