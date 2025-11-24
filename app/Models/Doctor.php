<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'specialty', 'bio', 'photo', 
        'email', 'phone', 'is_active', 'user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Route Model Binding por slug
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Generar slug automáticamente
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($doctor) {
            if (empty($doctor->slug)) {
                $doctor->slug = Str::slug($doctor->name);
            }
        });
    }

    // Relaciones
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}