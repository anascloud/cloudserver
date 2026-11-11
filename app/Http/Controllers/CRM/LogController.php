<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class LogController extends CrmCrudController
{
    protected $dbTable = 'logs';

    protected $resource = 'Log';

    protected $resourcePlural = 'Logs';

    protected $fields = [
        'userid',
        'message',
        'actionName',
        'ipAddress',
        'timestamp',
    ];

    protected $searchable = ['message', 'actionName', 'ipAddress'];

    protected $searchOnList = false;

    protected $rules = [
        'message' => 'nullable|string',
        'actionName' => 'nullable|string|max:255',
    ];

    protected $legacyStoreIdKey = 'logId';

    protected $listMessage = 'Log List Fetched Successfully!';

    protected $indexAllMessage = 'All Logs Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Log Details Fetched Successfully!',
        'store' => 'New Log Created Successfully!',
        'update' => 'Log Updated Successfully!',
        'destroy' => 'Log Deleted Successfully!',
        'search' => 'Log Search Results Fetched Successfully!',
        'not_found' => 'Log Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['timestamp'])) {
            $data['timestamp'] = now()->toDateTimeString();
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        foreach (['userid' => 'userid', 'actionName' => 'actionName'] as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }
    }
}
