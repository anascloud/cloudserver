<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
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

            $query = ChartOfAccount::query();

            if ($search) {
                $query->where('account_name', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'ChartOfAccounts fetched successfully!',
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
            $item = ChartOfAccount::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'ChartOfAccount fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ChartOfAccount not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'account_name' => 'sometimes|required_without:accountName|string|max:255',
                'accountName' => 'sometimes|required_without:account_name|string|max:255',
                'account_name_with_abbr' => 'sometimes|nullable|string|max:255',
                'accountNameWithAbbr' => 'sometimes|nullable|string|max:255',
                'account_number' => 'sometimes|nullable|string|max:255',
                'accountNumber' => 'sometimes|nullable|string|max:255',
                'accounting_type_id' => 'sometimes|nullable|integer',
                'accountingTypeId' => 'sometimes|nullable|integer',
                'parent_account_id' => 'sometimes|nullable|integer',
                'parentAccountId' => 'sometimes|nullable|integer',
                'balance_must_be' => 'sometimes|nullable|string|max:255',
                'balanceMustBe' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'currency_id' => 'sometimes|nullable|integer',
                'currencyId' => 'sometimes|nullable|integer',
                'tax_rate' => 'sometimes|nullable|numeric',
                'taxRate' => 'sometimes|nullable|numeric',
                'is_disabled' => 'sometimes|boolean',
                'isDisabled' => 'sometimes|boolean',
            ]);

            $data = [
                'account_name' => $request->input('account_name') ?? $request->input('accountName'),
                'account_name_with_abbr' => $request->input('account_name_with_abbr') ?? $request->input('accountNameWithAbbr'),
                'account_number' => $request->input('account_number') ?? $request->input('accountNumber'),
                'accounting_type_id' => $request->input('accounting_type_id') ?? $request->input('accountingTypeId'),
                'parent_account_id' => $request->input('parent_account_id') ?? $request->input('parentAccountId'),
                'balance_must_be' => $request->input('balance_must_be') ?? $request->input('balanceMustBe'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'currency_id' => $request->input('currency_id') ?? $request->input('currencyId'),
                'tax_rate' => $request->input('tax_rate') ?? $request->input('taxRate'),
                'is_disabled' => $request->input('is_disabled') ?? $request->input('isDisabled'),
            ];

            $item = ChartOfAccount::create($data);

            return response()->json([
                'status' => true,
                'message' => 'ChartOfAccount created successfully!',
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
                'account_name' => 'sometimes|required_without:accountName|string|max:255',
                'accountName' => 'sometimes|required_without:account_name|string|max:255',
                'account_name_with_abbr' => 'sometimes|nullable|string|max:255',
                'accountNameWithAbbr' => 'sometimes|nullable|string|max:255',
                'account_number' => 'sometimes|nullable|string|max:255',
                'accountNumber' => 'sometimes|nullable|string|max:255',
                'accounting_type_id' => 'sometimes|nullable|integer',
                'accountingTypeId' => 'sometimes|nullable|integer',
                'parent_account_id' => 'sometimes|nullable|integer',
                'parentAccountId' => 'sometimes|nullable|integer',
                'balance_must_be' => 'sometimes|nullable|string|max:255',
                'balanceMustBe' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'currency_id' => 'sometimes|nullable|integer',
                'currencyId' => 'sometimes|nullable|integer',
                'tax_rate' => 'sometimes|nullable|numeric',
                'taxRate' => 'sometimes|nullable|numeric',
                'is_disabled' => 'sometimes|boolean',
                'isDisabled' => 'sometimes|boolean',
            ]);

            $item = ChartOfAccount::findOrFail($request->id);
            $data = [
                'account_name' => $request->input('account_name') ?? $request->input('accountName'),
                'account_name_with_abbr' => $request->input('account_name_with_abbr') ?? $request->input('accountNameWithAbbr'),
                'account_number' => $request->input('account_number') ?? $request->input('accountNumber'),
                'accounting_type_id' => $request->input('accounting_type_id') ?? $request->input('accountingTypeId'),
                'parent_account_id' => $request->input('parent_account_id') ?? $request->input('parentAccountId'),
                'balance_must_be' => $request->input('balance_must_be') ?? $request->input('balanceMustBe'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'currency_id' => $request->input('currency_id') ?? $request->input('currencyId'),
                'tax_rate' => $request->input('tax_rate') ?? $request->input('taxRate'),
                'is_disabled' => $request->input('is_disabled') ?? $request->input('isDisabled'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'ChartOfAccount updated successfully!',
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
            $item = ChartOfAccount::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'ChartOfAccount deleted successfully!',
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
            ChartOfAccount::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'ChartOfAccounts deleted successfully!',
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
