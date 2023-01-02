<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SegmentController extends Controller
{
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
        $segment->delete();
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

            return response('success',200);
        }else{
            return response(['errors'=>$validator->errors()],200);
        }
    }
    public function show($id){
        $segment=Segment::find($id);
        return view('segments.detail',['segment'=>$segment]);
    }
    public function update(Request $request){
        $segment=Segment::find($request->id);
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

            return response('success',200);
        }else{
            return response(['errors'=>$validator->errors()],200);
        }
    }
}
