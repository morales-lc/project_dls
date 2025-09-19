<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlinetAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefix',
        'firstname',
        'lastname',
        'email',
        'strand_course',
        'institution_college',
        'appointment_date',
        'services',
        'status',
    ];

    protected $casts = [
        'services' => 'array',
        'appointment_date' => 'date',
        'status' => 'string',
    ];

    // Scope: filter by status
    public function scopeStatus($query, ?string $status)
    {
        if (!empty($status)) {
            $query->where('status', $status);
        }
        return $query;
    }

    // Scope: simple search on name and email
    public function scopeSearch($query, ?string $term)
    {
        if (!empty($term)) {
            $like = '%' . $term . '%';
            $query->where(function ($q) use ($like) {
                $q->where('firstname', 'like', $like)
                  ->orWhere('lastname', 'like', $like)
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", [$like])
                  ->orWhere('email', 'like', $like)
                  ->orWhere('strand_course', 'like', $like)
                  ->orWhere('institution_college', 'like', $like);
            });
        }
        return $query;
    }

    // Scope: date between
    public function scopeDateBetween($query, ?string $from, ?string $to)
    {
        if (!empty($from)) {
            $query->whereDate('appointment_date', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('appointment_date', '<=', $to);
        }
        return $query;
    }

    // Scope: filter by service contained in JSON array
    public function scopeService($query, ?string $service)
    {
        if (!empty($service)) {
            $query->whereJsonContains('services', $service);
        }
        return $query;
    }
}
