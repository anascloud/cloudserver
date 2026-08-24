<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingType extends Model
{
    protected $fillable = [
        'accounting_type_name',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountingType::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AccountingType::class, 'parent_id');
    }
}
