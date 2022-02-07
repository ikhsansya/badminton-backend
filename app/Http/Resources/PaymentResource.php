<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }

    public function toArray($request)
    {   
        $tanggal    = Carbon::parse($this->tanggal)->format('d F Y');
        $created_at = Carbon::parse($this->created_at)->format('d F Y');
        $updated_at = Carbon::parse($this->updated_at)->format('d F Y');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'tanggal' => $tanggal,
            'type' => $this->type,
            'keterangan' => $this->keterangan,
            'payment_id' => $this->payment_id,
            'total' => $this->total,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];

        // return $this->collection->map(function ($payment) {
        //     $tanggal    = Carbon::parse($payment->tanggal)->format('d F Y');
        //     $created_at = Carbon::parse($payment->created_at)->format('d F Y');
        //     $updated_at = Carbon::parse($payment->updated_at)->format('d F Y');

        //     return [
        //         'id' => $payment->id,
        //         'title' => $payment->title,
        //         'tanggal' => $tanggal,
        //         'type' => $payment->type,
        //         'keterangan' => $payment->keterangan,
        //         'payment_id' => $payment->payment_id,
        //         'total' => $payment->total,
        //         'created_at' => $created_at,
        //         'updated_at' => $updated_at
        //     ];
        // });
    }

    public function with($request)
    {
        return [
            'meta' => [
                'key' => 'value',
            ],
        ];
    }

}
