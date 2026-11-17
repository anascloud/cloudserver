<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class MeetingController extends CrmCrudController
{
    protected $dbTable = 'meetings';

    protected $resource = 'Meeting';

    protected $resourcePlural = 'Meetings';

    protected $fields = [
        'title',
        'location',
        'meetingTime',
        'repeatTime',
        'relatedTo',
        'description',
        'participateType',
        'participantIds',
        'campaignId',
        'leadid',
        'opportunityId',
        'salesOrdersId',
        'invoiceid',
        'quotationId',
        'created_by',
    ];

    protected $dateFields = [
        'meetingTime' => 'Y-m-d H:i:s',
        'repeatTime' => 'Y-m-d H:i:s',
    ];

    protected $searchable = ['title', 'location', 'relatedTo', 'description', 'participateType'];

    protected $searchOnList = false;

    protected $rules = [
        'title' => 'nullable|string|max:255',
        'meetingTime' => 'nullable|date',
    ];

    protected $legacyStoreIdKey = 'meetingId';

    protected $listMessage = 'Meeting List Fetched Successfully!';

    protected $indexAllMessage = 'All Meetings Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Meeting Details Fetched Successfully!',
        'store' => 'New Meeting Created Successfully!',
        'update' => 'Meeting Updated Successfully!',
        'destroy' => 'Meeting Deleted Successfully!',
        'bulk' => 'Meetings Deleted Successfully!',
        'search' => 'Meeting Search Results Fetched Successfully!',
        'not_found' => 'Meeting Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        $data = $this->normalize($request, $data);

        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        if (empty($data['createdBy']) && auth()->check()) {
            $data['created_by'] = auth()->id();
        }

        return $data;
    }

    protected function beforeUpdate(Request $request, array $data, $item)
    {
        return $this->normalize($request, $data);
    }

    protected function normalize(Request $request, array $data)
    {
        if (isset($data['participantIds']) && is_array($data['participantIds'])) {
            $data['participantIds'] = implode(',', $data['participantIds']);
        }

        foreach (['repeat', 'reminder'] as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->booleanValue($data[$field]);
            }
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('type')) {
            $query->where('status', $request->input('type'));
        }
    }
}
