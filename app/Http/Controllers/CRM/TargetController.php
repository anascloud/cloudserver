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
        'type',
        'target_value',
        'quarter',
        'month',
        'status',
    ];

    protected $dateFields = [
        'month'    => 'Y-m-d',
    ];

    protected $searchable = ['title', 'subject', 'type'];

    protected $searchOnList = false;

    protected $rules = [
        'title'           => 'nullable|string|max:255',
        'type'            => 'nullable|string|max:100',
        'target_value'     => 'nullable|numeric',
        'quarter'         => 'nullable|string|max:20',
        'month'           => 'nullable|date',
        'status'          => 'nullable|string|in:Active,Inactive',
    ];

    protected $legacyStoreIdKey = 'targetId';

    protected $listMessage = 'Target List Fetched Successfully!';

    protected $indexAllMessage = 'All Targets Fetched Successfully!';

    protected $legacyMessages = [
        'show'      => 'Target Details Fetched Successfully!',
        'store'     => 'New Target Created Successfully!',
        'update'    => 'Target Updated Successfully!',
        'destroy'   => 'Target Deleted Successfully!',
        'bulk'      => 'Targets Deleted Successfully!',
        'search'    => 'Target Search Results Fetched Successfully!',
        'not_found' => 'Target Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['title'])) {
            $data['title'] = $data['subject'];
        }

        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        return $data;
    }

    protected function beforeUpdate(Request $request, array $data, $id)
    {
        // Mirror the store defaults so partial updates stay consistent.
        if (empty($data['title']) && !empty($data['subject'])) {
            $data['title'] = $data['subject'];
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('target_value')) {
            $query->where('target_value', $request->input('target_value'));
        }

        if ($request->filled('quarter')) {
            $query->where('quarter', $request->input('quarter'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('approvalStatus')) {
            $query->where('approvalStatus', $request->input('approvalStatus'));
        }

        return $query; // <-- original was missing this
    }
}
