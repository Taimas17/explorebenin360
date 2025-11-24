<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MessageThread;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function listThreads(Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:open,closed'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
        
        $userId = $request->user()->id;
        $perPage = $data['per_page'] ?? 15;
        
        $query = MessageThread::query()
            ->where(function($q) use ($userId) {
                $q->where('traveler_id', $userId)
                  ->orWhere('provider_id', $userId);
            })
            ->with([
                'traveler:id,name',
                'provider:id,name,business_name',
                'messages' => function($q) {
                    $q->latest('created_at')->limit(1);
                }
            ])
            ->orderByDesc('last_message_at');
        
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }
        
        $threads = $query->paginate($perPage);
        
        $transformed = $threads->getCollection()->map(function($thread) use ($userId) {
            return [
                'id' => $thread->id,
                'subject' => $thread->subject,
                'status' => $thread->status,
                'traveler' => [
                    'id' => $thread->traveler->id,
                    'name' => $thread->traveler->name,
                ],
                'provider' => [
                    'id' => $thread->provider->id,
                    'name' => $thread->provider->name,
                    'business_name' => $thread->provider->business_name,
                ],
                'unread_count' => $thread->getUnreadCountForUser($userId),
                'last_message_preview' => $thread->getLastMessagePreview(),
                'last_message_at' => $thread->last_message_at?->toISOString(),
                'created_at' => $thread->created_at->toISOString(),
            ];
        });
        
        return response()->json([
            'data' => $transformed,
            'meta' => [
                'current_page' => $threads->currentPage(),
                'per_page' => $threads->perPage(),
                'total' => $threads->total(),
                'last_page' => $threads->lastPage(),
            ]
        ]);
    }

    public function show(Request $request, int $id)
    {
        $thread = MessageThread::with([
            'messages.sender:id,name',
            'traveler',
            'provider',
            'booking',
            'offering'
        ])->findOrFail($id);
        
        $this->authorize('view', $thread);
        
        $thread->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json([
            'data' => [
                'id' => $thread->id,
                'subject' => $thread->subject,
                'status' => $thread->status,
                'created_at' => $thread->created_at->toISOString(),
                'participants' => [
                    'traveler' => $thread->traveler,
                    'provider' => $thread->provider,
                ],
                'booking' => $thread->booking ? [
                    'id' => $thread->booking->id,
                    'offering_title' => $thread->booking->offering->title ?? 'N/A',
                    'start_date' => $thread->booking->start_date,
                ] : null,
                'offering' => $thread->offering ? [
                    'id' => $thread->offering->id,
                    'title' => $thread->offering->title,
                    'slug' => $thread->offering->slug,
                ] : null,
                'messages' => $thread->messages->map(function($msg) {
                    return [
                        'id' => $msg->id,
                        'sender' => [
                            'id' => $msg->sender->id,
                            'name' => $msg->sender->name,
                        ],
                        'body' => $msg->body,
                        'read_at' => $msg->read_at?->toISOString(),
                        'created_at' => $msg->created_at->toISOString(),
                    ];
                })->sortBy('created_at')->values(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', MessageThread::class);
        
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'exists:users,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'offering_id' => ['nullable', 'exists:offerings,id'],
            'initial_message' => ['required', 'string', 'max:5000'],
        ]);
        
        $provider = User::findOrFail($data['provider_id']);
        if (!method_exists($provider, 'isProvider') || !$provider->isProvider()) {
            return response()->json(['message' => 'User is not a provider'], 422);
        }
        
        $thread = MessageThread::create([
            'subject' => $data['subject'],
            'traveler_id' => $request->user()->id,
            'provider_id' => $data['provider_id'],
            'booking_id' => $data['booking_id'] ?? null,
            'offering_id' => $data['offering_id'] ?? null,
            'last_message_at' => now(),
        ]);
        
        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $request->user()->id,
            'body' => $data['initial_message'],
        ]);
        
        $provider->notify(new \App\Notifications\MessageReceivedNotification($thread, $message));
        
        return response()->json(['data' => $thread->load('traveler', 'provider')], 201);
    }

    public function reply(Request $request, int $id)
    {
        $thread = MessageThread::findOrFail($id);
        
        $this->authorize('reply', $thread);
        
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);
        
        if ($thread->status === 'closed') {
            return response()->json(['message' => 'Thread is closed'], 422);
        }
        
        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);
        
        $thread->update(['last_message_at' => now()]);
        
        $recipientId = $thread->traveler_id === $request->user()->id 
            ? $thread->provider_id 
            : $thread->traveler_id;
        
        $recipient = User::find($recipientId);
        if ($recipient) {
            $recipient->notify(new \App\Notifications\MessageReceivedNotification($thread, $message));
        }
        
        return response()->json(['data' => $message->load('sender:id,name')], 201);
    }

    public function close(Request $request, int $id)
    {
        $thread = MessageThread::findOrFail($id);
        
        $this->authorize('update', $thread);
        
        $thread->update(['status' => 'closed']);
        
        return response()->json(['message' => 'Thread closed']);
    }
}
