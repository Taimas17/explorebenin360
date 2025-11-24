<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use Illuminate\Http\Request;

class OfferingController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable','string','max:120'],
            'type' => ['nullable','in:accommodation,experience,guide_service'],
            'min_price' => ['nullable','numeric','min:0'],
            'max_price' => ['nullable','numeric','min:0'],
            'capacity' => ['nullable','integer','min:1'],
            'city' => ['nullable','string','max:80'],
            'page' => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $query = Offering::query()
            ->with('place:id,title,slug,city')
            ->where('status','published');
        if (!empty($data['q'])) {
            $q = $data['q'];
            $query->where(function($w) use ($q) {
                $w->where('title','like','%'.$q.'%')
                  ->orWhere('description','like','%'.$q.'%');
            });
        }
        if (!empty($data['type'])) $query->where('type', $data['type']);
        if (!empty($data['min_price'])) $query->where('price','>=',$data['min_price']);
        if (!empty($data['max_price'])) $query->where('price','<=',$data['max_price']);
        if (!empty($data['capacity'])) $query->where('capacity','>=',$data['capacity']);
        if (!empty($data['city'])) {
            $query->whereHas('place', fn($q) => $q->where('city', $data['city']));
        }

        $paginator = $query->orderBy('price')->paginate((int)($data['per_page'] ?? 15))->appends($request->query());
        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function show(string $slug)
    {
        $item = Offering::with('place','provider')->where('slug',$slug)->where('status','published')->firstOrFail();
        return response()->json(['data' => $item]);
    }
}
