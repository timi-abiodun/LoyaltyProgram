<?php

namespace App\Models;

use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'name', 'type', 'points_awarded', 'threshold', 
    ];

    protected function casts(): array
    {
        return [
            'points_awarded' => 'integer',
            'threshold'      => 'integer',
            'type'           => AchievementType::class,
        ];
    }
    

    // Relationships 
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                   ->withPivot('unlocked_at');
    }
}
