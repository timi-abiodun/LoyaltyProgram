<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseStatus::class,
            'amount' => 'integer',
        ];
    }

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
