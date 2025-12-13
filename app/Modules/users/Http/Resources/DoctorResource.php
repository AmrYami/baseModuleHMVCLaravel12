<?php

namespace Users\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get allowed fields from the request.
        $allowedFields = $request->get('allowedFields', []);

        // Convert the model to an array and filter only the allowed fields.
        return Arr::only($this->resource->toArray(), $allowedFields);
    }
}
