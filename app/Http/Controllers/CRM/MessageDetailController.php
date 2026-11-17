<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class MessageDetailController extends CrmCrudController
{
    protected $dbTable = 'message_details';

    protected $resource = 'Message Detail';

    protected $resourcePlural = 'Message Details';

    // The chat UI consumes the message rows directly from `data`.
    protected $indexFormat = 'items';

    // Newest first so that "load more" pages walk backwards into older messages.
    protected $orderBy = ['id', 'desc'];

    protected $fields = [
        'messageId',
        'messageContent',
        'sender',
        'createdBy',
    ];

    protected $searchable = ['messageContent'];

    protected $searchOnList = false;

    protected $rules = [
        'messageId' => 'required|integer',
        'messageContent' => 'nullable|string',
        'sender' => 'nullable|integer',
        'createdBy' => 'nullable|integer',
    ];

    protected $legacyStoreIdKey = 'messageDetailId';

    protected $listMessage = 'Message Details Fetched Successfully!';

    protected $indexAllMessage = 'All Message Details Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Message Detail Fetched Successfully!',
        'store' => 'Message Sent Successfully!',
        'update' => 'Message Detail Updated Successfully!',
        'destroy' => 'Message Detail Deleted Successfully!',
        'bulk' => 'Message Details Deleted Successfully!',
        'search' => 'Message Detail Search Results Fetched Successfully!',
        'not_found' => 'Message Detail Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['createdBy']) && auth()->check()) {
            $data['createdBy'] = auth()->id();
        }

        if (empty($data['sender']) && auth()->check()) {
            $data['sender'] = auth()->id();
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        $messageId = $request->input('id', $request->input('messageId'));

        if ($messageId === null || (int) $messageId <= 0) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where('messageId', $messageId);
    }

    protected function formatList($items, Request $request, $perPage, $format = null)
    {
        $data = parent::formatList($items, $request, $perPage, $format);

        $effectiveFormat = $format ?: $this->listFormat;

        if ($effectiveFormat === 'items' && is_array($data)) {
            $rows = $data;
        } elseif (isset($data['data']) && is_array($data['data'])) {
            $rows = $data['data'];
        } else {
            $rows = [];
        }

        foreach ($rows as $row) {
            $this->decorateRow($row);
        }

        return $data;
    }
}