<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'student_nis',
        'visited_at',
        'scan_token',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Get the student who visited.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_nis', 'nis');
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
     * Record a new visit.
     */
    public static function recordVisit(string $nis, ?string $token = null): static
    {
        return static::create([
            'student_nis' => $nis,
            'visited_at' => now(),
            'scan_token' => $token,
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
}
