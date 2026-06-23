<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AhpComparison extends Model
{
    protected $fillable = [
        'criterion_1_id',
        'criterion_2_id',
        'value',
    ];

    public function criterion1(): BelongsTo
    {
        return $this->belongsTo(Criterion::class, 'criterion_1_id');
    }

    public function criterion2(): BelongsTo
    {
        return $this->belongsTo(Criterion::class, 'criterion_2_id');
    }
}
