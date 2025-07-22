<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Traits\UserLogsActivity; // Tambahkan import trait
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    use UserLogsActivity; // Tambahkan penggunaan trait
    
    public function savePreferences(Request $request){

        $prf=Configuration::find(1);
        
        // Simpan data lama untuk logging
        $oldData = [
            'hotel_name' => $prf->hotel_name,
            'app_title' => $prf->app_title,
            'gm_name' => $prf->gm_name,
            'sender_email' => $prf->sender_email,
            'sender_name' => $prf->sender_name,
            'cc_recipient' => $prf->cc_recipient,
            'logo' => $prf->logo
        ];
        
        $prf->hotel_name=$request->hotel_name;
        $prf->app_title=$request->app_title;
        $prf->gm_name=$request->gm_name;
        $prf->sender_email=$request->sender_email;
        $prf->sender_name=$request->sender_name;
        $prf->cc_recipient=$request->cc_recipient;
        if(!empty($request->file('file'))){
            $file=$request->file('file');
            $name=$file->getClientOriginalName();
            $dir='logo';
            $file->move($dir,$name);
            $prf->logo=$dir.'/'.$name;
        }
        $prf->save();
        
        // Tambahkan logging aktivitas pengguna
        $this->logActivity(
            'update_preferences',
            Configuration::class,
            $prf->id,
            $oldData,
            [
                'hotel_name' => $prf->hotel_name,
                'app_title' => $prf->app_title,
                'gm_name' => $prf->gm_name,
                'sender_email' => $prf->sender_email,
                'sender_name' => $prf->sender_name,
                'cc_recipient' => $prf->cc_recipient,
                'logo' => $prf->logo
            ],
            'Updated system preferences'
        );
        
        return response('success',200);
    }
}
