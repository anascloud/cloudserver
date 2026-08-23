<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
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
            $companyId = $request->input('companyId');

            $query = BankAccount::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('bank_account_name', 'LIKE', "%{$search}%")
                      ->orWhere('account_number', 'LIKE', "%{$search}%")
                      ->orWhere('bank_name', 'LIKE', "%{$search}%");
                });
            }

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $accounts = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Bank accounts fetched successfully!',
                'errors' => null,
                'data' => [
                    'pageIndex' => $accounts->currentPage(),
                    'pageSize' => (int) $pageSize,
                    'count' => $accounts->total(),
                    'data' => $accounts->items(),
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
            $account = BankAccount::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Bank account fetched successfully!',
                'errors' => null,
                'data' => $account,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Bank account not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_account_name' => 'required|string|max:255',
            ]);

            $account = BankAccount::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Bank account created successfully!',
                'errors' => null,
                'data' => $account,
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
            $account = BankAccount::findOrFail($request->id);
            $account->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Bank account updated successfully!',
                'errors' => null,
                'data' => $account,
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
            $account = BankAccount::findOrFail($id);
            $account->delete();

            return response()->json([
                'status' => true,
                'message' => 'Bank account deleted successfully!',
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
            BankAccount::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Bank accounts deleted successfully!',
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
