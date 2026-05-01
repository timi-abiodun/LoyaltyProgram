<?php

namespace App\Enums;

enum AchievementType: string
{
    case AMOUNT_SPENT = 'amount_spent';
    case PURCHASES_COUNT = 'purchases_count';
}