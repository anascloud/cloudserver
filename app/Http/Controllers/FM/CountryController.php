<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Country;

class CountryController extends ApiCrudController
{
    protected $model = Country::class;

    protected $resource = 'Country';

    protected $resourcePlural = 'Countries';

    protected $searchable = ['country_name', 'country_code'];

    protected $rules = [
        'country_name' => 'required|string|max:255',
        'country_code' => 'nullable|string|max:10',
        'dateFormat' => 'nullable|string|max:50',
        'timeFormat' => 'nullable|string|max:50',
        'timeZone' => 'nullable|string|max:100',
    ];

    protected $listFormat = 'compact';

    protected $listMessage = 'Country List Fetched Successfully!';
}
