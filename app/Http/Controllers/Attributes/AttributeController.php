<?php

namespace App\Http\Controllers\Attributes;

use App\Http\Controllers\ApiCrudController;

class AttributeController extends ApiCrudController
{
    protected $dbTable = 'attributes';

    protected $resource = 'Attribute';

    protected $resourcePlural = 'Attributes';

    protected $fields = ['name'];

    protected $searchable = ['name'];

    protected $searchOnList = false;

    protected $indexPerPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $writeResponse = 'legacy';

    protected $destroyResponse = 'standard';

    protected $listMessage = 'Attribute List Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Attribute Details Fetched Successfully!',
        'store' => 'New Attribute Created Successfully!',
        'update' => 'Attribute Updated Successfully.',
        'destroy' => 'Attribute Deleted Successfully!',
        'search' => 'Attribute Search Results Fetched Successfully!',
        'not_found' => 'Attribute Not Found',
    ];
}
