<?php

namespace App\Http\Controllers;

require base_path().'/vendor/autoload.php';

use App\Models\Campaign;
use App\Models\Configuration;
use App\Models\Contact;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\MailgunLogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Psr7\str;
use Mailgun\Mailgun;


class MailgunController extends Controller
{

    public function setting($param){
	if ($param=='key'){
        return env('MAILGUN_SECRET');
     } else{
         return env('MAILGUN_DOMAIN');
     }
    }

    public function convertstring($str){
        $spl=explode(' ',$str);
        $frag=[];
        foreach ($spl as $s){
            array_push($frag,ucfirst(strtolower($s)));
        }
        return implode(' ',$frag);
    }
    public function sendemail($contacts,$template){
        $domain=$this->setting('domain');
        $key=$this->setting('key');
        $mg=Mailgun::create($key);
	foreach ($contacts as $contact){
            $data=[
                'firstname'=>$contact->fname ,
                'lastname'=>$contact->lname ,
                'title'=>$contact->salutation,
                'hotelname'=>'RRP',
                'gmname'=>'Mr X',
            ];

            $tagOne = "email/";
            $tagTwo = "/review";
            $replacement = 'email/'.$contact->contactid.'/';



            $startTagPos = strrpos($template->content, $tagOne);
            $endTagPos = strrpos($template->content, $tagTwo);
            $tagLength = $endTagPos - $startTagPos + 1;

            $newText = substr_replace($template->content, $replacement,
                $startTagPos, $tagLength);

            $template->content=$newText;
            $template->save();
            if (!empty($contact->email)){
                $mg->messages()->send($domain,
                    [
                        'from' => 'Admin<it.sysdev@rcoid.com>',
                        'to' => $contact->fname.' '.$contact->lname.'<danabala72@gmail.com>',
                        'subject' => $template->subject,
                        'html' => $template->parse($data),
                        'o:tracking-opens' => true,
                        'o:tracking-clicks' => true,
                    ]);
            }
        }
    }
    public function sendmail($user, $template,$tags=null){
        $config=Configuration::find(1);
        $domain=$config->mailgun_domain;
        $key=$config->mailgun_apikey;
        $mg=Mailgun::create($key);
        $recepient=[];
        $excludeds=ExcludedEmail::all();
        foreach ($excludeds as $excluded){
            array_push($recepient,$excluded->email);
        }
        $response=EmailResponse::where('event','=','unsubscribed')->select('recepient')->get();
        foreach ($response as $res){
            array_push($recepient,$res->recepient);
        }
        $data=[
            'contact_id'=>$user->contactid,
            'firstname'=>$this->convertstring($user->fname),
            'lastname'=>$this->convertstring($user->lname) ,
            'title'=>$this->convertstring($user->salutation),
            'hotelname'=>$config->hotel_name,
            'gmname'=>$config->gm_name,
        ];
        if (!in_array($user->email,$recepient)){
            $mg->messages()->send($domain,
                [
                    'from' => $config->sender_name.'<'.$config->sender_email.'>',
                    'to' => $this->convertstring($user->fname).' '.$this->convertstring($user->lname).'<'.$user->email.'>',
                    'subject' => $template->subject,
                    'html' => $template->parse($data),
                    'o:tracking-opens' => true,
                    'o:tracking-clicks' => true,
                    'o:tag' => $tags
                ]);
        }
    }

    public function blast($user,$template,$tags=null){
        $config=Configuration::find(1);
        $domain=$config->mailgun_domain;
        $key=$config->mailgun_apikey;
        $mg=Mailgun::create($key);
        $data=[
            'contact_id'=>$user->contactid,
            'firstname'=>$this->convertstring($user->fname),
            'lastname'=>$this->convertstring($user->lname) ,
            'title'=>'',
            'hotelname'=>$config->hotel_name,
            'gmname'=>$config->gm_name,
        ];
        $mg->messages()->send($domain,
            [
                'from' => $config->sender_name.'<'.$config->sender_email.'>',
                'to' => $this->convertstring($user->fname).' '.$this->convertstring($user->lname).'<'.$user->email.'>',
                'subject' => $template->subject,
                'html' => $template->parse($data),
                'o:tracking-opens' => true,
                'o:tracking-clicks' => true,
                'o:tag' => $tags
            ]);


    }

