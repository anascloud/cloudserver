<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class BillsController extends ApiCrudController
{
    protected $dbTable = 'bills';

    protected $resource = 'Bill';

    protected $resourcePlural = 'Bills';

    protected $fields = [
        'income_category',
        'type',
        'amount',
        'issue_date',
        'customer_id',
        'payment_method',
        'chart_of_account_id',
        'note',
        'file',
    ];

    protected $dateFields = [
        'issue_date' => 'Y-m-d',
    ];

    protected $searchable = ['income_category', 'type', 'payment_method', 'note'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'income_category' => 'required|string|max:255',
        'type' => 'required|string|max:50',
        'amount' => 'required|numeric|min:0',
        'issue_date' => 'required|date',
        'customer_id' => 'nullable|integer|exists:customer,id',
        'payment_method' => 'nullable|string|max:100',
        'chart_of_account_id' => 'nullable|integer|exists:chart_of_accounts,id',
        'note' => 'nullable|string|max:2000',
        'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:4096',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'billId';

    protected $listMessage = 'Bill List Fetched Successfully!';

    protected $indexAllMessage = 'All Bills Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Bill Details Fetched Successfully!',
        'store' => 'New Bill Created Successfully!',
        'update' => 'Bill Updated Successfully!',
        'destroy' => 'Bill Deleted Successfully!',
        'search' => 'Bill Search Results Fetched Successfully!',
        'not_found' => 'Bill Not Found',
    ];

    /**
     * Narrow the columns returned for the index listing.
     */
    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'income_category',
                'type',
                'amount',
                'issue_date',
                'customer_id',
                'payment_method',
                'chart_of_account_id',
                'note',
                'file',
                'created_at',
                'updated_at'
            );
        }
    }
}
