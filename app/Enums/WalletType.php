<?php

namespace App\Enums;

enum WalletType: string
{
    case BTC = 'BTC';
    case ETH = 'ETH';
    case USDT = 'USDT';
    case AFFILIATE = 'AFFILIATE';

    public static function all(): array
    {
        return [
            self::BTC,
            self::ETH,
            self::USDT
        ];
    }
}