    public function bdayemail($user, $template,$tags=null){
        $config=Configuration::find(1);
        $domain=$config->mailgun_domain;
        $key=$config->mailgun_apikey;
        $mg=Mailgun::create($key);
        $recepient=[];
        $excludeds=ExcludedEmail::all();
        foreach ($excludeds as $excluded){
            array_push($recepient,$excluded->email);
        }
        $response=EmailResponse::where('event','=','unsubscribed')->select('recepient')->get();
        foreach ($response as $res){
            array_push($recepient,$res->recepient);
        }


        $data=[
            'contact_id'=>$user->contactid,
            'firstname'=>$this->convertstring($user->fname),
            'lastname'=>$this->convertstring($user->lname) ,
            'title'=>$this->convertstring($user->salutation),
            'hotelname'=>$config->hotel_name,
            'gmname'=>$config->gm_name,
        ];
        if (!in_array($user->email,$recepient)){
            $mg->messages()->send($domain,
                [
                    'from' => $config->sender_name.'<'.$config->sender_email.'>',
                    'to' => $this->convertstring($user->fname).' '.$this->convertstring($user->lname).'<'.$user->email.'>',
                    'subject' => $template->subject.' '.$this->convertstring($user->salutation).' '.$this->convertstring($user->fname).' '.$this->convertstring($user->lname),
                    'html' => $template->parse($data),
                    'o:tracking-opens' => true,
                    'o:tracking-clicks' => true,
                    'o:tag' => $tags
                ]);
        }
    }

    public function sendcampaign($campaign_id){
        $config=Configuration::find(1);
        $domain=$config->mailgun_domain;
        $key=$config->mailgun_apikey;
        $mg=Mailgun::create($key);
        $campaign=Campaign::find($campaign_id);
        $recepient=[];
        $excludeds=ExcludedEmail::all();
        foreach ($excludeds as $excluded){
            array_push($recepient,$excluded->email);
        }
        $response=EmailResponse::where('event','=','unsubscribed')->select('recepient')->get();
        foreach ($response as $res){
            array_push($recepient,$res->recepient);
        }
        $template=$campaign->template;
        $contacts=$campaign->contact->whereNotIn('email',$recepient);
        foreach ($contacts as $contact){
            $data=[
                'contact_id'=>$contact->contactid,
                'firstname'=>$this->convertstring($contact->fname) ,
                'lastname'=>$this->convertstring($contact->lname) ,
                'title'=>$this->convertstring($contact->salutation),
                'hotelname'=>$config->hotel_name,
                'gmname'=>$config->gm_name,
            ];
            if (!empty($campaign)){
                $tag=[$campaign->id, $contact->email];
            }else{
                $tag=[];
            }
            if (!empty($contact->email)){
                $mg->messages()->send($domain,
                    [
                        'from' => $config->sender_name.'<'.$config->sender_email.'>',
                      //  'to' => $this->convertstring($contact->fname).' '.$this->convertstring($contact->lname).'<'.$contact->email.'>',
                      'to' => $this->convertstring($contact->fname).' '.$this->convertstring($contact->lname).'<danabala72@gmail.com>',
                        'subject' => $template[0]->subject,
                        'html' => $template[0]->parse($data),
                        'o:tracking-opens' => true,
                        'o:tracking-clicks' => true,
                        'o:tag' => $tag
                    ]);
            }
        }
    }
    public function sendexternalcampaign($campaign_id){
        $config=Configuration::find(1);
        $domain=$config->mailgun_domain;
        $key=$config->mailgun_apikey;
        $mg=Mailgun::create($key);
        $campaign=Campaign::find($campaign_id);
        $recepient=[];
        $excludeds=ExcludedEmail::all();
        foreach ($excludeds as $excluded){
            array_push($recepient,$excluded->email);
        }
        $response=EmailResponse::where('event','=','unsubscribed')->select('recepient')->get();
        foreach ($response as $res){
            array_push($recepient,$res->recepient);
        }
        $template=$campaign->template;
        $contacts=$campaign->external->whereNotIn('email',$recepient);
        foreach ($contacts as $contact){
            $data=[

                'firstname'=>$this->convertstring($contact->fname) ,
                'lastname'=>$this->convertstring($contact->lname) ,

            ];
            if (!empty($campaign)){
                $tag=[$campaign->id, $contact->email];
            }else{
                $tag=[];
            }
            if (!empty($contact->email)){
                $mg->messages()->send($domain,
                    [
                        'from' => $config->sender_name.'<'.$config->sender_email.'>',
                        'to' => $this->convertstring($contact->fname).' '.$this->convertstring($contact->lname).'<'.$contact->email.'>',
                        'subject' => $template[0]->subject,
                        'html' => $template[0]->parse($data),
                        'o:tracking-opens' => true,
                        'o:tracking-clicks' => true,
                        'o:tag' => $tag
                    ]);
            }
        }
    }

