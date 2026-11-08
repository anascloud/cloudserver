<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\JournalTemplate;

class JournalTemplateController extends ApiCrudController
{
    protected $model = JournalTemplate::class;

    protected $resource = 'JournalTemplate';

    protected $resourcePlural = 'JournalTemplates';

    protected $searchable = ['journal_template_title'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'journal_template_title' => 'sometimes|required_without:journalTemplateTitle|string|max:255',
        'journalTemplateTitle' => 'sometimes|required_without:journal_template_title|string|max:255',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'journal_type_id' => 'sometimes|required_without:journalTypeId|integer',
        'journalTypeId' => 'sometimes|required_without:journal_type_id|integer',
    ];
}
