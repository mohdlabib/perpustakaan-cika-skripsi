<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'item_code',
        'title',
        'author',
        'edition',
        'category_id',
        'shelf_id',
        'shelf_column_id',
        'publisher',
        'publication_year',
        'publication_place',
        'description',
        'physical_description',
        'classification',
        'call_number',
        'inventory_code',
        'shelf_location',
        'received_date',
        'price',
        'stock',
        'cover_image',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'publication_year' => 'integer',
        'stock' => 'integer',
        'price' => 'decimal:2',
        'received_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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

    public function activeBorrowings()
    {
        return $this->borrowings()->where('status', 'borrowed');
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->stock - $this->activeBorrowings()->count();
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_stock > 0;
    }

    public function scopeAvailable($query)
    {
        return $query->whereRaw('stock > (SELECT COUNT(*) FROM borrowings WHERE borrowings.book_id = books.id AND borrowings.status = "borrowed")');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('item_code', 'like', "%{$search}%")
              ->orWhere('call_number', 'like', "%{$search}%")
              ->orWhere('inventory_code', 'like', "%{$search}%");
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
        
        // Always use /storage/app/public/ path for hosting compatibility
        return url('/storage/app/public/' . $this->cover_image);
    }
}
