<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class UserDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'achievements' => [
                'unlocked_achievements' => $this->resource['unlocked_achievements'],
                'next_available_achievements' => $this->resource['next_available_achievements'],
            ],
            'badges' => [
                'current_badge' => $this->resource['current_badge'],
                'next_badge' => $this->resource['next_badge'],
                'remaining_to_unlock_next_badge' => (int) $this->resource['remaining_to_unlock_next_badge'],
            ],            
            'meta' => [
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
