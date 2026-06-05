<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
     * Get visited_at formatted in WIB timezone.
     */
    public function getVisitedAtWibAttribute(): string
    {
        if (!$this->visited_at) return '-';
        return $this->visited_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB';
    }

    /**
     * Check if student already visited today (WIB timezone).
     */
    public static function hasVisitedToday(string $nis): bool
    {
        return static::where('student_nis', $nis)
            ->whereDate('visited_at', Carbon::today('Asia/Jakarta'))
            ->exists();
    }

    /**
     * Record a student visit with explicit WIB timezone.
     */
    public static function recordVisit(string $nis, ?string $token = null): static
    {
        return static::create([
            'visitor_type' => 'student',
            'student_nis' => $nis,
            'visited_at' => Carbon::now('Asia/Jakarta'),
            'scan_token' => $token,
        ]);
    }

    /**
     * Record a guest visit with explicit WIB timezone.
     */
    public static function recordGuestVisit(string $name, ?string $institution = null, ?string $purpose = null): static
    {
        return static::create([
            'visitor_type' => 'guest',
            'guest_name' => $name,
            'guest_institution' => $institution,
            'guest_purpose' => $purpose,
            'visited_at' => Carbon::now('Asia/Jakarta'),
        ]);
    }

    /**
     * Scope for today's visits (WIB timezone).
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', Carbon::today('Asia/Jakarta'));
    }

    /**
     * Scope for this week's visits (WIB timezone).
     */
    public function scopeThisWeek($query)
    {
        $now = Carbon::now('Asia/Jakarta');
        return $query->whereBetween('visited_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        ]);
    }

    /**
     * Scope for this month's visits (WIB timezone).
     */
    public function scopeThisMonth($query)
    {
        $now = Carbon::now('Asia/Jakarta');
        return $query->whereMonth('visited_at', $now->month)
                     ->whereYear('visited_at', $now->year);
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
