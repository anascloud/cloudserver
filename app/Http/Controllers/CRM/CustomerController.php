<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\FM\Payment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'indexAll']]);
    }

    public function index(Request $request)
    {
        return $this->indexAll($request);
    }

    public function indexAll(Request $request)
    {
        try {
            $pageSize = (int) $request->input('pageSize', $request->input('per_page', 100));
            $search = $request->input('search', '');

            // Derive customer options from the distinct parties recorded in fm_payments
            // (there is no dedicated customers table in this backend yet).
            $query = Payment::query()
                ->select('fm_payments.party_id', 'fm_payments.party_name')
                ->where('fm_payments.party_type', '=', 'Customer')
                ->whereNotNull('fm_payments.party_name')
                ->where('fm_payments.party_name', '!=', '')
                ->groupBy('fm_payments.party_id', 'fm_payments.party_name');

            if ($search) {
                $query->where('fm_payments.party_name', 'LIKE', "%{$search}%");
            }

            $customers = $query->orderBy('fm_payments.party_name')
                ->get()
                ->values()
                ->map(function ($customer) {
                    return [
                        // Use party_id when available, otherwise fall back to the
                        // party name so the dropdown value still matches the report filter.
                        'id' => $customer->party_id ?? $customer->party_name,
                        'firstName' => $customer->party_name,
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'Customers fetched successfully!',
                'errors' => null,
                'data' => [
                    'pageIndex' => 1,
                    'pageSize' => $pageSize,
                    'count' => $customers->count(),
                    'data' => $customers,
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
}
