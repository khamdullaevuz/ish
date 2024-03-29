<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Employee */
class EmployeeResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'passport' => $this->passport,
            'surname' => $this->surname,
            'name' => $this->name,
            'patronymic' => $this->patronymic,
            'position' => $this->position,
            'phone' => $this->phone,
            'address' => $this->address,
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}
