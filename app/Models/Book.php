<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'edition',
        'category_id',
        'publisher',
        'publication_year',
        'publication_place',
        'description',
        'physical_description',
        'classification',
        'call_number',
        'cover_image',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'publication_year' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowings()
    {
        return $this->borrowings()->where('status', 'borrowed');
    }

    /**
     * Get total stock (all copies excluding 'hilang').
     */
    public function getStockAttribute(): int
    {
        return $this->copies()->where('condition', '!=', 'hilang')->count();
    }

    /**
     * Get available stock (copies not currently borrowed and in good condition).
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->copies()
            ->where('condition', 'baik')
            ->where('is_available', true)
            ->whereDoesntHave('borrowings', fn($q) => $q->where('status', 'borrowed'))
            ->count();
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_stock > 0;
    }

    public function scopeAvailable($query)
    {
        return $query->whereHas('copies', function ($q) {
            $q->where('condition', 'baik')
              ->where('is_available', true)
              ->whereDoesntHave('borrowings', fn($bq) => $bq->where('status', 'borrowed'));
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('call_number', 'like', "%{$search}%")
              ->orWhereHas('copies', function ($cq) use ($search) {
                  $cq->where('copy_code', 'like', "%{$search}%")
                    ->orWhere('inventory_code', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Get the cover image URL with correct path for hosting compatibility
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }
        
        return \Illuminate\Support\Facades\Storage::url($this->cover_image);
    }
}
