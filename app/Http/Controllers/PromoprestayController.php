<?php

namespace App\Http\Controllers;

use App\Models\ConfigPrestay;
use App\Models\MailEditor;
use App\Models\Promoprestay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PromoprestayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $configPrestay=ConfigPrestay::find(1);
        if($configPrestay->active == 'y') {
            $configPrestay_templ = MailEditor::find($configPrestay->template_id);
        }

        $promotempl = Promoprestay::all();
        foreach ($promotempl as $key=>$promo)
        {
            $promotempl[$key]['duration_start'] = substr($promo->event_duration,'0','10');
            $promotempl[$key]['duration_end'] = substr($promo->event_duration,'-10');

            $replace = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div><p style="text-align: center;"><a href="'.$promo->event_url.'" target="_blank" rel="noopener">
                        <img title="'.$promo->name.'" src="'.$promo->event_picture.'" alt="" width="500" height="160" />
                        </a></p>';
            $search = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div>';
            $promotempl[$key]['content'] = str_replace($search,$replace,$configPrestay_templ->content);

        }

        return view('prestay.prestaypromo.index',['promotempl'=>$promotempl]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $action='create';
        $promoprestay=new Promoprestay();
        return view('prestay.prestaypromo.manage',['action'=>$action,'promoprestay'=>$promoprestay]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $this->validate($request,[
            'name' => 'required|unique:promo_prestays',
            'eventduration' => 'required',
            'eventurl' => 'required',
            'eventpicture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:width=500,height=160'
        ],
        [
            'name.required' => 'Event Name tidak boleh kosong !',
            'name.unique' => 'Event Name sudah terpakai !',
            'eventduration.required' => 'Event Duration tidak boleh kosong !',
            'eventpicture.required' => 'Event Picture tidak boleh kosong !',
            'eventurl.required' => 'Event Url tidak boleh kosong !',
        ]);

        $name_space=$request->name;
        //Upload gambar ke hosting
        $file = $request->file('eventpicture');
        //get filename with extension
        $filenamewithextension = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $name=str_replace(' ','_',$name_space);
        //filename to store
        $filenametostore = $name.'.'.$extension;
        $base='public_html/'.env('FTP_TEMPLATE_PATH').'';
        $image_name = $base.'/other/'.$filenametostore;

        if (Storage::disk('ftp')->exists($image_name)){
            Storage::disk('ftp')->delete($image_name);
            //Upload File to external server
            Storage::disk('ftp')->put($image_name, fopen($request->file('eventpicture'), 'r+'));

        }else{
            //Upload File to external server
            Storage::disk('ftp')->put($image_name, fopen($request->file('eventpicture'), 'r+'));
        }



        $promoprestay = new Promoprestay();
        $promoprestay->name = $request->name;
        $promoprestay->event_duration = $request->eventduration;
        $promoprestay->event_picture = 'https://'.env('FTP_TEMPLATE_PATH').'/other/'.$filenametostore;
        $promoprestay->event_url = $request->eventurl;
        $promoprestay->event_text = $request->eventtext;
        $promoprestay->save();



        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Data berhasil tersimpan !"
        ]);
        return redirect()->route('promo-configuration.index');
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
        $action='edit';
        $promoprestay=Promoprestay::find($id);

        return view('prestay.prestaypromo.manage',['action'=>$action,'promoprestay'=>$promoprestay]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required|unique:promo_prestays,name,'. $id,
            'eventduration' => 'required',
            'eventpicture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:width=500,height=160',
            'eventurl' => 'required',
        ],
        [
            'name.required' => 'Event Name tidak boleh kosong !',
            'name.unique' => 'Event Name sudah terpakai !',
            'eventduration.required' => 'Event Duration tidak boleh kosong !',
            'eventurl.required' => 'Event Url tidak boleh kosong !',
        ]);

        $promoprestay = Promoprestay::find($id);
        if ($request->eventpicture != null)
        {
            $name_space=$request->name;
            //Upload gambar ke hosting
            $file = $request->file('eventpicture');
            //get filename with extension
            $extension = $file->getClientOriginalExtension();

            $name=str_replace(' ','_',$name_space);
            //filename to store
            $filenametostore = $name.'.'.$extension;
            $base='public_html/'.env('FTP_TEMPLATE_PATH').'';
            $image_name = $base.'/other/'.$filenametostore;

            if (Storage::disk('ftp')->exists($image_name)){
                Storage::disk('ftp')->delete($image_name);
                //Upload File to external server
                Storage::disk('ftp')->put($image_name, fopen($request->file('eventpicture'), 'r+'));

            }else{
                //Upload File to external server
                Storage::disk('ftp')->put($image_name, fopen($request->file('eventpicture'), 'r+'));
            }
            $eventpicture = 'https://'.env('FTP_TEMPLATE_PATH').'/other/'.$filenametostore;
        }
        else
        {
            $eventpicture = $promoprestay->event_picture;
        }


        $promoprestay->update([
            'name' => $request->name,
            'event_duration' => $request->eventduration,
            'event_picture' => $eventpicture,
            'event_url' => $request->eventurl,
            'event_text' => $request->eventtext,
        ]);
        return redirect()->route('promo-configuration.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $promo=Promoprestay::find($request->id);
        $contactpromo = $promo->promoprestaycontact;
        if($contactpromo->isEmpty()){
            $promo->delete();
            return response(['status'=>'success']);
        }else{
            return response(['status'=>'error']);
        }

    }
}
