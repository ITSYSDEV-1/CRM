<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Traits\UserLogsActivity; // Tambahkan import trait
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SegmentController extends Controller
{
    use UserLogsActivity; // Gunakan trait
    
    public function index (){
        return view('segments.index');
    }
    public function create()
    {
        //
        $action='create';
        $model=new Segment();
        return view('segments.manage',['action'=>$action,'model'=>$model,'option'=>[]]);
    }
    public function edit($id)
    {
        //
        $action='edit';
        $model=Segment::find($id);
        return view('segments.manage',['action'=>$action,'model'=>$model]);
    }
    public function destroy($id){
        $segment=Segment::find($id);
        
        // Simpan data untuk logging sebelum dihapus
        $segmentData = [
            'id' => $segment->id,
            'name' => $segment->name
        ];
        
        $segment->delete();
        
        // Tambahkan logging aktivitas pengguna
        $this->logActivity(
            'delete',
            Segment::class,
            $id,
            $segmentData,
            null,
            'Deleted segment: ' . $segmentData['name']
        );
        
        return redirect()->back();
    }

    public function filterSegment(Request $request){

    }
    public function store(Request $request){
        $rules=['segmentname'=>'required'];
        $message=['segmentname.required'=>'Segment Name Required'];
        $validator=Validator::make($request->all(),$rules,$message);
        if(!$validator->fails()) {
            $guest_status = serialize($request->guest_status);
            $country_id = serialize($request->country_id);
            $gender = serialize($request->gender);
            $booking_source = serialize($request->booking_source);
            $segment = new Segment();
            $segment->name = $request->segmentname;
            $segment->guest_status = $guest_status;
            $segment->country_id = $country_id;
            $segment->area=serialize($request->area);
            $segment->gender = $gender;
            $segment->booking_source = $booking_source;
            if ($request->stay_from != null) {
                $segment->stay_from = Carbon::parse($request->stay_from)->format('Y-m-d');
            } else {
                $segment->stay_from = null;
            }
            if ($request->stay_to != null) {
                $segment->stay_to = Carbon::parse($request->stay_to)->format('Y-m-d');
            } else {
                $segment->stay_to = null;
            }
            if ($request->spending_from != null) {
                $segment->spending_from = str_replace('.', '', $request->spending_from);
            } else {
                $segment->spending_from = null;
            }
            if ($request->spending_to != null) {
                $segment->spending_to = str_replace('.', '', $request->spending_to);
            } else {
                $segment->spending_to = null;
            }

            $segment->total_stay_from = $request->total_stay_from;
            $segment->total_stay_to = $request->total_stay_to;
            $segment->total_night_from = $request->total_night_from;
            $segment->total_night_to = $request->total_night_to;
            $segment->age_from = $request->age_from;
            $segment->age_to = $request->age_to;
            $segment->save();
            
            // Hitung jumlah kontak yang termasuk dalam segment ini
            $contactCount = $this->countContactsInSegment($segment);
            
            // Tambahkan logging aktivitas pengguna dengan jumlah kontak
            $this->logActivity(
                'create',
                Segment::class,
                $segment->id,
                null,
                $segment->toArray(),
                'Created new segment: ' . $segment->name . ' (Contains ' . $contactCount . ' contacts)'
            );

            return response('success',200);
        }else{
            return response(['errors'=>$validator->errors()],200);
        }
    }
    
    public function update(Request $request){
        $segment=Segment::find($request->id);
        
        // Simpan data lama untuk logging
        $oldData = $segment->toArray();
        
        $rules=['name'=>'required'];
        $message=['name.required'=>'Segment Name Required'];
        $validator=Validator::make($request->all(),$rules,$message);
        if(!$validator->fails()) {
            $guest_status = serialize($request->guest_status);
            $country_id = serialize($request->country_id);
            $gender = serialize($request->gender);
            $booking_source = serialize($request->booking_source);
            $segment->name = $request->name;
            $segment->guest_status = $guest_status;
            $segment->country_id = $country_id;
            $segment->area=serialize($request->area);
            $segment->gender = $gender;
            $segment->booking_source = $booking_source;
            if ($request->stay_from != null) {
                $segment->stay_from = Carbon::parse($request->stay_from)->format('Y-m-d');
            } else {
                $segment->stay_from = null;
            }
            if ($request->stay_to != null) {
                $segment->stay_to = Carbon::parse($request->stay_to)->format('Y-m-d');
            } else {
                $segment->stay_to = null;
            }
            if ($request->spending_from != null) {
                $segment->spending_from = str_replace('.', '', $request->spending_from);
            } else {
                $segment->spending_from = null;
            }
            if ($request->spending_to != null) {
                $segment->spending_to = str_replace('.', '', $request->spending_to);
            } else {
                $segment->spending_to = null;
            }

            $segment->total_stay_from = $request->total_stay_from;
            $segment->total_stay_to = $request->total_stay_to;
            $segment->total_night_from = $request->total_night_from;
            $segment->total_night_to = $request->total_night_to;
            $segment->age_from = $request->age_from;
            $segment->age_to = $request->age_to;
            if ($request->bday_from){
                $segment->bday_from=Carbon::parse($request->bday_from)->format('Y-m-d');
            }else{
                $segment->bday_from=NULL;
            }
            if($request->bday_to){
                $segment->bday_to=Carbon::parse($request->bday_to)->format('Y-m-d');
            }else{
                $segment->bday_to=NULL;
            }
            if($request->wedding_bday_from){
                $segment->wedding_bday_from=Carbon::parse($request->wedding_bday_from)->format('Y-m-d');
            }else{
                $segment->wedding_bday_from=NULL;
            }
            if($request->wedding_bday_to){
                $segment->wedding_bday_to=Carbon::parse($request->wedding_bday_to)->format('Y-m-d');
            }else{
                $segment->wedding_bday_to=NULL;
            }
            $segment->save();
            
            // Hitung jumlah kontak yang termasuk dalam segment ini setelah update
            $contactCount = $this->countContactsInSegment($segment);
            
            // Tambahkan logging aktivitas pengguna dengan jumlah kontak
            $this->logActivity(
                'update',
                Segment::class,
                $segment->id,
                $oldData,
                $segment->toArray(),
                'Updated segment: ' . $segment->name . ' (Contains ' . $contactCount . ' contacts)'
            );

            return response('success',200);
        }else{
            return response(['errors'=>$validator->errors()],200);
        }
    }
    
    /**
     * Menghitung jumlah kontak yang termasuk dalam segment
     *
     * @param  \App\Models\Segment  $segment
     * @return int
     */
    private function countContactsInSegment($segment)
    {
        // Import model Contact
        $contacts = \App\Models\Contact::query();
        
        // Filter berdasarkan guest_status jika ada
        $guest_status = unserialize($segment->guest_status);
        if (!empty($guest_status)) {
            $contacts->whereIn('guest_status', $guest_status);
        }
        
        // Filter berdasarkan country_id jika ada
        $country_id = unserialize($segment->country_id);
        if (!empty($country_id)) {
            $contacts->whereIn('country_id', $country_id);
        }
        
        // Filter berdasarkan gender jika ada
        $gender = unserialize($segment->gender);
        if (!empty($gender)) {
            $contacts->whereIn('gender', $gender);
        }
        
        // Filter berdasarkan area jika ada
        $area = unserialize($segment->area);
        if (!empty($area)) {
            $contacts->whereIn('area', $area);
        }
        
        // Filter berdasarkan tanggal stay jika ada
        if ($segment->stay_from && $segment->stay_to) {
            $contacts->whereHas('transaction', function($query) use ($segment) {
                $query->whereHas('profilefolio', function($q) use ($segment) {
                    $q->whereBetween('dateci', [$segment->stay_from, $segment->stay_to]);
                });
            });
        }
        
        // Filter berdasarkan umur jika ada
        if ($segment->age_from && $segment->age_to) {
            $contacts->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN ? AND ?', 
                [$segment->age_from, $segment->age_to]);
        }
        
        // Filter berdasarkan birthday jika ada
        if ($segment->bday_from && $segment->bday_to) {
            $contacts->whereRaw("DATE_FORMAT(birthday, '%m-%d') BETWEEN DATE_FORMAT(?, '%m-%d') AND DATE_FORMAT(?, '%m-%d')", 
                [$segment->bday_from, $segment->bday_to]);
        }
        
        // Filter berdasarkan wedding_bday jika ada
        if ($segment->wedding_bday_from && $segment->wedding_bday_to) {
            $contacts->whereRaw("DATE_FORMAT(wedding_bday, '%m-%d') BETWEEN DATE_FORMAT(?, '%m-%d') AND DATE_FORMAT(?, '%m-%d')", 
                [$segment->wedding_bday_from, $segment->wedding_bday_to]);
        }
        
        // Hitung total kontak yang memenuhi kriteria
        return $contacts->count();
    }
    public function show($id){
        $segment=Segment::find($id);
        return view('segments.detail',['segment'=>$segment]);
    }
}
