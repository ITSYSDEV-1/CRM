<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Prestay extends JsonResource
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
            'contact_id' =>$this->contact_id,
            'idnumber'=>$this->idnumber,
            'registrationcode'=>$this->registrationcode,
            'fname'=>$this->fname,
            'lname'=>$this->lname,
            'email'=>$this->email,
            'birthday'=>$this->birthday,
            'salutation'=>$this->salutation,
            'country'=>$this->country_id,
            'mobile'=>$this->mobile,
            'folio_master'=>$this->folio_master,
            'prestay_status'=>$this->prestay_status,
            'dateci'=>$this->dateci,
            'dateco'=>$this->dateco,
            'createdate'=>$this->createdate,
            'editdate'=>$this->editdate
        ];
    }
}
