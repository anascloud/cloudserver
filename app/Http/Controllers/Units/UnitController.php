<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\ApiCrudController;

class UnitController extends ApiCrudController
{
    protected $dbTable = 'units';

    protected $resource = 'Unit';

    protected $resourcePlural = 'Units';

    protected $fields = ['name'];

    protected $searchable = ['name'];

    protected $searchOnList = false;

    protected $indexPerPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $writeResponse = 'legacy';

    protected $destroyResponse = 'standard';

    protected $listMessage = 'Unit List Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Unit Details Fetched Successfully!',
        'store' => 'New Unit Created Successfully!',
        'update' => 'Unit Updated Successfully.',
        'destroy' => 'Unit Deleted Successfully!',
        'search' => 'Unit Search Results Fetched Successfully!',
        'not_found' => 'Unit Not Found',
    ];
}
