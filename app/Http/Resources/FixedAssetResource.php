<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'date'-> $this->date,
        ];
    }
}
