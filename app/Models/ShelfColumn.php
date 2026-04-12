<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShelfColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'shelf_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }

    public function books()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function bookCopies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullNameAttribute()
    {
        return $this->shelf ? "{$this->shelf->code} - Kolom {$this->name}" : "Kolom {$this->name}";
    }
}
