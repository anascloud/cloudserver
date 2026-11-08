<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\FiscalYear;

class FiscalYearController extends ApiCrudController
{
    protected $model = FiscalYear::class;

    protected $resource = 'FiscalYear';

    protected $resourcePlural = 'FiscalYears';

    protected $searchable = ['year_range'];

    protected $rules = [
        'year_range' => 'sometimes|required_without:yearRange|string|max:255',
        'yearRange' => 'sometimes|required_without:year_range|string|max:255',
        'start_date' => 'sometimes|nullable|date',
        'startDate' => 'sometimes|nullable|date',
        'end_date' => 'sometimes|nullable|date',
        'endDate' => 'sometimes|nullable|date',
    ];
}
