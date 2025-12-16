<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students_registry';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'name',
        'class',
        'grade_id',
        'phone',
        'photo',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'student_nis', 'nis');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'student_nis', 'nis');
    }

    public function activeBorrowings()
    {
        return $this->borrowings()->where('status', 'borrowed');
    }

    public function canBorrow(): bool
    {
        return $this->activeBorrowings()->count() < 3;
    }

    public function borrowingCount(): int
    {
        return $this->activeBorrowings()->count();
    }

    public function visitCountThisMonth(): int
    {
        return $this->visits()
            ->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year)
            ->count();
    }
}
