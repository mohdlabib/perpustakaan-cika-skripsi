<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'students_registry';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'name',
        'class',
        'phone',
        'photo',
    ];

    /**
     * Get all borrowings for the student.
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'student_nis', 'nis');
    }

    /**
     * Get all visits for the student.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'student_nis', 'nis');
    }

    /**
     * Get active borrowings (not returned).
     */
    public function activeBorrowings(): HasMany
    {
        return $this->borrowings()->where('status', 'borrowed');
    }

    /**
     * Check if student can borrow more books (max 3 active).
     */
    public function canBorrow(): bool
    {
        return $this->activeBorrowings()->count() < 3;
    }

    /**
     * Get remaining borrow slots.
     */
    public function getRemainingBorrowSlotsAttribute(): int
    {
        return max(0, 3 - $this->activeBorrowings()->count());
    }

    /**
     * Get total visits count.
     */
    public function getTotalVisitsAttribute(): int
    {
        return $this->visits()->count();
    }
}
