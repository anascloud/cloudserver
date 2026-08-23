<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
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

            $query = Payment::with('company');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_no', 'LIKE', "%{$search}%")
                      ->orWhere('party_name', 'LIKE', "%{$search}%")
                      ->orWhere('reference_number', 'LIKE', "%{$search}%");
                });
            }

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $payments = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Payments fetched successfully!',
                'errors' => null,
                'data' => [
                    'pageIndex' => $payments->currentPage(),
                    'pageSize' => (int) $pageSize,
                    'count' => $payments->total(),
                    'data' => $payments->items(),
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
            $payment = Payment::with('company')->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Payment fetched successfully!',
                'errors' => null,
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'posting_date' => 'required|date',
                'payment_amount' => 'required|numeric',
            ]);

            $lastPayment = Payment::orderBy('id', 'DESC')->first();
            $nextNumber = $lastPayment ? intval(substr($lastPayment->payment_no, 4)) + 1 : 1;
            $request->merge(['payment_no' => 'PAY-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT)]);

            $payment = Payment::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Payment created successfully!',
                'errors' => null,
                'data' => $payment,
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
            $payment = Payment::findOrFail($request->id);
            $payment->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Payment updated successfully!',
                'errors' => null,
                'data' => $payment,
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
            $payment = Payment::findOrFail($id);
            $payment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Payment deleted successfully!',
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
            Payment::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Payments deleted successfully!',
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
