<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeoBoundsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bounds' => ['nullable', 'string', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
        ];
    }
    
    public function getBounds(): ?array
    {
        if (!$this->bounds) return null;
        
        $parts = array_map('floatval', explode(',', $this->bounds));
        
        if (count($parts) !== 4) return null;
        
        [$swLat, $swLng, $neLat, $neLng] = $parts;
        
        if ($swLat < -90 || $swLat > 90 || $neLat < -90 || $neLat > 90) {
            return null;
        }
        
        if ($swLng < -180 || $swLng > 180 || $neLng < -180 || $neLng > 180) {
            return null;
        }
        
        if ($swLat >= $neLat || $swLng >= $neLng) {
            return null;
        }
        
        return compact('swLat', 'swLng', 'neLat', 'neLng');
    }
}
