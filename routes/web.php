<?php

use App\Http\Controllers\API\PrestayController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\InhouseController;
use App\Http\Controllers\PepipostMail;
use App\Http\Controllers\PromoprestayController;
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
use App\Models\Configuration;
use App\Models\MailgunLogs;
use App\Models\MailEditor;
use App\Models\ProfileFolio;
use App\Models\Promoprestay;
use App\Models\Promoprestaycontact;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Models\Contact;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


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

Route::get('apitest',function (){

    $mail = new PepipostMail();
    $config=App\Models\Configuration::first();

    $contacts=[];
    $poststay=App\Models\Contact::has('profilesfolio','>',0)->whereHas('profilesfolio',function($q){
        return $q->whereDate('dateco','>=',\Carbon\Carbon::now()->subDays(2)->format('Y-m-d'))
            ->whereDate('dateco','<=',\Carbon\Carbon::now()->format('Y-m-d'));
    })->get();

    foreach ($poststay as $key => $contact) {
        array_push($contacts, $contact);
    }

    foreach ($contacts as $key => $contact) {
        $logs = $mail->getMailLogs($config->sender_email,$contact);
        dd($logs,"FOR CHECK TO ACCESS THE API");
    }
});



Route::get('email/quota/{month?}', function($month = null) {
    $mail = new PepipostMail();
    return $mail->getEmailQuota($month);
});
// Route untuk assign user ID 1 ke role Super Admin
Route::get('/assign-super-admin', function() {
    // Buat role super admin jika belum ada
    $superAdminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
    
    // Buat permission dashboard jika belum ada
    $dashboardPermission = Permission::firstOrCreate(['name' => '2.1.1_view_dashboard']);
    
    // Assign permission ke role super admin
    $superAdminRole->givePermissionTo($dashboardPermission);
    
    // Assign user ID 1 sebagai super admin
    $adminUser = User::find(1);
    if ($adminUser) {
        $adminUser->assignRole('SUPER_ADMIN');
        $message = "User ID 1 ({$adminUser->name}) berhasil diassign sebagai super admin";
    } else {
        $message = "User dengan ID 1 tidak ditemukan";
    }
    
    return response()->json([
        'status' => 'success',
        'message' => $message,
        'user' => $adminUser ? [
            'id' => $adminUser->id,
            'name' => $adminUser->name,
            'email' => $adminUser->email,
            'roles' => $adminUser->getRoleNames()
        ] : null
    ]);
});

// Route untuk membuat user testing
Route::get('/create-test-user', function() {
    // Buat user testing baru
    $testUser = User::updateOrCreate(
        ['email' => 'testing@example.com'],
        [
            'name' => 'User Testing',
            'password' => bcrypt('password123'),
        ]
    );
    
    return response()->json([
        'status' => 'success',
        'message' => 'User testing berhasil dibuat',
        'user' => [
            'id' => $testUser->id,
            'name' => $testUser->name,
            'email' => $testUser->email,
            'password' => 'password123' // Password dalam plaintext untuk keperluan testing
        ]
    ]);
});

Route::get('/unauthorized', function() {
    return view('errors.unauthorized');
})->name('unauthorized');

