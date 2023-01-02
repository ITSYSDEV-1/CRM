<?php

use App\Http\Controllers\API\PrestayController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\InhouseController;
use App\Http\Controllers\PepipostMail;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Emailtemplate;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ExternalEmailController;
use App\Http\Controllers\MailgunController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\TransactionController;
use App\Models\ConfigPrestay;
use App\Models\Contactprestay;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\ProfileFolio;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Models\Contact;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>'auth'],function (){
    Route::get('home',function (){
        return redirect('/');
    });
    Route::get('/',[ContactController::class,'dashboard']);
//    Route::get('reviews',[ContactController::class,'reviews']);
    Route::post('campaign/getsegment',[CampaignController::class,'getSegment']);
    Route::post('campaign/activate',[CampaignController::class,'activateCampaign'])->name('campaign.activate');
    Route::get('contacts/f/male',[ContactController::class,'male']);
    Route::get('contacts/f/female',[ContactController::class,'female']);
    Route::get('contacts/f/country/{country}',[ContactController::class,'country']);
    Route::get('contacts/f/created/{dateadded}',[ContactController::class,'dateadded']);
    Route::get('contacts/f/status/{status}',[ContactController::class,'dstatus']);
    Route::get('contacts/f/longest/{contact}',[ContactController::class,'longest']);
    Route::get('contacts/f/spending/{spending}',[ContactController::class,'spending']);
    Route::get('contacts/f/roomtype/{type}',[ContactController::class,'type']);
    Route::get('contacts/f/ages/{type}',[ContactController::class,'ages']);
    Route::get('contacts/f/source/{type}',[ContactController::class,'source']);
    Route::get('contacts/birthday',function (){
        return view('contacts.birthday');
    });
    Route::get('contacts/filter',[ContactController::class,'filter']);
    Route::post('contacts/filter',[ContactController::class,'filterPost'])->name('filter');
    Route::post('contacts/birthday/search',[ContactController::class,'search']);
    Route::get('contacts/detail/{id}',[ContactController::class,'show']);
    Route::post('contacts/update',[ContactController::class,'update'])->name('contacts.update');
//    Route::get('contacts/add',[ContactController::class,'create']);
    Route::post('contacts/store',[ContactController::class,'store'])->name('contacts.store');
    Route::get('contacts/delete/{id}',[ContactController::class,'destroy'])->name('contacts.destroy');
    Route::get('contacts/incomplete',[ContactController::class,'incomplete']);
    Route::post('contacts/incomplete/update',[ContactController::class,'updateStatus']);
    Route::get('contacts/excluded/email',[ContactController::class,'excluded']);
    Route::post('contacts/excluded/addemail',[ContactController::class,'addEmail']);
    Route::post('excldued/email/update',[ContactController::class,'updateexcluded'])->name('update.exclude');
    Route::post('deliverystatus',[MailgunController::class,'deliveryStatus'])->name('deliverystatus');
    Route::post('deliveryschart',[MailgunController::class,'deliverychart'])->name('deliverychart');
    Route::get('email/template',[Emailtemplate::class,'template'])->name('email.template');
    //birthday config
    Route::get('email/config/birthday',[Emailtemplate::class,'birthdayConfig']);
    Route::post('email/config/birthday/update',[Emailtemplate::class,'birthdayUpdate'])->name('birthday.update');
    Route::post('email/config/template',[Emailtemplate::class,'birthdayTemplate'])->name('email.birthdayTemplate');
    Route::post('email/config/birthday/activate',[Emailtemplate::class,'birthdayActivate']);
    //post stay config
    Route::get('email/config/poststay',[Emailtemplate::class,'postStayConfig']);
    Route::post('email/config/poststay/update',[Emailtemplate::class,'postStayUpdate'])->name('poststay.update');
    Route::post('email/config/template',[Emailtemplate::class,'poststayTemplate'])->name('email.poststayTemplate');
    Route::post('email/config/poststay/activate',[Emailtemplate::class,'poststayActivate']);
    Route::get('email/config/confirm',[Emailtemplate::class,'confirmConfig']);
    Route::post('email/config/confirm/update',[Emailtemplate::class,'confirmUpdate'])->name('confirm.update');
    Route::post('email/config/confirm/activate',[Emailtemplate::class,'confirmActivate']);

    //Pre Stay Config
    Route::get('email/config/prestay',[Emailtemplate::class,'preStayConfig']);
    Route::post('email/config/prestay/update',[Emailtemplate::class,'preStayUpdate'])->name('prestay.update');

    Route::get('email/{id}/review',[ContactController::class,'review']);
    Route::get('email/delivery/status',[MailgunController::class,'delivery']);
    Route::post('getClick',[MailgunController::class,'getclick'])->name('getClick');

    //We Miss You Letter
    Route::get('email/config/miss',[Emailtemplate::class,'missConfig']);
    Route::post('email/config/miss/update',[Emailtemplate::class,'missUpdate'])->name('miss.update');
    Route::post('email/config/miss',[Emailtemplate::class,'missTemplate'])->name('email.missTemplate');
    Route::post('email/config/miss/activate',[Emailtemplate::class,'missActivate']);
    Route::post('email/{id}/saverating',[Emailtemplate::class,'saveRating']);
    Route::resource('email',Emailtemplate::class);
    Route::post('template/destroy',[Emailtemplate::class,'destroy']);
    Route::post('campaign/template',[EmailTemplateController::class,'getTemplate'])->name('campaign.template');
    Route::post('campaign/recepient',[CampaignController::class,'getRecepient'])->name('campaign.recepient');
    Route::post('campaign/{id}/recepient',[CampaignController::class,'getRecepient']);
    Route::post('campaign/gettype',[CampaignController::class,'getType']);
    Route::post('campaign/{id}/gettype',[CampaignController::class,'getType']);

    route::get('campaign/list',[CampaignController::class,'index']);
    Route::get('campaigns',[CampaignController::class,'campaign']);
    Route::post('campaignlist',[CampaignController::class,'campaignlist'])->name('campaignlist');
    Route::post('campaign-recepient',[CampaignController::class,'campaignrecepient'])->name('campaignrecepient');
    Route::delete('campaign/{id}',[CampaignController::class,'destroy'])->name('campaign.destroy');
    Route::post('campaign/delete',[CampaignController::class,'delete'])->name('campaign.delete');
    Route::get('campaign/create',[CampaignController::class,'create']);
    Route::post('campaign/store',[CampaignController::class,'store'])->name('campaign.store');
    Route::get('campaign',[CampaignController::class,'index']);
    Route::get('mailsend/',[EmailTemplateController::class,'birthdaymail']);

    //External contact
    Route::get('contacts/category/{categoty}',[ExternalEmailController::class,'contactsbycategory']);
    Route::get('contacts/external',[ExternalEmailController::class,'index']);
    Route::post('contacts/saveexternalcontact',[ExternalEmailController::class,'saveexternalcontact']);
    Route::post('contacts/delete',[ExternalEmailController::class,'delcontact'])->name('delcontact');
//    Route::delete('contacts/external/destroy',[ExternalEmailController::class,'destroy']);
    Route::post('loadcategory',[ExternalEmailController::class,'categorylist'])->name('loadcategory');
    Route::post('delcategory',[ExternalEmailController::class,'delcategory'])->name('delcategory');
    Route::get('externalcontact/template',[ExternalEmailController::class,'template'])->name('downloadtemplate');
    Route::post('listexternalcontact',[ExternalEmailController::class,'listexternalcontact'])->name('listexternalcontact');

    //import contact
//    Route::get('contacts/import',[ContactController::class,'import']);
    Route::get('contacts/template/contact',function (){
        return response()->download(public_path().'/files/contacts-template.csv');
    });
    Route::post('contacts/upload/contact',[ContactController::class,'uploadContact']);

    //import stay
//    Route::get('contacts/importstay',[ContactController::class,'importStay']);
    Route::get('contacts/template/stay',function (){
        return response()->download(public_path().'/files/stays-template.csv');
    });
    Route::post('contacts/upload/stay',[ContactController::class,'uploadStay']);
    Route::post('contacts/company/store',[ContactController::class,'store'])->name('contacts.company.store');
    Route::get('contacts/stay/add/{id}',[TransactionController::class,'add']);
    Route::get('contacts/stay/edit/{id}',[TransactionController::class,'edit']);
    Route::post('contacts/stay/store',[TransactionController::class,'store'])->name('stay.store');
    Route::post('contacts/stay/update',[TransactionController::class,'update'])->name('stay.update');
    Route::get('contacts/stay/delete/{id}',[TransactionController::class,'delete'])->name('stay.delete');
    Route::post('updateschedule',[CampaignController::class,'updateschedule'])->name('updateschedule');
    Route::post('contacts/newcampaign',[CampaignController::class,'newCampaign']);
    Route::post('email/saveclone',[Emailtemplate::class,'cloneTemplate']);
    Route::post('email/sendtest',[Emailtemplate::class,'sendTest']);
    Route::post('campaign/savesegment',[CampaignController::class,'saveSegment'])->name('savesegment');
    Route::post('segments/updatesegment',[SegmentController::class,'update']);

    //segment
    Route::post('segments/filtersegment',[SegmentController::class,'filterSegment'])->name('filtersegment');
    Route::resource('segments',SegmentController::class);
    Route::get('preferences',function (){

        return view('preferences.index');
    });
    Route::post('savepreferences',[PreferencesController::class,'savePreferences']);
    Route::post('getcountry',[ContactController::class,'getcountry'])->name('getcountry');

    //  Reservation
    Route::get('reservation',[ReservationController::class,'index']);
    Route::get('reservation/{registrationcode}',[ReservationController::class,'registrationformprint'])->name('registrationformprint');

    //  In House
    Route::get('inhouse',[InhouseController::class,'index']);
});
Route::get('list',function (){
    return view('contacts.list3');
})->name('list');
Route::post('contactslist',[ContactController::class,'contactslist'])->name('contactslist');
Route::get('contacts/list',function (){
    return view('contacts.list3',['gender'=>NULL,'country'=>NULL]);
});
Route::post('loadcontacts',[ContactController::class,'loadcontacts'])->name('loadcontacts');
Auth::routes();

