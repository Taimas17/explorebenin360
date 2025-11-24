<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\Media;
use App\Models\Offering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\MediaStorage\MediaStorage;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordChangedNotification;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()
            ->load('roles')
            ->loadCount(['bookings as bookings_count', 'favorites as favorites_count'])
        ;
        if ($user->isProvider()) {
            $user->loadCount(['offerings as offerings_count']);
        }
        return response()->json(['data' => new UserResource($user)]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone' => ['nullable','string','max:20'],
            'avatar_url' => ['nullable','url','max:500'],
            'cover_image_url' => ['nullable','url','max:500'],
            'date_of_birth' => ['nullable','date','before:today','after:1900-01-01'],
            'gender' => ['nullable', Rule::in(['male','female','other','prefer_not_to_say'])],
            'country' => ['nullable','string','max:100'],
            'city' => ['nullable','string','max:100'],
            'address' => ['nullable','string','max:500'],
            'postal_code' => ['nullable','string','max:20'],
            'website_url' => ['nullable','url','max:500'],
            'social_links' => ['nullable','array'],
            'social_links.facebook' => ['nullable','url'],
            'social_links.instagram' => ['nullable','url'],
            'social_links.twitter' => ['nullable','url'],
            'social_links.linkedin' => ['nullable','url'],
            'preferences' => ['nullable','array'],
            'preferences.language' => ['nullable', Rule::in(['fr','en'])],
            'preferences.currency' => ['nullable', Rule::in(['XOF','EUR','USD'])],
            'preferences.notifications_enabled' => ['nullable','boolean'],
            'about_me' => ['nullable','string','max:1000'],
            'business_name' => ['sometimes','nullable','string','max:255'],
            'bio' => ['sometimes','nullable','string','max:2000'],
        ]);

        $emailChanged = array_key_exists('email', $data) && $data['email'] !== $user->email;

        $user->fill($data);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        $user->save();

        if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        $user->load('roles')
            ->loadCount(['bookings as bookings_count', 'favorites as favorites_count']);
        if ($user->isProvider()) {
            $user->loadCount(['offerings as offerings_count']);
        }

        return response()->json(['data' => new UserResource($user)]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => ['required','string'],
            'password' => ['required','string','min:8','confirmed'],
            'password_confirmation' => ['required'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        try { $user->notify(new PasswordChangedNotification()); } catch (\Throwable $e) {}

        $currentTokenId = $user->currentAccessToken()?->id;
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->when($currentTokenId, fn($q) => $q->where('id','!=', $currentTokenId))
            ->delete();

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function uploadAvatar(Request $request, MediaStorage $storage)
    {
        $user = $request->user();
        $data = $request->validate([
            'file' => ['required','image','max:5120','mimes:jpg,jpeg,png,webp'],
        ]);

        $upload = $storage->upload($request->file('file'), []);

        $oldUrl = $user->avatar_url;

        $media = Media::create([
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id,
            'type' => 'image',
            'url' => $upload['url'],
            'provider' => $upload['provider'] ?? null,
            'alt' => $user->name . ' avatar',
            'width' => $upload['width'] ?? null,
            'height' => $upload['height'] ?? null,
            'size_bytes' => $upload['size_bytes'] ?? null,
            'mime' => $upload['mime'] ?? null,
            'metadata_json' => $upload['metadata'] ?? [],
            'created_by' => $user->id,
        ]);

        $user->avatar_url = $media->url;
        $user->save();

        if ($oldUrl) {
            Media::where('model_type', \App\Models\User::class)
                ->where('model_id', $user->id)
                ->where('url', $oldUrl)
                ->delete();
        }

        return response()->json(['data' => ['avatar_url' => $media->url]]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        $oldUrl = $user->avatar_url;
        $user->avatar_url = null;
        $user->save();
        if ($oldUrl) {
            Media::where('model_type', \App\Models\User::class)
                ->where('model_id', $user->id)
                ->where('url', $oldUrl)
                ->delete();
        }
        return response()->json(['message' => 'Avatar deleted']);
    }

    public function uploadCover(Request $request, MediaStorage $storage)
    {
        $user = $request->user();
        $data = $request->validate([
            'file' => ['required','image','max:5120','mimes:jpg,jpeg,png,webp'],
        ]);

        $upload = $storage->upload($request->file('file'), []);

        $oldUrl = $user->cover_image_url;

        $media = Media::create([
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id,
            'type' => 'image',
            'url' => $upload['url'],
            'provider' => $upload['provider'] ?? null,
            'alt' => $user->name . ' cover',
            'width' => $upload['width'] ?? null,
            'height' => $upload['height'] ?? null,
            'size_bytes' => $upload['size_bytes'] ?? null,
            'mime' => $upload['mime'] ?? null,
            'metadata_json' => $upload['metadata'] ?? [],
            'created_by' => $user->id,
        ]);

        $user->cover_image_url = $media->url;
        $user->save();

        if ($oldUrl) {
            Media::where('model_type', \App\Models\User::class)
                ->where('model_id', $user->id)
                ->where('url', $oldUrl)
                ->delete();
        }

        return response()->json(['data' => ['cover_image_url' => $media->url]]);
    }

    public function deleteCover(Request $request)
    {
        $user = $request->user();
        $oldUrl = $user->cover_image_url;
        $user->cover_image_url = null;
        $user->save();
        if ($oldUrl) {
            Media::where('model_type', \App\Models\User::class)
                ->where('model_id', $user->id)
                ->where('url', $oldUrl)
                ->delete();
        }
        return response()->json(['message' => 'Cover deleted']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'password' => ['required','string']
        ]);

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid password'], 422);
        }

        if ($user->isProvider()) {
            $activeStatuses = ['pending','authorized','confirmed'];
            $activeCount = Booking::whereHas('offering', function($q) use ($user) {
                    $q->where('provider_id', $user->id);
                })
                ->whereIn('status', $activeStatuses)
                ->count();
            if ($activeCount > 0) {
                return response()->json(['message' => 'Cannot delete account with active bookings'], 422);
            }
        }

        Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending','authorized'])
            ->update(['status' => 'cancelled', 'cancel_reason' => 'Account deleted by user']);

        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        $user->delete();

        return response()->json(['message' => 'Account deleted']);
    }
}
