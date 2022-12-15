<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'desc' => $this->description,
            'no_ref' => $this->no_ref,
            'value' => $this->value,
            'detail' => $this->detail,
            'date' => Carbon::createFromDate($this->date)->toFormattedDateString(),
        ];
    }
}
