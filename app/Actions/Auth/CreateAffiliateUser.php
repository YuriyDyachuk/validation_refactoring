<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use App\Enums\WalletType;

class CreateAffiliateUser
{
    public static function run(User $user): void
    {
        if (!$user->affiliate()->exists()) {
            $user->affiliate()->create();
            // Todo: create wallet
            $user->wallet()->create(['type' => WalletType::AFFILIATE]);
        }
    }
}
