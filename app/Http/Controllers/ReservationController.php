<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Contactprestay;
use App\Models\ProfileFolio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use function PHPUnit\Framework\isEmpty;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $profilesfolio = ProfileFolio::whereIn('foliostatus',['C','G','T'])
            ->whereColumn('folio_master','folio')
            ->where('dateci','>=', Carbon::now()->format('Y-m-d'))
            ->orderBy('dateci','ASC')
            ->get();

//        dd(isset($profilesfolio));

        if(isset($profilesfolio)){
            $contacts = [];
        }
        else
        {
            foreach ($profilesfolio as $key=>$val)
            {
                $contacts[] = Contact::find($val->profileid);
                $contacts[$key]['foliostatus'] = $val->foliostatus;
                $contacts[$key]['dateci'] = $val->dateci;
                $contacts[$key]['dateco'] = $val->dateco;
                $contacts[$key]['source'] = $val->source;
                $contacts[$key]['room'] = $val->room;
                $contacts[$key]['roomtype'] = $val->roomtype;
                $contacts[$key]['folio_master'] = $val->folio_master;
                $contacts[$key]['prestay_status'] = Contactprestay::select('next_action')->where('contact_id', $val->profileid)->first();
                $contacts[$key]['sendtoguest_at'] = Contactprestay::select('sendtoguest_at')->where('contact_id', $val->profileid)->first();
                $contacts[$key]['registration_code'] = Contactprestay::select(['registration_code'])->where('folio_master',$val->folio_master)->first();
            }

        }
        return view('reservation.list')->with(compact('contacts'));
    }
    public function registrationformprint ($registrationformcode){
        $completeContactPrestay = Contactprestay::where('registration_code', $registrationformcode)->where('next_action','=','COMPLETED')->first();
        $profilesFolio = ProfileFolio::select('room','roomtype','pax','dateci','dateco')->where('folio',$completeContactPrestay['folio_master'])->first();
        $completeContactPrestay['dateci'] = $profilesFolio->dateci == null ? '-':$profilesFolio->dateci;
        $completeContactPrestay['dateco'] = $profilesFolio->dateco == null ? '-':$profilesFolio->dateco;
        $completeContactPrestay['room'] = $profilesFolio->room == null ? '-':$profilesFolio->room;
        $completeContactPrestay['roomtype'] = $profilesFolio->roomtype == null ? '-':$profilesFolio->roomtype;
        $completeContactPrestay['pax'] = $profilesFolio->pax == null ? '-':$profilesFolio->pax;
        $completeContactPrestays = $completeContactPrestay->toarray();

    $pdf = PDF::loadView('reservation.registrationpdf',$completeContactPrestays);
    return $pdf->download('Registration Form folio '.$completeContactPrestay['folio_master'].' .pdf');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
