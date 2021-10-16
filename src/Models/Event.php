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
        'last_crawled_at',
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

    public function getDescriptionTextonlyAttribute(): string
    {
        $html = new Html2Text($this->description);

        return $html->getText();
    }

    public function getTitleWithSourceAttribute(): string
    {
        return sprintf('%s (%s)', $this->title, $this->calendarSource->name);
    }

    public function getUidAttribute(): string
    {
        return sprintf('%s-%s', e($this->source_internal_id), e($this->calendarSource->location));
    }
}
