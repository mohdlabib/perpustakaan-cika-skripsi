<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    protected $fillable = [
        'student_nis',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    /**
     * Get the student who borrowed.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_nis', 'nis');
    }

    /**
     * Get the borrowed book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if this borrowing is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'borrowed' && $this->due_date->isPast();
    }

    /**
     * Get days until due/overdue.
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'borrowed') {
            return 0;
        }
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Scope for active borrowings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'borrowed');
    }

    /**
     * Scope for returned borrowings.
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope for overdue borrowings.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'borrowed')
                     ->where('due_date', '<', now());
    }

    /**
     * Mark as returned.
     */
    public function markAsReturned(): void
    {
        $this->update([
            'status' => 'returned',
            'return_date' => now(),
        ]);
    }
}
