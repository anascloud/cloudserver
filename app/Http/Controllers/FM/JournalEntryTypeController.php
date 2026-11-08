<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\JournalEntryType;

class JournalEntryTypeController extends ApiCrudController
{
    protected $model = JournalEntryType::class;

    protected $resource = 'JournalEntryType';

    protected $resourcePlural = 'JournalEntryTypes';

    protected $searchable = ['journal_type_name'];

    protected $rules = [
        'journal_type_name' => 'sometimes|required_without:journalTypeName|string|max:255',
        'journalTypeName' => 'sometimes|required_without:journal_type_name|string|max:255',
    ];
}
