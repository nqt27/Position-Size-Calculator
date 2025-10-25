<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'balance' => ['required', 'numeric', 'min:0'],
            'risk_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'stop_loss' => ['required', 'numeric', 'gt:0'],
            'pair' => ['required', 'string'],
            'account_currency' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'balance.required' => 'Vui lòng nhập số dư tài khoản.',
            'balance.numeric' => 'Số dư phải là số.',
            'risk_percent.required' => 'Vui lòng nhập Risk %.',
            'risk_percent.numeric' => 'Risk % phải là số.',
            'risk_percent.min' => 'Risk % phải lớn hơn hoặc bằng 0.',
            'risk_percent.max' => 'Risk % phải nhỏ hơn hoặc bằng 100.',
            'stop_loss.required' => 'Vui lòng nhập Stop Loss (pips).',
            'stop_loss.gt' => 'Stop Loss phải lớn hơn 0.',
            'pair.required' => 'Vui lòng chọn cặp tiền.',
        ];
    }
}
