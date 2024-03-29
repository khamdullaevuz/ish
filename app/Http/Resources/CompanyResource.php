<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Company */
class CompanyResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner_name' => $this->owner_name,
            'address' => $this->address,
            'email' => $this->email,
            'website' => $this->website,
            'phone' => $this->phone,
        ];
    }
}
