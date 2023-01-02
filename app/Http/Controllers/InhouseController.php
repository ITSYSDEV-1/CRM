<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ProfileFolio;
use App\Models\Transaction;
use Illuminate\Http\Request;

class InhouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profilesfolio = ProfileFolio::where('foliostatus','I')
            ->whereColumn('folio_master','folio')
            ->orderBy('dateci','ASC')
            ->get();
        foreach ($profilesfolio as $key=>$val)
        {
            $contacts[] = Contact::find($val->profileid);
            $contacts[$key]['foliostatus'] = $val->foliostatus;
            $contacts[$key]['source'] = $val->source;
            $contacts[$key]['folio_master'] = $val->folio_master;
            $contacts[$key]['dateci'] = $val->dateci;
            $contacts[$key]['dateco'] = $val->dateco;
            $contacts[$key]['room'] = $val->room;
            $contacts[$key]['roomtype'] = $val->roomtype;
        }

        return view('inhouse.list')->with(compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
