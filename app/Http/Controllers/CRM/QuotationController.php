<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationController extends ApiCrudController
{
    protected $dbTable = 'quotations';

    protected $resource = 'Quotation';

    protected $resourcePlural = 'Quotations';

    protected $fields = [
        'title',
        'quotationNo',
        'customerId',
        'leadId',
        'campaignId',
        'type',
        'status',
        'stage',
        'amount',
        'probability',
        'forecastedRevenue',
        'expiryDate',
        'paymentTerms',
        'deliveryStatus',
        'primaryContact',
        'nextStep',
        'description',
    ];

    /**
     * Date columns normalized before INSERT/UPDATE by ApiCrudController::normalizeDates().
     * Handles ISO 8601 like "2026-11-04T09:20:27.174Z" sent by the DatePicker.
     */
    protected $dateFields = [
        'expiryDate' => 'Y-m-d',
    ];

    protected $searchable = ['title', 'quotationNo', 'primaryContact', 'description'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'title'             => 'required|string|max:255',
        'quotationNo'       => 'nullable|string|max:100',
        'CustomerId'        => 'nullable|integer|exists:customers,id',  // was required
        'leadId'            => 'nullable|integer|exists:leads,id',
        'campaignId'        => 'nullable|integer|exists:campaigns,id',
        'type'              => 'required|string|max:100',
        'status'            => 'nullable|string|max:50',
        'stage'             => 'nullable|string|max:100',
        'amount'            => 'nullable|numeric|min:0',                // was required; computed in finalizeDetails
        'probability'       => 'nullable|integer|min:0|max:100',        // was required
        'forecastedRevenue' => 'nullable|numeric|min:0',
        'expiryDate'        => 'nullable|date',
        'paymentTerms'      => 'nullable|string|max:100',
        'deliveryStatus'    => 'nullable|string|max:100',
        'primaryContact'    => 'nullable|string|max:255',
        'nextStep'          => 'nullable|string|max:255',
        'description'       => 'nullable|string|max:5000',

        'details'              => 'nullable|array',
        'details.*.productId'  => 'required_with:details|integer|exists:products,id',
        'details.*.quantity'   => 'required_with:details|numeric|min:0.01',
        'details.*.unitPrice'  => 'nullable|numeric|min:0',
        'details.*.unit'       => 'nullable|string|max:50',
        'details.*.vat'        => 'nullable|numeric|min:0',
        'details.*.tax'        => 'nullable|numeric|min:0',
        'details.*.discount'   => 'nullable|numeric|min:0',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'QuotationId';

    protected $listMessage = 'Quotation List Fetched Successfully!';

    protected $indexAllMessage = 'All Quotations Fetched Successfully!';

    protected $legacyMessages = [
        'show'      => 'Quotation Details Fetched Successfully!',
        'store'     => 'New Quotation Created Successfully!',
        'update'    => 'Quotation Updated Successfully!',
        'destroy'   => 'Quotation Deleted Successfully!',
        'search'    => 'Quotation Search Results Fetched Successfully!',
        'not_found' => 'Quotation Not Found',
    ];

    /**
     * Enable base-class detail sync into quotation_details.
     */
    protected $syncDetails = true;

    protected $detailTable = 'quotation_details';

    protected $detailForeignKey = 'quotationId';

    protected function beforeStore(Request $request, array $data)
    {
        $data = parent::beforeStore($request, $data);

        if (empty($data['quotationNo'])) {
            $data['quotationNo'] = 'QTN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        }

        if (empty($data['status'])) {
            $data['status'] = 'Draft';
        }

        return $data;
    }

    /**
     * Recompute totalPrice per detail row and set the parent `amount` to the
     * sum of the details, before the base class persists them.
     */
    protected function afterStore(Request $request, $item)
    {
        parent::afterStore($request, $item);
        $this->finalizeDetails($request, $item);
    }

    protected function afterUpdate(Request $request, $item)
    {
        parent::afterUpdate($request, $item);
        $this->finalizeDetails($request, $item);
    }

    /**
     * Mutate the request 'details' array so the base syncDetails persists
     * computed totalPrice, and roll up the parent total into `amount`.
     */
    protected function finalizeDetails(Request $request, $item)
    {
        if (!$request->has('details') || !is_array($request->input('details'))) {
            return;
        }

        $details = $request->input('details');
        $grandTotal = 0.0;

        foreach ($details as $i => $detail) {
            $qty        = (float) ($detail['quantity']  ?? 0);
            $unitPrice  = (float) ($detail['unitPrice'] ?? 0);
            $vat        = (float) ($detail['vat']       ?? 0);
            $tax        = (float) ($detail['tax']       ?? 0);
            $discount   = (float) ($detail['discount']  ?? 0);

            $line       = $qty * $unitPrice;
            $lineTotal  = $line + $vat + $tax - $discount;

            $details[$i]['totalPrice'] = $lineTotal;
            $grandTotal += $lineTotal;
        }

        // Persist computed rows via the base syncDetails
        $request->merge(['details' => $details]);

        // Roll parent amount up from details
        if ($grandTotal > 0 && $this->isDbMode()) {
            DB::table($this->dbTable)
                ->where('id', $item->id ?? $item->getId())
                ->update(['amount' => $grandTotal, 'updated_at' => now()]);
        }
    }

    /**
     * Provide `details` alongside the parent row on `show`.
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
            }

            return $this->respondShow($item);
        } catch (\Throwable $e) {
            return ResponseRepository::ResponseError(null, $e->getMessage(), 500);
        }
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'title',
                'quotationNo',
                'CustomerId',
                'leadId',
                'campaignId',
                'type',
                'status',
                'stage',
                'amount',
                'probability',
                'forecastedRevenue',
                'expiryDate',
                'paymentTerms',
                'deliveryStatus',
                'primaryContact',
                'nextStep',
                'description',
                'created_at',
                'updated_at'
            );
        }
    }
}
