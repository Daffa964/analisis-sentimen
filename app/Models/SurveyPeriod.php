<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyPeriod extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($period) {
            if ($period->is_active) {
                // Deactivate all other periods
                static::where('id', '!=', $period->id)->update(['is_active' => false]);
            }
        });
    }

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class);
    }

    public function sawResults(): HasMany
    {
        return $this->hasMany(SawResult::class);
    }
}
