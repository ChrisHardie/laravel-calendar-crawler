<?php

namespace ChrisHardie\CalendarCrawler\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_internal_id',
        'title',
        'start_timestamp',
        'end_timestamp',
        'all_day',
        'description',
        'address_name',
        'address',
        'organizer_name',
        'organizer_email',
        'organizer_url',
        'status',
        'attachment_url',
        'last_crawled_at',
        'url',
    ];

    protected $dates = [
        'start_timestamp',
        'end_timestamp',
        'last_crawled_at',
    ];

    public function calendarSource(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CalendarSource::class);
    }
}
