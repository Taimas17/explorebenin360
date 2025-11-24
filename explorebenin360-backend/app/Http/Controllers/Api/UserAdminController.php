<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\AccountBannedNotification;
use App\Notifications\AccountSuspendedNotification;
use App\Notifications\AccountUnsuspendedNotification;
use App\Notifications\RolesUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $q = (string) $request->get('q', '');
        $role = $request->get('role');
        $accountStatus = $request->get('account_status');
        $providerStatus = $request->get('provider_status');
        $sort = $request->get('sort', 'recent');
        $perPage = (int) ($request->get('per_page', 20));

        $query = User::query()
            ->with(['roles:id,name'])
            ->withCount(['bookings','favorites','offerings']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }
        if ($role) {
            $query->whereHas('roles', fn($qr) => $qr->where('name', $role));
        }
        if ($accountStatus) {
            $query->where('account_status', $accountStatus);
        }
        if ($providerStatus) {
            $query->where('provider_status', $providerStatus);
        }

        if ($sort === 'name') $query->orderBy('name');
        elseif ($sort === 'email') $query->orderBy('email');
        elseif ($sort === 'login_count') $query->orderByDesc('login_count');
        else $query->orderByDesc('created_at');

        $users = $query->paginate($perPage);

        $data = array_map(fn($u) => (new UserResource($u))->toArray($request), $users->items());
        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = User::with(['roles', 'suspendedBy:id,name'])
            ->withCount(['bookings','favorites','offerings'])
            ->findOrFail($id);
        $this->authorize('view', $user);

        return response()->json(['data' => (new UserResource($user))->toArray($request)]);
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255','unique:users,email,'.$user->id],
            'phone' => ['nullable','string','max:20'],
            'business_name' => ['nullable','string','max:255'],
            'bio' => ['nullable','string','max:2000'],
        ]);

        $user->fill($data);
        $user->save();

        return response()->json(['data' => (new UserResource($user->fresh(['roles'])))->toArray($request) ]);
    }

    public function destroy(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Cannot delete an admin user'], 422);
        }
        if ($user->hasRole('provider')) {
            $hasActive = Booking::where('user_id', $user->id)
                ->whereIn('status', ['pending','authorized','confirmed'])
                ->exists();
            if ($hasActive) {
                return response()->json(['message' => 'Cannot delete a provider with active bookings'], 422);
            }
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function suspend(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('suspend', $user);

        $data = $request->validate([
            'reason' => ['required','string','max:1000'],
            'duration_days' => ['nullable','integer','min:1','max:365'],
        ]);

        $user->update([
            'account_status' => 'suspended',
            'suspended_at' => now(),
            'suspended_by' => $request->user()->id,
            'suspension_reason' => $data['reason'],
        ]);

        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
        $user->notify(new AccountSuspendedNotification($data['reason']));

        return response()->json(['message' => 'User suspended']);
    }

    public function unsuspend(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('suspend', $user);

        $user->update([
            'account_status' => 'active',
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ]);

        $user->notify(new AccountUnsuspendedNotification());
        return response()->json(['message' => 'User unsuspended']);
    }

    public function ban(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('suspend', $user);

        $data = $request->validate([
            'reason' => ['required','string','max:1000'],
        ]);

        $user->update([
            'account_status' => 'banned',
            'suspended_at' => now(),
            'suspended_by' => $request->user()->id,
            'suspension_reason' => $data['reason'],
        ]);

        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending','authorized','confirmed'])
            ->update([
                'status' => 'cancelled',
                'cancel_reason' => 'Cancelled due to account ban',
            ]);

        $user->notify(new AccountBannedNotification($data['reason']));
        return response()->json(['message' => 'User banned']);
    }

    public function updateRoles(Request $request, int $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $this->authorize('manageRoles', $user);

        $data = $request->validate([
            'roles' => ['required','array'],
            'roles.*' => ['string','exists:roles,name'],
        ]);

        $before = $user->roles->pluck('name')->toArray();
        $user->syncRoles($data['roles']);
        $user->refresh();

        $after = $user->roles->pluck('name')->toArray();
        $addedProvider = in_array('provider', $after, true) && !in_array('provider', $before, true);
        $removedProvider = !in_array('provider', $after, true) && in_array('provider', $before, true);

        if ($addedProvider && $user->provider_status === 'none') {
            $user->provider_status = 'approved';
            $user->save();
        }
        if ($removedProvider) {
            $user->provider_status = 'none';
            $user->save();
        }

        $user->notify(new RolesUpdatedNotification($after));
        return response()->json(['message' => 'Roles updated', 'data' => ['roles' => $user->roles->pluck('name')]]);
    }

    public function resetPassword(Request $request, int $id)
    {
        $auth = $request->user();
        if (!$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $user = User::findOrFail($id);

        Password::sendResetLink(['email' => $user->email]);
        return response()->json(['message' => 'Password reset email sent']);
    }
}
