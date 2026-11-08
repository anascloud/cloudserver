<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class CustomerController extends ApiCrudController
{
    protected $dbTable = 'customer';

    protected $resource = 'Customer';

    protected $resourcePlural = 'Customers';

    /**
     * Every persisted column, in camelCase — matches the React form payload.
     * Both `customer_form` and the DB are aligned on these names.
     */
    protected $fields = [
        'firstName',
        'lastName',
        'email',
        'phone',
        'company',
        'street',
        'house',
        'zip',
        'city',
        'state',
        'country',
        'accountNo',
        'bankName',
        'currencyId',
        'paymentTerms',
        'chartOfAccountId',
        'residencePermitNo',
        'photo',
        'coverPhoto',
        'leadId',
    ];

    /**
     * Columns that must be coerced to a date on write.
     * (Kept as array of column => format — same shape as OpportunityController.)
     */
    protected $dateFields = [
        'closingDate' => 'Y-m-d', // retained for parity — customer has none, see note below
    ];

    protected $searchable = ['firstName', 'lastName', 'email', 'phone', 'company'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'firstName'          => 'required|string|max:255',
        'lastName'           => 'nullable|string|max:255',
        'email'              => 'required|email|max:255',
        'phone'              => 'required|string|max:50',
        'company'            => 'nullable|string|max:255',
        'street'             => 'nullable|string|max:255',
        'house'              => 'nullable|string|max:255',
        'zip'                => 'nullable|string|max:20',
        'city'               => 'nullable|string|max:255',
        'state'              => 'nullable|string|max:255',
        'country'            => 'nullable|integer|exists:countries,id',
        'accountNo'          => 'nullable|string|max:100',
        'bankName'           => 'nullable|string|max:255',
        'currencyId'         => 'nullable|integer|exists:currencies,id',
        'paymentTerms'       => 'nullable|integer',
        'chartOfAccountId'   => 'nullable|integer|exists:chart_of_accounts,id',
        'residencePermitNo'  => 'nullable|string|max:100',
        'photo'              => 'nullable',   // file OR existing path string
        'coverPhoto'         => 'nullable',   // file OR existing path string
        'leadId'             => 'nullable|integer|exists:leads,id',
    ];

    protected $indexFormat     = 'paginated_links';
    protected $indexAllFormat  = 'items';
    protected $searchFormat    = 'items';
    protected $writeResponse   = 'legacy';
    protected $showResponse    = 'legacy';
    protected $legacyStoreIdKey = 'customerId';

    protected $listMessage     = 'Customer List Fetched Successfully!';
    protected $indexAllMessage = 'All Customers Fetched Successfully!';

    protected $legacyMessages = [
        'show'      => 'Customer Details Fetched Successfully!',
        'store'     => 'New Customer Created Successfully!',
        'update'    => 'Customer Updated Successfully!',
        'destroy'   => 'Customer Deleted Successfully!',
        'search'    => 'Customer Search Results Fetched Successfully!',
        'not_found' => 'Customer Not Found',
    ];

    /**
     * Same contract as OpportunityController — restrict columns returned by index.
     */
    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'firstName',
                'lastName',
                'email',
                'phone',
                'company',
                'street',
                'house',
                'zip',
                'city',
                'state',
                'country',
                'accountNo',
                'bankName',
                'currencyId',
                'paymentTerms',
                'chartOfAccountId',
                'residencePermitNo',
                'photo',
                'coverPhoto',
                'leadId',
                'status',
                'created_at',
                'updated_at'
            );
        }
    }
}
