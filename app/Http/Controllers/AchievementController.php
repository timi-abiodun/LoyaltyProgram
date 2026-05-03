<?php

namespace App\Http\Controllers;

use App\Services\UserDashboardService;
use App\Models\User;
use App\Http\Resources\AchievementResource;

class AchievementController extends Controller
{
    public function __construct(protected UserDashboardService $userDashboardService){}

    public function show(User $user): AchievementResource {
        $data = $this->userDashboardService->handle($user);

        return new AchievementResource($data);
    }
}
