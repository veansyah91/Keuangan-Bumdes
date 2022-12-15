<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'desc' => $this->desc,
            'no_ref' => $this->no_ref,
            'value' => $this->value,
            'detail' => $this->detail,
            'date' => Carbon::createFromDate($this->date)->toFormattedDateString(),
        ];
    }
}
