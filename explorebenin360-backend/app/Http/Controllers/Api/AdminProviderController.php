<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ProviderStatusNotification;

class AdminProviderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $status = $request->get('status', 'pending');
        $providers = User::where('provider_status', $status)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $providers->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'business_name' => $u->business_name,
                    'phone' => $u->phone,
                    'bio' => $u->bio,
                    'provider_status' => $u->provider_status,
                    'kyc_submitted' => (bool) $u->kyc_submitted,
                    'kyc_verified' => (bool) $u->kyc_verified,
                    'kyc_documents' => $u->kyc_documents,
                    'created_at' => $u->created_at,
                ];
            })
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $user = User::findOrFail($id);
        $user->update([
            'provider_status' => 'approved',
            'kyc_verified' => true,
            'provider_approved_at' => now(),
        ]);
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('provider');
        }
        $user->notify(new ProviderStatusNotification('approved'));
        return response()->json(['message' => 'Provider approved successfully']);
    }

    public function reject(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500']
        ]);
        $user = User::findOrFail($id);
        $user->update([
            'provider_status' => 'rejected',
            'provider_rejection_reason' => $data['reason'],
        ]);
        $user->notify(new ProviderStatusNotification('rejected', $data['reason']));
        return response()->json(['message' => 'Provider rejected']);
    }
}
