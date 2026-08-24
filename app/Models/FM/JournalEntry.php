<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fm_journal_entries = 'fm_journal_entries';
    protected $ = ["journal_no","journal_type_id","posting_date","company_id","journal_template_id","reference_no","reference_date","total_debit","total_credit","created_by","updated_by","is_active","is_deleted"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
