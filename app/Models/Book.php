<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'isbn',
        'title',
        'author',
        'category_id',
        'publisher',
        'publication_year',
        'shelf_location',
        'stock',
        'cover_image',
        'description',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'publication_year' => 'integer',
        'stock' => 'integer',
    ];

    /**
     * Get the category of the book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all borrowings for this book.
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Get active borrowings.
     */
    public function activeBorrowings(): HasMany
    {
        return $this->borrowings()->where('status', 'borrowed');
    }

    /**
     * Calculate available stock (stock - active borrowings).
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->activeBorrowings()->count());
    }

    /**
     * Check if book is available for borrowing.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->available_stock > 0;
    }

    /**
     * Get total times borrowed.
     */
    public function getTotalBorrowedAttribute(): int
    {
        return $this->borrowings()->count();
    }

    /**
     * Scope for available books.
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope for search.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('author', 'like', "%{$term}%")
              ->orWhere('isbn', 'like', "%{$term}%");
        });
    }
}
