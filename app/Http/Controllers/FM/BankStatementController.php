<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankStatement;
use Illuminate\Http\Request;

class BankStatementController extends Controller
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

            $query = BankStatement::query();

            if ($search) {
                $query->where('id', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'BankStatements fetched successfully!',
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
            $item = BankStatement::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'BankStatement fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BankStatement not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'bank_account_id' => 'sometimes|nullable|integer',
                'bankAccountId' => 'sometimes|nullable|integer',
                'currency_id' => 'sometimes|nullable|integer',
                'currencyId' => 'sometimes|nullable|integer',
                'bs_file' => 'sometimes|nullable|file',
                'bsFile' => 'sometimes|nullable|file',
            ]);

            $data = [
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'bank_account_id' => $request->input('bank_account_id') ?? $request->input('bankAccountId'),
                'currency_id' => $request->input('currency_id') ?? $request->input('currencyId'),
            ];

            if ($request->hasFile('bs_file') || $request->hasFile('bsFile')) {
                $file = $request->file('bs_file') ?? $request->file('bsFile');
                $data['bs_file'] = $file->store('bank_statements', 'public');
            }

            $item = BankStatement::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['bank_statement_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'BankStatement created successfully!',
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
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'bank_account_id' => 'sometimes|nullable|integer',
                'bankAccountId' => 'sometimes|nullable|integer',
                'currency_id' => 'sometimes|nullable|integer',
                'currencyId' => 'sometimes|nullable|integer',
                'bs_file' => 'sometimes|nullable|file',
                'bsFile' => 'sometimes|nullable|file',
            ]);

            $item = BankStatement::findOrFail($request->id);
            $data = [
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'bank_account_id' => $request->input('bank_account_id') ?? $request->input('bankAccountId'),
                'currency_id' => $request->input('currency_id') ?? $request->input('currencyId'),
            ];

            if ($request->hasFile('bs_file') || $request->hasFile('bsFile')) {
                $file = $request->file('bs_file') ?? $request->file('bsFile');
                $data['bs_file'] = $file->store('bank_statements', 'public');
            }

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['bank_statement_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'BankStatement updated successfully!',
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
            $item = BankStatement::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankStatement deleted successfully!',
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
            BankStatement::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankStatements deleted successfully!',
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
