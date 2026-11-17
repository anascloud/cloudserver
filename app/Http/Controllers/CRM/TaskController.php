<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;

class TaskController extends CrmCrudController
{
    protected $dbTable = 'tasks';

    protected $resource = 'Task';

    protected $resourcePlural = 'Tasks';

    protected $fields = [
        'subject',
        'ticketId',
        'assignedTo',
        'startDate',
        'dueDate',
        'priority',
        'description',
        'status',
        'repeatDate',
        'reminder',
        'file',
        'campaignId',
        'leadId',
        'opportunityId',
        'salesOrdersId',
        'invoiceId',
        'quotationId',
        'created_by',
    ];

    protected $fileFields = [
        'file' => 'uploads/tasks',
    ];

    protected $dateFields = [
        'startDate' => 'Y-m-d H:i:s',
        'dueDate' => 'Y-m-d H:i:s',
        'repeatDate' => 'Y-m-d H:i:s',
    ];

    protected $searchable = ['subject', 'description', 'priority'];

    protected $searchOnList = false;

    protected $rules = [
        'subject' => 'nullable|string|max:255',
        'startDate' => 'nullable|date',
        'dueDate' => 'nullable|date',
        'repeatDate' => 'nullable|date',
        'priority' => 'nullable|string|max:50',
        'status' => 'nullable',
        'file' => 'nullable|file',
    ];

    protected $legacyStoreIdKey = 'taskId';

    protected $listMessage = 'Task List Fetched Successfully!';

    protected $indexAllMessage = 'All Tasks Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Task Details Fetched Successfully!',
        'store' => 'New Task Created Successfully!',
        'update' => 'Task Updated Successfully!',
        'destroy' => 'Task Deleted Successfully!',
        'bulk' => 'Tasks Deleted Successfully!',
        'search' => 'Task Search Results Fetched Successfully!',
        'not_found' => 'Task Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        $data = $this->normalize($request, $data);

        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        if (empty($data['created_by']) && auth()->check()) {
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
        if (array_key_exists('reminder', $data)) {
            $data['reminder'] = $this->booleanValue($data['reminder']);
        }

        return $data;
    }

    public function updateStatus(Request $request, $id)
    {
        $this->listMode = null;

        try {
            $item = $this->findItem($id);

            if ($item === null) {
                return $this->notFoundForWrite();
            }

            $status = $request->input('status');

            if ($status === null) {
                $status = trim((string) $request->getContent(), " \t\n\r\0\x0B\"'");
            }

            $status = (string) $status;

            $allowed = ['1', '2', '3', '4', '5'];

            if (!in_array($status, $allowed, true)) {
                return response()->json([
                    'message' => 'Invalid status. Allowed: ' . implode(', ', $allowed),
                    'type' => 'error',
                ], 422);
            }

            $data = ['status' => $status];

            if ($request->filled('shortOrder')) {
                $data['shortOrder'] = $request->input('shortOrder');
            }

            if ($this->timestamps) {
                $data['updated_at'] = now();
            }

            $this->query()->where('id', $id)->update($data);

            $item = $this->query()->where('id', $id)->first();

            return $this->respondWrite($item, $this->message('update'));
        } catch (\Throwable $e) {
            return $this->respondWriteError($e->getMessage(), $this->writeErrorStatus);
        }
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('type')) {
            $query->where('status', $request->input('type'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('assignedTo')) {
            $query->where('assignedTo', $request->input('assignedTo'));
        }
    }

    public function reminders()
    {
        return $this->query()->where('reminder', true)->get();
    }
}
