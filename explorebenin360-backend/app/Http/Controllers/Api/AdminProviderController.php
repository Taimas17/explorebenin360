<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ProviderApproved;
use App\Mail\ProviderRejected;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        Mail::to($user->email)->queue(new ProviderApproved($user));
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
        Mail::to($user->email)->queue(new ProviderRejected($user, $data['reason']));
        return response()->json(['message' => 'Provider rejected']);
    }

    /**
     * Voir les documents KYC d'un provider
     * GET /api/v1/admin/providers/{id}/kyc-documents
     */
    public function viewKYCDocuments(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $provider = User::findOrFail($id);

        return response()->json([
            'data' => [
                'documents' => $provider->kyc_documents ?? [],
                'kyc_submitted' => (bool) $provider->kyc_submitted,
                'kyc_verified' => (bool) $provider->kyc_verified,
                'business_name' => $provider->business_name,
                'phone' => $provider->phone,
                'bio' => $provider->bio,
            ]
        ]);
    }

    /**
     * Suspendre un provider
     * PATCH /api/v1/admin/providers/{id}/suspend
     */
    public function suspend(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500']
        ]);

        $provider = User::findOrFail($id);

        $provider->update([
            'provider_status' => 'suspended',
            'provider_rejection_reason' => $data['reason'],
        ]);

        if (method_exists($provider, 'removeRole')) {
            $provider->removeRole('provider');
        }

        // TODO: Send suspension email/notification if needed

        return response()->json(['message' => 'Provider suspended']);
    }

    /**
     * Réactiver un provider
     * PATCH /api/v1/admin/providers/{id}/reactivate
     */
    public function reactivate(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $provider = User::findOrFail($id);

        $provider->update([
            'provider_status' => 'approved',
            'provider_rejection_reason' => null,
        ]);

        if (method_exists($provider, 'assignRole')) {
            $provider->assignRole('provider');
        }

        return response()->json(['message' => 'Provider reactivated']);
    }
}
