<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class TicketController extends CrmCrudController
{
    protected $dbTable = 'tickets';

    protected $resource = 'Ticket';

    protected $resourcePlural = 'Tickets';

    protected $fields = [
        'subject',
        'type',
        'departmentId',
        'mergeTicketId',
        'service',
        'email',
        'project',
        'file',
        'status',
        'createdBy',
    ];

    protected $fileFields = [
        'file' => 'uploads/tickets',
    ];

    protected $searchable = ['subject', 'service', 'email', 'project', 'status'];

    protected $searchOnList = false;

    protected $rules = [
        'subject' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
    ];

    protected $legacyStoreIdKey = 'ticketId';

    protected $listMessage = 'Ticket List Fetched Successfully!';

    protected $indexAllMessage = 'All Tickets Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Ticket Details Fetched Successfully!',
        'store' => 'New Ticket Created Successfully!',
        'update' => 'Ticket Updated Successfully!',
        'destroy' => 'Ticket Deleted Successfully!',
        'bulk' => 'Tickets Deleted Successfully!',
        'search' => 'Ticket Search Results Fetched Successfully!',
        'not_found' => 'Ticket Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        if (empty($data['createdBy']) && auth()->check()) {
            $data['createdBy'] = auth()->user()->email ?? auth()->user()->name;
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        foreach (['departmentid' => 'departmentId', 'service' => 'service', 'email' => 'email'] as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }
    }
}
