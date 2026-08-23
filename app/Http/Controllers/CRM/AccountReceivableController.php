<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\FM\Company;
use App\Models\FM\Payment;
use Illuminate\Http\Request;

class AccountReceivableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Get Accounts Receivable Report
     *
     * Returns a paginated list of accounts receivable records derived
     * from outstanding customer payments recorded in fm_payments.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $page = (int) $request->input('page', $request->input('pageIndex', 1));
            $pageSize = (int) $request->input('pageSize', 10);
            $invoiceDateFrom = $request->input('invoiceDateFrom', '');
            $deliveryDateFrom = $request->input('deliveryDateFrom', '');
            $customerId = $request->input('customerId', '');
            $courier = $request->input('courier', '');
            $status = $request->input('status', '');

            $query = Payment::query()
                ->leftJoin('fm_companies', 'fm_payments.company_id', '=', 'fm_companies.id')
                ->select(
                    'fm_payments.id',
                    'fm_payments.payment_no',
                    'fm_payments.serial_number',
                    'fm_payments.posting_date',
                    'fm_payments.transaction_type',
                    'fm_payments.party_name',
                    'fm_payments.party_type',
                    'fm_payments.party_id',
                    'fm_payments.payment_amount',
                    'fm_payments.total_allocation_amount',
                    'fm_payments.unallocated_amount',
                    'fm_payments.different_amount',
                    'fm_payments.total_tax',
                    'fm_payments.reference_number',
                    'fm_payments.reference_date',
                    'fm_payments.payment_status',
                    'fm_payments.from_currency_id',
                    'fm_payments.to_currency_id',
                    'fm_payments.account_paid_from_id',
                    'fm_payments.account_paid_to_id',
                    'fm_companies.company_name',
                    'fm_companies.currency_id'
                );

            // Restrict to customer transactions for accounts receivable
            $query->where(function ($q) {
                $q->where('fm_payments.party_type', '=', 'Customer')
                    ->orWhereNull('fm_payments.party_type');
            });

            // Filter by customer (party_id or party name)
            if (!empty($customerId)) {
                $query->where(function ($q) use ($customerId) {
                    $q->where('fm_payments.party_id', $customerId)
                        ->orWhere('fm_payments.party_name', 'like', "%{$customerId}%");
                });
            }

            // Filter by invoice date (posting_date)
            if (!empty($invoiceDateFrom)) {
                $query->whereDate('fm_payments.posting_date', '>=', $invoiceDateFrom);
            }

            // Filter by delivery date (reference_date)
            if (!empty($deliveryDateFrom)) {
                $query->whereDate('fm_payments.reference_date', '>=', $deliveryDateFrom);
            }

            // Filter by status
            if (!empty($status)) {
                $query->where('fm_payments.payment_status', $status);
            }

            // Filter by courier / reference number
            if (!empty($courier)) {
                $query->where(function ($q) use ($courier) {
                    $q->where('fm_payments.reference_number', 'like', "%{$courier}%")
                        ->orWhere('fm_payments.serial_number', 'like', "%{$courier}%");
                });
            }

            // Global search
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('fm_payments.payment_no', 'like', "%{$search}%")
                        ->orWhere('fm_payments.party_name', 'like', "%{$search}%")
                        ->orWhere('fm_payments.reference_number', 'like', "%{$search}%")
                        ->orWhere('fm_companies.company_name', 'like', "%{$search}%");
                });
            }

            $count = $query->count();

            $payments = $query
                ->orderBy('fm_payments.posting_date', 'DESC')
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get();

            $data = $payments->map(function ($payment) {
                $invoicedAmount = (float) $payment->payment_amount;
                $paidAmount = (float) $payment->total_allocation_amount;
                $outstandingAmount = (float) $payment->unallocated_amount;
                $advancedAmount = (float) $payment->different_amount;

                return [
                    'id' => $payment->id,
                    'customer' => $payment->party_name ?? $payment->company_name ?? 'N/A',
                    'invoiceNo' => $payment->payment_no ?? $payment->reference_number ?? '',
                    'invoiceDate' => $payment->posting_date ? date('Y-m-d', strtotime($payment->posting_date)) : '',
                    'advancedAmount' => $advancedAmount,
                    'invoicedAmount' => $invoicedAmount,
                    'outstandingAmount' => $outstandingAmount,
                    'totalAmountDue' => $invoicedAmount - $paidAmount,
                ];
            });

            return response()->json([
                'pageIndex' => $page,
                'pageSize' => $pageSize,
                'count' => $count,
                'data' => $data,
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
