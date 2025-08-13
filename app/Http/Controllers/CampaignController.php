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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
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
        // Define column mapping for DataTables sorting
        $columns = [
            0 => 'id', // Row number (handled separately)
            1 => 'name',
            2 => 'segment', // Requires special handling due to relationship
            3 => 'status',
            4 => 'schedule', // Requires special handling due to relationship
            5 => 'accepted', // Calculated field from email responses
            6 => 'delivered', // Calculated field from email responses
            7 => 'opened', // Calculated field from email responses
            8 => 'clicked', // Calculated field from email responses
            9 => 'unsubscribed', // Calculated field from email responses
            10 => 'failed', // Calculated field from email responses
            11 => 'rejected' // Calculated field from email responses
        ];
    
        // Get total count of campaigns with schedules
        $totalData = Campaign::whereHas('schedule')->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        
        // Extract sorting parameters
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'created_at';
    
        // Build base query with eager loading for performance
        $query = Campaign::with(['schedule', 'segment', 'externalSegment', 'emailresponse'])
                    ->whereHas('schedule');
    
        // Apply search filter if provided
        if(!empty($search)){
            $query->where('name','LIKE',"%$search%");
            $totalFiltered = $query->count();
        }
    
        // Check if sorting is needed for calculated columns
        $isCalculatedColumn = in_array($orderColumn, ['accepted', 'delivered', 'opened', 'clicked', 'unsubscribed', 'failed', 'rejected']);
        
        if (!$isCalculatedColumn) {
            // Handle sorting for database columns
            switch($orderColumn) {
                case 'name':
                case 'status':
                    $query->orderBy($orderColumn, $orderDirection);
                    break;
                case 'schedule':
                    // Join with schedules table for sorting
                    $query->leftJoin('schedules', 'campaigns.id', '=', 'schedules.campaign_id')
                          ->orderBy('schedules.schedule', $orderDirection)
                          ->select('campaigns.*');
                    break;
                case 'segment':
                    // Join with segment tables for sorting by first segment name
                    $query->leftJoin('campaign_segment', 'campaigns.id', '=', 'campaign_segment.campaign_id')
                          ->leftJoin('segments', 'campaign_segment.segment_id', '=', 'segments.id')
                          ->orderBy('segments.name', $orderDirection)
                          ->select('campaigns.*')
                          ->distinct();
                    break;
                default:
                    // Default sorting by creation date
                    $query->orderBy('created_at', $orderDirection);
                    break;
            }
            
            // Apply pagination for non-calculated columns
            $campaignlist = $query->offset($start)->limit($limit)->get();
        } else {
            // For calculated columns, fetch all data first (pagination applied later)
            $campaignlist = $query->get();
        }
    
        $data = [];
    
        if(!empty($campaignlist)){
            foreach ($campaignlist as $item){
                // Determine segment name based on campaign type
                if($item->type=='internal'){
                    $segment = $item->segment->isNotEmpty() ? $item->segment[0]->name : '';
                }else{
                    $segment = $item->externalSegment->isNotEmpty() ? $item->externalSegment[0]->category : '';
                }
                
                // Filter email responses for current campaign
                $campaignResponses = $item->emailresponse->where('campaign_id', $item->id);
                
                // Calculate email statistics using efficient grouping
                $processedEvents = $campaignResponses->whereIn('event', ['processed','sent','open','click'])->groupBy('email_id');
                $sentEvents = $campaignResponses->whereIn('event', ['sent','open','click'])->groupBy('email_id');
                $openEvents = $campaignResponses->whereIn('event', ['open','click'])->groupBy('email_id');
                $clickEvents = $campaignResponses->whereIn('event', ['click']);
                $unsubscribeEvents = $campaignResponses->where('event', 'unsubscribe');
                $failedEvents = $campaignResponses->whereIn('event', ['hardbounce','softbounce','invalid']);
                $rejectedEvents = $campaignResponses->whereIn('event', ['dropped'])->groupBy('email_id');
                
                // Build response data structure
                $nestedData['type'] = $item->type;
                $nestedData['id'] = $item->id;
                $nestedData['name'] = $item->name;
                $nestedData['segment'] = $segment;
                $nestedData['status'] = $item->status;
                $nestedData['schedule'] = $item->schedule ? $item->schedule->schedule : '';
                $nestedData['accepted'] = $processedEvents->count();
                $nestedData['delivered'] = $sentEvents->count();
                $nestedData['opened'] = $openEvents->count();
                $nestedData['clicked'] = $clickEvents;
                $nestedData['unsubscribed'] = $unsubscribeEvents->count();
                $nestedData['failed'] = $failedEvents->count();
                $nestedData['rejected'] = $rejectedEvents->count();
                $nestedData['template'] = $item->template;
                $data[] = $nestedData;
            }
        }
    
        // Handle sorting for calculated columns
        if ($isCalculatedColumn) {
            // Sort data based on selected calculated column
            usort($data, function($a, $b) use ($orderColumn, $orderDirection) {
                $valueA = $orderColumn === 'clicked' ? count($a[$orderColumn]) : $a[$orderColumn];
                $valueB = $orderColumn === 'clicked' ? count($b[$orderColumn]) : $b[$orderColumn];
                
                if ($orderDirection === 'asc') {
                    return $valueA <=> $valueB;
                } else {
                    return $valueB <=> $valueA;
                }
            });
            
            // Apply pagination after sorting
            $data = array_slice($data, $start, $limit);
        }
    
        // Prepare JSON response for DataTables
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

        // Buat campaign utama dulu
        $campaign=new Campaign();
        $campaign->name=$request->name;
        $campaign->status='Pending Approval'; // Status baru
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
        })->when($seg->name !=null,function ($q) use ($seg){
            return $q->whereRaw('CONCAT(fname,lname) like \'%'.$seg->name.'%\'');
        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from!=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to!=null,function ($q) use ($seg){
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
       
       // Hapus schedule terlebih dahulu
       if ($campaign->schedule) {
           $campaign->schedule->delete();
       }
       
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
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Campaign ID is required'], 400);
        }
        
        $campaign = Campaign::find($id);
        
        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'Campaign not found'], 404);
        }
        
        try {
            // STEP 1: Laporkan pembatalan ke Campaign Center SEBELUM menghapus dari database
            $cancellationResult = $this->reportCampaignCancellation($campaign);
            
            // STEP 2: Laporkan pembatalan untuk campaign children juga
            $children = Campaign::where('parent_campaign_id', $id)->get();
            foreach ($children as $child) {
                $this->reportCampaignCancellation($child);
            }
            
            // STEP 3: Setelah berhasil melaporkan pembatalan, baru hapus dari database lokal
            
            // Hapus campaign children terlebih dahulu
            foreach ($children as $child) {
                $this->detachCampaignRelations($child);
                $child->delete();
            }
            
            // Hapus campaign utama
            $campaignName = $campaign->name; // Simpan nama sebelum dihapus untuk logging
            $this->detachCampaignRelations($campaign);
            $campaign->delete();
            
            // Log aktivitas
            $this->logActivity('Campaign Deleted', 'Campaign ' . $campaignName . ' has been deleted');
            
            return response()->json([
                'success' => true, 
                'message' => 'Campaign deleted successfully',
                'cancellation_result' => $cancellationResult
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete campaign', [
                'campaign_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    private function reportCampaignCancellation($campaign)
    {
        if (!$campaign->campaign_center_id) {
            Log::warning('Campaign cancellation not reported: no campaign_center_id', [
                'campaign_id' => $campaign->id,
                'campaign_name' => $campaign->name
            ]);
            return ['success' => false, 'reason' => 'No campaign_center_id'];
        }
        
        try {
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL') . "/api/schedule/cancel/{$campaign->campaign_center_id}";
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            $requestData = [
                'app_code' => env('CAMPAIGN_CENTER_CODE', 'RRP'),
                'reason' => 'Campaign no longer needed'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->delete($campaignCenterUrl, $requestData);
            
            Log::info('Campaign cancellation reported', [
                'campaign_id' => $campaign->id,
                'campaign_center_id' => $campaign->campaign_center_id,
                'url' => $campaignCenterUrl,
                'request_data' => $requestData,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'successful' => $response->successful()
            ]);
            
            if ($response->successful()) {
                return [
                    'success' => true, 
                    'response' => $response->json()
                ];
            } else {
                Log::error('Campaign cancellation failed', [
                    'campaign_id' => $campaign->id,
                    'campaign_center_id' => $campaign->campaign_center_id,
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return [
                    'success' => false, 
                    'error' => 'HTTP ' . $response->status() . ': ' . $response->body()
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to report campaign cancellation', [
                'campaign_id' => $campaign->id,
                'campaign_center_id' => $campaign->campaign_center_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false, 
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper function untuk detach semua relasi campaign
     */
    private function detachCampaignRelations($campaign)
    {
        // Hapus schedule terlebih dahulu
        if ($campaign->schedule) {
            $campaign->schedule->delete();
        }
        
        // Detach contacts
        if (!$campaign->contact->isEmpty()) {
            foreach ($campaign->contact as $contact) {
                $campaign->contact()->detach($contact->id);
            }
        }
        
        // Detach segments
        if (!$campaign->segment->isEmpty()) {
            foreach ($campaign->segment as $segment) {
                $campaign->segment()->detach($segment->id);
            }
        }
        
        // Detach external contacts
        if (!$campaign->external->isEmpty()) {
            foreach ($campaign->external as $external) {
                $campaign->external()->detach($external->id);
            }
        }
        
        // Detach external segments
        if (!$campaign->externalSegment->isEmpty()) {
            foreach ($campaign->externalSegment as $externalSegment) {
                $campaign->externalSegment()->detach($externalSegment->id);
            }
        }
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
            $guest_status=serialize($seg->guest_status);
            $country_id=serialize($seg->country_id);
            $gender=serialize($seg->gender);
            $booking_source=serialize($seg->booking_source);
            $segment=new Segment();
            $segment->name=$seg->name;
            $segment->guest_status=$guest_status;
            $segment->country_id=$country_id;
            $segment->area=serialize($seg->area);
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

    private function requestCampaignApproval($data)
    {
        try {
            // Simulasi request ke campaign center
            $response = Http::timeout(30)->post(config('campaign.center_url') . '/api/request-approval', [
                'unit_id' => config('campaign.unit_id'),
                'campaign_data' => $data
            ]);
            
            if($response->successful()) {
                return $response->json();
            }
            
            // Fallback jika campaign center tidak response
            return [
                'status' => 'approved',
                'type' => 'single',
                'schedules' => [[
                    'date' => $data['schedule_date'],
                    'recipients_count' => $data['total_recipients']
                ]]
            ];
            
        } catch(\Exception $e) {
            // Fallback approval
            return [
                'status' => 'approved',
                'type' => 'single', 
                'schedules' => [[
                    'date' => $data['schedule_date'],
                    'recipients_count' => $data['total_recipients']
                ]]
            ];
        }
    }

    private function processApprovalResponse($originalCampaign, $allContacts, $approvalResponse, $request)
    {
        DB::beginTransaction();
        
        try {
            if($approvalResponse['status'] === 'rejected') {
                $originalCampaign->status = 'Rejected';
                $originalCampaign->save();
                throw new \Exception('Campaign rejected by campaign center');
            }
            
            $schedules = $approvalResponse['schedules'];
            $campaignsCreated = [];
            
            // Jika hanya satu schedule, update campaign asli
            if(count($schedules) === 1) {
                $this->finalizeSingleCampaign($originalCampaign, $allContacts, $schedules[0], $request);
                $campaignsCreated[] = $originalCampaign;
            } else {
                // Multiple schedules - buat campaign terpisah
                $campaignsCreated = $this->createMultipleCampaigns($originalCampaign, $allContacts, $schedules, $request);
                
                // Update campaign asli jadi parent/master
                $originalCampaign->status = 'Split into Multiple';
                $originalCampaign->save();
            }
            
            // Log activity
            $this->logActivity(
                'campaign_approval_processed',
                Campaign::class,
                $originalCampaign->id,
                null,
                [
                    'approval_status' => $approvalResponse['status'],
                    'campaigns_created' => count($campaignsCreated),
                    'total_recipients' => count($allContacts)
                ],
                'Campaign approval processed: ' . $approvalResponse['status'] . ' with ' . count($campaignsCreated) . ' campaigns created'
            );
            
            DB::commit();
            return $campaignsCreated;
            
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function finalizeSingleCampaign($campaign, $allContacts, $schedule, $request)
    {
        // Attach semua contacts
        $this->attachContactsOptimized($campaign, $allContacts);
        
        // Attach template dan segment
        $campaign->template()->attach($campaign->template_id);
        if($campaign->type === 'internal') {
            $campaign->segment()->attach($request->segments);
        } else {
            $campaign->externalSegment()->attach($request->category);
        }
        
        // Set schedule dan status
        $this->setSheduleFunc($campaign->id, $schedule['date']);
        
        return $campaign;
    }
    
    private function createMultipleCampaigns($originalCampaign, $allContacts, $schedules, $request)
    {
        $campaignsCreated = [];
        $contactIndex = 0;
        
        foreach($schedules as $index => $schedule) {
            // Buat campaign baru untuk setiap schedule
            $newCampaign = new Campaign();
            $newCampaign->name = $originalCampaign->name . ' - Part ' . ($index + 1);
            $newCampaign->status = 'Draft';
            $newCampaign->type = $originalCampaign->type;
            $newCampaign->template_id = $originalCampaign->template_id;
            $newCampaign->parent_campaign_id = $originalCampaign->id; // Reference ke campaign asli
            $newCampaign->save();
            
            // Ambil contacts sesuai jumlah yang dialokasikan
            $recipientsCount = $schedule['recipients_count'];
            $campaignContacts = array_slice($allContacts, $contactIndex, $recipientsCount);
            $contactIndex += $recipientsCount;
            
            // Attach contacts dengan batch processing untuk optimasi
            $this->attachContactsOptimized($newCampaign, $campaignContacts);
            
            // Attach template dan segment
            $newCampaign->template()->attach($originalCampaign->template_id);
            if($originalCampaign->type === 'internal') {
                $newCampaign->segment()->attach($request->segments);
            } else {
                $newCampaign->externalSegment()->attach($request->category);
            }
            
            // Set schedule
            $this->setSheduleFunc($newCampaign->id, $schedule['date']);
            
            $campaignsCreated[] = $newCampaign;
        }
        
        return $campaignsCreated;
    }

    private function attachContactsOptimized($campaign, $contacts)
    {
        // Batch insert untuk optimasi performance dengan 16k+ contacts
        $batchSize = 1000;
        $batches = array_chunk($contacts, $batchSize);
        
        foreach($batches as $batch) {
            $attachData = [];
            foreach($batch as $contact) {
                $attachData[$contact->id] = ['status' => 'queue'];
            }
            $campaign->contact()->attach($attachData);
        }
    }

}