// Tambahkan route untuk login form
Route::get('/login-form', function() {
    if (Auth::check()) {
        Auth::logout();
    }
    return redirect('/login');
})->name('login.form');
Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
Route::put('profile', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');


Route::group(['middleware'=>'auth'],function (){


// Role-Permission Management Routes
Route::get('/role-permissions', 'App\Http\Controllers\RolePermissionController@index')->name('role-permissions.index')->middleware('can:1.1.3_manage_roles');
Route::get('/role-permissions/{id}/edit', 'App\Http\Controllers\RolePermissionController@edit')->name('role-permissions.edit')->middleware('can:1.1.3_manage_roles');
Route::put('/role-permissions/{id}', 'App\Http\Controllers\RolePermissionController@update')->name('role-permissions.update')->middleware('can:1.1.3_manage_roles');
Route::get('/role-permissions/{id}/users', 'App\Http\Controllers\RolePermissionController@getRoleUsers')->name('role-permissions.users')->middleware('can:1.1.3_manage_roles');
Route::delete('/role-permissions/{id}', 'App\Http\Controllers\RolePermissionController@destroy')->name('role-permissions.destroy')->middleware('can:1.1.3_manage_roles');
Route::get('/role-permissions/create', 'App\Http\Controllers\RolePermissionController@create')->name('role-permissions.create')->middleware('can:1.1.3_manage_roles');
Route::post('/role-permissions', 'App\Http\Controllers\RolePermissionController@store')->name('role-permissions.store')->middleware('can:1.1.3_manage_roles');


// User Assignment Routes
Route::get('/role-permissions/{id}/assign-users', 'App\Http\Controllers\RolePermissionController@assignUsers')->name('role-permissions.assign-users')->middleware('can:1.1.3_manage_roles');
Route::post('/role-permissions/{id}/assign-users', 'App\Http\Controllers\RolePermissionController@storeUserAssignments')->name('role-permissions.store-assignments')->middleware('can:1.1.3_manage_roles');
Route::delete('/role-permissions/{roleId}/users/{userId}', 'App\Http\Controllers\RolePermissionController@removeUserFromRole')->name('role-permissions.remove-user')->middleware('can:1.1.3_manage_roles');
  
// User Management Routes
Route::get('/role-permissions/create-user', 'App\Http\Controllers\RolePermissionController@createUser')->name('role-permissions.create-user')->middleware('can:1.1.3_manage_roles');
Route::post('/role-permissions/store-user', 'App\Http\Controllers\RolePermissionController@storeUser')->name('role-permissions.store-user')->middleware('can:1.1.3_manage_roles');

// New User Management Routes
Route::get('/role-permissions/manage-users', 'App\Http\Controllers\RolePermissionController@manageUsers')->name('role-permissions.manage-users')->middleware('can:1.1.3_manage_roles');
Route::get('/role-permissions/users/{id}/edit', 'App\Http\Controllers\RolePermissionController@editUser')->name('role-permissions.edit-user')->middleware('can:1.1.3_manage_roles');
Route::put('/role-permissions/users/{id}', 'App\Http\Controllers\RolePermissionController@updateUser')->name('role-permissions.update-user')->middleware('can:1.1.3_manage_roles');

Route::get('/logs', 'App\Http\Controllers\LogController@index')->name('logs.index');
    
    Route::get('home',function (){
        return redirect('/');
    });
    Route::get('/',[ContactController::class,'dashboard'])->middleware('can:2.1.1_view_dashboard');
//    Route::get('reviews',[ContactController::class,'reviews']);
    Route::post('campaign/getsegment',[CampaignController::class,'getSegment']);
    Route::post('campaign/activate',[CampaignController::class,'activateCampaign'])->name('campaign.activate');
    Route::get('contacts/f/male',[ContactController::class,'male']);
    Route::get('contacts/f/female',[ContactController::class,'female']);
    Route::get('contacts/f/country/{country}',[ContactController::class,'country']);
    Route::get('contacts/f/created/{dateadded}',[ContactController::class,'dateadded']);
    Route::get('contacts/f/status/{status}',[ContactController::class,'dstatus']);
    Route::get('contacts/repeaters', [ContactController::class, 'repeaters']);
    Route::get('contacts/inhouse-repeaters', [ContactController::class, 'inhouseRepeaters']);
    Route::get('contacts/f/repeater-month/{month}', [ContactController::class, 'repeaterByMonth']);
    Route::post('dashboard/repeater-data', [ContactController::class, 'getRepeaterDataAjax'])->name('dashboard.repeater.data');
    Route::get('/contacts/inhouse-birthday-today', function() {
    $contacts = \App\Models\Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") = ?', [\Carbon\Carbon::now()->format('m-d')])
        ->whereHas('profilesfolio', function($q) {
            return $q->where('foliostatus', '=', 'I');
        })
        ->get();
    
    return view('contacts.list', ['data' => $contacts, 'title' => 'In-House Guests Birthday Today']);
})->name('contacts.inhouse.birthday.today');
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
    Route::post('contacts/excluded/delete',[ContactController::class,'deleteExcludedEmail']);
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
    Route::post('email/config/poststay/surveyactivate',[Emailtemplate::class,'surveyActivate']);
    Route::get('email/config/confirm',[Emailtemplate::class,'confirmConfig']);
    Route::post('email/config/confirm/update',[Emailtemplate::class,'confirmUpdate'])->name('confirm.update');
    Route::post('email/config/confirm/activate',[Emailtemplate::class,'confirmActivate']);

    //Pre Stay Config
    Route::get('email/config/prestay',[Emailtemplate::class,'preStayConfig']);
    Route::post('email/config/prestay/update',[Emailtemplate::class,'preStayUpdate'])->name('prestay.update');
    Route::post('email/config/prestay/activate',[Emailtemplate::class,'prestayActivate']);

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
    // Existing route
    Route::post('email/sendtest',[Emailtemplate::class,'sendTest']);
    
    // New simple test email route with form
    Route::get('email123/simple-test', function() {
        return view('email.simple-test');
    });
    
    Route::post('email123/send-simple-test', function() {
        try {
            $config = App\Models\Configuration::first();
            $mail = new App\Http\Controllers\PepipostMail();
            
            // Email tujuan dari form
            $testEmail = request('email');
            
            if (empty($testEmail)) {
                return back()->with('error', 'Email tujuan harus diisi');
            }
            
            // Subject dan body email sederhana
            $subject = 'Test Email dari CRM-RMS';
            $body = '<html><body><h1>Test Email</h1><p>Ini adalah test email sederhana dari CRM-RMS.</p><p>Waktu kirim: ' . date('Y-m-d H:i:s') . '</p></body></html>';
            
            // Kirim email langsung tanpa template
            $result = $mail->sendSimpleEmail($config->sender_email, $testEmail, $subject, $body);
            
            return back()->with('success', 'Email berhasil dikirim ke ' . $testEmail);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    });
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
    Route::resource('prestay/promo-configuration',PromoprestayController::class);
    Route::post('prestay/promo-configuration/destroy',[PromoprestayController::class,'destroy']);
Route::get('/registration-preview/{code}', [ReservationController::class, 'registrationformpreview'])->name('registration.preview');

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
Route::get('contacts/repeaters/list',function (){
    return view('contacts.list3',['gender'=>NULL,'repeater_only'=>true]);
});
Route::post('loadcontacts',[ContactController::class,'loadcontacts'])->name('loadcontacts');
Auth::routes();

Route::get('/test-birthday-email', function() {
    $controller = new App\Http\Controllers\PepipostMail();
    return $controller->forceSendBirthdayEmail('angga1201putra@gmail.com');
});

//Route for testing
Route::get('test', function () {
//    $configPrestay=ConfigPrestay::find(1);
//    $mail=new PepipostMail();
//    if($configPrestay->active=='y') {
//        $configPrestay_templ = MailEditor::find($configPrestay->template_id);
//        $excluded = ExcludedEmail::pluck('email')->all();
//        $contact_lists = [];
//        $contactPrestayLists = Contactprestay::select('contact_prestays.id', 'contacts.contactid','contacts.fname', 'contacts.lname', 'contacts.email', 'contact_prestays.dateci', 'contact_prestays.registration_code')
//            ->leftJoin('contacts', 'contacts.contactid', '=', 'contact_prestays.contact_id')
//            ->leftJoin('profilesfolio', 'contacts.contactid', '=', 'profilesfolio.profileid')
//            ->where('sendtoguest_at', null)
//            ->where('registration_code', '!=', null)
//            ->where('next_action', 'FETCHFROMWEB')
//            ->whereNotIn('contacts.email', $excluded)->get();
//        foreach ($contactPrestayLists as $contactPrestay) {
//            if (Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
//                array_push($contact_lists, $contactPrestay);
//            }
//        }
//        $promoprestay = Promoprestay::all();
//        foreach ($promoprestay as $key=>$promo)
//        {
//            $promoprestay[$key]['duration_start'] = Carbon::parse(substr($promo->event_duration,'0','10'))->format('Y-m-d');
//            $promoprestay[$key]['duration_end'] = Carbon::parse(substr($promo->event_duration,'-10'))->format('Y-m-d');
//        }
//
//        //Kirim email ke list email Prestay
//        foreach ($contact_lists as $contactlist) {
//            foreach ($promoprestay as $key=>$promo)
//            {
//                if (($contactlist->dateci >= $promo->duration_start) && ($contactlist->dateci  <= $promo->duration_end))
//                {
//                    $replace = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div><p style="text-align: center;"><a href="'.$promo->event_url.'" target="_blank" rel="noopener">
//                        <img title="'.$promo->name.'" src="'.$promo->event_picture.'" alt="" width="500" height="160" />
//                        </a></p>';
//                    $search = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div>';
//                    $configPrestay_templ['content'] = str_replace($search,$replace,$configPrestay_templ->content);
//
//                    //Insert ke Promo Prestay contact
//                    Promoprestaycontact::create([
//                        'promo_prestay_id' =>$promo->id,
//                        'contact_id' => $contactlist->contactid,
//                        'sent_at' => Carbon::now()
//                    ]);
//                }
//
//            }
//
//            $mail->send($contactlist, $configPrestay_templ, 'prestay,' . env('UNIT') . '', 'prestay', null, $contactPrestay->registration_code);
//            $contact_prestay = Contactprestay::find($contactlist->id);
//            $contact_prestay->update(
//                [
//                    'sendtoguest_at' => Carbon::now(),
//                ]
//            );
//        }
//        dd('email sent');
//    }

});

// Force get mail logs for April 2, 2025
Route::get('/force-get-mail-logs', function() {
    $mail = new PepipostMail();
    $config = Configuration::first();
    
    // Set tanggal spesifik 2 April 2025
    $date = Carbon::parse('2025-05-06')->format('Y-m-d');
    $enddate = Carbon::parse('2025-05-06')->addDays(1)->format('Y-m-d');
    
    // Ambil log dari API Pepipost
    $logs = $mail->getMailLogsForDate($config->sender_email, $date, $enddate);
    
    $processedCount = 0;
    
    if(!empty($logs['data'])) {
        foreach ($logs['data'] as $log) {
            $str = $log['fromaddress'];
            $domains = substr($str, strpos($str, "@") + 1);
            $urls = [];
            $email_id = $log['trid'];
            $recipient = $log['rcptEmail'];
            $status = $log['status'];
            $url = '';
            $tag = '';
            $id = $log['xapiheader'];
            $remarks = $log['remarks'];
            $delivery_status = '';
            
            if ($status == 'dropped') {
                $event = 'failed';
                $delivery_status = $log['remarks'];
            } elseif ($status == 'open') {
                $event = 'opened';
            } elseif($status == 'click') {
                foreach ($log['clicks'] as $clicks) {
                    array_push($urls, $clicks['link']);
                }
                $url = implode(';', $urls);
                $event = 'clicked';
            } elseif ($status == 'hardbounce' || $status == 'invalid') {
                $delivery_status = $log['remarks'];
                $event = 'failed';
                ExcludedEmail::updateOrCreate(
                    ['email' => $recipient],
                    ['reason' => 'Invalid email / Hard bounce']
                );
            } elseif ($status == 'spam') {
                $event = 'spam';
            } elseif ($status == 'unsubscribe') {
                $event = 'unsubscribed';
                ExcludedEmail::updateOrCreate(
                    ['email' => $recipient],
                    ['reason' => 'The recipient opted out using unsubscribe link']
                );
            } else {
                $event = 'delivered';
            }
            
            $tags = isset($log['tags'][0]) ? $log['tags'][0] : '';
            if($tags) {
                $tag = $tags;
            } else {
                $tag = '';
            }
            
            $time = Carbon::parse($log['deliveryTime'])->format('Y-m-d H:i:s');
            
            // Simpan ke database jika tidak dalam daftar pengecualian
            if (!in_array($recipient, ['sempowlajuz@gmail.com', 'danabala72@gmail.com', 'mudanakomang@hotmail.com', 'agussatria712@gmail.com', 'it.sysdev@rcoid.com'])) {
                $unitTag = isset($log['tags'][1]) ? $log['tags'][1] : '';
                if ($domains == env('DOMAIN') && ($id != "" || $id != NULL) && $unitTag == env('UNIT')) {
                    MailgunLogs::updateOrCreate(
                        ['email_id' => $id, 'recipient' => $recipient],
                        [
                            'message_id' => $email_id, 
                            'event' => $event, 
                            'severity' => NULL, 
                            'url' => $url, 
                            'tags' => $tag, 
                            'recipient' => $recipient, 
                            'timestamp' => $time, 
                            'delivery_status' => $delivery_status
                        ]
                    );
                    $processedCount++;
                }
            }
        }
    }
    
    // Proses excluded emails
    $mailgunlogs = MailgunLogs::whereIn('event', ['failed', 'unsubscribed'])->get();
    foreach ($mailgunlogs as $mailgunlog) {
        if ($mailgunlog->event == 'unsubscribed') {
            ExcludedEmail::updateOrCreate(
                ['email' => $mailgunlog->recipient],
                ['reason' => 'The recipient opted out using unsubscribe link']
            );
        } else {
            ExcludedEmail::updateOrCreate(
                ['email' => $mailgunlog->recipient],
                ['reason' => $mailgunlog->delivery_status]
            );
        }
    }
    
    return [
        'status' => 'success',
        'message' => 'Processed ' . $processedCount . ' logs for May 4, 2025',
        'logs_count' => count($logs['data'] ?? [])
    ];
});


// Route untuk melihat kontak yang akan menerima email PostStay hari ini
Route::get('/check-poststay-contacts', function() {
    $poststay = \App\Models\PostStay::find(1);
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Jika konfigurasi poststay tidak aktif
    if ($poststay->active != 'y') {
        return response()->json([
            'status' => 'info',
            'message' => 'PostStay email is not active',
            'config' => $poststay
        ]);
    }
    
    // Tambahkan informasi template
    $template = \App\Models\MailEditor::find($poststay->template_id);
    
    // Cari kontak yang akan menerima email poststay hari ini
    $user_list = [];
    
    // Kontak dengan lebih dari 1 transaksi
    $users1 = \App\Models\Contact::has('transaction', '>', 1)
        ->whereNotIn('email', $excluded)
        ->get();
    
    foreach ($users1 as $u) {
        if (\Carbon\Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
            array_push($user_list, $u);
        }
    }
    
    // Kontak dengan tepat 1 transaksi
    $users2 = \App\Models\Contact::has('transaction', '=', 1)
        ->whereNotIn('email', $excluded)
        ->get();
    
    foreach ($users2 as $u) {
        if (\Carbon\Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
            array_push($user_list, $u);
        }
    }
    
    return response()->json([
        'status' => 'success',
        'message' => count($user_list) . ' contacts will receive PostStay email today',
        'config' => [
            'active' => $poststay->active,
            'sendafter' => $poststay->sendafter,
            'template_id' => $poststay->template_id,
            'template_name' => $template ? $template->name : 'Template not found'
        ],
        'send_date' => \Carbon\Carbon::now()->format('Y-m-d'),
        'contacts' => collect($user_list)->map(function($contact) use ($poststay) {
            return [
                'id' => $contact->contactid,
                'name' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'checkout_date' => \Carbon\Carbon::parse($contact->profilesfolio[0]->dateco)->format('Y-m-d'),
                'days_after_checkout' => $poststay->sendafter,
                'will_receive_on' => \Carbon\Carbon::now()->format('Y-m-d')
            ];
        })
    ]);
});

// Route untuk mengirim email PostStay secara manual ke semua kontak yang seharusnya menerima hari ini
Route::get('/send-poststay-emails-manually', function() {
    $poststay = \App\Models\PostStay::find(1);
    $mail = new \App\Http\Controllers\PepipostMail();
    
    if ($poststay->active != 'y') {
        return response()->json([
            'status' => 'error',
            'message' => 'PostStay email is not active'
        ]);
    }
    
    $template = \App\Models\MailEditor::find($poststay->template_id);
    if (!$template) {
        return response()->json([
            'status' => 'error',
            'message' => 'PostStay email template not found'
        ]);
    }
    
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Cari kontak yang akan menerima email poststay hari ini
    $user_list = [];
    
    // Kontak dengan lebih dari 1 transaksi
    $users1 = \App\Models\Contact::has('transaction', '>', 1)
        ->whereNotIn('email', $excluded)
        ->get();
    
    foreach ($users1 as $u) {
        if (\Carbon\Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
            array_push($user_list, $u);
        }
    }
    
    // Kontak dengan tepat 1 transaksi
    $users2 = \App\Models\Contact::has('transaction', '=', 1)
        ->whereNotIn('email', $excluded)
        ->get();
    
    foreach ($users2 as $u) {
        if (\Carbon\Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
            array_push($user_list, $u);
        }
    }
    
    $sent = 0;
    $failed = 0;
    $results = [];
    
    foreach ($user_list as $contact) {
        try {
            $result = $mail->send($contact, $template, 'poststay,'.env('UNIT'), 'poststay', null);
            $sent++;
            $results[] = [
                'contact' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'status' => 'sent',
                'result' => $result
            ];
        } catch (\Exception $e) {
            $failed++;
            $results[] = [
                'contact' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return response()->json([
        'status' => 'success',
        'message' => "Processed {$sent} emails successfully, {$failed} failed",
        'total_contacts' => count($user_list),
        'results' => $results
    ]);
});

Route::get('/check-birthday-contacts', function() {
    $birthday = \App\Models\Birthday::find(1);
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Jika konfigurasi birthday tidak aktif
    if ($birthday->active != 'y') {
        return response()->json([
            'status' => 'info',
            'message' => 'Birthday email is not active',
            'config' => $birthday
        ]);
    }
    
    // Cari kontak yang ulang tahunnya jatuh pada tanggal saat ini + sendafter hari
    $contacts = \App\Models\Contact::whereRaw('DATE_FORMAT(birthday,\'%m-%d\')=DATE_FORMAT(DATE_ADD(now(),INTERVAL \''.abs($birthday->sendafter).'\' day),\'%m-%d\') ')
        ->whereNotIn('email', $excluded)
        ->get();
    
    // Tambahkan informasi template
    $template = \App\Models\MailEditor::find($birthday->template_id);
    
    // Hitung tanggal pengiriman email (hari ini) dan tanggal ulang tahun (hari ini + sendafter)
    $sendDate = now()->format('Y-m-d'); // Tanggal pengiriman adalah hari ini
    $birthdayDate = now()->addDays(abs($birthday->sendafter))->format('Y-m-d'); // Tanggal ulang tahun
    
    return response()->json([
        'status' => 'success',
        'message' => count($contacts) . ' contacts will receive birthday email today',
        'config' => [
            'active' => $birthday->active,
            'sendafter' => $birthday->sendafter,
            'template_id' => $birthday->template_id,
            'template_name' => $template ? $template->name : 'Template not found'
        ],
        'send_date' => $sendDate, // Tambahkan informasi tanggal pengiriman
        'birthday_date' => $birthdayDate, // Tambahkan informasi tanggal ulang tahun
        'contacts' => $contacts->map(function($contact) use ($birthday, $sendDate) {
            return [
                'id' => $contact->contactid,
                'name' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,

                'birthday' => $contact->birthday,
                'will_receive_on' => $sendDate, // Tanggal pengiriman adalah hari ini
                'birthday_date' => now()->addDays(abs($birthday->sendafter))->format('Y-m-d') // Tanggal ulang tahun
            ];
        })
    ]);
});

// Route untuk mengirim email ulang tahun secara manual ke semua kontak yang seharusnya menerima hari ini
Route::get('/send-birthday-emails-manually', function() {
    $birthday = \App\Models\Birthday::find(1);
    $email = new \App\Http\Controllers\PepipostMail();
    
    if ($birthday->active != 'y') {
        return response()->json([
            'status' => 'error',
            'message' => 'Birthday email is not active'
        ]);
    }
    
    $template = \App\Models\MailEditor::find($birthday->template_id);
    if (!$template) {
        return response()->json([
            'status' => 'error',
            'message' => 'Birthday email template not found'
        ]);
    }
    
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    $contacts = \App\Models\Contact::whereRaw('DATE_FORMAT(birthday,\'%m-%d\')=DATE_FORMAT(DATE_ADD(now(),INTERVAL \''.abs($birthday->sendafter).'\' day),\'%m-%d\') ')
        ->whereNotIn('email', $excluded)
        ->get();
    
    $sent = 0;
    $failed = 0;
    $results = [];
    
    foreach ($contacts as $contact) {
        try {
            $result = $email->send($contact, $template, 'birthday,'.env('UNIT'), 'bday', null);
            $sent++;
            $results[] = [
                'contact' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'status' => 'sent',
                'result' => $result
            ];
        } catch (\Exception $e) {
            $failed++;
            $results[] = [
                'contact' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return response()->json([
        'status' => 'success',
        'message' => "Processed {$sent} emails successfully, {$failed} failed",
        'total_contacts' => count($contacts),
        'results' => $results
    ]);
});
// Route untuk melihat kontak yang akan menerima email MissYou hari ini
Route::get('/check-missyou-contacts', function() {
    $miss = \App\Models\MissYou::find(1);
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Jika konfigurasi missyou tidak aktif
    if ($miss->active != 'y') {
        return response()->json([
            'status' => 'info',
            'message' => 'MissYou email is not active',
            'config' => $miss
        ]);
    }
    
    // Tambahkan informasi template
    $template = \App\Models\MailEditor::find($miss->template_id);
    
    // Cari kontak yang akan menerima email missyou hari ini
    $users = \App\Models\Contact::whereHas('profilesfolio', function ($q) use ($miss) {
        return $q->latest()->limit(1)
            ->whereRaw('DATE(dateco) = DATE(NOW() - INTERVAL \''.$miss->sendafter.'\' MONTH)')
            ->where('foliostatus', '=', 'O');
    })->whereNotIn('email', $excluded)->get();
    
    return response()->json([
        'status' => 'success',
        'message' => count($users) . ' contacts will receive MissYou email today',
        'config' => [
            'active' => $miss->active,
            'sendafter' => $miss->sendafter,
            'template_id' => $miss->template_id,
            'template_name' => $template ? $template->name : 'Template not found'
        ],
        'send_date' => \Carbon\Carbon::now()->format('Y-m-d'),
        'contacts' => $users->map(function($contact) use ($miss) {
            $lastStay = $contact->profilesfolio()->latest()->first();
            return [
                'id' => $contact->contactid,
                'name' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'last_checkout_date' => $lastStay ? \Carbon\Carbon::parse($lastStay->dateco)->format('Y-m-d') : null,
                'months_since_checkout' => $miss->sendafter,
                'will_receive_on' => \Carbon\Carbon::now()->format('Y-m-d')
            ];
        })
    ]);
});

// Route untuk mengirim email MissYou secara manual ke semua kontak yang seharusnya menerima hari ini
Route::get('/send-missyou-emails-manually', function() {
    $miss = \App\Models\MissYou::find(1);
    $mail = new \App\Http\Controllers\PepipostMail();
    
    if ($miss->active != 'y') {
        return response()->json([
            'status' => 'error',
            'message' => 'MissYou email is not active'
        ]);
    }
    
    $template = \App\Models\MailEditor::find($miss->template_id);
    if (!$template) {
        return response()->json([
            'status' => 'error',
            'message' => 'MissYou email template not found'
        ]);
    }
    
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Cari kontak yang akan menerima email missyou hari ini
    $users = \App\Models\Contact::whereHas('profilesfolio', function ($q) use ($miss) {
        return $q->latest()->limit(1)
            ->whereRaw('DATE(dateco) = DATE(NOW() - INTERVAL \''.$miss->sendafter.'\' MONTH)')
            ->where('foliostatus', '=', 'O');
    })->whereNotIn('email', $excluded)->get();
    
    $sent = 0;
    $failed = 0;
    $results = [];
    
    foreach ($users as $contact) {
        try {
            $result = $mail->send($contact, $template, 'missyou,'.env('UNIT'), 'missyou', null);
            $sent++;
            $results[] = [
                'contact' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'status' => 'sent',
                'result' => $result
            ];
        } catch (\Exception $e) {
            $failed++;
            $results[] = [
                'contact' => $contact->fname . ' ' . $contact->lname,
                'email' => $contact->email,
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return response()->json([
        'status' => 'success',
        'message' => "Processed {$sent} emails successfully, {$failed} failed",
        'total_contacts' => count($users),
        'results' => $results
    ]);
});

// Route untuk melihat kontak yang akan menerima email PreStay hari ini
Route::get('/check-prestay-contacts', function() {
    $configPrestay = \App\Models\ConfigPrestay::find(1);
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Jika konfigurasi prestay tidak aktif
    if ($configPrestay->active != 'y') {
        return response()->json([
            'status' => 'info',
            'message' => 'PreStay email is not active',
            'config' => $configPrestay
        ]);
    }
    
    // Tambahkan informasi template
    $template = \App\Models\MailEditor::find($configPrestay->template_id);
    
    // Cari kontak yang akan menerima email prestay hari ini
    $contact_lists = [];
    $contactPrestayLists = \App\Models\Contactprestay::select(
            'contact_prestays.id', 
            'contacts.contactid',
            'contacts.fname', 
            'contacts.lname', 
            'contacts.email', 
            'contact_prestays.dateci', 
            'contact_prestays.registration_code'
        )
        ->leftJoin('contacts', 'contacts.contactid', '=', 'contact_prestays.contact_id')
        ->leftJoin('profilesfolio', 'contacts.contactid', '=', 'profilesfolio.profileid')
        ->where('sendtoguest_at', null)
        ->where('registration_code', '!=', null)
        ->where('next_action', 'FETCHFROMWEB')
        ->whereNotIn('contacts.email', $excluded)
        ->get();
    
    foreach ($contactPrestayLists as $contactPrestay) {
        if (\Carbon\Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
            array_push($contact_lists, $contactPrestay);
        }
    }
    
    // Ambil informasi promo prestay
    $promoprestay = \App\Models\Promoprestay::all();
    foreach ($promoprestay as $key => $promo) {
        $promoprestay[$key]['duration_start'] = \Carbon\Carbon::parse(substr($promo->event_duration, '0', '10'))->format('Y-m-d');
        $promoprestay[$key]['duration_end'] = \Carbon\Carbon::parse(substr($promo->event_duration, '-10'))->format('Y-m-d');
    }
    
    // Siapkan data promo yang akan ditampilkan untuk setiap kontak
    $contactsWithPromo = [];
    foreach ($contact_lists as $contact) {
        $applicablePromos = [];
        foreach ($promoprestay as $promo) {
            if (($contact->dateci >= $promo->duration_start) && ($contact->dateci <= $promo->duration_end)) {
                $applicablePromos[] = [
                    'id' => $promo->id,
                    'name' => $promo->name,
                    'url' => $promo->event_url,
                    'picture' => $promo->event_picture
                ];
            }
        }
        
        $contactsWithPromo[] = [
            'id' => $contact->id,
            'contact_id' => $contact->contactid,
            'name' => $contact->fname . ' ' . $contact->lname,
            'email' => $contact->email,
            'check_in_date' => $contact->dateci,
            'registration_code' => $contact->registration_code,
            'will_receive_on' => \Carbon\Carbon::now()->format('Y-m-d'),
            'applicable_promos' => $applicablePromos
        ];
    }
    
    return response()->json([
        'status' => 'success',
        'message' => count($contact_lists) . ' contacts will receive PreStay email today',
        'config' => [
            'active' => $configPrestay->active,
            'sendafter' => $configPrestay->sendafter,
            'template_id' => $configPrestay->template_id,
            'template_name' => $template ? $template->name : 'Template not found'
        ],
        'send_date' => \Carbon\Carbon::now()->format('Y-m-d'),
        'contacts' => $contactsWithPromo
    ]);
});

// Route untuk mengirim email PreStay secara manual ke semua kontak yang seharusnya menerima hari ini
Route::get('/send-prestay-emails-manually', function() {
    $configPrestay = \App\Models\ConfigPrestay::find(1);
    $mail = new \App\Http\Controllers\PepipostMail();
    
    if ($configPrestay->active != 'y') {
        return response()->json([
            'status' => 'error',
            'message' => 'PreStay email is not active'
        ]);
    }
    
    $template = \App\Models\MailEditor::find($configPrestay->template_id);
    if (!$template) {
        return response()->json([
            'status' => 'error',
            'message' => 'PreStay email template not found'
        ]);
    }
    
    $excluded = \App\Models\ExcludedEmail::pluck('email')->all();
    
    // Cari kontak yang akan menerima email prestay hari ini
    $contact_lists = [];
    $contactPrestayLists = \App\Models\Contactprestay::select(
            'contact_prestays.id', 
            'contacts.contactid',
            'contacts.fname', 
            'contacts.lname', 
            'contacts.email', 
            'contact_prestays.dateci', 
            'contact_prestays.registration_code'
        )
        ->leftJoin('contacts', 'contacts.contactid', '=', 'contact_prestays.contact_id')
        ->leftJoin('profilesfolio', 'contacts.contactid', '=', 'profilesfolio.profileid')
        ->where('sendtoguest_at', null)
        ->where('registration_code', '!=', null)
        ->where('next_action', 'FETCHFROMWEB')
        ->whereNotIn('contacts.email', $excluded)
        ->get();
    
    foreach ($contactPrestayLists as $contactPrestay) {
        if (\Carbon\Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
            array_push($contact_lists, $contactPrestay);
        }
    }
    
    // Ambil informasi promo prestay
    $promoprestay = \App\Models\Promoprestay::all();
    foreach ($promoprestay as $key => $promo) {
        $promoprestay[$key]['duration_start'] = \Carbon\Carbon::parse(substr($promo->event_duration, '0', '10'))->format('Y-m-d');
        $promoprestay[$key]['duration_end'] = \Carbon\Carbon::parse(substr($promo->event_duration, '-10'))->format('Y-m-d');
    }
    
    $sent = 0;
    $failed = 0;
    $results = [];
    
    // Kirim email ke setiap kontak
    foreach ($contact_lists as $contactlist) {
        try {
            // Reset template content untuk setiap kontak
            $configPrestay_templ = clone $template;
            
            // Cek dan tambahkan promo jika ada
            foreach ($promoprestay as $key => $promo) {
                if (($contactlist->dateci >= $promo->duration_start) && ($contactlist->dateci <= $promo->duration_end)) {
                    $replace = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div><p style="text-align: center;"><a href="'.$promo->event_url.'" target="_blank" rel="noopener">
                    <img title="'.$promo->name.'" src="'.$promo->event_picture.'" alt="" width="500" height="160" />
                    </a></p>';
                    $search = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div>';
                    $configPrestay_templ->content = str_replace($search, $replace, $configPrestay_templ->content);
                    
                    // Insert ke Promo Prestay contact
                    \App\Models\Promoprestaycontact::create([
                        'promo_prestay_id' => $promo->id,
                        'contact_id' => $contactlist->contactid,
                        'sent_at' => \Carbon\Carbon::now()
                    ]);
                }
            }
            
            // Kirim email
            $result = $mail->send($contactlist, $configPrestay_templ, 'prestay,'.env('UNIT'), 'prestay', null, $contactlist->registration_code);
            
            // Update status pengiriman
            $contact_prestay = \App\Models\Contactprestay::find($contactlist->id);
            $contact_prestay->update([
                'sendtoguest_at' => \Carbon\Carbon::now(),
            ]);
            
            $sent++;
            $results[] = [
                'contact' => $contactlist->fname . ' ' . $contactlist->lname,
                'email' => $contactlist->email,
                'status' => 'sent',
                'result' => $result
            ];
        } catch (\Exception $e) {
            $failed++;
            $results[] = [
                'contact' => $contactlist->fname . ' ' . $contactlist->lname,
                'email' => $contactlist->email,
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return response()->json([
        'status' => 'success',
        'message' => "Processed {$sent} emails successfully, {$failed} failed",
        'total_contacts' => count($contact_lists),
        'results' => $results
    ]);
});