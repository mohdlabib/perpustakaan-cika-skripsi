<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'location',
        'description',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function columns()
    {
        return $this->hasMany(ShelfColumn::class);
    }

    public function books()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function bookCopies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function getBooksCountAttribute()
    {
        return $this->bookCopies()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
}

