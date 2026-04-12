<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'copy_code',
        'inventory_code',
        'shelf_id',
        'shelf_column_id',
        'shelf_location',
        'condition',
        'received_date',
        'price',
        'is_available',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }

    public function shelfColumn()
    {
        return $this->belongsTo(ShelfColumn::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowing()
    {
        return $this->hasOne(Borrowing::class)->where('status', 'borrowed');
    }

    /**
     * Check if this copy is currently available for borrowing.
     */
    public function getIsLentAttribute(): bool
    {
        return $this->borrowings()->where('status', 'borrowed')->exists();
    }

    /**
     * Get display status.
     */
    public function getStatusAttribute(): string
    {
        if ($this->condition === 'hilang') return 'Hilang';
        if ($this->condition === 'rusak') return 'Rusak';
        if ($this->is_lent) return 'Dipinjam';
        return 'Tersedia';
    }

    /**
     * Get status color for badges.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Tersedia' => 'green',
            'Dipinjam' => 'blue',
            'Rusak' => 'yellow',
            'Hilang' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get shelf display name.
     */
    public function getShelfDisplayAttribute(): ?string
    {
        if ($this->shelf) {
            $display = $this->shelf->code . ' - ' . $this->shelf->name;
            if ($this->shelfColumn) {
                $display .= ' / Kolom ' . $this->shelfColumn->name;
            }
            return $display;
        }
        return $this->shelf_location;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('condition', 'baik')
                     ->where('is_available', true)
                     ->whereDoesntHave('borrowings', fn($q) => $q->where('status', 'borrowed'));
    }

    public function scopeCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('copy_code', 'like', "%{$search}%")
              ->orWhere('inventory_code', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }
}
