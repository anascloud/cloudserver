<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryType extends Model
{
    use HasFactory;

    protected $table = 'fm_journal_entry_types';
    protected $fillable = ["journal_type_name"];
}
