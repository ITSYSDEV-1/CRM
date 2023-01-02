<?php

namespace App\Http\Controllers;

use App\Blast;
use App\Blastlist;
use Illuminate\Http\Request;
use Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class BlastController extends Controller
{
    //
    public function blast(){
        return view('contacts.blast');
    }
    public  function saveemailblast(Request $request){
        $rules=[
          'file'=>'required|mimes:xls,xlsx',
            'template'=>'required',
            'campaign_name'=>'required',
            'schedule'=>'required'
        ];
        $message=[
          'file.required'=>'File is required',
            'file.mimes'=>'Invalid extension',
            'template.required'=>'No template Selected',
            'campaign_name.required'=>'Campaign name is required',
            'schedule.required'=>'Schedule is required'
        ];
        $val=Validator::make($request->all(),$rules,$message);
        if (!$val->fails()){
            $path=$request->file('file');
            if ($path->getClientOriginalExtension()=='xls'){
                $reader=new Xls();
            }else {
                $reader = new Xlsx();
            }
            $spreadsheet=$reader->load($path)->getActiveSheet()->toArray();
            for ($j=1;$j<=count($spreadsheet)-1;$j++){
                $emails[]=$spreadsheet[$j];
            }
            $blast=Blast::create(['campaign_name'=>$request->campaign_name,'template_id'=>$request->template,'schedule'=>\Carbon\Carbon::parse($request->schedule)->format('Y-m-d H:i:s')]);

            foreach ($emails as $list){
                if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$list[0])) {
                    $blastlist=Blastlist::updateOrCreate(['email'=>$list[0]],['fname'=>$list[1],'lname'=>$list[2]]);
                    $blastlist->blast()->attach($blast->id);
                    $data[]=$blastlist;
                }

            }
            return response(['success'=>$data],200);
        }else{
         //return back()->withErrors($val->messages())->withInput();
            return response(['errors'=>$val->messages()]);
        }


    }
    public function destroy(Request $request){
        $blast=Blast::find($request->id);
        $blast->email()->detach();
        $blast->delete();
        return response('success',200);
    }
}
