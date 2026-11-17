<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class ContactController extends CrmCrudController
{
    protected $dbTable = 'contacts';

    protected $resource = 'Contact';

    protected $resourcePlural = 'Contacts';

    protected $fields = [
        'type',
        'subject',
        'contactDate',
        'contactTime',
        'relatedTo',
        'description',
        'file',
        'duration',
        'status',
        'campaignId',
        'leadId',
        'opportunityId',
        'salesOrdersId',
        'invoiceId',
        'quotationId',
    ];

    protected $fileFields = [
        'file' => 'uploads/contacts',
    ];

    protected $dateFields = [
        'contactDate' => 'Y-m-d',
        'contactTime' => 'Y-m-d H:i:s',
    ];

    protected $searchable = ['subject', 'type', 'relatedTo', 'description'];

    protected $searchOnList = false;

    protected $rules = [
        'subject' => 'nullable|string|max:255',
        'contactDate' => 'nullable|date',
        'contactTime' => 'nullable|date',
        'duration' => 'nullable|numeric',
        'file' => 'nullable|file',
    ];

    protected $legacyStoreIdKey = 'contactId';

    protected $listMessage = 'Contact List Fetched Successfully!';

    protected $indexAllMessage = 'All Contacts Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Contact Details Fetched Successfully!',
        'store' => 'New Contact Created Successfully!',
        'update' => 'Contact Updated Successfully!',
        'destroy' => 'Contact Deleted Successfully!',
        'bulk' => 'Contacts Deleted Successfully!',
        'search' => 'Contact Search Results Fetched Successfully!',
        'not_found' => 'Contact Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        return $data;
    }

    protected function beforeUpdate(Request $request, array $data, $item)
    {
        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
    }
}
