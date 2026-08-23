<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\FM\Company;
use App\Models\FM\Payment;
use Illuminate\Http\Request;

class FeatureReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAccountPayableReport']]);
    }

    /**
     * Get Accounts Payable Report
     *
     * Returns a paginated list of accounts payable records.
     */
    public function getAccountPayableReport(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $pageIndex = (int) $request->input('pageIndex', 1);
            $pageSize = (int) $request->input('pageSize', 10);
            $party = $request->input('party', '');
            $dueDate = $request->input('dueDate', '');
            $payableAccount = $request->input('payableAccount', '');

            $query = Payment::query()
                ->leftJoin('fm_companies', 'fm_payments.company_id', '=', 'fm_companies.id')
                ->where('fm_payments.party_type', '=', 'Supplier')
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
                    'fm_companies.currency_id',
                    'fm_companies.default_payable_account_id'
                );

            // Filter by party name
            if (!empty($party)) {
                $query->where('fm_payments.party_name', 'like', "%{$party}%");
            }

            // Filter by due date (reference_date used as due date)
            if (!empty($dueDate)) {
                $query->whereDate('fm_payments.reference_date', $dueDate);
            }

            // Filter by payable account
            if (!empty($payableAccount)) {
                $query->where(function ($q) use ($payableAccount) {
                    $q->where('fm_payments.account_paid_to_id', $payableAccount)
                        ->orWhere('fm_payments.account_paid_from_id', $payableAccount);
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
                ->skip(($pageIndex - 1) * $pageSize)
                ->take($pageSize)
                ->get();

            $data = $payments->map(function ($payment, $index) {
                $invoicedAmount = (float) $payment->payment_amount;
                $paidAmount = (float) $payment->total_allocation_amount;
                $outstandingAmount = (float) $payment->unallocated_amount;
                $advancedAmount = (float) $payment->different_amount;

                return [
                    'id' => $payment->id,
                    'sl' => $index + 1,
                    'party' => $payment->party_name ?? $payment->company_name ?? 'N/A',
                    'currency' => (string) ($payment->currency_id ?? $payment->from_currency_id ?? ''),
                    'payableAccount' => (string) ($payment->default_payable_account_id ?? $payment->account_paid_to_id ?? ''),
                    'invoiceNo' => $payment->payment_no ?? $payment->reference_number ?? '',
                    'invoiceDate' => $payment->posting_date ? date('Y-m-d', strtotime($payment->posting_date)) : '',
                    'dueDate' => $payment->reference_date ? date('Y-m-d', strtotime($payment->reference_date)) : '',
                    'advancedAmount' => $advancedAmount,
                    'invoicedAmount' => $invoicedAmount,
                    'paidAmount' => $paidAmount,
                    'outstandingAmount' => $outstandingAmount,
                    'totalAmountDue' => $invoicedAmount - $paidAmount,
                ];
            });

            return response()->json([
                'pageIndex' => $pageIndex,
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
