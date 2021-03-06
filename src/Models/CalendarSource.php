<?php

namespace ChrisHardie\CalendarCrawler\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CalendarSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'fail_count',
        'last_check_at',
        'last_success_at',
        'last_fail_at',
        'last_fail_reason',
        'next_check_after',
        'fail_count',
    ];

    protected $dates = [
        'last_check_at',
        'last_success_at',
        'last_fail_at',
        'next_check_after',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Scope a query to only include tracking objects not paused
     *
     * @param  Builder  $query
     * @param  string  $frequency
     * @return mixed
     */
    public function scopeCheckable(Builder $query)
    {
        $query
            ->where('active', true)
            ->where('admin_pause_checks', false);

        $query->where(function ($query) {
            $query
                // Never been checked
                ->whereNull('last_check_at')
                // Haven't been checked in the last X minutes given sitewide update frequency
                ->orWhereRaw('last_check_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL frequency MINUTE)');
        });

        // Sources where no next check is set or where it has passed.
        $query->where(function ($query) {
            $query
                // Never been checked
                ->whereNull('next_check_after')
                // Haven't been checked in the last X minutes given sitewide update frequency
                ->orWhere(
                    'next_check_after',
                    '<=',
                    Carbon::now()
                );
        });

        return $query;
    }
}
