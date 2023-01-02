<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //
    public function add($id){

        $trx=new Transaction();
        $act='add';
        return view('contacts.stay',['model'=>$trx,'action'=>$act,'contact_id'=>$id]);
    }
    public function store(Request $request){

        $trx=new Transaction();
        $trx->resv_id=$request->resv_id;
        $trx->checkin=Carbon::parse($request->checkin)->format('Y-m-d');
        $trx->checkout=Carbon::parse($request->checkout)->format('Y-m-d');
        $trx->room=$request->room;
        $trx->room_type=$request->room_type;
        $trx->revenue=$request->revenue;
        $trx->status=$request->status;
        $trx->save();
        $contact=Contact::where('contactid','=',$request->contact_id)->first();

        $contact->transaction()->attach($trx->id);
        return redirect('contacts/detail'.'/'.$request->contact_id);
    }
    public  function edit($id){
        $trx=Transaction::find($id);
        $act='edit';
        return view('contacts.stay',['model'=>$trx,'action'=>$act]);
    }
    public  function update(Request $request){

        $trx=Transaction::find($request->id);
        $contact=$trx->contact;
        $trx->resv_id=$request->resv_id;
        $trx->checkin=Carbon::parse($request->checkin)->format('Y-m-d');
        $trx->checkout=Carbon::parse($request->checkout)->format('Y-m-d');
        $trx->room=$request->room;
        $trx->room_type=$request->room_type;
        $trx->revenue=$request->revenue;
        $trx->status=$request->status;
        $trx->save();
        return redirect('contacts/detail/'.$contact[0]->id);
    }
    public function delete($id){
        $trx=Transaction::find($id);
        $contact=$trx->contact[0]->id;
        $trx->contact()->detach($contact);
        $trx->delete();
        return redirect()->back();
    }
}
