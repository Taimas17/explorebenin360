<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProviderApplicationController extends Controller
{
    public function apply(Request $request)
    {
        $user = $request->user();
        if ($user->provider_status !== 'none') {
            return response()->json(['message' => 'Application already submitted or processed'], 422);
        }
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'bio' => ['required', 'string', 'max:1000'],
            'kyc_documents' => ['nullable', 'array'],
            'kyc_documents.*' => ['url']
        ]);
        $user->update([
            'business_name' => $data['business_name'],
            'phone' => $data['phone'],
            'bio' => $data['bio'],
            'kyc_documents' => $data['kyc_documents'] ?? null,
            'kyc_submitted' => !empty($data['kyc_documents']),
            'provider_status' => 'pending',
        ]);
        return response()->json([
            'message' => 'Provider application submitted successfully',
            'data' => [
                'status' => 'pending'
            ]
        ], 201);
    }

    public function status(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'data' => [
                'provider_status' => $user->provider_status,
                'kyc_submitted' => (bool) $user->kyc_submitted,
                'kyc_verified' => (bool) $user->kyc_verified,
                'rejection_reason' => $user->provider_rejection_reason,
                'approved_at' => $user->provider_approved_at,
            ]
        ]);
    }
}
