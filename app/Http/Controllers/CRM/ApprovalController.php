<?php

namespace App\Http\Controllers\CRM;

use App\Repositories\ResponseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends CrmCrudController
{
    protected $dbTable = 'approvals';

    protected $resource = 'Approval';

    protected $resourcePlural = 'Approvals';

    protected $fields = [
        'referenceid',
        'type',
        'referenceName',
        'level',
        'approvalStatus',
        'createdBy',
    ];

    protected $searchable = ['referenceName', 'approvalStatus'];

    protected $searchOnList = false;

    protected $rules = [
        'referenceid' => 'nullable|integer',
        'type' => 'nullable',
    ];

    protected $legacyStoreIdKey = 'approvalId';

    protected $listMessage = 'Approval List Fetched Successfully!';

    protected $indexAllMessage = 'All Approvals Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Approval Details Fetched Successfully!',
        'store' => 'New Approval Created Successfully!',
        'update' => 'Approval Updated Successfully!',
        'destroy' => 'Approval Deleted Successfully!',
        'search' => 'Approval Search Results Fetched Successfully!',
        'not_found' => 'Approval Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['approvalStatus'])) {
            $data['approvalStatus'] = 'Pending';
        }

        if (empty($data['createdBy']) && auth()->check()) {
            $data['createdBy'] = auth()->id();
        }

        return $data;
    }

    protected function afterStore(Request $request, $item)
    {
        $this->applyStatusToResource($item->referenceid ?? null, $item->type ?? null, $item->approvalStatus ?? null);
    }

    protected function afterUpdate(Request $request, $item)
    {
        $this->applyStatusToResource($item->referenceid ?? null, $item->type ?? null, $item->approvalStatus ?? null);
    }

    public function updateApproval(Request $request)
    {
        $type = $request->input('type');
        $referenceId = $request->input('referenceId', $request->input('referenceid'));
        $status = $request->input('approvalStatus');

        if ($referenceId === null || $type === null || $status === null) {
            return ResponseRepository::ResponseError(null, 'Reference id, type and approval status are required.', 422);
        }

        $query = DB::table($this->dbTable)
            ->where('referenceid', $referenceId)
            ->where('type', $type);

        $updated = $query->update([
            'approvalStatus' => $status,
            'updated_at' => now(),
        ]);

        if ($updated === 0) {
            $id = DB::table($this->dbTable)->insertGetId([
                'referenceid' => $referenceId,
                'type' => $type,
                'referenceName' => null,
                'level' => 1,
                'approvalStatus' => $status,
                'createdBy' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->applyStatusToResource($referenceId, $type, $status);

            return $this->respondWrite(['id' => $id, 'approvalStatus' => $status], 'Approval Updated Successfully!');
        }

        $this->applyStatusToResource($referenceId, $type, $status);

        return $this->respondWrite(['referenceid' => $referenceId, 'type' => $type, 'approvalStatus' => $status], 'Approval Updated Successfully!');
    }

    protected function applyStatusToResource($referenceId, $type, $status)
    {
        if ($referenceId === null || $status === null) {
            return;
        }

        $tables = [
            '1' => 'products',
            '2' => 'customer',
            '3' => 'campaigns',
            '4' => 'quotations',
            '5' => 'bills',
            '6' => 'targets',
        ];

        $table = $tables[(string) $type] ?? null;

        if ($table === null || !DB::getSchemaBuilder()->hasColumn($table, 'approvalStatus')) {
            return;
        }

        DB::table($table)->where('id', $referenceId)->update([
            'approvalStatus' => $status,
            'updated_at' => now(),
        ]);
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('approvalStatus')) {
            $query->where('approvalStatus', $request->input('approvalStatus'));
        }

        if ($request->filled('referenceid')) {
            $query->where('referenceid', $request->input('referenceid'));
        }
    }
}
