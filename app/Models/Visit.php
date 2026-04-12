<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'visitor_type',
        'student_nis',
        'guest_name',
        'guest_institution',
        'guest_purpose',
        'visited_at',
        'scan_token',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Get the student who visited (null for guests).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_nis', 'nis');
    }

    /**
     * Get visitor name regardless of type.
     */
    public function getVisitorNameAttribute(): string
    {
        if ($this->visitor_type === 'guest') {
            return $this->guest_name ?? 'Tamu';
        }
        return $this->student->name ?? 'Siswa #' . $this->student_nis;
    }

    /**
     * Get visitor detail (class/institution).
     */
    public function getVisitorDetailAttribute(): string
    {
        if ($this->visitor_type === 'guest') {
            return $this->guest_institution ?? '-';
        }
        return $this->student->grade->name ?? ($this->student->class ?? '-');
    }

    /**
     * Check if student already visited today.
     */
    public static function hasVisitedToday(string $nis): bool
    {
        return static::where('student_nis', $nis)
            ->whereDate('visited_at', today())
            ->exists();
    }

    /**
     * Record a student visit.
     */
    public static function recordVisit(string $nis, ?string $token = null): static
    {
        return static::create([
            'visitor_type' => 'student',
            'student_nis' => $nis,
            'visited_at' => now(),
            'scan_token' => $token,
        ]);
    }

    /**
     * Record a guest visit.
     */
    public static function recordGuestVisit(string $name, ?string $institution = null, ?string $purpose = null): static
    {
        return static::create([
            'visitor_type' => 'guest',
            'guest_name' => $name,
            'guest_institution' => $institution,
            'guest_purpose' => $purpose,
            'visited_at' => now(),
        ]);
    }

    /**
     * Scope for today's visits.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    /**
     * Scope for this week's visits.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('visited_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope for this month's visits.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
                     ->whereYear('visited_at', now()->year);
    }

    /**
     * Scope for student visitors only.
     */
    public function scopeStudents($query)
    {
        return $query->where('visitor_type', 'student');
    }

    /**
     * Scope for guest visitors only.
     */
    public function scopeGuests($query)
    {
        return $query->where('visitor_type', 'guest');
    }
}
