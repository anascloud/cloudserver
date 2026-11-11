<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class SlaController extends CrmCrudController
{
    protected $dbTable = 'slas';

    protected $resource = 'SLA';

    protected $resourcePlural = 'SLAs';

    protected $fields = [
        'name',
        'responseTime',
        'resolutionTime',
        'status',
    ];

    protected $searchable = ['name', 'status'];

    protected $searchOnList = false;

    protected $rules = [
        'name' => 'nullable|string|max:255',
    ];

    protected $legacyStoreIdKey = 'slaId';

    protected $listMessage = 'SLA List Fetched Successfully!';

    protected $indexAllMessage = 'All SLAs Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'SLA Details Fetched Successfully!',
        'store' => 'New SLA Created Successfully!',
        'update' => 'SLA Updated Successfully!',
        'destroy' => 'SLA Deleted Successfully!',
        'search' => 'SLA Search Results Fetched Successfully!',
        'not_found' => 'SLA Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }
    }
}
