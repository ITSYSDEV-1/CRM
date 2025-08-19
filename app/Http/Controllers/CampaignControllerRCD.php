<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use App\Models\ExternalContact;
use App\Models\ExternalContactCategory;
use App\Models\MailEditor;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Segment;
use App\Traits\UserLogsActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CampaignControllerRCD extends Controller
{
    use UserLogsActivity;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $seg){
        dd($seg->all());
    }
    public function index()
    {
        $campaign=Campaign::orderBy('created_at','desc')->get();
        return view('campaign.index',['campaigns'=>$campaign]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function campaign(){
        return view('campaign.list');
    }

    public function campaignlist(Request $request){
        $campaign=Campaign::whereHas('schedule')->get();

        $totalData = $campaign->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $search=$request->input('search.value');

        if(!empty($search)){
            $campaignlist=Campaign::where('name','LIKE',"%$search%")->offset($start)->limit($limit)->orderBy('created_at','desc')->get();
            $totalFiltered=count($campaignlist);
        }else{
            $campaignlist=Campaign::select()->offset($start)->limit($limit)->orderBy('created_at','desc')->get();
        }


        if(!empty($campaignlist)){
            foreach ($campaignlist as $item){
                if($item->type=='internal'){
                    $segment=count($item->segment) ? $item->segment[0]->name:'';
                }else{
                    $segment=count($item->externalSegment) ? $item->externalSegment[0]->category:'';
                }
                $nestedData['type']=$item->type;
//                $nestedData['contact']=$contact;
                $nestedData['id']=$item->id;
                $nestedData['name']=$item->name;
                $nestedData['segment']=$segment;
                $nestedData['status']=$item->status;
                $nestedData['schedule']= $item->schedule ? $item->schedule->schedule:'';
                $nestedData['accepted']=count($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['processed','sent','open','click'])->groupBy('email_id'));
                $nestedData['delivered']=count($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['sent','open','click'])->groupBy('email_id'));
                $nestedData['opened']=count($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['open','click'])->groupBy('email_id'));
                $nestedData['clicked']=$item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['click']);
                $nestedData['unsubscribed']=count($item->emailresponse->where('campaign_id','=',$item->id)->where('event','=','unsubscribe'));
                $nestedData['failed']=count($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['hardbounce','softbounce','invalid']));
                $nestedData['rejected']=count($item->emailresponse->where('campaign_id','=',$item->id)->whereIn('event',['dropped'])->groupBy('email_id'));
                $nestedData['template']=$item->template;
                $data[]=$nestedData;

            }

        }
        $json_data=array(
            "draw"=>intval($request->input('draw')),
            "recordsTotal"=>intval($totalData),
            "recordsFiltered"=>intval($totalFiltered),
            "data"=>$data
        );

        return response($json_data);
    }
    public function campaignrecepient(Request $request){
        $campaignlist = Campaign::find($request->id);
        $contact = [];
        
        if($campaignlist->type=='internal' || $campaignlist->type=='Promo'){
            foreach ($campaignlist->contact as $val)
            {
                $contact[]=$val;
            }
        }else{
            foreach ($campaignlist->external as $val)
            {
                $contact[]=$val;
            }
        }

        return response($contact);
    }

    public function create()
    {
        $action='create';
        $model=new Campaign();
        return view('campaign.manage',['action'=>$action,'model'=>$model,'option'=>[]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $seg
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->category=='' || $request->category==null){
            $type='internal';
            $segment=$request->segments;
        }else{
            $type='external';
            $segment=$request->category;
        }

        $campaign=new Campaign();
        $campaign->name=$request->name;
        $campaign->status='Draft';
        $campaign->type=$type;
        $campaign->template_id=$request->template;
        $campaign->save();

        $seg=Segment::find($segment);
        $cat=ExternalContactCategory::find($segment);

        $ex=[];
        $excluded=ExcludedEmail::all();
        foreach ($excluded as $exc){
            array_push($ex,$exc->email);
        }

        // Variabel untuk menghitung jumlah penerima
        $recipientCount = 0;

        // if($type=='internal') {
        //     $contacts = Contact::with('transaction', 'profilesfolio')->when(unserialize($seg->country_id)[0] != null, function ($q) use ($seg) {
        //         return $q->whereIn('country_id',unserialize($seg->country_id));
        // })->when(unserialize($seg->area)[0]!=null,function ($q) use ($seg){
        //     return $q->whereIn('area',unserialize($seg->area));
        // })->when(unserialize($seg->guest_status)[0] !=null,function ($q) use ($seg){
        //     return $q->whereHas('profilesfolio',function ($q) use ($seg){
        //         return $q->whereIn('foliostatus',unserialize($seg->guest_status));
        //     });
        if($type=='internal') {
            $contacts = Contact::with('transaction', 'profilesfolio')->when(unserialize($seg->country_id) && isset(unserialize($seg->country_id)[0]), function ($q) use ($seg) {
                return $q->whereIn('country_id',unserialize($seg->country_id));
        })->when($seg->area && unserialize($seg->area) && isset(unserialize($seg->area)[0]),function ($q) use ($seg){
            return $q->whereIn('area',unserialize($seg->area));
        })->when($seg->guest_status && unserialize($seg->guest_status) && isset(unserialize($seg->guest_status)[0]),function ($q) use ($seg){
            return $q->whereHas('profilesfolio',function ($q) use ($seg){
                return $q->whereIn('foliostatus',unserialize($seg->guest_status));
        });
        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->spending_from!=null and $seg->spending_to ==null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('revenue','>=',str_replace('.','',$seg->spending_from));
            });
        })->when($seg->spending_from!=null and $seg->spending_to !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
            });
        // })->when(unserialize($seg->gender)[0] !=null,function ($q) use ($seg) {
        //     return $q->whereIn('gender', unserialize($seg->gender));
        })->when($seg->gender && unserialize($seg->gender) && isset(unserialize($seg->gender)[0]), function ($q) use ($seg) {
            return $q->whereIn('gender', unserialize($seg->gender));

        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
            return  $q->whereHas('transaction', function ($q) use ($seg) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
            });
        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from);
        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
            return $q->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from !=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to !=null,function ($q) use ($seg){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
        // })->when(unserialize($seg->booking_source)[0]!=null,function ($q) use ($seg){
        //     return   $q->whereHas('profilesfolio',function ($q) use ($seg){
        //          return  $q->whereIn('source',unserialize($seg->booking_source));
        //     });

        })->when($seg->booking_source && unserialize($seg->booking_source) && isset(unserialize($seg->booking_source)[0]), function ($q) use ($seg){
            return $q->whereHas('profilesfolio',function ($q) use ($seg){
                return $q->whereIn('source', unserialize($seg->booking_source));
            });

        })->when($seg->wedding_bday_from ==null and $seg->wedding_bday_to !=null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->when($seg->wedding_bday_from !=null and $seg->wedding_bday_to == null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')]);
        })->when($seg->wedding_bday_from != null and $seg->wedding_bday_to !=null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->get();

            foreach ($contacts as $contact) {
                if(!in_array($contact->email,$ex)) {
                    $recipientCount++; // Menghitung jumlah penerima yang valid
                    $campaign->contact()->attach($contact);
                    $campaign->contact()->updateExistingPivot($contact, ['status' => 'queue']);
                }
            }
            $campaign->segment()->attach($segment);
            $campaign->template()->attach($request->template);
            
            // Log campaign creation activity dengan jumlah penerima
            $this->logActivity(
                'create_campaign',
                Campaign::class,
                $campaign->id,
                null,
                [
                    'name' => $campaign->name,
                    'type' => $campaign->type,
                    'status' => $campaign->status,
                    'template_id' => $campaign->template_id,
                    'recipient_count' => $recipientCount
                ],
                'User created a new campaign: ' . $campaign->name . ' with ' . $recipientCount . ' recipients'
            );
            
            $this->setSheduleFunc($campaign->id,$request->schedule);
            return redirect('campaigns');
        } else {
            $contacts=$cat->email;
            
            foreach ($contacts as $contact){
                if(!in_array($contact->email,$ex)) {
                    $recipientCount++; // Menghitung jumlah penerima yang valid
                    $campaign->external()->attach($contact);
                    $campaign->external()->updateExistingPivot($contact, ['status' => 'queue']);
                }
            }
            $campaign->externalSegment()->attach($segment);
            $campaign->template()->attach($request->template);
            
            // Log campaign creation activity dengan jumlah penerima
            $this->logActivity(
                'create_campaign',
                Campaign::class,
                $campaign->id,
                null,
                [
                    'name' => $campaign->name,
                    'type' => $campaign->type,
                    'status' => $campaign->status,
                    'template_id' => $campaign->template_id,
                    'recipient_count' => $recipientCount
                ],
                'User created a new campaign: ' . $campaign->name . ' with ' . $recipientCount . ' recipients'
            );
            
            $this->setSheduleFunc($campaign->id,$request->schedule);
            return redirect('campaigns');
        }
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        //
        $action='edit';
        $model=Campaign::find($id);
        return view('campaigns.manage',['action'=>$action,'model'=>$model]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $seg
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $seg, $id)
    {
        $contacts=Contact::with('transaction','profilesfolio')->when($seg->country_id !=null,function ($q) use ($seg){
            return $q->whereIn('country_id',$seg->country_id);
        })->when($seg->guest_status !=null,function ($q) use ($seg){
            return $q->whereHas('profilesfolio',function ($q) use ($seg){
                return $q->whereIn('foliostatus',$seg->guest_status);
            });
        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->spending_from !=null and $seg->spending_to ==null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('revenue','>=',str_replace('.','',$seg->spending_from));
            });
        })->when($seg->spending_from !=null and $seg->spending_to !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->gender !=null,function ($q) use ($seg) {
            return $q->whereIn('gender', $seg->gender);
        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
            return  $q->whereHas('transaction', function ($q) use ($seg) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
            });
        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from);
        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
            return $q->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);

        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from!=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to!=null,function ($q) use ($seg){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
        })->when($seg->booking_source!=null,function ($q) use ($seg){
            $q->whereHas('profilesfolio',function ($q) use ($seg){
                $q->whereIn('source',$seg->booking_source);
            });
        })
            ->get();
     // dd($seg->all());
        $campaign=Campaign::find($id);
        $campaign->name=$seg->name;
        $campaign->type=$seg->type;
        $campaign->country_id=serialize($seg->country_id);
        $campaign->guest_status=$seg->guest_status;
        $campaign->spending_from=str_replace('.','',$seg->spending_from);
        $campaign->spending_to=str_replace('.','',$seg->spending_to);
        $campaign->stay_from=Carbon::parse($seg->stay_from)->format('Y-m-d');
        $campaign->stay_to=Carbon::parse($seg->stay_to)->format('Y-m-d');
        $campaign->total_stay_from=$seg->total_stay_from;
        $campaign->total_stay_to=$seg->total_stay_to;
        $campaign->total_night_from=$seg->total_night_from;
        $campaign->total_night_to=$seg->total_night_to;
        $campaign->gender=serialize($seg->gender);
        $campaign->age_from=$seg->age_from;
        $campaign->age_to=$seg->age_to;
        $campaign->booking_source=serialize($seg->booking);
        if ($seg->template<>'') {
            $campaign->template_id = $seg->template;
        }
        $campaign->save();
        $campaign->template()->sync($seg->template);
        $campaign->contact()->detach();
        foreach ($contacts as $contact) {
            $campaign->contact()->attach($contact);
            $campaign->contact()->updateExistingPivot($contact,['status'=>'queue']);
        }
        return redirect('campaign');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
       $campaign=Campaign::find($id);
       
       // Simpan informasi campaign untuk logging
       $campaignInfo = [
           'id' => $campaign->id,
           'name' => $campaign->name,
           'status' => $campaign->status
       ];
       
       foreach ($campaign->contact as $key => $contact) {
           $campaign->contact()->detach($contact->id);
       }
       foreach ($campaign->segment as $key => $value) {
           $campaign->segment()->detach($value->id);
       }
       foreach ($campaign->external as $key => $value) {
           $campaign->external()->detach($value->id);
       }
       foreach ($campaign->externalSegment as $key => $value) {
           $campaign->externalSegment()->detach($value->id);
       }
       $campaign->delete();
       
       // Log aktivitas penghapusan setelah campaign berhasil dihapus
       $this->logActivity(
           'delete',
           Campaign::class,
           $id,
           $campaignInfo,
           null,
           'Deleted campaign: ' . $campaignInfo['name']
       );

       return redirect()->back();
    }
    
    public function delete(Request $request)
    {
        $campaign=Campaign::find($request->id);
        
        // Simpan informasi campaign untuk logging
        $campaignInfo = [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'status' => $campaign->status
        ];
        
        if(!$campaign->contact->isEmpty()) {
            foreach ($campaign->contact as $key => $contact) {
                $campaign->contact()->detach($contact->id);
            }
        }
        if(!$campaign->segment->isEmpty()) {
            foreach ($campaign->segment as $key => $value) {
            $campaign->segment()->detach($value->id);
            }
        }
        if(!$campaign->external->isEmpty()) {
            foreach ($campaign->external as $key => $value) {
                $campaign->external()->detach($value->id);
            }
        }
        if(!$campaign->externalSegment->isEmpty()) {
            foreach ($campaign->externalSegment as $key => $value) {
                $campaign->externalSegment()->detach($value->id);
            }
        }
        $campaign->delete();
        
        // Log aktivitas penghapusan setelah campaign berhasil dihapus
        $this->logActivity(
           'delete',
           Campaign::class,
           $request->id,
           $campaignInfo,
           null,
           'Deleted campaign: ' . $campaignInfo['name']
        );

        return response('ok');
    }

    public function getRecepient(Request $seg){
        $contacts=Contact::with('transaction','profilesfolio')->when($seg->country_id !=null,function ($q) use ($seg) {
            return $q->whereIn('country_id', $seg->country_id);
        })->when($seg->area!=null,function($q) use ($seg){
            return $q->whereIn('area',$seg->area);
        })->when($seg->guest_status !=null,function ($q) use ($seg) {
            return $q->whereHas('profilesfolio', function ($q) use ($seg) {
                return $q->whereIn('foliostatus', $seg->guest_status);
            })->orderBy('created_at', 'desc');
        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->spending_from !=null and $seg->spending_to ==null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('revenue', '>=', str_replace('.', '', $seg->spending_from));
            });
        })->when($seg->spending_from !=null and $seg->spending_to !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->gender !=null,function ($q) use ($seg) {
            return $q->whereIn('gender', $seg->gender);
        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->bday_from ==null and $seg->bday_to !=null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($seg->bday_to)->format('m-d')]);
        })->when($seg->bday_from!=null and $seg->bday_to ==null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($seg->bday_from)->format('m-d')]);
        })->when($seg->bday_from !=null and $seg->bday_to !=null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') >= ?',[Carbon::parse($seg->bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') <= ?',[Carbon::parse($seg->bday_to)->format('m-d')]);
        })->when($seg->wedding_bday_from ==null and $seg->wedding_bday_to !=null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->when($seg->wedding_bday_from !=null and $seg->wedding_bday_to == null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')]);
        })->when($seg->wedding_bday_from != null and $seg->wedding_bday_to !=null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
            return  $q->whereHas('transaction', function ($q) use ($seg) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
            });
        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from);
        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
            return $q->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->name !=null,function ($q) use ($seg){
            return $q->whereRaw('CONCAT(fname,lname) like \'%'.$seg->name.'%\'');
        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from!=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to!=null,function ($q) use ($seg){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
        })->when($seg->booking_source!=null,function ($q) use ($seg){
            $q->whereHas('profilesfolio',function ($q) use ($seg){
                $q->whereIn('source',$seg->booking_source);
            });
        })
            ->get();

        return response($contacts,200);
    }

    public function getType(Request $seg){

        $template=MailEditor::where('type',$seg->type)->pluck('name','id')->all();
        return response()->json($template);
    }
    public function activateCampaign(Request $seg){

       $campaign=Campaign::find($seg->id);
       if ($seg->status=='on'){
           $campaign->status='Active';
           $campaign->save();
       }else{
           $campaign->status='Inactive';
           $campaign->save();
       }

       return response($campaign,200);
    }

    public function setSheduleFunc($campaign_id,$date){
        $schedule=Schedule::updateOrCreate(
            ['campaign_id'=>$campaign_id],
            ['schedule'=>Carbon::parse($date)->format('Y-m-d H:i')]
        );
        $campaign=Campaign::find($campaign_id);
        $campaign->status='Scheduled';
        $campaign->save();
        return response(['status'=>'success','id'=>$schedule->id,'campstatus'=>$campaign->status,'schedule'=>$schedule->schedule,'campaignid'=>$campaign_id],200);
    }
    public function updateschedule(Request $seg){
        // Mendapatkan campaign dan schedule lama sebelum diupdate
        $campaign = Campaign::find($seg->id);
        $oldSchedule = $campaign->schedule ? $campaign->schedule->schedule : null;
        
        // Memanggil fungsi untuk update schedule dengan parameter yang benar ($seg->value)
        $result = $this->setSheduleFunc($seg->id, $seg->value);
        
        // Log perubahan schedule
        $this->logActivity(
            'update_campaign_schedule',
            Campaign::class,
            $seg->id,
            ['schedule' => $oldSchedule],
            ['schedule' => Carbon::parse($seg->value)->format('Y-m-d H:i')],
            'User updated schedule for campaign: ' . $campaign->name . ' from ' . 
            ($oldSchedule ?: 'unscheduled') . ' to ' . Carbon::parse($seg->value)->format('Y-m-d H:i')
        );
        
        return $result;
    }

    public function getSegment(Request $seg){

        $campaign=Segment::find($seg->id);
        if (!empty($campaign->country_id)){
           $country=unserialize($campaign->country_id);
        }else{
            $country='';
        }
        if (!empty($campaign->area)){
            $area=unserialize($campaign->area);
        }else{
            $area='';
        }
        if(!empty($campaign->guest_status)){
            $guestsatus=unserialize($campaign->guest_status);
        }else{
            $guestsatus='';
        }
        if(!empty($campaign->gender)){
            $gender=unserialize($campaign->gender);
        }else{
            $gender='';
        }
        if(!empty($campaign->booking_source)){
            $booking=unserialize($campaign->booking_source);
        }else{
            $booking='';
        }

        return response([$campaign,$country,$guestsatus,$gender,$booking,$area],200);
    }
    // public function newCampaign(Request $seg){

    //     $rules=[
    //        'cname'=>'required',
    //       'schedule'=>'required',
    //     ];
    //     $messages=[
    //         'cname.required'=>'Campaign name Required',
    //        'schedule.required'=>'Schedule Required',
    //     ];

    //     $validator =Validator::make($seg->all(),$rules,$messages);
    //     if(!$validator->fails()){
    //         $campaign=new Campaign();
    //         $campaign->name=$seg->cname;
    //         $campaign->status='Draft';
    //         $campaign->type='Promo';
    //         // $campaign->segment_id=$seg->segment_id;
    //         $campaign->template_id=$seg->template_id;
    //         $campaign->save();

    //         foreach ($seg->contacts as $cid){
    //             $contact=Contact::find($cid['value']);
    //             $campaign->contact()->attach($contact);
    //             $campaign->contact()->updateExistingPivot($contact,['status'=>'queue']);
    //         }
    //         $campaign->template()->attach($seg->template_id);
    //         $this->setSheduleFunc($campaign->id,$seg->schedule);
    //         return response('success',200);
    //     } else{
    //         return response(['errors'=>$validator->errors()]);
    //     }

    // }

    public function newCampaign(Request $seg){
        $rules=[
           'cname'=>'required',
           'schedule'=>'required',
           'template_id' => 'required',
           'contacts' => 'required|array'
        ];
        $messages=[
            'cname.required'=>'Campaign name Required',
            'schedule.required'=>'Schedule Required',
            'template_id.required' => 'Template Required',
            'contacts.required' => 'Contacts Required',
            'contacts.array' => 'Contacts must be array'
        ];

        $validator = Validator::make($seg->all(), $rules, $messages);
        
        if(!$validator->fails()){
            try {
                DB::beginTransaction();
                
                $campaign = new Campaign();
                $campaign->name = $seg->cname;
                $campaign->status = 'Draft';
                $campaign->type = 'Promo';
                $campaign->template_id = $seg->template_id;
                $campaign->save();

                // Pastikan contacts adalah array sebelum di-loop
                if(is_array($seg->contacts)) {
                    foreach ($seg->contacts as $contactId) {
                        // Ambil ID contact langsung jika format data berbeda
                        $id = is_array($contactId) ? $contactId['value'] : $contactId;
                        $contact = Contact::find($id);
                        
                        if($contact) {
                            $campaign->contact()->attach($contact, ['status' => 'queue']);
                        }
                    }
                }
                
                $campaign->template()->attach($seg->template_id);
                $this->setSheduleFunc($campaign->id, $seg->schedule);
                
                // Add user activity log
                $this->logActivity(
                    'create',
                    Campaign::class,
                    $campaign->id,
                    null,
                    [
                        'name' => $campaign->name,
                        'type' => $campaign->type,
                        'template_id' => $campaign->template_id,
                        'contacts_count' => is_array($seg->contacts) ? count($seg->contacts) : 0
                    ],
                    'Created new campaign: ' . $campaign->name
                );
                
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Campaign created successfully']);
                
            } catch(\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create campaign',
                    'error' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    }
    public function saveSegment(Request $seg){
        $rules=['name'=>'required'];
        $message=['name.required'=>'Segment Name Required'];
        $validator=Validator::make($seg->all(),$rules,$message);
        
        if(!$validator->fails()){
            // Perbaikan serialisasi - pastikan selalu array kosong jika null/kosong
            $guest_status = $seg->has('guest_status') && is_array($seg->guest_status) && !empty($seg->guest_status) 
                           ? serialize($seg->guest_status) 
                           : serialize([]);
            $country_id = $seg->has('country_id') && is_array($seg->country_id) && !empty($seg->country_id) 
                         ? serialize($seg->country_id) 
                         : serialize([]);
            $gender = $seg->has('gender') && is_array($seg->gender) && !empty($seg->gender) 
                     ? serialize($seg->gender) 
                     : serialize([]);
            $booking_source = $seg->has('booking_source') && is_array($seg->booking_source) && !empty($seg->booking_source) 
                             ? serialize($seg->booking_source) 
                             : serialize([]);
            $area = $seg->has('area') && is_array($seg->area) && !empty($seg->area) 
                   ? serialize($seg->area) 
                   : serialize([]);
            
            $segment=new Segment();
            $segment->name=$seg->name;
            $segment->guest_status=$guest_status;
            $segment->country_id=$country_id;
            $segment->area=$area;
            $segment->gender=$gender;
            $segment->booking_source=$booking_source;
            if($seg->stay_from!=null){
                $segment->stay_from=Carbon::parse($seg->stay_from)->format('Y-m-d');
            } else
            {
                $segment->stay_from=null;
            }
            if($seg->stay_to!=null){
                $segment->stay_to=Carbon::parse($seg->stay_to)->format('Y-m-d');
            } else{
                $segment->stay_to=null;
            }
            if($seg->spending_from!=null){
                $segment->spending_from=str_replace('.','',$seg->spending_from);
            }else{
                $segment->spending_from=null;
            }
            if($seg->spending_to!=null){
                $segment->spending_to=str_replace('.','',$seg->spending_to);
            }else{
                $segment->spending_to=null;
            }

            $segment->total_stay_from=$seg->total_stay_from;
            $segment->total_stay_to=$seg->total_stay_to;
            $segment->total_night_from=$seg->total_night_from;
            $segment->total_night_to=$seg->total_night_to;
            $segment->age_from=$seg->age_from;
            $segment->age_to=$seg->age_to;
            if($seg->bday_from){
                $segment->bday_from=Carbon::parse($seg->bday_from)->format('Y-m-d');
            }else{
                $segment->bday_from=NULL;
            }
            if ($seg->bday_to){
                $segment->bday_to=Carbon::parse($seg->bday_to)->format('Y-m-d');
            }else{
                $segment->bday_to=NULL;
            }
            if($seg->wedding_bday_from){
                $segment->wedding_bday_from=Carbon::parse($seg->wedding_bday_from)->format('Y-m-d');
            }else{
                $segment->wedding_bday_from=NULL;
            }
            if($seg->wedding_bday_to){
                $segment->wedding_bday_to=Carbon::parse($seg->wedding_bday_to)->format('Y-m-d');
            }else{
                $segment->wedding_bday_to=NULL;
            }
            $segment->save();
            
            // Log the segment creation activity
            $this->logActivity(
                'create',
                'Segment',
                $segment->id,
                null,
                $segment->toArray(),
                'Created new segment: ' . $segment->name
            );
            
            return response(['success'=>['id'=>$segment->id,'name'=>$seg->name]],200);
        }else{
            return response(['error'=>$validator->errors()],200);
        }



    }

}
