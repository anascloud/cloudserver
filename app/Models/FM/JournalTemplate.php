<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalTemplate extends Model
{
    use HasFactory;

    protected $fm_journal_templates = 'fm_journal_templates';
    protected $ = ["journal_template_title","company_id","journal_type_id"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
