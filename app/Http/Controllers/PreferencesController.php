<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    //
    public function savePreferences(Request $request){


        $prf=Configuration::find(1);
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
        return response('success',200);
    }
}
