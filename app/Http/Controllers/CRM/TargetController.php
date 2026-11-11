<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class TargetController extends CrmCrudController
{
    protected $dbTable = 'targets';

    protected $resource = 'Target';

    protected $resourcePlural = 'Targets';

    protected $fields = [
        'title',
        'subject',
        'type',
        'targetValue',
        'quarter',
        'month',
        'company',
        'service',
        'createdBy',
        'primaryContact',
        'source',
        'approvalStatus',
        'status',
    ];

    protected $searchable = ['title', 'subject', 'type', 'company', 'service'];

    protected $searchOnList = false;

    protected $rules = [
        'title' => 'nullable|string|max:255',
        'subject' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:100',
        'targetValue' => 'nullable|numeric',
        'quarter' => 'nullable|string|max:20',
        'month' => 'nullable|string|max:20',
    ];

    protected $legacyStoreIdKey = 'targetId';

    protected $listMessage = 'Target List Fetched Successfully!';

    protected $indexAllMessage = 'All Targets Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Target Details Fetched Successfully!',
        'store' => 'New Target Created Successfully!',
        'update' => 'Target Updated Successfully!',
        'destroy' => 'Target Deleted Successfully!',
        'bulk' => 'Targets Deleted Successfully!',
        'search' => 'Target Search Results Fetched Successfully!',
        'not_found' => 'Target Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['title']) && !empty($data['subject'])) {
            $data['title'] = $data['subject'];
        }

        if (empty($data['approvalStatus'])) {
            $data['approvalStatus'] = 'Draft';
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('targetValue')) {
            $query->where('targetValue', $request->input('targetValue'));
        }
    }
}
