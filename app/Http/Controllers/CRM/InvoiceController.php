<?php

namespace App\Http\Controllers\CRM;

use App\Repositories\ResponseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends CrmCrudController
{
    protected $dbTable = 'invoices';

    protected $resource = 'Invoice';

    protected $resourcePlural = 'Invoices';

    protected $fields = [
        'title',
        'salesOrderid',
        'customerid',
        'quotationNo',
        'invoice_no',
        'type',
        'invoiceDate',
        'dueDate',
        'paymentTerms',
        'paymentMethod',
        'currencyId',
        'discountAmount',
        'subtotal',
        'discount',
        'tax',
        'total',
        'description',
        'customersPO',
        'customerPODate',
        'status',
        'stages',
        'file',

        // extra fields the JS form actually sends
        'customerCurrencyId',
        'companyCurrencyId',
        'company_id',
        'exchangeRate',
        'updateStock',
        'shippingStreet',
        'shippingHouse',
        'shippingZip',
        'shippingState',
        'shippingCity',
        'shippingCountryid',
        'shippingContactPerson',
        'billingStreet',
        'billingHouse',
        'billingZip',
        'billingState',
        'billingCity',
        'billingCountryid',
        'billingContactPerson',
        'companyStreet',
        'companyHouse',
        'companyZip',
        'companyState',
        'companyCity',
        'companyCountryid',
        'companyContactPerson',
        'discountType',
        'applyDiscount',
        'totalQuantity',
        'total_amount',
        'total_amountOnCustomerCurrency',
        'total_amountOnCompanyCurrency',
        'grandTotal',
        'totalAdvanceAmount',
        'totalAdvanceQuantity',
        'isOpeningEntry',
        'discountPercentage',
        'ignorePricingRule',
        'isCashOrNonTradeDiscount',
        'isRateAdjustmentEntry',
        'isReturn',
        'netPayable',
        'totalAdvance',
        'totalTaxes',
        'outstandingAmount',
        'bankAccountid',
        'cost_centerid',
        'debitToid',
        'priceList',
        'taxCategoryid',
        'taxTemplateid',
        'warehouseid',
        'grandTotalOnCustomerCurrency',
        'grandTotalOnCompanyCurrency',
    ];

    protected $fileFields = [
        'file' => 'uploads/invoices',
    ];

    protected $dateFields = [
        'invoiceDate'    => 'Y-m-d',
        'dueDate'        => 'Y-m-d',
        'customerPODate' => 'Y-m-d',
    ];

    protected $searchable = ['title', 'invoice_no', 'customerid', 'status', 'description'];

    protected $searchOnList = false;

    protected $rules = [
        'invoice_no'       => 'nullable|string|max:100',
        'type'             => 'nullable|string|max:100',
        'total'            => 'nullable|numeric',
        'customerid'       => 'nullable|integer|exists:customers,id',
        'invoiceProductDetailsDTOs'   => 'sometimes|array',
        'invoiceProductDetailsDTOs.*.productId' => 'nullable|integer',
        'invoiceProductDetailsDTOs.*.quantity'  => 'nullable|numeric',
        'invoiceProductDetailsDTOs.*.unitPrice' => 'nullable|numeric',
        'invoiceProductDetailsDTOs.*.totalPrice'=> 'nullable|numeric',
        'invoiceVatTaxDetailsDTOs'    => 'sometimes|array',
        'invoiceVatTaxDetailsDTOs.*.chartOfAccountId' => 'nullable|integer',
        'invoiceVatTaxDetailsDTOs.*.tax_rate'         => 'nullable|numeric',
        'invoiceVatTaxDetailsDTOs.*.TaxAmount'        => 'nullable|numeric',
        'invoiceVatTaxDetailsDTOs.*.tax_typeId'       => 'nullable|integer',
    ];

    protected $legacyStoreIdKey = 'invoiceId';

    protected $listMessage = 'Invoice List Fetched Successfully!';
    protected $indexAllMessage = 'All Invoices Fetched Successfully!';

    protected $legacyMessages = [
        'show'      => 'Invoice Details Fetched Successfully!',
        'store'     => 'New Invoice Created Successfully!',
        'update'    => 'Invoice Updated Successfully!',
        'destroy'   => 'Invoice Deleted Successfully!',
        'bulk'      => 'Invoices Deleted Successfully!',
        'search'    => 'Invoice Search Results Fetched Successfully!',
        'not_found' => 'Invoice Not Found',
    ];

    protected $detailTable      = 'invoice_details';
    protected $detailForeignKey = 'invoiceId';
    protected $taxDetailTable   = 'invoice_vat_tax_details';

    /* -----------------------------------------------------------------
     |  Hooks
     * ----------------------------------------------------------------- */

    protected function beforeStore(Request $request, array $data): array
    {
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        }

        if (empty($data['status'])) {
            $data['status'] = 'Draft';
        }

        return $data;
    }

    /**
     * Persist child rows for a freshly created invoice.
     * Wrapped by the parent in a transaction — see note below.
     */
    protected function afterStore(Request $request, $item): void
    {
        $this->syncInvoiceDetails($request, $item);
        $this->syncTaxDetails($request, $item);
    }

    protected function afterUpdate(Request $request, $item): void
    {
        $this->syncInvoiceDetails($request, $item);
        $this->syncTaxDetails($request, $item);
    }

    /* -----------------------------------------------------------------
     |  Detail sync
     * ----------------------------------------------------------------- */

    /**
     * Resolve the product detail payload regardless of which key the
     * caller used.
     */
    protected function invoiceDetails(Request $request): ?array
    {
        foreach (['invoiceProductDetailsDTOs', 'invoiceDetails', 'details'] as $key) {
            $details = $request->input($key);
            if (is_array($details)) {
                return $details;
            }
        }
        return null;
    }

    protected function syncInvoiceDetails(Request $request, $item): void
    {
        $details = $this->invoiceDetails($request);

        // null = payload didn't include details → leave existing rows alone
        if ($details === null) {
            return;
        }

        $invoiceId = $this->itemKey($item);

        DB::table($this->detailTable)
            ->where($this->detailForeignKey, $invoiceId)
            ->delete();

        if (empty($details)) {
            return;
        }

        $now  = now();
        $rows = [];

        foreach ($details as $detail) {
            if (!is_array($detail)) {
                continue;
            }

            $quantity  = (float) ($detail['quantity'] ?? 0);
            $unitPrice = (float) ($detail['unitPrice'] ?? 0);

            $rows[] = [
                $this->detailForeignKey => $invoiceId,
                // JS sends camelCase `productId` → prefer it, fall back to legacy
                'productId'  => $detail['productId'] ?? ($detail['productid'] ?? null),
                'quantity'   => $quantity,
                'unit'       => $detail['unit'] ?? null,
                'unitPrice'  => $unitPrice,
                'vat'        => (float) ($detail['vat'] ?? 0),
                'tax'        => (float) ($detail['tax'] ?? 0),
                'discount'   => (float) ($detail['discount'] ?? 0),
                'totalPrice' => (float) ($detail['totalPrice'] ?? round($quantity * $unitPrice, 4)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table($this->detailTable)->insert($rows);
        }
    }

    protected function syncTaxDetails(Request $request, $item): void
    {
        $taxDetails = $request->input('invoiceVatTaxDetailsDTOs', $request->input('taxDetails'));

        if (!is_array($taxDetails)) {
            return;
        }

        $invoiceId = $this->itemKey($item);

        DB::table($this->taxDetailTable)
            ->where('invoiceId', $invoiceId)
            ->delete();

        if (empty($taxDetails)) {
            return;
        }

        $now  = now();
        $rows = [];

        foreach ($taxDetails as $detail) {
            if (!is_array($detail)) {
                continue;
            }

            // JS sends `TaxAmount` (capital T). Accept both casings.
            $taxAmount = $detail['taxAmount'] ?? $detail['TaxAmount'] ?? 0;

            $rows[] = [
                'invoiceId'        => $invoiceId,
                'chartOfAccountId' => $detail['chartOfAccountId'] ?? null,
                'tax_rate'         => (float) ($detail['tax_rate'] ?? 0),
                'taxAmount'        => (float) $taxAmount,
                'tax_typeId'       => $detail['tax_typeId'] ?? null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table($this->taxDetailTable)->insert($rows);
        }
    }

    /* -----------------------------------------------------------------
     |  Read / status endpoints
     * ----------------------------------------------------------------- */

    public function show($id)
    {
        $this->listMode = null;

        try {
            $item = $this->findItem($id);

            if ($item === null) {
                return $this->notFound();
            }

            if (is_object($item)) {
                $item->details = DB::table($this->detailTable)
                    ->where($this->detailForeignKey, $id)
                    ->orderBy('id')
                    ->get();

                $item->invoiceProductDetailsDTOs = $item->details;

                $item->taxDetails = DB::table($this->taxDetailTable)
                    ->where('invoiceId', $id)
                    ->orderBy('id')
                    ->get();

                $item->invoiceVatTaxDetailsDTOs = $item->taxDetails;
            }

            return $this->respondShow($item);
        } catch (\Throwable $e) {
            return ResponseRepository::ResponseError(null, $e->getMessage(), 500);
        }
    }

    public function updateStatus(Request $request, $id = null)
    {
        $id = $id ?: $request->input('id');

        $item = $id
            ? DB::table($this->dbTable)->where('id', $id)->first()
            : null;

        if ($item === null) {
            return $this->notFoundForWrite();
        }

        // Normalise: accept {status:"..."} JSON or a raw quoted string body
        $status = $request->input('status');
        if ($status === null) {
            $status = trim((string) $request->getContent(), " \t\n\r\0\x0B\"'");
        }

        $allowed = ['Draft', 'Open', 'Paid', 'Partially Paid', 'Overdue', 'Cancelled', 'Returned'];
        if (!in_array($status, $allowed, true)) {
            return ResponseRepository::ResponseError(
                null,
                "Invalid status. Allowed: " . implode(', ', $allowed),
                422
            );
        }

        DB::table($this->dbTable)->where('id', $id)->update([
            'status'     => $status,
            'updated_at' => now(),
        ]);

        return $this->respondWrite(
            ['id' => $id, 'status' => $status],
            'Invoice Status Updated Successfully!'
        );
    }

    /* -----------------------------------------------------------------
     |  Optional: safe bulk-clear if the parent doesn't override it
     * ----------------------------------------------------------------- */

    public function destroy($id)
    {
        $item = $this->findItem($id);
        if ($item === null) {
            return $this->notFoundForWrite();
        }

        DB::transaction(function () use ($id) {
            DB::table($this->detailTable)->where($this->detailForeignKey, $id)->delete();
            DB::table($this->taxDetailTable)->where('invoiceId', $id)->delete();
            DB::table($this->dbTable)->where('id', $id)->delete();
        });

        return $this->respondWrite(['id' => $id], $this->legacyMessages['destroy']);
    }
}
