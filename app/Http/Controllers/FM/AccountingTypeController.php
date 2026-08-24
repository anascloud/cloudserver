<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\AccountingType;
use Illuminate\Http\Request;

class AccountingTypeController extends Controller
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

            $query = AccountingType::query();

            if ($search) {
                $query->where('accounting_type_name', 'LIKE', "%{$search}%");
            }

            $types = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Accounting types fetched successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $types->currentPage(),
                    'data' => $types->items(),
                    'first_page_url' => $types->url(1),
                    'from' => $types->firstItem(),
                    'last_page' => $types->lastPage(),
                    'last_page_url' => $types->url($types->lastPage()),
                    'next_page_url' => $types->nextPageUrl(),
                    'path' => $request->url(),
                    'per_page' => $types->perPage(),
                    'prev_page_url' => $types->previousPageUrl(),
                    'to' => $types->lastItem(),
                    'total' => $types->total(),
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
            $type = AccountingType::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Accounting type fetched successfully!',
                'errors' => null,
                'data' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Accounting type not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'accounting_type_name' => 'required_without:accountingTypeName|string|max:255',
                'accountingTypeName' => 'required_without:accounting_type_name|string|max:255',
                'parent_id' => 'nullable|integer|exists:accounting_types,id',
            ]);

            $data = [
                'accounting_type_name' => $request->input('accounting_type_name') ?? $request->input('accountingTypeName'),
                'parent_id' => $request->input('parent_id'),
            ];

            $type = AccountingType::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Accounting type created successfully!',
                'errors' => null,
                'data' => $type,
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
            $type = AccountingType::findOrFail($request->id);

            $validated = $request->validate([
                'accounting_type_name' => 'required_without:accountingTypeName|string|max:255',
                'accountingTypeName' => 'required_without:accounting_type_name|string|max:255',
                'parent_id' => 'nullable|integer|exists:accounting_types,id',
            ]);

            $data = [
                'accounting_type_name' => $request->input('accounting_type_name') ?? $request->input('accountingTypeName'),
                'parent_id' => $request->input('parent_id'),
            ];

            $type->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Accounting type updated successfully!',
                'errors' => null,
                'data' => $type,
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
            $type = AccountingType::findOrFail($id);
            $type->delete();

            return response()->json([
                'status' => true,
                'message' => 'Accounting type deleted successfully!',
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
            AccountingType::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Accounting types deleted successfully!',
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
