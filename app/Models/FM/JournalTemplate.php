<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalTemplate extends Model
{
    use HasFactory;

    protected $table = 'fm_journal_templates';
    protected $fillable = ["journal_template_title","company_id","journal_type_id"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
