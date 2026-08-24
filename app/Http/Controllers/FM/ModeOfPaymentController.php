<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\ModeOfPayment;
use Illuminate\Http\Request;

class ModeOfPaymentController extends Controller
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

            $query = ModeOfPayment::query();

            if ($search) {
                $query->where('mode_of_payment_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'ModeOfPayments fetched successfully!',
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
            $item = ModeOfPayment::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'ModeOfPayment fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ModeOfPayment not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mode_of_payment_name' => 'sometimes|required_without:modeOfPaymentName|string|max:255',
                'modeOfPaymentName' => 'sometimes|required_without:mode_of_payment_name|string|max:255',
                'mode_of_payment_type' => 'sometimes|nullable|string|max:255',
                'modeOfPaymentType' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|nullable|integer',
                'companyId' => 'sometimes|nullable|integer',
                'chart_of_account_id' => 'sometimes|nullable|integer',
                'chartOfAccountId' => 'sometimes|nullable|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $data = [
                'mode_of_payment_name' => $request->input('mode_of_payment_name') ?? $request->input('modeOfPaymentName'),
                'mode_of_payment_type' => $request->input('mode_of_payment_type') ?? $request->input('modeOfPaymentType'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'chart_of_account_id' => $request->input('chart_of_account_id') ?? $request->input('chartOfAccountId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item = ModeOfPayment::create($data);

            return response()->json([
                'status' => true,
                'message' => 'ModeOfPayment created successfully!',
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
                'mode_of_payment_name' => 'sometimes|required_without:modeOfPaymentName|string|max:255',
                'modeOfPaymentName' => 'sometimes|required_without:mode_of_payment_name|string|max:255',
                'mode_of_payment_type' => 'sometimes|nullable|string|max:255',
                'modeOfPaymentType' => 'sometimes|nullable|string|max:255',
                'company_id' => 'sometimes|nullable|integer',
                'companyId' => 'sometimes|nullable|integer',
                'chart_of_account_id' => 'sometimes|nullable|integer',
                'chartOfAccountId' => 'sometimes|nullable|integer',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean',
            ]);

            $item = ModeOfPayment::findOrFail($request->id);
            $data = [
                'mode_of_payment_name' => $request->input('mode_of_payment_name') ?? $request->input('modeOfPaymentName'),
                'mode_of_payment_type' => $request->input('mode_of_payment_type') ?? $request->input('modeOfPaymentType'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'chart_of_account_id' => $request->input('chart_of_account_id') ?? $request->input('chartOfAccountId'),
                'is_active' => $request->input('is_active') ?? $request->input('isActive'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'ModeOfPayment updated successfully!',
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
            $item = ModeOfPayment::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'ModeOfPayment deleted successfully!',
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
            ModeOfPayment::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'ModeOfPayments deleted successfully!',
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
