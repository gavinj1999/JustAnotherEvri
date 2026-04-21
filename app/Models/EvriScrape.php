<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvriScrape extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'round',
        'earnings',
        'parcel_count',
        'execution_time',
        'status',
        'raw_output',
        'execution_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'earnings' => 'decimal:2',
            'execution_time' => 'decimal:2',
            'parcel_count' => 'integer',
        ];
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): void
    {
        $query->whereIn('status', ['failed', 'timeout']);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): void
    {
        $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
