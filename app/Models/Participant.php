<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'uuid',
        'name',
        'phone',
        'nip',
        'position',
        'work_unit',
        'signature',
        'attendance_status',
        'attended_at',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($participant) {
            if (empty($participant->uuid)) {
                $participant->uuid = (string) Str::uuid();
            }
        });
    }

    // Tambahkan relasi ini agar Filament mengenalinya
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}