    public function getLogs($limit=null,$asc=null,$subject=null,$event=null,$recipient=null){
        $config=Configuration::find(1);
        $domain=$config->mailgun_domain;

        $mg=Mailgun::create($config->mailgun_apikey);
        $q=[
            'limit'=>$limit,
            'ascending'=>$asc,
            'subject'=>$subject,
            'event'=>$event,
            'recipient'=>$recipient
        ];
        $res=$mg->events()->get($domain,$q);
        return $res->getItems();
    }

    public function unsub(){
        $mg=Mailgun::create($this->setting('key'));
        $res=$mg->events()->get($this->setting('domain'),['event'=>'unsubscribed'])->getItems();
        dd($res);
    }
    public function delivery(){
        return view('email.delivery');
    }

    public function deliverychart(Request $request){

        $logs=DB::table('mailgun_logs as a')->where('tags','=',$request->d)->whereRaw('(recipient, timestamp) IN (SELECT recipient, MAX(timestamp) FROM mailgun_logs GROUP BY recipient,message_id)')->groupBy(['recipient','message_id'])->get();
        return response($logs);
    }
    public function deliveryStatus(Request $request){
       $logs=DB::table('mailgun_logs as a')->where('tags','=',$request->d)->whereRaw('(recipient, timestamp) IN (SELECT recipient, MAX(timestamp) FROM mailgun_logs GROUP BY recipient,message_id)')->groupBy(['recipient','message_id'])->get();
       $columns =[
            0=>'id',
            1=>'event',
            2=>'url',
            3=>'recipient',
            4=>'delivery_status',
            5=>'timestamp'
        ];
        $totalData=count($logs);
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $emaillogs= DB::table('mailgun_logs as a')->where('tags','=',$request->d)->whereRaw('(recipient, timestamp) IN (SELECT recipient, MAX(timestamp) FROM mailgun_logs GROUP BY recipient,message_id)')->groupBy(['recipient','message_id'])->offset($start)->limit($limit)->orderBy($order,$dir)->get();
        } else{
            $search = $request->input('search.value');
            $emaillogs=DB::table('mailgun_logs as a')
                        ->where('tags','=',$request->d)
                        ->whereRaw('(recipient, timestamp) IN (SELECT recipient, MAX(timestamp) FROM mailgun_logs GROUP BY recipient,message_id)')
                        ->where('event','LIKE',"%{$search}%")
                        ->orWhere('recipient','LIKE',"%{$search}%")
                        ->groupBy(['recipient','message_id'])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
            $total=DB::table('mailgun_logs as a')
                        ->where('tags','=',$request->d)
                        ->whereRaw('(recipient, timestamp) IN (SELECT recipient, MAX(timestamp) FROM mailgun_logs GROUP BY recipient,message_id)')
                        ->where('event','LIKE',"%{$search}%")
                        ->orWhere('recipient','LIKE',"%{$search}%")
                        ->groupBy(['recipient','message_id'])
                        ->get();
            $totalFiltered=count($total);
        }
        $data=[];
        if(!empty($emaillogs)){
            foreach($emaillogs as $emaillog){
                $nestedData['id'] = $emaillog->id;
                $nestedData['event'] = $emaillog->event;
                $nestedData['url'] = $emaillog->url;
                $nestedData['recipient'] = $emaillog->recipient;
                $nestedData['delivery_status'] = $emaillog->delivery_status;
                $nestedData['timestamp'] = $emaillog->timestamp;
                $data[] = $nestedData;
            }
        }
        $json_data=[
            "draw"=> intval($request->input('draw')),
            "recordsTotal"=> intval($totalData),
            "recordsFiltered"=> intval($totalFiltered),
            "data"=> $data
        ];
        echo json_encode($json_data);
    }
    public function getclick(Request $request){
        $res=DB::table('mailgun_logs')->whereNotNull('url')->where('recipient','=',$request->recipient)->groupBy('url')->groupBy('message_id')->get();
        return response($res);
    }


}

