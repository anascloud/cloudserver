<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use App\Models\Salesorder;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesorderController extends ApiCrudController
{
    protected $model = Salesorder::class;

    protected $resource = 'Salesorder';

    protected $resourcePlural = 'Salesorders';

    /**
     * Date columns normalized before INSERT/UPDATE by ApiCrudController::normalizeDates().
     */
    protected $dateFields = [
        'delivaryDate' => 'Y-m-d',
        'posting_date' => 'Y-m-d',
    ];

    protected $searchable = ['title', 'salesOrderNo'];

    protected $searchOnList = false;

    protected $rules = [
        'title'             => 'required|string|max:255',
        'salesOrderNo'      => 'nullable|string|max:100',
        'customerId'        => 'nullable|integer|exists:customer,id',
        'quotationId'       => 'nullable|integer|exists:quotations,id',
        'currencyId'        => 'nullable|integer|exists:fm_currencies,id',
        'type'              => 'required|string|max:100',
        'stages'            => 'nullable|string|max:100',
        'updateStock'       => 'nullable|boolean',
        'company_id'        => 'nullable|integer',
        'delivaryDate'      => 'nullable|date',
        'posting_date'      => 'nullable|date',

        'shippingStreet'    => 'nullable|string|max:255',
        'shippingHouse'     => 'nullable|string|max:255',
        'shippingZip'       => 'nullable|string|max:20',
        'shippingState'     => 'nullable|string|max:255',
        'shippingCity'      => 'nullable|string|max:255',
        'shippingCountryId' => 'nullable|integer',
        'billingStreet'     => 'nullable|string|max:255',
        'billingHouse'      => 'nullable|string|max:255',
        'billingZip'        => 'nullable|string|max:20',
        'billingState'      => 'nullable|string|max:255',
        'billingCity'       => 'nullable|string|max:255',
        'billingCountryId'  => 'nullable|integer',

        'discountType'      => 'nullable|string|max:50',
        'applyAmount'       => 'nullable|numeric|min:0',
        'discountAmount'    => 'nullable|numeric|min:0',
        'subtotal'          => 'nullable|numeric|min:0',
        'discount'          => 'nullable|numeric|min:0',
        'tax'               => 'nullable|numeric|min:0',
        'total'             => 'nullable|numeric|min:0',

        'details'              => 'nullable|array',
        'details.*.productId'  => 'required_with:details|integer|exists:products,id',
        'details.*.quantity'   => 'required_with:details|numeric|min:0.01',
        'details.*.unitPrice'  => 'nullable|numeric|min:0',
        'details.*.totalPrice' => 'nullable|numeric|min:0',

        'taxDetails'                    => 'nullable|array',
        'taxDetails.*.tax_typeId'       => 'nullable|string',
        'taxDetails.*.ChartOfAccountId' => 'nullable|integer',
        'taxDetails.*.tax_rate'         => 'nullable|numeric|min:0',
        'taxDetails.*.TaxAmount'        => 'nullable|numeric|min:0',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'SalesorderId';

    /**
     * Return validation failures with a non-2xx status so the SPA surfaces
     * them as errors instead of treating the response as a successful write.
     */
    protected $legacyValidationStatus = 422;

    protected $listMessage = 'Salesorder List Fetched Successfully!';

    protected $indexAllMessage = 'All Salesorders Fetched Successfully!';

    protected $legacyMessages = [
        'show'      => 'Salesorder Details Fetched Successfully!',
        'store'     => 'New Salesorder Created Successfully!',
        'update'    => 'Salesorder Updated Successfully!',
        'destroy'   => 'Salesorder Deleted Successfully!',
        'search'    => 'Salesorder Search Results Fetched Successfully!',
        'not_found' => 'Salesorder Not Found',
    ];

    /**
     * Enable base-class detail sync into sales_order_details.
     */
    protected $syncDetails = true;

    protected $detailTable = 'sales_order_details';

    protected $detailForeignKey = 'salesOrderId';

    /**
     * Separate child table for VAT/tax breakdown (base class only syncs one).
     */
    protected $taxDetailTable = 'sales_order_vat_tax_details';

    /**
     * Accept the string booleans "true"/"false" (multipart forms) in addition
     * to the values the `boolean` rule natively understands.
     */
    protected function validateRequest(Request $request, $action)
    {
        if ($request->has('updateStock')) {
            $raw = $request->input('updateStock');

            if (is_string($raw) && in_array(strtolower($raw), ['true', 'false'], true)) {
                $request->merge(['updateStock' => strtolower($raw) === 'true']);
            }
        }

        parent::validateRequest($request, $action);
    }

    /**
     * Keep the legacy response body shape but do not rewrite a 422 into a 500.
     */
    protected function respondWriteError($message, $status = 422, $errors = null, $mode = null)
    {
        if (($mode ?: $this->writeResponse) === 'legacy') {
            return response()->json(['message' => $message, 'type' => 'error'], $status);
        }

        return parent::respondWriteError($message, $status, $errors, $mode);
    }

    protected function beforeStore(Request $request, array $data)
    {
        $data = parent::beforeStore($request, $data);

        if (empty($data['salesOrderNo'])) {
            $data['salesOrderNo'] = 'SO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        }

        if (empty($data['stages'])) {
            $data['stages'] = 'Draft';
        }

        if (array_key_exists('updateStock', $data)) {
            $data['updateStock'] = filter_var($data['updateStock'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        return $data;
    }

    protected function beforeUpdate(Request $request, array $data, $item)
    {
        $data = parent::beforeUpdate($request, $data, $item);

        if (array_key_exists('updateStock', $data)) {
            $data['updateStock'] = filter_var($data['updateStock'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        return $data;
    }

    protected function afterStore(Request $request, $item)
    {
        parent::afterStore($request, $item);
        $this->finalizeDetails($request, $item);
        $this->syncTaxDetails($request, $item);
    }

    protected function afterUpdate(Request $request, $item)
    {
        parent::afterUpdate($request, $item);
        $this->finalizeDetails($request, $item);
        $this->syncTaxDetails($request, $item);
    }

    /**
     * Recompute totalPrice per detail row and roll up parent totals
     * (subtotal / discount / tax / total) from the request.
     */
    protected function finalizeDetails(Request $request, $item)
    {
        if (!$request->has('details') || !is_array($request->input('details'))) {
            return;
        }

        $details = $request->input('details');
        $grandTotal = 0.0;

        foreach ($details as $i => $detail) {
            $qty       = (float) ($detail['quantity']  ?? 0);
            $unitPrice = (float) ($detail['unitPrice'] ?? 0);

            $lineTotal = $qty * $unitPrice;

            $details[$i]['totalPrice'] = $lineTotal;
            $grandTotal += $lineTotal;
        }

        // Persist the computed totalPrice on the child rows the base class inserted.
        DB::table($this->detailTable)
            ->where($this->detailForeignKey, $item->getKey())
            ->delete();

        $now = now();
        $rows = [];
        foreach ($details as $row) {
            $rows[] = [
                $this->detailForeignKey => $item->getKey(),
                'productId'  => $row['productId']  ?? null,
                'quantity'   => $row['quantity']   ?? 0,
                'unitPrice'  => $row['unitPrice']  ?? 0,
                'totalPrice' => $row['totalPrice'] ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($rows) {
            DB::table($this->detailTable)->insert($rows);
        }

        // Parent totals — client summary is authoritative; fall back to sum.
        $update = [
            'subtotal'       => (float) $request->input('subtotal', $grandTotal),
            'discountAmount' => (float) $request->input('discountAmount', $request->input('discount', 0)),
            'discount'       => (float) $request->input('discount', 0),
            'tax'            => (float) $request->input('tax', 0),
            'total'          => (float) $request->input('total', $grandTotal),
            'updated_at'     => now(),
        ];

        DB::table($item->getTable())
            ->where('id', $item->getKey())
            ->update($update);

        foreach ($update as $k => $v) {
            if ($k !== 'updated_at') {
                $item->{$k} = $v;
            }
        }
    }

    /**
     * Sync sales_order_vat_tax_details from the client `taxDetails` array.
     */
    protected function syncTaxDetails(Request $request, $item)
    {
        if (!$request->has('taxDetails') || !is_array($request->input('taxDetails'))) {
            return;
        }

        DB::table($this->taxDetailTable)
            ->where('salesOrderId', $item->getKey())
            ->delete();

        $taxDetails = $request->input('taxDetails');
        if (empty($taxDetails)) {
            return;
        }

        $now = now();
        $rows = [];
        foreach ($taxDetails as $row) {
            if (!is_array($row)) {
                continue;
            }
            $rows[] = [
                'salesOrderId'     => $item->getKey(),
                'tax_typeId'       => $row['tax_typeId']       ?? null,
                'chartOfAccountId' => $row['chartOfAccountId'] ?? null,
                'tax_rate'         => (float) ($row['tax_rate'] ?? 0),
                'taxAmount'        => (float) ($row['taxAmount'] ?? 0),
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        if ($rows) {
            DB::table($this->taxDetailTable)->insert($rows);
        }
    }

    /**
     * Provide `details` and `taxDetails` alongside the parent row on `show`.
     */
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

                $item->taxDetails = DB::table($this->taxDetailTable)
                    ->where('salesOrderId', $id)
                    ->orderBy('id')
                    ->get();
            }

            return $this->respondShow($item);
        } catch (\Throwable $e) {
            return ResponseRepository::ResponseError(null, $e->getMessage(), 500);
        }
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->with('quotation:id,quotationNo')->select(
                'id',
                'type',
                'title',
                'customerId',
                'quotationId',
                'salesOrderNo',
                'currencyId',
                'delivaryDate',
                'updateStock',
                'company_id',
                'stages',
                'posting_date',
                'shippingStreet',
                'shippingHouse',
                'shippingZip',
                'shippingState',
                'shippingCity',
                'shippingCountryId',
                'billingStreet',
                'billingHouse',
                'billingZip',
                'billingState',
                'billingCity',
                'billingCountryId',
                'discountType',
                'applyAmount',
                'discountAmount',
                'subtotal',
                'discount',
                'tax',
                'total',
                'created_at',
                'updated_at'
            );
        }
    }
}
