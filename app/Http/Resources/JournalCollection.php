<?php

namespace App\Http\Resources;

use App\Models\Ledger;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JournalCollection extends ResourceCollection
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
            'desc' => $this->desc,
            'no_ref' => $this->no_ref,
            'value' => $this->value,
            'detail' => $this->detail,
            'ledgers' => Ledger::where('no_ref', $this->no_ref)->get(),
            'date' => Carbon::createFromDate($this->date)->toFormattedDateString(),
        ];
    }
}
