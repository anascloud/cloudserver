<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryType extends Model
{
    use HasFactory;

    protected $fm_journal_entry_types = 'fm_journal_entry_types';
    protected $ = ["journal_type_name"];
}
