<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'reader_type',
        'reader_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function reader()
    {
        return $this->morphTo();
    }

    public static function markAsRead($announcementId, $reader)
    {
        return self::firstOrCreate([
            'announcement_id' => $announcementId,
            'reader_type' => get_class($reader),
            'reader_id' => $reader->id,
        ], [
            'read_at' => now(),
        ]);
    }

    public static function hasRead($announcementId, $reader)
    {
        return self::where('announcement_id', $announcementId)
            ->where('reader_type', get_class($reader))
            ->where('reader_id', $reader->id)
            ->exists();
    }
}