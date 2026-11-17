<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class NotificationController extends CrmCrudController
{
    protected $dbTable = 'notifications';

    protected $resource = 'Notification';

    protected $resourcePlural = 'Notifications';

    // Notifications are updated individually (e.g. marking one as read); never
    // blank out columns that were not part of the request payload.
    protected $clearMissingOnUpdate = false;

    // The notification list UI consumes the rows directly from `data`.
    protected $indexFormat = 'items';

    protected $fields = [
        'title',
        'type',
        'referenceId',
        'status',
        'description',
        'createdBy',
    ];

    protected $searchable = ['title', 'type'];

    protected $searchOnList = false;

    protected $rules = [
        'title' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:255',
        'referenceId' => 'nullable|integer',
        'status' => 'nullable|integer',
        'description' => 'nullable|string',
        'createdBy' => 'nullable|integer',
    ];

    protected $legacyStoreIdKey = 'notificationId';

    protected $listMessage = 'Notification List Fetched Successfully!';

    protected $indexAllMessage = 'All Notifications Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Notification Details Fetched Successfully!',
        'store' => 'New Notification Created Successfully!',
        'update' => 'Notification Updated Successfully!',
        'destroy' => 'Notification Deleted Successfully!',
        'bulk' => 'Notifications Deleted Successfully!',
        'search' => 'Notification Search Results Fetched Successfully!',
        'not_found' => 'Notification Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['status'])) {
            $data['status'] = 2;
        }

        if (empty($data['createdBy']) && auth()->check()) {
            $data['createdBy'] = auth()->id();
        }

        return $data;
    }

    protected function formatList($items, Request $request, $perPage, $format = null)
    {
        $data = parent::formatList($items, $request, $perPage, $format);

        if (($format ?: $this->listFormat) === 'items' && is_array($data)) {
            foreach ($data as $row) {
                $this->decorateRow($row);
            }
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        foreach (['type' => 'type', 'status' => 'status', 'referenceId' => 'referenceId'] as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }
    }
}