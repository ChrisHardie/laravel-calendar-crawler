<?php

namespace ChrisHardie\CalendarCrawler\Models;

use Html2Text\Html2Text;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $appends = [
        'description_textonly',
    ];

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
        'last_success_at',
        'last_fail_at',
        'last_fail_reason',
        'next_check_after',
        'fail_count',
        'url',
    ];

    protected $dates = [
        'start_timestamp',
        'end_timestamp',
        'last_crawled_at',
        'last_success_at',
        'last_fail_at',
        'next_check_after',
    ];

    public function calendarSource(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CalendarSource::class);
    }

    public function getDescriptionTextonlyAttribute()
    {
        $html = new Html2Text($this->description);
        return $html->getText();
    }
}
