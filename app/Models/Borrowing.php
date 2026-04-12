<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    protected $fillable = [
        'student_nis',
        'book_id',
        'book_copy_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'approved_at',
        'approved_by',
        'rejected_reason',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
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
     * Get the specific book copy that was borrowed.
     */
    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    /**
     * Get the admin who approved/rejected.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if this borrowing is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'borrowed' && $this->due_date && $this->due_date->isPast();
    }

    /**
     * Get days until due/overdue.
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'borrowed' || !$this->due_date) {
            return 0;
        }
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Scope for pending approval.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
     * Scope for rejected borrowings.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
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
     * Approve the borrowing request.
     */
    public function approve($dueDate, $adminId): void
    {
        $this->update([
            'status' => 'borrowed',
            'borrow_date' => now(),
            'due_date' => $dueDate,
            'approved_at' => now(),
            'approved_by' => $adminId,
        ]);
    }

    /**
     * Reject the borrowing request.
     */
    public function reject($reason, $adminId): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejected_reason' => $reason,
        ]);
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

