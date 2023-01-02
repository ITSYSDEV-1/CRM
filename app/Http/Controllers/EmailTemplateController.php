<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Configuration;
use App\Models\MailEditor;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use PepipostAPILib\Models\EmailBody;
use PepipostAPILib\Models\From;
use PepipostAPILib\Models\Personalizations;
use PepipostAPILib\Models\Settings;
use PepipostAPILib\PepipostAPIClient;


class EmailTemplateController extends Controller
{

    public function randomstr(){
        $bytes = random_bytes(8);
        return bin2hex($bytes);
    }

    public function sendmail()
    {
        $user = Contact::find(785);
        $template = MailEditor::where('name', 'oke')->first();
        Mail::send([], [], function ($message) use ($template, $user) {

            $data = [
                'firstname' => $user->fname,
                'title' => $user->salutation
            ];

            $message->to('mudanakomang@hotmail.com', $user->fname)
                ->subject($template->subject)
                ->setBody($template->parse($data), 'text/html');

        });
        return response('OK', 200);
    }

    public function emailsend($user, $template, $subject)
    {

        Mail::send([], [], function ($message) use ($template, $user, $subject) {
            $config = Config::find(1);
            $data = [

                'firstname' => $user->fname,
                'lastname' => $user->lname,
                'title' => $user->salutation,
                'hotelname' => $config->hotel_name,
                'gmname' => $config->gm_name,

            ];
            $message->to($user->email, $user->salutation . ' ' . $user->fname)
                ->subject($subject)
                ->setBody($template->parse($data), 'text/html');
        });
    }

    public function testmail($request)
    {
        $template=MailEditor::find($request->id);
        $config=Configuration::find(1);
        $client=new PepipostAPIClient();
        $emailController=$client->getEmail();
        $apikey=env('PEPIPOST_API_KEY');
        $body = new EmailBody();
        $body->personalizations=[];
        $body->personalizations[0]=new Personalizations();
        $body->personalizations[0]->recipient=$request->email;
        $body->personalizations[0]->xApiheader=$this->randomstr();
        $body->from =new From();
        $body->from->fromEmail=$config->sender_email;
        $body->from->fromName=$config->sender_name;
        $data = [
            'contact_id' => '{contact_id}',
            'firstname' => '{firstname}',
            'lastname' => '{lastname}',
            'title' => '{title}',
            'hotelname' =>$config->hotel_name,
            'gmname' => '{gmname}',
        ];

        $body->subject=$template->subject;
        $body->content=$template->parse($data);

        $body->settings=new Settings();
        $body->settings->clicktrack=1;
        $body->settings->opentrack=1;
        $body->settings->unsubscribe=1;
        $body->tags=''.env('UNIT').',Testing';
        $emailController->createSendEmail($apikey,$body);
    }

    public function birthdaymail()
    {
        $contacts = Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?', [Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?', [Carbon::now()->addDays(7)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'), 'asc')->limit(10)->get();
        $template = MailEditor::where('name', 'happy_birthday')->first();
        foreach ($contacts as $contact) {
            $this->emailsend($contact, $template, $template->subject . ' ' . $contact->gender == 'M' ? 'Mr.' : 'Ms. /Mrs.' . ' ' . $contact->fname . ' ' . $contact->lname);
        }
    }

    public function getTemplate(Request $request)
    {
        $template = MailEditor::find($request->id);
        return response($template, 200);
    }
}
