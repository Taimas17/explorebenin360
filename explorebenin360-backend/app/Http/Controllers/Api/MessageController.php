<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MessageThread;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Liste les threads de conversation de l'utilisateur authentifié
     * GET /api/v1/messages/threads
     */
    public function listThreads(Request $request)
    {
        $user = $request->user();
        
        $threads = MessageThread::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['user', 'recipient', 'latestMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($thread) use ($user) {
                $otherUser = $thread->user_id === $user->id ? $thread->recipient : $thread->user;
                $latest = $thread->latestMessage;
                
                return [
                    'id' => $thread->id,
                    'subject' => $thread->subject ?? "Conversation avec {$otherUser->name}",
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                    ],
                    'last_message_preview' => $latest ? substr($latest->body, 0, 100) : '',
                    'unread_count' => $thread->unreadCountFor($user->id),
                    'updated_at' => $thread->last_message_at ?? $thread->updated_at,
                ];
            });
        
        return response()->json(['data' => $threads]);
    }

    /**
     * Récupère les messages d'un thread
     * GET /api/v1/messages/threads/{threadId}
     */
    public function listMessages(Request $request, int $threadId)
    {
        $user = $request->user();
        $thread = MessageThread::findOrFail($threadId);
        
        // Vérifier que l'utilisateur est participant
        if (!$thread->hasParticipant($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $messages = Message::where('thread_id', $threadId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'thread_id' => $message->thread_id,
                    'author' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                    ],
                    'body' => $message->body,
                    'is_mine' => $message->sender_id === $user->id,
                    'read_at' => $message->read_at,
                    'created_at' => $message->created_at,
                ];
            });
        
        // Marquer comme lus les messages reçus
        Message::where('thread_id', $threadId)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['data' => $messages]);
    }

    /**
     * Envoie un message dans un thread
     * POST /api/v1/messages/threads/{threadId}
     */
    public function sendMessage(Request $request, int $threadId)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000']
        ]);
        
        $user = $request->user();
        $thread = MessageThread::findOrFail($threadId);
        
        // Vérifier participation
        if (!$thread->hasParticipant($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $message = Message::create([
            'thread_id' => $threadId,
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);
        
        // Mettre à jour last_message_at du thread
        $thread->update(['last_message_at' => now()]);
        
        $message->load('sender');
        
        return response()->json([
            'data' => [
                'id' => $message->id,
                'thread_id' => $message->thread_id,
                'author' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                ],
                'body' => $message->body,
                'is_mine' => true,
                'created_at' => $message->created_at,
            ]
        ], 201);
    }

    /**
     * Crée un nouveau thread de conversation
     * POST /api/v1/messages/threads
     */
    public function createThread(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'related_offering_id' => ['nullable', 'exists:offerings,id']
        ]);
        
        $user = $request->user();
        
        // Vérifier qu'on n'envoie pas à soi-même
        if ($user->id === (int)$data['recipient_id']) {
            return response()->json(['message' => 'Cannot message yourself'], 422);
        }
        
        // Vérifier si un thread existe déjà entre ces deux utilisateurs
        $existingThread = MessageThread::where(function ($q) use ($user, $data) {
                $q->where('user_id', $user->id)->where('recipient_id', $data['recipient_id']);
            })
            ->orWhere(function ($q) use ($user, $data) {
                $q->where('user_id', $data['recipient_id'])->where('recipient_id', $user->id);
            })
            ->first();
        
        if ($existingThread) {
            // Utiliser le thread existant
            $thread = $existingThread;
        } else {
            // Créer nouveau thread
            $thread = MessageThread::create([
                'user_id' => $user->id,
                'recipient_id' => $data['recipient_id'],
                'subject' => $data['subject'] ?? null,
                'related_offering_id' => $data['related_offering_id'] ?? null,
                'last_message_at' => now(),
            ]);
        }
        
        // Créer le premier message
        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);
        
        return response()->json([
            'data' => [
                'thread_id' => $thread->id,
                'message_id' => $message->id,
            ]
        ], 201);
    }
}
