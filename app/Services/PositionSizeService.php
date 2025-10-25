<?php

namespace App\Services;

class PositionSizeService
{
   // pip valuee
    public static function getPipValue(string $pair): float
    {
        $pair = strtoupper($pair);

        $map9_1 = ['USDJPY', 'EURJPY', 'GBPJPY'];
        $map10 = ['EURUSD', 'GBPUSD', 'AUDUSD', 'NZDUSD', 'XAUUSD'];

        if (in_array($pair, $map9_1)) {
            return 9.1;
        }

        if (in_array($pair, $map10)) {
            return 10.0;
        }

        return 10.0;
    }

    
    public static function calculate(
        float $balance,
        float $riskPercent,
        float $stopLoss,
        string $pair,
        string $accountCurrency = 'USD',
        ?float $exchangeRateToUSD = null
    ): array {
        $accountCurrency = strtoupper($accountCurrency);

        // chuyển đổi tỉ giá sang usd
        if ($accountCurrency !== 'USD' && $exchangeRateToUSD !== null) {
            $balanceUsd = $balance * $exchangeRateToUSD;
        } else {
            $balanceUsd = $balance;
        }

        // Tính Risk Amount 
        $riskAmountUsd = $balanceUsd * ($riskPercent / 100.0);

        // pip value
        $pipValue = self::getPipValue($pair);

        // position size 
        $positionSize = $stopLoss > 0
            ? ($riskAmountUsd / ($stopLoss * $pipValue))
            : 0;

        return [
            'balance_usd' => round($balanceUsd, 2),
            'risk_amount_usd' => round($riskAmountUsd, 2),
            'pip_value' => $pipValue,
            'position_size' => round($positionSize, 4),
        ];
    }
}
