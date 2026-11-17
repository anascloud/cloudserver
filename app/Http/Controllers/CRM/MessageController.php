<?php

namespace App\Http\Controllers\CRM;

use App\Repositories\ResponseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends CrmCrudController
{
    protected $dbTable = 'messages';

    protected $resource = 'Message';

    protected $resourcePlural = 'Messages';

    // Blocking a conversation should only change the flag, not wipe the other
    // columns that were not part of the request.
    protected $clearMissingOnUpdate = false;

    // The chat UI consumes the conversation rows directly from `data`.
    protected $indexFormat = 'items';

    protected $fields = [
        'name',
        'customer_id',
        'createdBy',
        'online',
        'isBlock',
    ];

    protected $searchable = ['name'];

    protected $searchOnList = false;

    protected $rules = [
        'name' => 'nullable|string|max:255',
        'customer_id' => 'nullable|integer',
        'createdBy' => 'nullable|integer',
        'online' => 'nullable',
        'isBlock' => 'nullable',
    ];

    protected $legacyStoreIdKey = 'messageId';

    protected $listMessage = 'Message List Fetched Successfully!';

    protected $indexAllMessage = 'All Messages Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Message Details Fetched Successfully!',
        'store' => 'New Message Created Successfully!',
        'update' => 'Message Updated Successfully!',
        'destroy' => 'Message Deleted Successfully!',
        'bulk' => 'Messages Deleted Successfully!',
        'search' => 'Message Search Results Fetched Successfully!',
        'not_found' => 'Message Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        $this->normalizeBooleans($data);

        if (empty($data['createdBy']) && auth()->check()) {
            $data['createdBy'] = auth()->id();
        }

        if (empty($data['name']) && auth()->check()) {
            $data['name'] = auth()->user()->firstName;
        }

        if (!array_key_exists('online', $data)) {
            $data['online'] = 0;
        }

        if (!array_key_exists('isBlock', $data)) {
            $data['isBlock'] = 0;
        }

        return $data;
    }

    protected function beforeUpdate(Request $request, array $data, $item)
    {
        $this->normalizeBooleans($data);

        return $data;
    }

    protected function normalizeBooleans(array &$data)
    {
        foreach (['online', 'isBlock'] as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->booleanValue($data[$field]);
            }
        }
    }

    protected function applyListQuery($query, Request $request)
    {
        $userId = auth()->id();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('customer_id', $userId)->orWhere('createdBy', $userId);
            });
        }
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
            $this->decorateConversation($row, auth()->id());
        }

        return $data;
    }

    protected function decorateConversation($row, $userId)
    {
        $otherId = ((int) $row->customer_id === (int) $userId)
            ? $row->createdBy
            : $row->customer_id;

        $other = $otherId
            ? DB::table('users')->where('id', $otherId)->first()
            : null;

        if ($other) {
            $row->name = trim(($other->firstName ?? '') . ' ' . ($other->lastName ?? ''));
        }

        $row->isBlock = (bool) $row->isBlock;
        $row->online = (bool) $row->online;

        $details = DB::table('message_details')
            ->where('messageId', $row->id)
            ->orderBy('id', 'desc')
            ->limit(1)
            ->get();

        foreach ($details as $detail) {
            $this->decorateRow($detail);
        }

        $row->messageDetails = $details->all();
    }

    public function changeBlock(Request $request, $id)
    {
        $item = $this->findItem($id);

        if ($item === null) {
            return $this->notFoundForWrite();
        }

        $value = $request->input('isBlock', $request->input('block'));

        if ($value === null && $request->getContent()) {
            $value = filter_var($request->getContent(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($value === null) {
            return ResponseRepository::ResponseError(null, 'The isBlock field is required.', 422);
        }

        $this->query()->where('id', $id)->update([
            'isBlock' => $this->booleanValue($value),
            'updated_at' => now(),
        ]);

        return $this->respondWrite(['id' => (int) $id, 'isBlock' => (bool) $this->booleanValue($value)], $this->message('update'));
    }

    protected function afterDestroy($item)
    {
        DB::table('message_details')->where('messageId', $item->id)->delete();
    }
}