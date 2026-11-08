<?php

namespace App\Http\Controllers;

class BrandController extends ApiCrudController
{
    protected $dbTable = 'brands';

    protected $resource = 'Brand';

    protected $resourcePlural = 'Brands';

    protected $fields = ['name'];

    protected $searchable = ['name'];

    protected $searchOnList = false;

    protected $indexPerPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $writeResponse = 'legacy';

    protected $destroyResponse = 'standard';

    protected $listMessage = 'Brand List Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Brand Details Fetched Successfully!',
        'store' => 'New Brand Created Successfully!',
        'update' => 'Brand Updated Successfully.',
        'destroy' => 'Brand Deleted Successfully!',
        'search' => 'Brand Search Results Fetched Successfully!',
        'not_found' => 'Brand Not Found',
    ];
}
