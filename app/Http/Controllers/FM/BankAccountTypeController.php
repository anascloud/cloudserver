<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankAccountType;
use Illuminate\Http\Request;

class BankAccountTypeController extends Controller
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

            $query = BankAccountType::query();

            if ($search) {
                $query->where('bank_account_type_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'BankAccountTypes fetched successfully!',
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
            $item = BankAccountType::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'BankAccountType fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BankAccountType not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_account_type_name' => 'sometimes|required_without:bankAccountTypeName|string|max:255',
                'bankAccountTypeName' => 'sometimes|required_without:bank_account_type_name|string|max:255',
            ]);

            $data = [
                'bank_account_type_name' => $request->input('bank_account_type_name') ?? $request->input('bankAccountTypeName'),
            ];

            $item = BankAccountType::create($data);

            return response()->json([
                'status' => true,
                'message' => 'BankAccountType created successfully!',
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
                'bank_account_type_name' => 'sometimes|required_without:bankAccountTypeName|string|max:255',
                'bankAccountTypeName' => 'sometimes|required_without:bank_account_type_name|string|max:255',
            ]);

            $item = BankAccountType::findOrFail($request->id);
            $data = [
                'bank_account_type_name' => $request->input('bank_account_type_name') ?? $request->input('bankAccountTypeName'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'BankAccountType updated successfully!',
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
            $item = BankAccountType::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankAccountType deleted successfully!',
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
            BankAccountType::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'BankAccountTypes deleted successfully!',
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
