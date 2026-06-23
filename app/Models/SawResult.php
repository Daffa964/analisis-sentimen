<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SawResult extends Model
{
    protected $fillable = [
        'survey_period_id',
        'ward_id',
        'preference_value',
        'ranking',
    ];

    public function surveyPeriod(): BelongsTo
    {
        return $this->belongsTo(SurveyPeriod::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }
}
