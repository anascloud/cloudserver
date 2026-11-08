<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\JournalEntry;

class JournalEntryController extends ApiCrudController
{
    protected $model = JournalEntry::class;

    protected $resource = 'JournalEntry';

    protected $resourcePlural = 'JournalEntrys';

    protected $searchable = ['journal_no'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'journal_no' => 'sometimes|required_without:journalNo|string|max:255',
        'journalNo' => 'sometimes|required_without:journal_no|string|max:255',
        'journal_type_id' => 'sometimes|required_without:journalTypeId|integer',
        'journalTypeId' => 'sometimes|required_without:journal_type_id|integer',
        'posting_date' => 'sometimes|nullable|date',
        'postingDate' => 'sometimes|nullable|date',
        'company_id' => 'sometimes|required_without:companyId|integer',
        'companyId' => 'sometimes|required_without:company_id|integer',
        'journal_template_id' => 'sometimes|nullable|integer',
        'journalTemplateId' => 'sometimes|nullable|integer',
        'reference_no' => 'sometimes|nullable|string|max:255',
        'referenceNo' => 'sometimes|nullable|string|max:255',
        'reference_date' => 'sometimes|nullable|date',
        'referenceDate' => 'sometimes|nullable|date',
        'total_debit' => 'sometimes|nullable|numeric',
        'totalDebit' => 'sometimes|nullable|numeric',
        'total_credit' => 'sometimes|nullable|numeric',
        'totalCredit' => 'sometimes|nullable|numeric',
    ];

    protected $syncDetails = true;

    protected $detailTable = 'fm_journal_details';
}
