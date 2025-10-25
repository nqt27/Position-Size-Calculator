<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateRequest;
use App\Models\Calculation;
use App\Services\PositionSizeService;
use Illuminate\Http\Request;

class CalculationController extends Controller
{
    
    public function index()
    {
        //hiển thị 10 dòng mỗi trang
        $history = Calculation::orderBy('created_at', 'desc')->paginate(10)->withPath('/');

        $result = null;

        return view('calculator.index', compact('history', 'result'));
    }
    //xóa
    public function destroy($id)
    {
        //id dòng cần xóa
        $calculation = Calculation::find($id);
        if (!$calculation) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi!']);
        }

        $calculation->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa bản ghi thành công!']);
    }

    // xử lý số liệu
    public function calculate(CalculateRequest $request)
    {
        $data = $request->validated();

        $balance = (float) $data['balance'];
        $riskPercent = (float) $data['risk_percent'];
        $stopLoss = (float) $data['stop_loss'];
        $pair = strtoupper($data['pair']);
        $accountCurrency = $data['account_currency'] ?? 'USD';

        //chuyển đổi tỉ giá
        $exchangeRates = [
            'EUR' => 1.1,
            'GBP' => 1.25,
        ];

        $exchangeRateToUSD = null;
        if (strtoupper($accountCurrency) !== 'USD') {
            $exchangeRateToUSD = $exchangeRates[strtoupper($accountCurrency)] ?? null;
        }

        $calc = PositionSizeService::calculate(
            $balance,
            $riskPercent,
            $stopLoss,
            $pair,
            $accountCurrency,
            $exchangeRateToUSD
        );

        // lưu vào DB
        $saved = Calculation::create([
            'balance' => $calc['balance_usd'],
            'risk_percent' => $riskPercent,
            'stop_loss' => $stopLoss,
            'pair' => $pair,
            'risk_amount_usd' => $calc['risk_amount_usd'],
            'pip_value' => $calc['pip_value'],
            'position_size' => $calc['position_size'],
            'account_currency' => $accountCurrency,
        ]);

        // hiển thị kết quả
        $result = [
            'balance' => number_format($calc['balance_usd'], 2),
            'risk_percent' => $riskPercent,
            'stop_loss' => $stopLoss,
            'pair' => $pair,
            'risk_amount_usd' => number_format($calc['risk_amount_usd'], 2) . ' USD',
            'pip_value' => $calc['pip_value'] . ' USD',
            'position_size' => $calc['position_size'],
        ];

        // hiển thị danh sách lịch sử
        $history = Calculation::orderBy('created_at', 'desc')->paginate(10)->withPath('/');

        return view('calculator.index', compact('history', 'result'));
    }

    // API

    // POST /api/calc
    public function apiCalc(CalculateRequest $request)
    {
        $data = $request->validated();

        $balance = (float) $data['balance'];
        $riskPercent = (float) $data['risk_percent'];
        $stopLoss = (float) $data['stop_loss'];
        $pair = strtoupper($data['pair']);
        $accountCurrency = $data['account_currency'] ?? 'USD';

        $exchangeRates = [
            'EUR' => 1.1,
            'GBP' => 1.25,
        ];

        $exchangeRateToUSD = null;
        if (strtoupper($accountCurrency) !== 'USD') {
            $exchangeRateToUSD = $exchangeRates[strtoupper($accountCurrency)] ?? null;
        }

        $calc = PositionSizeService::calculate(
            $balance,
            $riskPercent,
            $stopLoss,
            $pair,
            $accountCurrency,
            $exchangeRateToUSD
        );

        // lưu vào DB
        $saved = Calculation::create([
            'balance' => $calc['balance_usd'],
            'risk_percent' => $riskPercent,
            'stop_loss' => $stopLoss,
            'pair' => $pair,
            'risk_amount_usd' => $calc['risk_amount_usd'],
            'pip_value' => $calc['pip_value'],
            'position_size' => $calc['position_size'],
            'account_currency' => $accountCurrency,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $calc['balance_usd'],
                'risk_percent' => $riskPercent,
                'stop_loss' => $stopLoss,
                'pair' => $pair,
                'risk_amount_usd' => $calc['risk_amount_usd'],
                'pip_value' => $calc['pip_value'],
                'position_size' => $calc['position_size'],
            ]
        ]);
    }

    // GET /api/history
    public function apiHistory()
    {
        $history = Calculation::orderBy('created_at', 'desc')->get();
        return response()->json($history);
    }
}