//Route for testing
Route::get('test', function () {
    $configPrestay=ConfigPrestay::find(1);
    $mail=new PepipostMail();
    if($configPrestay->active=='y') {
        $configPrestay_templ = MailEditor::find($configPrestay->template_id);
        $excluded = ExcludedEmail::pluck('email')->all();
        $contact_lists = [];
        $contactPrestayLists = Contactprestay::select('contact_prestays.id','contacts.fname','contacts.lname','contacts.email','contact_prestays.dateci', 'contact_prestays.registration_code')
            ->leftJoin('contacts','contacts.contactid','=','contact_prestays.contact_id')
            ->leftJoin('profilesfolio','contacts.contactid','=','profilesfolio.profileid')
            ->where('sendtoguest_at', null)
            ->where('registration_code', '!=', null)
            ->where('next_action', 'FETCHFROMWEB')
            ->whereNotIn('contacts.email', $excluded)->get();
        foreach ($contactPrestayLists as $contactPrestay) {
            if (Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                array_push($contact_lists, $contactPrestay);
            }
        }

        //Kirim email ke list email Prestay
        foreach ($contact_lists as $contactlist){
            dd($contactPrestay->registration_code);
            $mail->send($contactlist,$configPrestay_templ,'prestay,'.env('UNIT').'','prestay',null,$contactPrestay->registration_code);
            $contact_prestay = Contactprestay::find($contactlist->id);
            $contact_prestay->update(
                [
                    'sendtoguest_at' => Carbon::now(),
                ]
            );
        }
    }
});
