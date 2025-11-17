<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use App\Models\Article;
use App\Models\Place;
use App\Models\Offering;
use Illuminate\Http\Request;

class ContentModerationController extends Controller
{
    public function listReports(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $status = $request->get('status', 'pending');

        $reports = ContentReport::where('status', $status)
            ->with(['reporter', 'reportable'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'type' => $report->reportable_type,
                    'reportable_id' => $report->reportable_id,
                    'title' => $this->getReportableTitle($report->reportable),
                    'reason' => $report->reason,
                    'description' => $report->description,
                    'reporter' => [
                        'id' => $report->reporter->id,
                        'name' => $report->reporter->name,
                        'email' => $report->reporter->email,
                    ],
                    'status' => $report->status,
                    'created_at' => $report->created_at,
                ];
            });

        return response()->json(['data' => $reports]);
    }

    public function createReport(Request $request)
    {
        $data = $request->validate([
            'reportable_type' => ['required', 'in:App\\Models\\Place,App\\Models\\Article,App\\Models\\Offering'],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', 'in:spam,inappropriate,offensive,fake,other'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $model = $data['reportable_type'];
        $exists = $model::where('id', $data['reportable_id'])->exists();

        if (!$exists) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        $report = ContentReport::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $data['reportable_type'],
            'reportable_id' => $data['reportable_id'],
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
        ]);

        return response()->json([
            'message' => 'Report submitted successfully',
            'data' => ['id' => $report->id]
        ], 201);
    }

    public function resolveReport(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'action' => ['required', 'in:remove,flag,warn'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $report = ContentReport::with('reportable')->findOrFail($id);

        switch ($data['action']) {
            case 'remove':
                if ($report->reportable && method_exists($report->reportable, 'delete')) {
                    $report->reportable->delete();
                }
                break;

            case 'flag':
                if ($report->reportable && method_exists($report->reportable, 'flag')) {
                    $report->reportable->flag($report->reason);
                }
                break;

            case 'warn':
                // TODO: send warning email
                break;
        }

        $report->resolve($data['note'] ?? ("Action: {$data['action']}"), $auth->id);

        return response()->json(['message' => 'Report resolved']);
    }

    public function dismissReport(Request $request, int $id)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $report = ContentReport::findOrFail($id);
        $report->dismiss($data['note'] ?? 'No violation found', $auth->id);

        return response()->json(['message' => 'Report dismissed']);
    }

    public function flaggedContent(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $type = $request->get('type');
        $flagged = [];

        if (!$type || $type === 'articles') {
            $articles = Article::whereNotNull('flagged_at')->get();
            foreach ($articles as $article) {
                $flagged[] = [
                    'type' => 'article',
                    'id' => $article->id,
                    'title' => $article->title,
                    'flagged_at' => $article->flagged_at,
                    'flagged_reason' => $article->flagged_reason,
                ];
            }
        }

        if (!$type || $type === 'places') {
            $places = Place::whereNotNull('flagged_at')->get();
            foreach ($places as $place) {
                $flagged[] = [
                    'type' => 'place',
                    'id' => $place->id,
                    'title' => $place->title,
                    'flagged_at' => $place->flagged_at,
                    'flagged_reason' => $place->flagged_reason,
                ];
            }
        }

        if (!$type || $type === 'offerings') {
            $offerings = Offering::whereNotNull('flagged_at')->get();
            foreach ($offerings as $offering) {
                $flagged[] = [
                    'type' => 'offering',
                    'id' => $offering->id,
                    'title' => $offering->title,
                    'flagged_at' => $offering->flagged_at,
                    'flagged_reason' => $offering->flagged_reason,
                ];
            }
        }

        return response()->json(['data' => $flagged]);
    }

    public function unflagContent(Request $request)
    {
        $auth = $request->user();
        if (!method_exists($auth, 'hasRole') || !$auth->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'type' => ['required', 'in:article,place,offering'],
            'id' => ['required', 'integer'],
        ]);

        $model = match($data['type']) {
            'article' => Article::class,
            'place' => Place::class,
            'offering' => Offering::class,
        };

        $content = $model::findOrFail($data['id']);

        if (method_exists($content, 'unflag')) {
            $content->unflag();
        }

        return response()->json(['message' => 'Content unflagged']);
    }

    private function getReportableTitle($reportable): string
    {
        if (!$reportable) return 'N/A';
        if (isset($reportable->title)) return $reportable->title;
        if (isset($reportable->name)) return $reportable->name;
        return 'N/A';
    }
}
