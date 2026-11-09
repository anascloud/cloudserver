<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class WarehouseManagerController extends ApiCrudController
{
    protected $dbTable = 'warehouse_manager';

    protected $resource = 'WarehouseManager';

    protected $resourcePlural = 'WarehouseManagers';

    protected $fields = [
        'name',
        'contact',
    ];

    protected $searchable = ['name', 'contact'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'contact' => 'nullable|string|max:100',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'warehouseManagerId';

    protected $listMessage = 'Warehouse Manager List Fetched Successfully!';

    protected $indexAllMessage = 'All Warehouse Managers Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Warehouse Manager Details Fetched Successfully!',
        'store' => 'New Warehouse Manager Created Successfully!',
        'update' => 'Warehouse Manager Updated Successfully!',
        'destroy' => 'Warehouse Manager Deleted Successfully!',
        'search' => 'Warehouse Manager Search Results Fetched Successfully!',
        'not_found' => 'Warehouse Manager Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'name',
                'contact',
                'created_at',
                'updated_at'
            );
        }
    }
}
