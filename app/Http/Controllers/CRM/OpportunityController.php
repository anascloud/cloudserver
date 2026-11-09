<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class OpportunityController extends ApiCrudController
{
    protected $dbTable = 'opportunities';

    protected $resource = 'Opportunity';

    protected $resourcePlural = 'Opportunities';

    protected $fields = [
        'dealOwner',
        'name',
        'leadSource',
        'leadId',
        'campaignId',
        'customerId',
        'closingDate',
        'amount',
        'probability',
        'type',
        'primaryContact',
        'forecastedRevenue',
        'nextStep',
        'stage',
        'description',
    ];

    protected $dateFields = [
        'closingDate' => 'Y-m-d',
    ];

    protected $searchable = ['name', 'primaryContact', 'description'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'dealOwner' => 'required|integer|exists:users,id',
        'name' => 'required|string|max:255',
        'leadSource' => 'nullable|string|max:100',
        'leadId' => 'nullable|integer|exists:leads,id',
        'campaignId' => 'nullable|integer|exists:campaigns,id',
        'customerId' => 'nullable|integer|exists:customers,id',
        'closingDate' => 'nullable|date',
        'amount' => 'required|numeric|min:0',
        'probability' => 'required|integer|min:0|max:100',
        'type' => 'required|string|max:100',
        'primaryContact' => 'nullable|string|max:255',
        'forecastedRevenue' => 'nullable|numeric|min:0',
        'nextStep' => 'nullable|string|max:255',
        'stage' => 'nullable|string|max:100',
        'description' => 'nullable|string|max:5000',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'opportunityId';

    protected $listMessage = 'Opportunity List Fetched Successfully!';

    protected $indexAllMessage = 'All Opportunities Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Opportunity Details Fetched Successfully!',
        'store' => 'New Opportunity Created Successfully!',
        'update' => 'Opportunity Updated Successfully!',
        'destroy' => 'Opportunity Deleted Successfully!',
        'search' => 'Opportunity Search Results Fetched Successfully!',
        'not_found' => 'Opportunity Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'dealOwner',
                'name',
                'leadSource',
                'leadId',
                'campaignId',
                'customerId',
                'closingDate',
                'amount',
                'probability',
                'type',
                'primaryContact',
                'forecastedRevenue',
                'nextStep',
                'stage',
                'description',
                'created_at',
                'updated_at'
            );
        }
    }
}
