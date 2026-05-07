<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ){}

    public function store(StorePurchaseRequest $request): JsonResponse {
        $user = $request->user();
        $amount = $request->validated('amount');
        
        $this->purchaseService->completePurchase($user, $amount);

        return response()->json(['message' => 'Purchase completed successfully.'], 201);
    }
}
