<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class LeadController extends ApiCrudController
{
    protected $dbTable = 'leads';

    protected $resource = 'Lead';

    protected $resourcePlural = 'Leads';

    protected $fields = [
        'fullName',
        'email',
        'phone',
        'address',
        'company',
        'assignedTo',
        'region',
        'feedback',
        'industry',
        'title',
        'leadStatus',
        'description',
    ];

    protected $searchable = ['fullName', 'email', 'company', 'title'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'fullName' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'company' => 'required|string|max:255',
        'assignedTo' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:100',
        'feedback' => 'nullable|string|max:500',
        'industry' => 'nullable|string|max:100',
        'title' => 'nullable|string|max:100',
        'leadStatus' => 'required|string|max:50',
        'description' => 'nullable|string|max:1000',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'leadId';

    protected $listMessage = 'Lead List Fetched Successfully!';

    protected $indexAllMessage = 'All Leads Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Lead Details Fetched Successfully!',
        'store' => 'New Lead Created Successfully!',
        'update' => 'Lead Updated Successfully!',
        'destroy' => 'Lead Deleted Successfully!',
        'search' => 'Lead Search Results Fetched Successfully!',
        'not_found' => 'Lead Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'fullName',
                'email',
                'phone',
                'address',
                'company',
                'assignedTo',
                'region',
                'feedback',
                'industry',
                'title',
                'leadStatus',
                'description',
                'created_at',
                'updated_at'
            );
        }
    }
}
