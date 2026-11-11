<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillController extends CrmCrudController
{
    protected $dbTable = 'bills';

    protected $resource = 'Bill';

    protected $resourcePlural = 'Bills';

    protected $fields = [
        'invoice_no',
        'incomeCategory',
        'type',
        'amount',
        'issueDate',
        'customerid',
        'paymentMethod',
        'chartOfAccountId',
        'note',
        'description',
        'file',
        'approvalStatus',
        'status',
    ];

    protected $fileFields = [
        'file' => 'uploads/bills',
    ];

    protected $dateFields = [
        'issueDate' => 'Y-m-d',
    ];

    protected $searchable = ['invoice_no', 'incomeCategory', 'type', 'paymentMethod', 'description'];

    protected $searchOnList = false;

    protected $rules = [
        'incomeCategory' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:100',
        'amount' => 'nullable|numeric',
    ];

    protected $legacyStoreIdKey = 'billId';

    protected $listMessage = 'Bill List Fetched Successfully!';

    protected $indexAllMessage = 'All Bills Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Bill Details Fetched Successfully!',
        'store' => 'New Bill Created Successfully!',
        'update' => 'Bill Updated Successfully!',
        'destroy' => 'Bill Deleted Successfully!',
        'bulk' => 'Bills Deleted Successfully!',
        'search' => 'Bill Search Results Fetched Successfully!',
        'not_found' => 'Bill Not Found',
    ];

    protected function beforeStore(Request $request, array $data)
    {
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = 'BILL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        }

        if (empty($data['approvalStatus'])) {
            $data['approvalStatus'] = 'Draft';
        }

        if (empty($data['status'])) {
            $data['status'] = 'Active';
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        foreach (['category' => 'incomeCategory', 'type' => 'type', 'paymentMethod' => 'paymentMethod'] as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }
    }
}
