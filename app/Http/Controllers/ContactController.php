<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\CheckContact;
use App\Models\Contact;
use App\Models\Country;

use App\Models\EmailReview;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\PostStay;
use App\Models\ProfileFolio;
use App\Models\RoomType;
use App\Models\Transaction;
use Carbon\Carbon;

use Illuminate\Http\Request;
//use DB;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Traits\UserLogsActivity;

class ContactController extends Controller
{
    use UserLogsActivity;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function ages($type){

        $tr=Contact::with('transaction')->when($type=='low',function ($q){
            return $q->whereRaw('DATEDIFF(DATE_FORMAT(now(),\'%y-%m-%d\'),birthday) /365 < 30 ');
        })->when($type=='mid',function ($q){
            return $q->whereRaw('DATEDIFF(DATE_FORMAT(now(),\'%y-%m-%d\'),birthday) /365 between 30 and 60 ');
        })->when($type=='high',function ($q){
            return $q->whereRaw('DATEDIFF(DATE_FORMAT(now(),\'%y-%m-%d\'),birthday) /365 > 60 ');
        })->get();
        return view('contacts.list',['data'=>$tr]);

    }
    public function source($type){
        $contacts=Contact::whereHas('profilesfolio',function ($q) use ($type){
            return $q->where('source','=',$type);
        })->get();
        return view('contacts.list',['data'=>$contacts]);
    }
    public function reviews(){
        $ta=json_decode(file_get_contents('tripadvisor.json'));
//        dd($ta);
       // $ta_reviews=collect($ta->reviews);
	$ta_reviews=\App\Models\Reviews::where('source','=','tripadvisor')->orderBy('created_at','desc')->take(15)->get();
	//dd($ta_reviews);
        $currentPageTa = LengthAwarePaginator::resolveCurrentPage('ta');
        $currentPageB=LengthAwarePaginator::resolveCurrentPage('bo');
        $currentPageH=LengthAwarePaginator::resolveCurrentPage('ht');
        $perPage = 15;
        $currentResultsTa = $ta_reviews->slice(($currentPageTa - 1) * $perPage, $perPage)->take(10);

        $filebooking=file_get_contents('booking.json');
        $databooking=json_decode($filebooking,true);
        $booking_review=collect($databooking["reviews"]["reviewlist"]);

        $resultsta = new LengthAwarePaginator($currentResultsTa, $ta_reviews->count(), $perPage);
        $currentResultsB = $booking_review->slice(($currentPageB - 1) * $perPage, $perPage)->take(10);
        $resultsbooking=new LengthAwarePaginator($currentResultsB,$booking_review->count(),$perPage);

        $ht=json_decode(file_get_contents('hotels.json'));
        $ht_reviews=collect($ht->reviews);
        $currentResultsH=$ht_reviews->slice(($currentPageH-1)* $perPage,$perPage)->take(10);
        $resutlsHotel=new LengthAwarePaginator($currentResultsH,$ht_reviews->count(),$perPage);
        $poststay=DB::select(DB::raw('select sum(cleanliness)/count(*) as cleanliness,sum(comfort)/count(*) as comfort ,sum(location)/count(*) as location,sum(facilities)/count(*) as facilities,sum(staff)/count(*) as staff,sum(vfm)/count(*) as vfm,sum(wifi)/count(*) as wifi, (cleanliness+comfort+location+facilities+staff+vfm+wifi)*2/7 as total from email_reviews '));
        $poststaydata=EmailReview::all();
        return view('review.index',['tripadvisor'=>$ta,'ta_reviews'=>$ta_reviews,'booking'=>$databooking,'booking_reviews'=>$resultsbooking,'hotels'=>$ht,'hotelreview'=>$resutlsHotel,'poststay'=>$poststay,'poststaydata'=>$poststaydata]);
    }
    public function dashboard(){
        // Dapatkan tanggal data terakhir yang tersedia
        $latestDataDate = $this->getLatestDataDate();
        $dashboardMonths = env('DASHBOARD_MONTHS_PERIOD', 3);
        $useLatestData = env('DASHBOARD_USE_LATEST_DATA', true);
        
        // Tentukan periode dashboard
        if ($useLatestData && $latestDataDate) {
            $endDate = Carbon::parse($latestDataDate);
            $startDate = $endDate->copy()->subMonths($dashboardMonths);
        } else {
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMonths($dashboardMonths);
        }
        
        $total=0;
        $contact=Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?',[Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?',[Carbon::now()->addDays(7)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'),'asc')->limit(10)
            ->get();

        // Update query untuk menggunakan periode yang ditentukan
        $contacts=DB::select(DB::raw('select country as label, count(*) as value from contacts left join countries on contacts.country_id=countries.iso3 left join contact_transaction on contact_transaction.contact_id=contacts.contactid left join transactions on transactions.id=contact_transaction.transaction_id LEFT JOIN profilesfolio on profilesfolio.profileid = contacts.contactid where profilesfolio.dateci between ? and ? group by label order by value DESC'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        $country=json_encode($contacts);

        foreach ($contacts as $value){
            $total=$total+$value->value;
        }

        // Update query untuk contacts added
        $dateS = $startDate->copy()->startOfMonth();
        $dateE = $endDate->copy()->endOfMonth();
        $added=Contact::select(DB::raw('DATE_FORMAT(created_at,\'%Y %M\') as created,count(*) as count'))
            ->whereBetween('created_at',[$dateS,$dateE])
            ->groupBy(DB::raw('DATE_FORMAT(created_at,\'%Y %M\')'))
            ->get();

        $data=[];
        foreach ($added as $item) {
            $tmp=['x'=>$item->created,'y'=>$item->count];
            array_push($data,$tmp);
        }

        $data=json_encode($data);
        $status=DB::select(DB::raw('select foliostatus as status,count(*) as count from profilesfolio p inner join contacts c on c.contactid=p.profileid group by foliostatus'));
        $datastatus=[];
        foreach ($status as $item){
            if($item->status=='I'){
                $st='Inhouse';
            }elseif ($item->status=='O'){
                $st='Check Out';
            }elseif ($item->status=='C'){
                $st='Confirm';
            }elseif($item->status=='G'){
                $st='Guaranteed';
	        }elseif($item->status=='T'){
                $st='Tentative';
	        }else{
                $st='Cancel';
            }
            $tmp=['x'=>$st,'y'=>$item->count];
            array_push($datastatus,$tmp);
        }
        $datastatus=json_encode($datastatus);

        $spending=DB::select(DB::raw('SELECT distinct c.contactid, c.fname,c.lname,a.revenue from transactions a left join contact_transaction b on b.transaction_id=a.id left JOIN contacts c on b.contact_id=c.contactid left JOIN profilesfolio d ON d.profileid=c.contactid WHERE b.contact_id is not NULL and d.dateci BETWEEN ? and ? order by revenue desc limit 10;'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $dataspending=[];
        foreach ($spending as $sp){
              $temspend=['x'=>$sp->fname.' '.$sp->lname,'y'=>$sp->revenue];
              array_push($dataspending,$temspend);
        }

       $dataspending=json_encode($dataspending);

        $stays=DB::select(DB::raw('SELECT cpt.contactid, cpt.fname, cpt.lname, COUNT(cpt.contactid) AS stays, sum(cpt.revenue) AS revenue FROM contact_transaction ct JOIN (SELECT cp.contactid, cp.fname, cp.lname, cp.folio_master,t.revenue, t.id AS transid FROM transactions t LEFT join (SELECT a.contactid, a.fname, a.lname, b.dateci, b.folio_master FROM contacts a JOIN profilesfolio b ON a.contactid=b.profileid WHERE b.folio_master=b.folio AND b.foliostatus = \'O\' order BY a.contactid ASC) cp ON t.resv_id = cp.folio_master WHERE cp.contactid IS NOT NULL AND cp.dateci BETWEEN ? and ?) cpt ON cpt.contactid = ct.contact_id AND ct.transaction_id = transid GROUP BY contactid ORDER BY stays DESC,revenue DESC LIMIT 10'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $datastays=[];
        foreach ($stays as $stay){
            $tempstay=['x'=>$stay->fname.' '.$stay->lname,'y'=>$stay->stays];
            array_push($datastays,$tempstay);
        }

        $datatrx=[];
        $trx=DB::select(DB::raw('select fname,lname,datediff(dateco,dateci) as hari ,sum(revenue) as rev from transactions a left join contact_transaction b on b.transaction_id=a.id left join contacts c on c.contactid=b.contact_id left JOIN profilesfolio d ON c.contactid=d.profileid where contact_id is not null AND d.dateci BETWEEN ? and ? group by fname order by hari desc, rev desc limit 10'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        foreach ($trx as $tr){
            $tmp=['x'=>$tr->fname .' '.$tr->lname,'y'=>$tr->hari ];
            array_push($datatrx,$tmp);
        }

        $datatrx=json_encode($datatrx);

        $contacts_age=DB::select(DB::raw('select sum(if(floor(datediff(?,birthday)/365) <30,1,0)) as low, sum(if(floor(datediff(?,birthday)/365) >=30 and floor(datediff(?,birthday)/365)<=60,1,0)) as mid,sum(if(floor(datediff(?,birthday)/365) >=60,1,0)) as high from contacts where created_at BETWEEN ? and ?'), [$endDate->format('Y-m-d'), $endDate->format('Y-m-d'), $endDate->format('Y-m-d'), $endDate->format('Y-m-d'), $startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $data_age=[];
        array_push($data_age,['label'=>'Under 30','value'=>$contacts_age[0]->low,'type'=>'low']);
        array_push($data_age,['label'=>'Between 30 and 60','value'=>$contacts_age[0]->mid,'type'=>'mid']);
        array_push($data_age,['label'=>'Higher than 60','value'=>$contacts_age[0]->high,'type'=>'high']);
        $data_age=json_encode($data_age);
        $tages=$contacts_age[0]->low+$contacts_age[0]->mid+$contacts_age[0]->high;
        $room_type=DB::select(DB::raw('select room_name as label, count(*) as value from profilesfolio,room_type where roomtype is not null and room_type.room_code=profilesfolio.roomtype and profilesfolio.dateci between ? and ? group by roomtype order by value ASC'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $data_room_type=[];
        $troom=0;
        foreach ($room_type as $item) {
            $tmp=['label'=>$item->label,'value'=>$item->value];
            array_push($data_room_type,$tmp);
            $troom+=$item->value;
        }
        $data_room_type=json_encode($data_room_type);
        $reviews=json_decode(file_get_contents('tripadvisor.json'));
        $filebooking=file_get_contents('booking.json');
        $databooking=json_decode($filebooking,true);
        //dd($databooking["reviews"]["total"]);
        $bookingsource=[];
	 $tbookingsource=0;
        $booking=DB::select(DB::raw('select count(*) as count ,source from contacts left join contact_transaction on contact_transaction.contact_id=contacts.contactid LEFT JOIN transactions on contact_transaction.transaction_id=transactions.id LEFT JOIN profilesfolio on contactid=profileid where contacts.created_at between ? and ? group by source order by count ASC'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        foreach ($booking as $b){
        	if($b->source!=null){
		   array_push($bookingsource,['label'=>$b->source,'value'=>$b->count]);
		}

		$tbookingsource+=$b->count;
        }
        $datastays=json_encode($datastays);

        $databookingsource=json_encode($bookingsource);
        $temailcount=0;
        $dataemail=[];
        $emails=DB::select(DB::raw('select event,count(*) as count from (select event from mailgun_logs where timestamp in (select timestamp from mailgun_logs where timestamp between ? and ? group by message_id,recipient) and event<>\'Testing\' group by recipient,message_id ) a group by EVENT'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        foreach ($emails as $email){
            array_push($dataemail,['label'=>ucfirst($email->event),'value'=>$email->count]);
            $temailcount+=$email->count;
        }

        $dataemailreport=json_encode($dataemail);

        $inhouseRepeaterCount = Contact::inhouseRepeaters()->count();
        $totalRepeaterCount = Contact::repeaters()
            ->whereHas('transaction', function($q) {
                return $q->havingRaw('sum(revenue) >= 0');
            })
            ->count();

        // Tambahkan statistik untuk in-house birthday today
        $inhouseBirthdayTodayCount = Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") = ?', [Carbon::now()->format('m-d')])
            ->whereHas('profilesfolio', function($q) {
                return $q->where('foliostatus', '=', 'I');
            })
            ->count();
        
        // Format tanggal untuk ditampilkan di view
        $dateRangeDisplay = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');
            
            return view('main.index',[
                'data'=>$contact,
                'datastay'=>$datastays,
                'country'=>$country,
                'total'=>$total,
                'monthcount'=>$data,
                'countstatus'=>$datastatus,
                'spending'=>$dataspending,
                'longstay'=>$datatrx,
                'data_age'=>$data_age,
                'tages'=>$tages,
                'room_type'=>$data_room_type,
                'troom'=>$troom,
                'reviews'=>$reviews,
                'booking_com'=>$databooking,
                'databookingsource'=>$databookingsource,
                'tbookingsource'=>$tbookingsource,
                'dataemailreport'=>$dataemailreport,
                'temailcount'=>$temailcount,
                'inhouseRepeaterCount'=>$inhouseRepeaterCount,
                'totalRepeaterCount'=>$totalRepeaterCount,
                'inhouseBirthdayTodayCount'=>$inhouseBirthdayTodayCount,
                'dateRangeDisplay'=>$dateRangeDisplay,
                'latestDataDate'=>$latestDataDate,
               
            ]);
        }
            

 public function getRepeaterMonthlyData($months = 3) {
    // Dapatkan tanggal data terakhir yang tersedia
    $latestDataDate = $this->getLatestDataDate();
    $useLatestData = env('DASHBOARD_USE_LATEST_DATA', true);
    
    // Tentukan periode berdasarkan konfigurasi
    if ($useLatestData && $latestDataDate) {
        $dateE = Carbon::parse($latestDataDate)->endOfMonth();
        $dateS = $dateE->copy()->startOfMonth()->subMonths($months - 1);
    } else {
        $dateS = Carbon::now()->startOfMonth()->subMonths($months - 1);
        $dateE = Carbon::now()->endOfMonth();
    }
    
    // Query yang dioptimalkan - mengurangi redundant operations
    $repeaterData = DB::select(DB::raw("
        SELECT 
            DATE_FORMAT(pf.dateci, '%Y %M') as month_year,
            COUNT(DISTINCT c.contactid) as repeater_count
        FROM contacts c
        INNER JOIN contact_transaction ct ON ct.contact_id = c.contactid
        INNER JOIN transactions t ON t.id = ct.transaction_id
        INNER JOIN profilesfolio pf ON pf.folio = t.resv_id
        WHERE pf.dateci BETWEEN ? AND ?
        AND EXISTS (
            SELECT 1 
            FROM contact_transaction ct2
            INNER JOIN transactions t2 ON t2.id = ct2.transaction_id
            WHERE ct2.contact_id = c.contactid
            GROUP BY ct2.contact_id
            HAVING COUNT(DISTINCT t2.resv_id) > 1
        )
        GROUP BY DATE_FORMAT(pf.dateci, '%Y %M'), YEAR(pf.dateci), MONTH(pf.dateci)
        ORDER BY YEAR(pf.dateci) ASC, MONTH(pf.dateci) ASC
    "), [$dateS->format('Y-m-d'), $dateE->format('Y-m-d')]);
    
    $data = [];
    foreach ($repeaterData as $item) {
        $data[] = ['x' => $item->month_year, 'y' => (int)$item->repeater_count];
    }
    
    return $data;
}

public function getRepeaterDataAjax(Request $request) {
    $months = $request->input('months', 3);
    $data = $this->getRepeaterMonthlyData($months);
    return response()->json($data);
}


/**
 * Filter contacts berdasarkan bulan repeater
 */
public function repeaterByMonth($month)
{
    // Parse bulan dan tahun dari parameter
    $monthYear = explode(' ', $month);
    
    if (is_numeric($monthYear[0])) {
        $year = $monthYear[0];
        $monthName = $monthYear[1];
    } else {
        $monthName = $monthYear[0];
        $year = $monthYear[1];
    }
    
    $monthNumber = str_pad(date('m', strtotime($monthName)), 2, '0', STR_PAD_LEFT);
    $startDate = "{$year}-{$monthNumber}-01";
    $endDate = date('Y-m-t', strtotime($startDate));
    
    // Query yang dioptimalkan dengan CTE
    $contactIds = DB::select(DB::raw("
        WITH repeater_contacts AS (
            SELECT DISTINCT ct.contact_id
            FROM contact_transaction ct
            INNER JOIN transactions t ON t.id = ct.transaction_id
            GROUP BY ct.contact_id
            HAVING COUNT(DISTINCT t.resv_id) > 1
        )
        SELECT DISTINCT c.contactid
        FROM contacts c
        INNER JOIN repeater_contacts rc ON rc.contact_id = c.contactid
        INNER JOIN contact_transaction ct ON ct.contact_id = c.contactid
        INNER JOIN transactions t ON t.id = ct.transaction_id
        INNER JOIN profilesfolio pf ON pf.folio = t.resv_id
        WHERE pf.dateci BETWEEN ? AND ?
    "), [$startDate, $endDate]);
    
    $ids = array_column($contactIds, 'contactid');
    
    $contacts = Contact::with(['transaction', 'profilesfolio', 'country'])
        ->whereIn('contactid', $ids)
        ->withCount('profilesfolio')
        ->get();
    
    return view('contacts.list', [
        'data' => $contacts,
        'title' => "Repeater Contacts - {$month}",
    ]);
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $contact = new Contact();
        $act = 'add';
        
        // Log the activity
        $this->logActivity(
            'create_contact_form',
            Contact::class,
            null,
            null,
            null,
            'Accessed contact creation form'
        );
        
        return view('contacts.detail', ['data' => $contact, 'action' => $act]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function longest($contact){
        $contact=DB::select(DB::raw('select * from contacts where CONCAT(fname,\' \',lname)=\''.$contact.'\''));
        return $this->show($contact[0]->contactid);
    }

    public function spending($name)
    {
        $contact=DB::select(DB::raw('select contactid from contacts where CONCAT(fname,\' \',lname)=\''.$name.'\''));
//        dd($contact);
        return $this->show($contact[0]->contactid);
    }

    public function updateexcluded(Request $request)
    {
        $val = $request->val;
        $contact = Contact::find($request->id);
        $email = $contact->email;

        $oldData = [
            'excluded' => $val == 1 ? false : true,
            'email' => $email
        ];

        $newData = [
            'excluded' => $val == 1 ? true : false,
            'email' => $email
        ];

        if($val == 0) {
            $em = ExcludedEmail::where('email', '=', $email)->first();
            $em->delete();
        } else {
            ExcludedEmail::insert([
                'email' => $email,
                'contact_id' => $request->id,
                'reason' => 'Email blacklisted manually'
            ]);
        }

        // Log the activity
        $this->logActivity(
            $val == 1 ? 'exclude_email' : 'include_email',
            Contact::class,
            $contact->contactid,
            $oldData,
            $newData,
            $val == 1 ? "Email {$email} was excluded" : "Email {$email} was included"
        );

        return response('success', 200);
    }
public function loadcontacts(Request $request){
    $contacts=Contact::with(['transaction'=>function($qq){
	      	return $qq->sum('revenue');
	    }])->whereHas('transaction',function ($q){
       	 return $q->whereNotNull('status')->where('revenue','>=',0);
	    })->withCount('campaign')->withCount('transaction')->get();
		return response($contacts);

}
public function contactslist(Request $request){
    $contacts=Contact::with([
        'transaction'=>function($qq){
        return $qq->sum('revenue');
        },
        'profilesfolio'=>function(){
        }
    ])->whereHas('transaction')->withCount('campaign')->withCount('transaction')->when(!empty($request->gender),function ($qq) use ($request){
        return $qq->where('gender','=',$request->gender);
    })->when(!empty($request->repeater_only),function ($qq) use ($request){
        return $qq->whereHas('transaction', function($q) {
            $q->select(DB::raw('contact_id'))
              ->groupBy('contact_id')
              ->havingRaw('COUNT(*) > 1');
        });
    })->withCount('excluded')->groupBy('contactid')
        ->get();


        $columns = array(
        0 =>'contactid',
        1 =>'fname',
        2 =>'lname',
        3 =>'birthday',
        4 =>'wedding_bday',
        5 =>'country_id',
        6 =>'area',
        7 =>'campaign_count',
        8 =>'transaction_count',
        9 =>'is_repeater',
        10=>'profilesfolio.dateci',
        11=>'revenue'
    );
    $totalData = $contacts->count();
    $totalFiltered = $totalData;
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');
    $search=$request->input('search.value');
    
    // Tambahkan penanganan khusus untuk sorting pada kolom checkin
    $orderBy = $order;
    $orderJoin = "";
    
    // Jika sorting pada kolom checkin (Last Stay)
    if($order == 'profilesfolio.dateci') {
        $orderBy = 'pf.dateci';
        $orderJoin = " LEFT JOIN profilesfolio as pf ON pf.profileid = contacts.contactid ";
    }
    
    // Tambahkan penanganan untuk sorting kolom is_repeater
    if($order == 'is_repeater') {
        $orderBy = 'transaction_count';
    }
    
    if(empty($search)){
        $contactslist=Contact::whereHas('transaction')
            ->join(DB::raw('(select id,sum(revenue) as revenue,contact_id from transactions left join contact_transaction on contact_transaction.transaction_id=transactions.id group by id) as revenue'),'revenue.contact_id','=','contactid')
            ->when($order == 'profilesfolio.dateci', function($query) use ($orderJoin) {
                return $query->leftJoin(DB::raw('profilesfolio as pf'), 'pf.profileid', '=', 'contactid');
            })
            ->withCount('campaign')->withCount('transaction')
            ->offset($start)->limit($limit)
            ->orderBy($orderBy, $dir)
            ->when(!empty($request->gender),function($qq) use ($request){
                return $qq->where('gender','=',$request->gender);
            })->when(!empty($request->repeater_only),function ($qq) use ($request){
                return $qq->whereHas('transaction', function($q) {
                    $q->select(DB::raw('contact_id'))
                      ->groupBy('contact_id')
                      ->havingRaw('COUNT(*) > 1');
                });
            })->withCount('excluded')->groupBy('contactid')->get();
    }else{
        $contactslist=Contact::whereHas('transaction')->join(DB::raw('(select id,sum(revenue) as revenue,contact_id from transactions left join contact_transaction on contact_transaction.transaction_id=transactions.id  group by id) as revenue'),'revenue.contact_id','=','contactid')
            ->withCount('campaign')->withCount('transaction')
            ->Where('fname','LIKE',"%{$search}%")
            ->orWhere('lname','LIKE',"%{$search}%")
            ->orWhere('email','LIKE',"%{$search}%")
            ->orWhereHas('country',function ($q) use ($search) {
                return $q->where('country', 'LIKE', "%{$search}%");
            })->orWhere('area','LIKE',"%{$search}%")
            ->orWhere(function($query) use ($search) {
                if (strtolower($search) == 'repeater') {
                    $query->whereHas('transaction', function($q) {
                        $q->select(DB::raw('contact_id'))
                          ->groupBy('contact_id')
                          ->havingRaw('COUNT(*) > 1');
                    });
                } elseif (strtolower($search) == 'new guest' || strtolower($search) == 'new') {
                    $query->whereHas('transaction', function($q) {
                        $q->select(DB::raw('contact_id'))
                          ->groupBy('contact_id')
                          ->havingRaw('COUNT(*) = 1');
                    });
                }
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($orderBy,$dir)
            ->when(!empty($request->gender),function($qq) use ($request){
                return $qq->where('gender','=',$request->gender);
            })->withCount('excluded')->groupBy('contactid')->get();

        $totalFiltered=count($contactslist);

    }

    $data=array();
    if (!empty($contactslist)){
        foreach ($contactslist as $key=>$list){
            $rev=0;
            foreach ($list->transaction as $transaction){
                $rev+=$transaction->revenue;
            }

            $nestedData['contactid']=$list->contactid;
            $nestedData['fname']=$list->fname;
            $nestedData['lname']=$list->lname;
            $nestedData['birthday']=$list->birthday;
            $nestedData['wedding_bday']=$list->wedding_bday;
            $nestedData['country_id']=$list->country_id;
            $nestedData['area']=$list->area;
            $nestedData['campaign']=count($list->campaign);
            $nestedData['stay']=count($list->transaction);
            $nestedData['is_repeater']=count($list->transaction) > 1 ? 1 : 0;
            $nestedData['checkin']=$list->profilesfolio[0]->dateci;
            $nestedData['revenue']=$rev;
            $nestedData['excluded']=$list->excluded;
            $data[]=$nestedData;
        }
    }
    $json_data=array(
        "draw"=>intval($request->input('draw')),
        "recordsTotal"=>intval($totalData),
        "recordsFiltered"=>intval($totalFiltered),
        "data"=>$data
    );
//    echo json_encode($json_data);

    return response($json_data);
}
    public function store(Request $request)
    {
        $contact=new Contact();
        $contact->fname=$request->fname;
        $contact->lname=$request->lname;
        $contact->ccid=2;
        $contact->salutation=$request->salutation;
        $contact->marital_status=$request->marital_status;
        $contact->gender=$request->gender;
        $contact->birthday=Carbon::parse($request->birthday)->format('Y-m-d');
        $contact->country_id=$request->country_id;
        $contact->email=$request->email;
        $contact->save();
        $attr=Attribute::where('attr_name','=','address1')->first();
        $contact->address1()->attach($attr->id,['value'=>$request->address1]);
        $attr=Attribute::where('attr_name','=','address2')->first();
        $contact->address2()->attach($attr->id,['value'=>$request->address2]);



        return redirect('contacts/detail/'.$contact->id);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
         public function show($id)
    {
        // Coba dapatkan kontak dengan transaksi
        $contacts = Contact::leftJoin('contact_transaction','contact_transaction.contact_id','=','contacts.contactid')
            ->leftJoin('transactions','contact_transaction.transaction_id','=','transactions.id')
            ->leftJoin('profilesfolio', function($join) {
                $join->on('profilesfolio.folio_master','=','transactions.resv_id')
                     ->on('profilesfolio.profileid','=','contacts.contactid');
            })
            ->leftJoin('room_type','room_type.room_code','=','profilesfolio.roomtype')
            ->where('contact_transaction.contact_id', $id)
            ->where('profilesfolio.profileid', '=', DB::raw('contacts.contactid'))
            ->groupBy('transactions.resv_id', 'profilesfolio.profileid', 'contacts.contactid')
            ->get();

        // Jika tidak ada data transaksi, ambil data kontak langsung
        if (empty($contacts) || count($contacts) == 0 || !isset($contacts[0]->transaction) || count($contacts[0]->transaction) == 0) {
            // Ambil data kontak langsung tanpa join ke transaksi
            $contact = Contact::where('contactid', $id)->first();
            
            if ($contact) {
                // Ambil profilesfolio langsung dari relasi
                $profilesfolios = ProfileFolio::where('profileid', $id)
                    ->leftJoin('room_type', 'room_type.room_code', '=', 'profilesfolio.roomtype')
                    ->get();
                
                // Jika ada profilesfolio, gunakan data tersebut
                if ($profilesfolios && count($profilesfolios) > 0) {
                    $contacts = collect([$contact]);
                    // Pastikan property yang diperlukan tersedia
                    $contact->transaction = collect([]);
                }
            }
        }

        $totalnight = 0;

        // Hitung total malam jika ada transaksi
        if (!empty($contacts) && count($contacts) > 0 && isset($contacts[0]->transaction) && count($contacts[0]->transaction) > 0) {
            foreach ($contacts[0]->transaction as $transaction) {
                $night = ProfileFolio::select('dateci','dateco')->where('folio_master', $transaction->resv_id)->first();
                if ($night) {
                    $night = Carbon::parse($night->dateco)->diffInDays(Carbon::parse($night->dateci));
                    $totalnight += $night;
                }
            }
        }

        return view('contacts.detail',['data'=>$contacts,'totalnight'=>$totalnight]);
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
    public  function getcountry(Request $request){
        $country=Country::where('iso2','=',$request->code)->first()['country'];
        return response($country);
    }


/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function update(Request $request)
{
    $contact = Contact::where('contactid', '=', $request->id)->first();
    
    // Capture old data before changes
    $oldData = [
        'fname' => $contact->fname,
        'lname' => $contact->lname,
        'salutation' => $contact->salutation,
        'marital_status' => $contact->marital_status,
        'gender' => $contact->gender,
        'birthday' => $contact->birthday,
        'wedding_bday' => $contact->wedding_bday,
        'country_id' => $contact->country_id,
        'area' => $contact->area,
        'email' => $contact->email,
        'idnumber' => $contact->idnumber,
        'mobile' => empty($contact->mobile) ? null : $contact->mobile,
        'address1' => $contact->address1->isEmpty() ? null : $contact->address1[0]->pivot->value,
        'address2' => $contact->address2->isEmpty() ? null : $contact->address2[0]->pivot->value
    ];

     // Capture old company data before changes
     $oldCompanyData = [
        'company_name' => $contact->companyname->isEmpty() ? null : $contact->companyname[0]->pivot->value,
        'company_phone' => $contact->companyphone->isEmpty() ? null : $contact->companyphone[0]->pivot->value,
        'company_email' => $contact->companyemail->isEmpty() ? null : $contact->companyemail[0]->pivot->value,
        'company_area' => $contact->companyarea->isEmpty() ? null : $contact->companyarea[0]->pivot->value,
        'company_status' => $contact->companystatus->isEmpty() ? null : $contact->companystatus[0]->pivot->value,
        'company_address' => $contact->companyaddress->isEmpty() ? null : $contact->companyaddress[0]->pivot->value,
        'company_fax' => $contact->companyfax->isEmpty() ? null : $contact->companyfax[0]->pivot->value,
        'company_type' => $contact->companytype->isEmpty() ? null : $contact->companytype[0]->pivot->value,
        'company_nationality' => $contact->companynationality->isEmpty() ? null : $contact->companynationality[0]->pivot->value
    ];
        // Pastikan gender dan salutation tidak berubah menjadi null
        $request->merge([
            'gender' => $request->gender ?: $contact->gender,
            'salutation' => $request->salutation ?: $contact->salutation,
            'marital_status' => $request->has('marital_status') && $request->marital_status === '' ? '' : ($request->marital_status ?: $contact->marital_status)
        ]);
        
    // Update contact data (EXCLUDING readonly fields)
    // Don't update fname, lname, salutation, gender, birthday, country_id, area, idnumber, email, mobile, address1
    // $contact->fname = $request->fname;
    // $contact->lname = $request->lname;
    // $contact->salutation = $request->salutation;
    // Perbaikan untuk marital_status - gunakan nilai dari request yang sudah di-merge
    $contact->marital_status = $request->marital_status;
        
    // Jika nilai adalah string kosong, gunakan pendekatan yang lebih kuat untuk database
    if ($request->marital_status === '') {
        // Force the database to treat it as an empty string, not NULL
        DB::statement("UPDATE contacts SET marital_status = '' WHERE contactid = ?", [$contact->contactid]);
        
        // Refresh model untuk memastikan nilai benar
        $contact->refresh();
    }

    // $contact->gender = $request->gender;
 // Only update wedding_bday
 if($request->wedding_bday) {
    $contact->wedding_bday = Carbon::parse($request->wedding_bday)->format('Y-m-d');
} else {
    $contact->wedding_bday = NULL;
}

// Tambahkan debug untuk melihat nilai yang diterima
\Log::debug('Wedding_bday request value: ' . ($request->wedding_bday ?? 'NULL'));
\Log::debug('Birthday request value: ' . ($request->birthday ?? 'NULL'));

// Pastikan tidak ada nilai birthday yang tidak sengaja masuk ke wedding_bday
// Cek apakah request->birthday sama dengan nilai yang akan disimpan ke wedding_bday
if ($contact->wedding_bday && $request->birthday && 
    Carbon::parse($request->birthday)->format('Y-m-d') === $contact->wedding_bday) {
    // Jika sama, kemungkinan terjadi kesalahan, reset wedding_bday
    \Log::warning('Detected potential error: birthday value copied to wedding_bday');
    $contact->wedding_bday = $oldData['wedding_bday']; // Kembalikan ke nilai asli
}
    

    // Don't update these fields
    // $contact->country_id = $request->country_id;
    // $contact->area = $request->area;
    // $contact->idnumber = $request->idnumber;
    // $contact->email = $request->email;
    
    $contact->save();

   if(!$contact->companyname->isEmpty() && $request->company_name != NULL) {
       $contact->companyname[0]->pivot->value=$request->company_name;
       $contact->companyname[0]->pivot->save();
   }elseif($request->company_name != NULL){
       $attr = Attribute::where('attr_name', '=', 'company_name')->first();
       $contact->companyname()->attach($attr->id, ['value' => $request->company_name]);
   }else{
       $attr = Attribute::where('attr_name', '=', 'company_name')->first();
       $contact->companyname()->detach($attr->id);
   }
   if (!$contact->companyphone->isEmpty() && $request->company_phone !=NULL){
       $contact->companyphone[0]->pivot->value = $request->company_phone;
       $contact->companyname[0]->pivot->save();
   }elseif($request->company_phone !=NULL) {
       $attr = Attribute::where('attr_name', '=', 'company_phone')->first();
       $contact->companyphone()->attach($attr->id, ['value' => $request->company_phone]);
   }else{
       $attr=Attribute::where('attr_name','=','company_phone')->first();
       $contact->companyphone()->detach($attr->id);
   }
    if (!$contact->companyemail->isEmpty() && $request->company_email !=NULL){
        $contact->companyemail[0]->pivot->value = $request->company_email;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_email !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_email')->first();
        $contact->companyemail()->attach($attr->id, ['value' => $request->company_email]);
    }else{
        $attr=Attribute::where('attr_name','=','company_email')->first();
        $contact->companyemail()->detach($attr->id);
    }
    if (!$contact->companyarea->isEmpty() && $request->company_area !=NULL){
        $contact->companyarea[0]->pivot->value = $request->company_area;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_area !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_area')->first();
        $contact->companyarea()->attach($attr->id, ['value' => $request->company_area]);
    }else{
        $attr=Attribute::where('attr_name','=','company_area')->first();
        $contact->companyarea()->detach($attr->id);
    }
    if (!$contact->companystatus->isEmpty() && $request->company_status !=NULL){
        $contact->companystatus[0]->pivot->value = $request->company_status;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_status !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_status')->first();
        $contact->companystatus()->attach($attr->id, ['value' => $request->company_status]);
    }else{
        $attr=Attribute::where('attr_name','=','company_status')->first();
        $contact->companystatus()->detach($attr->id);
    }
    if (!$contact->companyaddress->isEmpty() && $request->company_address !=NULL){
        $contact->companyaddress[0]->pivot->value = $request->company_address;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_address !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_address')->first();
        $contact->companyaddress()->attach($attr->id, ['value' => $request->company_address]);
    }else{
        $attr=Attribute::where('attr_name','=','company_address')->first();
        $contact->companyaddress()->detach($attr->id);
    }
    if (!$contact->companyfax->isEmpty() && $request->company_fax !=NULL){
        $contact->companyfax[0]->pivot->value = $request->company_fax;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_fax !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_fax')->first();
        $contact->companyfax()->attach($attr->id, ['value' => $request->company_fax]);
    }else{
        $attr=Attribute::where('attr_name','=','company_fax')->first();
        $contact->companyfax()->detach($attr->id);
    }
    if (!$contact->companytype->isEmpty() && $request->company_type !=NULL){
        $contact->companytype[0]->pivot->value = $request->company_type;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_type !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_type')->first();
        $contact->companytype()->attach($attr->id, ['value' => $request->company_type]);
    }else{
        $attr=Attribute::where('attr_name','=','company_type')->first();
        $contact->companytype()->detach($attr->id);
    }
    if (!$contact->companynationality->isEmpty() && $request->company_nationality !=NULL){
        $contact->companynationality[0]->pivot->value = $request->company_nationality;
        $contact->companyname[0]->pivot->save();
    }elseif($request->company_nationality !=NULL) {
        $attr = Attribute::where('attr_name', '=', 'company_nationality')->first();
        $contact->companynationality()->attach($attr->id, ['value' => $request->company_nationality]);
    }else{
        $attr=Attribute::where('attr_name','=','company_nationality')->first();
        $contact->companynationality()->detach($attr->id);
    }

    if($contact->address1->isEmpty() and $request->address1 !='') {
       $attr = Attribute::where('attr_name', '=', 'address1')->first();
        $contact->address1()->attach($attr->id, ['value' => $request->address1]);
    }elseif ($request->address1==''){
        $attr = Attribute::where('attr_name', '=', 'address1')->first();
        $contact->address1()->detach($attr->id);
    } else {
        $contact->address1[0]->pivot->value=$request->address1;
        $contact->address1[0]->pivot->save();
    }

    if($contact->address2->isEmpty() and $request->address2 !=''){
        $attr = Attribute::where('attr_name', '=', 'address2')->first();
        $contact->address2()->attach($attr->id, ['value' => $request->address2]);
    }elseif($request->address2==''){
        $attr = Attribute::where('attr_name', '=', 'address2')->first();
        $contact->address2()->detach($attr->id);
    } else {
        $contact->address2[0]->pivot->value=$request->address2;
        $contact->address2[0]->pivot->save();
    }
    if (!empty($contact->mobile) && $request->mobile != NULL) {
        $contact->mobile = $request->mobile;
        $contact->save();
    } elseif($request->mobile != NULL) {
        $contact->mobile = $request->mobile;
        $contact->save();
    } else {
        $contact->mobile = NULL;
        $contact->save();
    }
    
    // Capture new data after changes (using existing values for protected fields)
    $newData = [
        'fname' => $request->fname,
        'lname' => $request->lname,
        'salutation' => $request->salutation,
        'marital_status' => $request->marital_status,
        'gender' => $request->gender,
        'birthday' => $request->birthday ? Carbon::parse($request->birthday)->format('Y-m-d') : $contact->birthday,
        'wedding_bday' => $contact->wedding_bday, // Gunakan nilai dari model yang sudah diupdate
        'country_id' => $contact->country_id, // Keep existing value
        'area' => $contact->area,             // Keep existing value
        'email' => $request->email,
        'idnumber' => $contact->idnumber,     // Keep existing value
        'mobile' => $request->mobile ?? null,
        'address1' => $request->address1 ?? null,
        'address2' => $request->address2 ?? null
    ];
    
    // Log the activity
    if ($oldData !== $newData) {
        $this->logActivity(
            'update_contact',
            Contact::class,
            $contact->contactid,
            $oldData,
            $newData,
            "Contact {$contact->fname} {$contact->lname} was updated"
        );
    }
    
    // Capture new company data
    $newCompanyData = [
        'company_name' => $request->company_name ?? null,
        'company_phone' => $request->company_phone ?? null,
        'company_email' => $request->company_email ?? null,
        'company_area' => $request->company_area ?? null,
        'company_status' => $request->company_status ?? null,
        'company_address' => $request->company_address ?? null,
        'company_fax' => $request->company_fax ?? null,
        'company_type' => $request->company_type ?? null,
        'company_nationality' => $request->company_nationality ?? null
    ];
    
    // Log company information update if any changes occurred
    if ($oldCompanyData !== $newCompanyData) {
        $this->logActivity(
            'update_company_info',
            Contact::class,
            $contact->contactid,
            $oldCompanyData,
            $newCompanyData,
            "Company information for contact {$contact->fname} {$contact->lname} was updated"
        );
    }

    return redirect()->back();
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $contact=Contact::find($id);
        foreach ($contact->attribute as $item) {
            $attr=Attribute::find($item->id);
            $contact->attribute()->detach($attr->id);
        }
        $contact->delete();
        return redirect()->back();
    }
    public  function  search(Request $request){
        $days=$request->days;
        $contact=Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?',[Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?',[Carbon::now()->addDays($days)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'),'asc')->get();
        return view('contacts.birthday',['contacts'=>$contact,'days'=>$days]);
    }

    public function import(){
        return view('contacts.import');
    }
    public function importStay(){
        return view('contacts.import_stay');
    }
    public function uploadContact(Request $request){

        $file=$request->file('file');
        $newName='contact.csv';
        $path='files/uploads/';
        $file->move($path,$newName);
        $created=0;
        $update=0;
        $created_data=[];
        $updated_data=[];
        $reader=Reader::createFromPath($path.'/'.$newName,'r');

        foreach ($reader as $key=>$row){

            if ($key>=2) {

                $country = Country::where('iso2', '=', $row[9])->value('id');
                $contacts = Contact::updateOrCreate([
                    'email' => $row[6],
                    'idnumber' => $row[0],
                ], [

                    'birthday' => Carbon::createFromFormat('Y-m-d',$row[7])->format('Y-m-d'),
                    'fname' => $row[2],
                    'lname' => $row[3],
                    'ccid' => $row[1],
                    'salutation' => $row[4],
                    'gender' => $row[5],
                    'country_id' => $country,
                    'marital_status'=>$row[8],

                ]);
                if ($contacts->wasRecentlyCreated) {
                    $created += 1;
                    array_push($created_data, [
                        'email' => $row[6],
                        'idnumber' => $row[0],
                        'birthday' => Carbon::createFromFormat('Y-m-d',$row[7])->format('Y-m-d'),
                        'fname' => $row[2],
                        'lname' => $row[3],
                        'ccid' => $row[1],
                        'salutation' => $row[4],
                        'gender' => $row[5],
                        'country_id' => $country,
                        'marital_status'=>$row[8],

                    ]);

                } else {
                    $update += 1;
                    array_push($updated_data, [
                        'email' => $row[6],
                        'idnumber' => $row[0],
                        'birthday' => Carbon::createFromFormat('Y-m-d',$row[7])->format('Y-m-d'),
                        'fname' => $row[2],
                        'lname' => $row[3],
                        'ccid' => $row[1],
                        'salutation' => $row[4],
                        'gender' => $row[5],
                        'country_id' => $country,
                        'marital_status'=>$row[8],

                    ]);
                }
            }
         }

        return view('contacts.import',['create'=>$created,'update'=>$update,'created_data'=>$created_data,'updated_data'=>$updated_data]);
    }
    public function country($c){

        $country=Country::where('country','=',$c)->first()[env('COUNTRY_ISO')];

        setlocale(LC_MONETARY,"id_ID");
        $a=[];
        $b=[];
        $c=[];

        $contacts=Contact::whereHas('transaction',function ($q){
            return $q->where('revenue','>',0);
        })->whereHas('profilesfolio',function ($q){
            return $q->where('foliostatus','!=',null);
        })->where('country_id','=',$country)->get();

        foreach ($contacts as $contact){
            array_push($b,$contact);
            foreach ($contact->transaction as $trx){
                array_push($c,$trx);
            }
        }
        return view('contacts.list',['data'=>$b]);
    }
    public function dateadded($date){
        $dt=Carbon::parse($date);
        setlocale(LC_MONETARY,"id_ID");
        $b=[];
        $c=[];

       $contacts=Contact::whereMonth('created_at','=',$dt->month)
           ->whereYear('created_at','=',$dt->year)
           ->get();

        foreach ($contacts as $contact){
            array_push($b,$contact);
            foreach ($contact->transaction as $trx){

                array_push($c,$trx);

            }
        }

        return view('contacts.list',['data'=>$b]);
    }
    public function dstatus($stat)
    {
        setlocale(LC_MONETARY, "id_ID");
        if($stat=='Inhouse') {
            $contacts = Contact::whereHas(
                'profilesfolio', function ($q) {
                 return $q->where('foliostatus','=','I')->groupBy('profileid');
                    }
                )->whereHas('transaction',function($q){
                return $q->havingRaw('sum(revenue)>=0');
                })
                ->withCount('transaction')
                ->get();
                $status = 'In House';
        }
        elseif ($stat=='Confirm'){
              $contacts = Contact::whereHas('profilesfolio', function ($q) {
               return $q->where('foliostatus', '=', 'C');
           })->get();
            $status = 'Confirm';
        }elseif ($stat=='Repeater'){
        $contacts = Contact::repeaters()
            ->whereHas('transaction', function($q) {
                return $q->havingRaw('sum(revenue) >= 0');
            })
            ->withCount('transaction')
            ->get();
        $status = 'Repeater Guests';
    }
    elseif ($stat=='InhouseRepeater'){
        $contacts = Contact::inhouseRepeaters()
            ->withCount('transaction')
            ->get();
        
    }

    return view('contacts.list', ['data' => $contacts]);
}

    public function male(){
       $gender='M';
       return view('contacts.list3',['gender'=>$gender]);
    }
    public function female(){
        $gender='F';
       return view('contacts.list3',['gender'=>$gender]);
    }

    public function repeaters()
    {
        $contacts = Contact::repeaters()
            ->whereHas('transaction', function($q) {
                return $q->havingRaw('sum(revenue) >= 0');
            })
            ->withCount('transaction')
            ->get();
        
        return view('contacts.list', ['data' => $contacts]);
    }

    public function inhouseRepeaters()
    {
        $contacts = Contact::inhouseRepeaters()
            ->withCount('transaction')
            ->get();
        
        return view('contacts.list', ['data' => $contacts]);
    }

    public function getRepeaterCount()
    {
        return Contact::repeaters()
            ->whereHas('transaction', function($q) {
                return $q->havingRaw('sum(revenue) >= 0');
            })
            ->count();
    }

    public function getInhouseRepeaterCount()
    {
        return Contact::inhouseRepeaters()->count();
    }


    public function uploadStay(Request $request){

        $file=$request->file('file');
        $newName='stay.csv';
        $path='files/uploads';
        $file->move($path,$newName);
        $created=0;
        $update=0;
        $created_data=[];
        $updated_data=[];

        $reader=Reader::createFromPath($path.'/'.$newName,'r');
        foreach ($reader as $key=>$row){
            if ($key>=2) {

                $stays=Transaction::updateOrCreate([
                    'resv_id'=>$row[1]
                ],[
                    'checkin'=>Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'),
                    'checkout'=>Carbon::createFromFormat('Y-m-d',$row[3])->format('Y-m-d'),
                    'room'=>$row[4],
                    'room_type'=>$row[5],
                    'revenue'=>(float)$row[6],
                    'status'=>$row[7]
                ]);
                $profiles=ProfileFolio::updateOrCreate([
                    'profileid'=>$row[0]
                ],[
                   'folio_master'=>$row[1],
                    'folio'=>$row[1],
                    'foliostatus'=>$row[7],

                ]);

                if ($stays->wasRecentlyCreated){
                    $stays->contact()->attach($row[0]);
                    $created += 1;
                    array_push($created_data,[
                        'resv_id'=>$row[1],
                        'checkin'=>Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'),
                        'checkout'=>Carbon::createFromFormat('Y-m-d',$row[3])->format('Y-m-d'),
                        'room'=>$row[4],
                        'room_type'=>$row[5],
                        'revenue'=>(float)$row[6],
                        'status'=>$row[7]
                    ]);
                } else{
                    $stays->contact()->sync($row[0]);
                    $update+=1;
                    array_push($updated_data,[
                        'resv_id'=>$row[1],
                        'checkin'=>Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'),
                        'checkout'=>Carbon::createFromFormat('Y-m-d',$row[3])->format('Y-m-d'),
                        'room'=>$row[4],
                        'room_type'=>$row[5],
                        'revenue'=>(float)$row[6],
                        'status'=>$row[7]
                    ]);
                }

            }
        }

        return view('contacts.import_stay',['create'=>$created,'update'=>$update,'created_data'=>$created_data,'updated_data'=>$updated_data]);
    }
    public function filter(){
        $retval=Contact::with('transaction','profilesfolio')->get();

        return view('contacts.filter',['data'=>$retval]);
    }

    public function filterPost(Request $request){
        $contacts=Contact::with('transaction','profilesfolio')->when($request->country_id !=null,function ($q) use ($request){
            return $q->whereIn('country_id',$request->country_id);
        })->when($request->area !=null,function($q) use ($request){
            return $q->whereIn('area',$request->area);
        })->when($request->guest_status !=null,function ($q) use ($request){
            return $q->whereHas('profilesfolio',function ($q) use ($request){
                return $q->whereIn('foliostatus',$request->guest_status);
            })->orderBy('created_at','desc');
        })->when($request->spending_from ==null and $request->spending_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
             return $q->havingRaw('SUM(revenue) between ? and ?',[0,str_replace('.','',$request->spending_to)]);
            });
        })->when($request->spending_from !=null and $request->spending_to ==null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->havingRaw('SUM(revenue) >= ?',[str_replace('.','',$request->spending_from)]);
            });
        })->when($request->spending_from !=null and $request->spending_to !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->havingRaw('SUM(revenue) between ? and ?',[str_replace('.','',$request->spending_from),str_replace('.','',$request->spending_to)]);
            });
        })->when($request->gender !=null,function ($q) use ($request) {
            return $q->whereIn('gender', $request->gender);
        })->when($request->stay_from == null and $request->stay_to != null,function ($q) use ($request) {
            return $q->whereHas('transaction', function ($q) use ($request) {
                return $q->where('checkout', '<=', Carbon::parse($request->stay_to)->format('Y-m-d'));
            });
        })->when($request->stay_from !=null and $request->stay_to ==null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->where('checkin','>=',Carbon::parse($request->stay_from)->format('Y-m-d'));
            });
        })->when($request->stay_from !=null and $request->stay_to !=null,function ($q) use ($request) {
            return $q->whereHas('transaction', function ($q) use ($request) {
                return $q->where('checkin', '>=', Carbon::parse($request->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($request->stay_to)->format('Y-m-d'));
            });
        })->when($request->bday_from ==null and $request->bday_to !=null, function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($request->bday_to)->format('m-d')]);
        })->when($request->bday_from!=null and $request->bday_to ==null, function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($request->bday_from)->format('m-d')]);
        })->when($request->bday_from !=null and $request->bday_to !=null , function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') >= ?',[Carbon::parse($request->bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') <= ?',[Carbon::parse($request->bday_to)->format('m-d')]);
        })->when($request->wedding_bday_from ==null and $request->wedding_bday_to !=null , function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($request->wedding_bday_to)->format('m-d')]);
        })->when($request->wedding_bday_from !=null and $request->wedding_bday_to == null , function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($request->wedding_bday_from)->format('m-d')]);
        })->when($request->wedding_bday_from != null and $request->wedding_bday_to !=null, function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($request->wedding_bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($request->wedding_bday_to)->format('m-d')]);
        })->when($request->total_night_from !=null and $request->total_night_to==null, function ($q) use ($request) {
            return $q->whereHas('transaction', function ($q) use ($request) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $request->total_night_from);
            });
        })->when($request->total_night_from == null and $request->total_night_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$request->total_night_to);
            });
        })->when($request->total_night_from !=null and $request->total_night_to !=null, function ($q) use ($request) {
            return  $q->whereHas('transaction', function ($q) use ($request) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $request->total_night_from . ' and ' . $request->total_night_to . '');
            });
        })->when($request->total_stay_from !=null and $request->total_stay_to ==null ,function ($q) use ($request){
            return $q->has('transaction','>=',$request->total_stay_from);
        })->when($request->total_stay_from == null and $request->total_stay_to !=null ,function ($q) use ($request){
            return $q->has('transaction','<=',$request->total_stay_to);
        })->when($request->total_stay_from !=null and $request->total_stay_to !=null, function ($q) use ($request){
            return $q->has('transaction','>=',$request->total_stay_from)->has('transaction','<=',$request->total_stay_to);
        })->when($request->name !=null,function ($q) use ($request){
            return $q->whereRaw('CONCAT(fname,lname) like \'%'.$request->name.'%\'');
        })->when($request->age_from!=null and $request->age_to!=null ,function ($q) use ($request){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$request->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$request->age_to.'\' year)');
        })->when($request->age_from!=null ,function($q) use ($request){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$request->age_from.'\' YEAR)');
        })->when($request->age_to!=null,function ($q) use ($request){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$request->age_to.'\' YEAR)');
        })->when($request->booking_source!=null,function ($q) use ($request){
            $q->whereHas('profilesfolio',function ($q) use ($request){
                $q->whereIn('source',$request->booking_source);
            });
        })
            ->get();


        return response($contacts,200);
    }
    public function review($id){
        $contacts=Contact::find($id);
        $contacts=$contacts->toJson();
        $review=EmailReview::where('contact_id','=',$id)->get();
        $review=$review->toJson();
        return view('review.feedback',['contacts'=>$contacts,'review'=>$review]);
    }
    public function incomplete()
    {
        $today = Carbon::now()->format('Y-m-d');
        $nextWeek = Carbon::now()->addDays(7)->format('Y-m-d');

        $incomplete = CheckContact::where(function($query) {
                $query->where('checked', '=', 'N')
                      ->orWhereNull('checked');
            })
            ->where('foliostatus', '!=', 'X')
            ->where('foliostatus', '=', 'I') // Hanya tampilkan status I, tidak termasuk G, C, T
            ->where(function($query) use ($today, $nextWeek) {
                $query->where(function($q) use ($today) {
                        // Tamu yang akan check-out dalam 3 hari ke depan
                        $q->where('dateco', '>=', $today)
                          ->where('dateco', '<=', Carbon::now()->addDays(3)->format('Y-m-d'));
                    })
                    ->orWhere(function($q) use ($today, $nextWeek) {
                        // Tamu yang akan check-in dalam 7 hari ke depan
                        $q->where('dateci', '>=', $today)
                          ->where('dateci', '<=', $nextWeek);
                    });
            })
            ->orderByRaw(
                "CASE 
                    WHEN foliostatus = 'I' AND problems = 'ID Number need to check' THEN 0
                    WHEN foliostatus = 'I' THEN 1
                    ELSE 8
                END"
            )
            ->orderByRaw(
                "CASE 
                    WHEN dateco <= DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY) THEN 0
                    ELSE 1
                END"
            )
            ->orderBy('dateco', 'asc')
            ->orderBy('dateci', 'asc')
            ->get();

        return view('contacts.incomplete', ['incompletes' => $incomplete]);
    }
    public function updateStatus(Request $request){
        $contact = CheckContact::where('folio', '=', $request->id)->first();
        
        // Capture old data before changes
        $oldData = [
            'folio' => $contact->folio,
            'checked' => $contact->checked,
            'dateci' => $contact->dateci,
            'foliostatus' => $contact->foliostatus
        ];
        
        $contact->checked = 'Y';
        $contact->update();
        
        // Capture new data after changes
        $newData = [
            'folio' => $contact->folio,
            'checked' => $contact->checked,
            'dateci' => $contact->dateci,
            'foliostatus' => $contact->foliostatus
        ];
        
        // Log the activity
        $this->logActivity(
            'update_incomplete_contact',
            CheckContact::class,
            $contact->id,
            $oldData,
            $newData,
            "Incomplete contact data completed for folio #{$contact->folio}"
        );
        
        return response('success', 200);
    }
    public function excluded(){
       $excs=ExcludedEmail::all();
       return view('contacts.excluded',['data'=>$excs]);
    }
    public function addEmail(Request $request)
    {
        $rules = [
            'email' => 'required|unique:excluded_emails,email'
        ];
        $message = ['email.unique' => 'Email/Domain exists in dataset'];
        
        $validator = Validator::make($request->all(), $rules, $message);
        
        if (!$validator->fails()) {
            $exc = new ExcludedEmail();
            $exc->email = $request->email;
            $exc->reason = 'Email blacklisted manually';
            $exc->save();
            
            // Add user activity log
            $this->logActivity(
                'add_excluded_email',
                ExcludedEmail::class,
                $exc->id,
                null,
                [
                    'email' => $exc->email,
                    'reason' => $exc->reason
                ],
                'User added email/domain to exclusion list: ' . $exc->email
            );
            
            return response('success');
        } else {
            return response(['errors' => $validator->errors()]);
        }
    }

    public function deleteExcludedEmail(Request $request)
{
    $rules = [
        'id' => 'required|exists:excluded_emails,id',
        'reason' => 'required|string|min:3'
    ];
    
    $validator = Validator::make($request->all(), $rules);
    
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    
    try {
        $excludedEmail = ExcludedEmail::find($request->id);
        
        if (!$excludedEmail) {
            return response()->json(['errors' => ['id' => 'Email not found']], 404);
        }
        
        // Capture old data before deletion for logging
        $oldData = [
            'id' => $excludedEmail->id,
            'email' => $excludedEmail->email,
            'reason_for_exclusion' => $excludedEmail->reason
        ];
        
        // Delete the excluded email
        $excludedEmail->delete();
        
        // Log the activity
        $this->logActivity(
            'delete_excluded_email',
            ExcludedEmail::class,
            $request->id,
            $oldData,
            [
                'removal_reason' => $request->reason
            ],
            'User removed email/domain from exclusion list: ' . $oldData['email'] . ' (Reason: ' . $request->reason . ')'
        );
        
        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        \Log::error('Error deleting excluded email: ' . $e->getMessage());
        return response()->json(['errors' => ['general' => 'An error occurred while processing your request: ' . $e->getMessage()]], 500);
    }
}
    public function type($ty){
        $type=RoomType::where('room_name',$ty)->first();

        $tr=ProfileFolio::where('roomtype',$type->room_code)->get();

        foreach ($tr as $pf)
        {
            $contact [] = Contact::where('contactid', $pf->profileid)->first();
        }

        return view('contacts.list',['data'=>$contact]);
    }

    // Tambahkan method baru untuk mendapatkan tanggal data terakhir
    private function getLatestDataDate() {
        $latestTransaction = DB::select(DB::raw('SELECT MAX(dateci) as latest_date FROM profilesfolio WHERE dateci IS NOT NULL'));
        
        if (!empty($latestTransaction) && $latestTransaction[0]->latest_date) {
            return $latestTransaction[0]->latest_date;
        }
        
        // Fallback ke tanggal transaksi terakhir jika profilesfolio kosong
        $latestTransactionFallback = DB::select(DB::raw('SELECT MAX(checkout) as latest_date FROM transactions WHERE checkout IS NOT NULL'));
        
        if (!empty($latestTransactionFallback) && $latestTransactionFallback[0]->latest_date) {
            return $latestTransactionFallback[0]->latest_date;
        }
        
        return null;
    }

}
