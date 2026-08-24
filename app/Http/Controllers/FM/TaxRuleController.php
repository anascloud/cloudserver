<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\TaxRule;
use Illuminate\Http\Request;

class TaxRuleController extends Controller
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

            $query = TaxRule::query();

            if ($search) {
                $query->where('serial_number', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'TaxRules fetched successfully!',
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
            $item = TaxRule::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'TaxRule fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'TaxRule not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'serial_number' => 'sometimes|required_without:serialNumber|string|max:255',
                'serialNumber' => 'sometimes|required_without:serial_number|string|max:255',
                'rule_type' => 'sometimes|nullable|string|max:255',
                'ruleType' => 'sometimes|nullable|string|max:255',
                'tax_template_id' => 'sometimes|nullable|integer',
                'taxTemplateId' => 'sometimes|nullable|integer',
                'customer_id' => 'sometimes|nullable|integer',
                'customerId' => 'sometimes|nullable|integer',
                'customer_name' => 'sometimes|nullable|string|max:255',
                'customerName' => 'sometimes|nullable|string|max:255',
                'supplier_id' => 'sometimes|nullable|integer',
                'supplierId' => 'sometimes|nullable|integer',
                'supplier_name' => 'sometimes|nullable|string|max:255',
                'supplierName' => 'sometimes|nullable|string|max:255',
                'product_id' => 'sometimes|nullable|integer',
                'productId' => 'sometimes|nullable|integer',
                'product_name' => 'sometimes|nullable|string|max:255',
                'productName' => 'sometimes|nullable|string|max:255',
                'tax_category_id' => 'sometimes|nullable|integer',
                'taxCategoryId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'valid_from' => 'sometimes|nullable|date',
                'validFrom' => 'sometimes|nullable|date',
                'valid_to' => 'sometimes|nullable|date',
                'validTo' => 'sometimes|nullable|date',
            ]);

            $data = [
                'serial_number' => $request->input('serial_number') ?? $request->input('serialNumber'),
                'rule_type' => $request->input('rule_type') ?? $request->input('ruleType'),
                'tax_template_id' => $request->input('tax_template_id') ?? $request->input('taxTemplateId'),
                'customer_id' => $request->input('customer_id') ?? $request->input('customerId'),
                'customer_name' => $request->input('customer_name') ?? $request->input('customerName'),
                'supplier_id' => $request->input('supplier_id') ?? $request->input('supplierId'),
                'supplier_name' => $request->input('supplier_name') ?? $request->input('supplierName'),
                'product_id' => $request->input('product_id') ?? $request->input('productId'),
                'product_name' => $request->input('product_name') ?? $request->input('productName'),
                'tax_category_id' => $request->input('tax_category_id') ?? $request->input('taxCategoryId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'valid_from' => $request->input('valid_from') ?? $request->input('validFrom'),
                'valid_to' => $request->input('valid_to') ?? $request->input('validTo'),
            ];

            $item = TaxRule::create($data);

            return response()->json([
                'status' => true,
                'message' => 'TaxRule created successfully!',
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
                'serial_number' => 'sometimes|required_without:serialNumber|string|max:255',
                'serialNumber' => 'sometimes|required_without:serial_number|string|max:255',
                'rule_type' => 'sometimes|nullable|string|max:255',
                'ruleType' => 'sometimes|nullable|string|max:255',
                'tax_template_id' => 'sometimes|nullable|integer',
                'taxTemplateId' => 'sometimes|nullable|integer',
                'customer_id' => 'sometimes|nullable|integer',
                'customerId' => 'sometimes|nullable|integer',
                'customer_name' => 'sometimes|nullable|string|max:255',
                'customerName' => 'sometimes|nullable|string|max:255',
                'supplier_id' => 'sometimes|nullable|integer',
                'supplierId' => 'sometimes|nullable|integer',
                'supplier_name' => 'sometimes|nullable|string|max:255',
                'supplierName' => 'sometimes|nullable|string|max:255',
                'product_id' => 'sometimes|nullable|integer',
                'productId' => 'sometimes|nullable|integer',
                'product_name' => 'sometimes|nullable|string|max:255',
                'productName' => 'sometimes|nullable|string|max:255',
                'tax_category_id' => 'sometimes|nullable|integer',
                'taxCategoryId' => 'sometimes|nullable|integer',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'valid_from' => 'sometimes|nullable|date',
                'validFrom' => 'sometimes|nullable|date',
                'valid_to' => 'sometimes|nullable|date',
                'validTo' => 'sometimes|nullable|date',
            ]);

            $item = TaxRule::findOrFail($request->id);
            $data = [
                'serial_number' => $request->input('serial_number') ?? $request->input('serialNumber'),
                'rule_type' => $request->input('rule_type') ?? $request->input('ruleType'),
                'tax_template_id' => $request->input('tax_template_id') ?? $request->input('taxTemplateId'),
                'customer_id' => $request->input('customer_id') ?? $request->input('customerId'),
                'customer_name' => $request->input('customer_name') ?? $request->input('customerName'),
                'supplier_id' => $request->input('supplier_id') ?? $request->input('supplierId'),
                'supplier_name' => $request->input('supplier_name') ?? $request->input('supplierName'),
                'product_id' => $request->input('product_id') ?? $request->input('productId'),
                'product_name' => $request->input('product_name') ?? $request->input('productName'),
                'tax_category_id' => $request->input('tax_category_id') ?? $request->input('taxCategoryId'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'valid_from' => $request->input('valid_from') ?? $request->input('validFrom'),
                'valid_to' => $request->input('valid_to') ?? $request->input('validTo'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'TaxRule updated successfully!',
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
            $item = TaxRule::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'TaxRule deleted successfully!',
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
            TaxRule::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'TaxRules deleted successfully!',
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
