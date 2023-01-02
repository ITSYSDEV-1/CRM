<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Prestay as Prestay;
use App\Models\Contact;
use App\Models\Contactprestay;
use App\Models\ProfileFolio;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class PrestayController extends BaseController
{
    public function index()
    {



        $prestays = Contactprestay::all();
        if(!$prestays->isEmpty())
        {
            foreach ($prestays as $key => $prestay) {
                //suffle code for PrestayCode
                $array = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                $registrationcode = substr(str_shuffle($array), 0, 20);

                //select Contact detail
                $contacts[] = Contact::select('idnumber', 'fname', 'lname', 'email', 'birthday', 'salutation', 'country_id', 'mobile', 'createdate', 'editdate')
                    ->where('contactid', $prestay->contact_id)
                    ->first();

                //select dateci & dateco
                $profilesfolio = ProfileFolio::select('dateci')
                    ->where('folio_master', $prestay->folio_master)
                    ->first();

                $contacts[$key]['contact_id'] = $prestay->contact_id;
                $contacts[$key]['folio_master'] = $prestay->folio_master;
                $contacts[$key]['prestay_status'] = "Collected";
                $contacts[$key]['dateci'] = $profilesfolio == null ? null:$profilesfolio->dateci;
                $contacts[$key]['registrationcode'] = $registrationcode;
            }
            return $this->sendResponse(Prestay::collection($contacts), 'Data fetched.');
        }
        else
        {
            return $this->sendError('No Content','No Content');
        }
    }

    public function update(Request $request)
    {
        $jsonString = $request->all();

        foreach ($jsonString as $val)
        {
            if (Contactprestay::where('folio_master', '=', $val['folio_master'])->exists()) {
                //Update Contacts Prestays
                $contactprestay = Contactprestay::select()->where('contact_id', $val['contact_id'])->first();
                $contactprestay->update([
                    'prestay_status' => $val['prestay_status']
                ]);

                //Update Contact
//                $contact = Contact::select('contactid')->where('contactid',$val['contact_id'])->first();
//                $contact->update([
//                    'fname'=>$val['fname'],
//                    'lname'=>$val['lname'],
//                    'email'=>$val['email'],
//                ]);
                $val['result'] = $val['folio_master'].' updated';
            }
            else
            {
                $val['result'] = 'Failed';
            }
            $result[] = $val['result'];
        }
        return $this->sendResponse($result,'Data updated');
    }

    public function delete(Request $request)
    {
        $jsonString = $request->all();
        foreach ($jsonString as $val) {
            if (Contactprestay::where('folio_master', '=', $val['folio_master'])->exists()) {
                //Delete Contacts Prestays
                $contactprestay = Contactprestay::select()->where('contact_id', $val['contact_id'])->first();
                $contactprestay->delete();
                $val['result'] = $val['folio_master'].' deleted';
            }
            else{
                $val['result'] = 'Failed';
            }
            $result[] = $val['result'];
        }
        return $this->sendResponse($result,'Data deleted');
    }
}
