<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->isProvider()) {
            return response()->json(['message' => 'Provider account required'], 403);
        }
        $methods = PaymentMethod::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();
        $transformed = $methods->map(function ($m) {
            return [
                'id' => $m->id,
                'type' => $m->type,
                'account_name' => $m->account_name,
                'account_number_masked' => $m->masked_account_number,
                'bank_name' => $m->bank_name,
                'mobile_provider' => $m->mobile_provider,
                'country' => $m->country,
                'is_default' => $m->is_default,
                'is_verified' => $m->is_verified,
                'verified_at' => $m->verified_at?->toISOString(),
                'created_at' => $m->created_at->toISOString(),
            ];
        });
        return response()->json(['data' => $transformed]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $this->authorize('create', PaymentMethod::class);
        $data = $request->validate([
            'type' => ['required', 'in:bank_account,mobile_money,paypal'],
            'account_name' => ['required', 'string', 'max:191'],
            'account_number' => ['required', 'string', 'max:191'],
            'bank_name' => ['nullable', 'required_if:type,bank_account', 'string', 'max:191'],
            'bank_code' => ['nullable', 'string', 'max:50'],
            'mobile_provider' => ['nullable', 'required_if:type,mobile_money', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'size:2'],
            'is_default' => ['nullable', 'boolean'],
        ]);
        $isFirstMethod = PaymentMethod::where('user_id', $user->id)->count() === 0;
        if ($isFirstMethod || ($data['is_default'] ?? false)) {
            PaymentMethod::where('user_id', $user->id)->update(['is_default' => false]);
            $data['is_default'] = true;
        }
        $method = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'bank_name' => $data['bank_name'] ?? null,
            'bank_code' => $data['bank_code'] ?? null,
            'mobile_provider' => $data['mobile_provider'] ?? null,
            'country' => $data['country'] ?? 'BJ',
            'is_default' => $data['is_default'] ?? $isFirstMethod,
        ]);
        return response()->json(['data' => $method], 201);
    }

    public function setDefault(Request $request, int $id)
    {
        $method = PaymentMethod::findOrFail($id);
        $this->authorize('update', $method);
        PaymentMethod::where('user_id', $request->user()->id)->update(['is_default' => false]);
        $method->update(['is_default' => true]);
        return response()->json(['data' => $method, 'message' => 'Default payment method updated']);
    }

    public function destroy(Request $request, int $id)
    {
        $method = PaymentMethod::findOrFail($id);
        $this->authorize('delete', $method);
        $method->delete();
        return response()->json(['message' => 'Payment method deleted']);
    }
}
