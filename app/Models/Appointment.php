<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'doctor_id', 'patient_name', 'patient_email', 
        'patient_phone', 'reason', 'appointment_date', 
        'duration_minutes', 'status', 'notes'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    // IMPORTANTE: Comentar o eliminar este método para usar ID
    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($appointment) {
            if (empty($appointment->slug)) {
                $appointment->slug = Str::random(10);
            }
            if (empty($appointment->duration_minutes)) {
                $appointment->duration_minutes = config('app.appointment_duration', 20);
            }
        });
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}