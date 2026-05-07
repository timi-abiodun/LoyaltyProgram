<?php

namespace App\Http\Controllers;

use App\Services\UserDashboardService;
use App\Models\User;
use App\Http\Resources\UserDashboardResource;

class UserDashboardController extends Controller
{
    public function __construct(
        protected UserDashboardService $userDashboardService
    ){}

    public function show(User $user): UserDashboardResource 
    {
        $data = $this->userDashboardService->handle($user);

        return new UserDashboardResource($data);
    }
}
