<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;

class FeedbacksController extends ApiCrudController
{
    protected $dbTable = 'feedbacks';

    protected $resource = 'Feedback';

    protected $resourcePlural = 'Feedbacks';

    protected $fields = ['title', 'product', 'reference', 'description'];

    protected $searchable = ['title', 'product', 'reference'];

    protected $searchOnList = false;

    protected $indexPerPage = 10;

    protected $perPage = 12;

    protected $rules = [
        'title' => 'required|string',
        'product' => 'required|string',
        'reference' => 'required|string',
        'description' => 'required|string',
    ];

    protected $writeResponse = 'legacy';

    protected $listMessage = 'Feedback List Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Feedback Details Fetched Successfully!',
        'store' => 'New Feedback Created Successfully!',
        'update' => 'Feedback Updated Successfully.',
        'destroy' => 'Feedback Deleted Successfully',
        'search' => 'Feedback Search Results Fetched Successfully!',
        'not_found' => 'Feedback Not Found',
    ];
}
