<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $v = Validator::make($request->all(), [
            'q' => ['nullable','string','max:100'],
            'city' => ['nullable','string','max:80'],
            'category' => ['nullable','string','max:50'],
            'from' => ['nullable','date'],
            'to' => ['nullable','date'],
            'featured' => ['nullable','boolean'],
            'sort' => ['nullable','in:date,recent'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);
        $data = $v->validated();

        $query = Event::query()->where('status','published');
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%')
                  ->orWhere('category','like','%'.$q.'%');
            });
        }
        if (!empty($data['city'])) $query->where('city', $data['city']);
        if (!empty($data['category'])) $query->where('category', $data['category']);
        if (array_key_exists('featured', $data)) $query->where('featured', (bool)$data['featured']);
        if (!empty($data['from'])) $query->whereDate('end_date', '>=', $data['from']);
        if (!empty($data['to'])) $query->whereDate('start_date', '<=', $data['to']);

        $sort = $data['sort'] ?? null;
        if ($sort === 'date') $query->orderBy('start_date');
        elseif ($sort === 'recent') $query->latest();
        else $query->orderBy('start_date');

        $perPage = (int)($data['per_page'] ?? 15);
        if ($perPage > 50) $perPage = 50;
        $paginator = $query->paginate($perPage)->appends($request->query());
        return EventResource::collection($paginator)->additional(['meta' => [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ]]);
    }

    public function show(string $slug)
    {
        $item = Event::where('slug',$slug)->where('status','published')->firstOrFail();
        return new EventResource($item);
    }
}
