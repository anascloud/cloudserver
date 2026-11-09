<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class WarehouseController extends ApiCrudController
{
    protected $dbTable = 'warehouses';

    protected $resource = 'Warehouse';

    protected $resourcePlural = 'Warehouses';

    protected $fields = [
        'warehouseName',
        'warehouseManagerId',
        'company_id',
        'company_name',
        'location',
        'capacity',
        'inUseCapacity',
        'emergencyContact',
        'binLocation',
        'zoningLocation',
        'startHour',
        'endHour',
        'pickedBy',
        'packedBy',
        'quantityToPick',
        'datePicked',
        'temperatureControlled',
        'fireSafetyCompliance',
        'status',
    ];

    protected $dateFields = [
        'datePicked' => 'Y-m-d H:i:s',
    ];

    protected $searchable = ['warehouseName', 'location', 'binLocation', 'zoningLocation'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'warehouseName' => 'required|string|max:255',
        'warehouseManagerId' => 'required|integer|exists:users,id',
        'company_id' => 'required|integer|exists:fm_companies,id',
        'company_name' => 'required|string|max:255',
        'location' => 'required|string|max:500',
        'capacity' => 'required|integer|min:0',
        'inUseCapacity' => 'required|integer|min:0',
        'emergencyContact' => 'nullable|string|max:100',
        'binLocation' => 'nullable|string|max:255',
        'zoningLocation' => 'nullable|string|max:255',
        'startHour' => 'required|date_format:H:i:s',
        'endHour' => 'required|date_format:H:i:s|after:startHour',
        'pickedBy' => 'required|string|max:255',
        'packedBy' => 'required|string|max:255',
        'quantityToPick' => 'required|integer|min:0',
        'datePicked' => 'required|date',
        'temperatureControlled' => 'required|boolean',
        'fireSafetyCompliance' => 'required|boolean',
        'status' => 'nullable|boolean',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'warehouseId';

    protected $listMessage = 'Warehouse List Fetched Successfully!';

    protected $indexAllMessage = 'All Warehouses Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Warehouse Details Fetched Successfully!',
        'store' => 'New Warehouse Created Successfully!',
        'update' => 'Warehouse Updated Successfully!',
        'destroy' => 'Warehouse Deleted Successfully!',
        'search' => 'Warehouse Search Results Fetched Successfully!',
        'not_found' => 'Warehouse Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'warehouseName',
                'warehouseManagerId',
                'company_id',
                'company_name',
                'location',
                'capacity',
                'inUseCapacity',
                'emergencyContact',
                'binLocation',
                'zoningLocation',
                'startHour',
                'endHour',
                'pickedBy',
                'packedBy',
                'quantityToPick',
                'datePicked',
                'temperatureControlled',
                'fireSafetyCompliance',
                'status',
                'created_at',
                'updated_at'
            );
        }
    }
}
