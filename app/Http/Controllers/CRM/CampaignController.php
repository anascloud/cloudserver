<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class CampaignController extends ApiCrudController
{
    protected $dbTable = 'campaigns';

    protected $resource = 'Campaign';

    protected $resourcePlural = 'Campaigns';

    protected $fields = [
        'subject',
        'deadline',
        'company',
        'service',
        'description',
        'contact',
        'source',
        'type',
        'status',
    ];

    protected $searchable = ['subject', 'company', 'service'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'deadline' => 'required|date',
        'company' => 'required|string|max:255',
        'service' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'contact' => 'nullable|string|max:255',
        'source' => 'nullable|string|max:255',
        'type' => 'required|string|max:100',
        'status' => 'required|string|max:100',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'campaignId';

    protected $listMessage = 'Campaign List Fetched Successfully!';

    protected $indexAllMessage = 'All Campaigns Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Campaign Details Fetched Successfully!',
        'store' => 'New Campaign Created Successfully!',
        'update' => 'Campaign Updated Successfully!',
        'destroy' => 'Campaign Deleted Successfully!',
        'search' => 'Campaign Search Results Fetched Successfully!',
        'not_found' => 'Campaign Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'subject',
                'deadline',
                'company',
                'service',
                'description',
                'contact',
                'source',
                'type',
                'status',
                'created_at',
                'updated_at'
            );
        }
    }
}
