<?php

namespace App\Http\Controllers;

use App\Models\ExternalContact;
use App\Models\ExternalContactCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\DB;
use App\Traits\UserLogsActivity;

class ExternalEmailController extends Controller
{
    use UserLogsActivity;
    
    //
    public function delcontact(Request $request){
        $contact = ExternalContact::find($request->id);
        
        // Capture old data before deletion for logging
        $oldData = [
            'id' => $contact->id,
            'email' => $contact->email,
            'fname' => $contact->fname,
            'address' => $contact->address,
            'phone' => $contact->phone,
            'categories' => $contact->category->pluck('category')->toArray()
        ];
        
        $categories = $contact->category;
        foreach($categories as $category){
            $contact->category()->detach($category->id);
        }
        
        $contact->delete();
        
        // Log the deletion activity
        $this->logActivity(
            'delete_external_contact',
            ExternalContact::class,
            $request->id,
            $oldData,
            null,
            'User deleted external contact: ' . $contact->email
        );
        
        return response('success', 200);
    }
    public function contacts(){
        $contacts=ExternalContact::with('category')->get();
        return view('contacts.contacts',['contacts'=>$contacts]);
    }
    public function index(){
        return view('contacts.external');
    }
    public function saveexternalcontact(Request $request){

        if ($request->getcategory==='on') {
            $rules = [
                'file' => 'required|mimes:xls,xlsx',
                'pick_category'=>'required',

            ];
            $message=[
                'file.required'=>'File is required',
                'file.mimes'=>'Invalid extension',
                'pick_category.required'=>'At least one category needed'
            ];
        }else
        {
            $rules = [
                'file' => 'required|mimes:xls,xlsx',
                'new_category.*'=>'required'
            ];
            $message=[
                'file.required'=>'File is required',
                'file.mimes'=>'Invalid extension',
                'new_category.*.required'=>'Category is required'

            ];
        }

        //  dd($message);
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
            
            // Initialize data array to avoid undefined variable error
            $data = [];
            
            if ($request->getcategory!=='on'){
                $categories=$request->new_category;
                foreach ($categories as $category){
                    $cat=ExternalContactCategory::updateOrCreate(['category'=>$category],['category'=>$category]);
                    foreach ($emails as $list){
                        if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$list[0])) {
                            $external=ExternalContact::updateOrCreate(['email'=>$list[0]],['fname'=>$list[1],'address'=>$list[2],'phone'=>$list[3]]);
                            $external->category()->syncWithoutDetaching($cat->id);
                            $data[]=$external;
                        }
                    }
                }
                
                // Log activity for new category
                $this->logActivity(
                    'upload_external_contacts_new_category',
                    ExternalContact::class,
                    null,
                    null,
                    [
                        'file_name' => $path->getClientOriginalName(),
                        'categories' => $categories,
                        'external_contacts_count' => count($data)
                    ],
                    'User uploaded external contacts with new categories: ' . implode(', ', $categories)
                );
            } else{
                $categories=$request->pick_category;
                foreach ($emails as $list){
                    if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$list[0])) {
                        $external=ExternalContact::updateOrCreate(['email'=>$list[0]],['fname'=>$list[1],'address'=>$list[2],'phone'=>$list[3]]);
                        foreach ($categories as $category){
                            $external->category()->sync($category,false);
                        }
                        $data[]=$external;
                    }
                }
                
                // Get category names for logging
                $categoryNames = ExternalContactCategory::whereIn('id', $categories)->pluck('category')->toArray();
                
                // Log activity for existing category
                $this->logActivity(
                    'upload_external_contacts_existing_category',
                    ExternalContact::class,
                    null,
                    null,
                    [
                        'file_name' => $path->getClientOriginalName(),
                        'category_ids' => $categories,
                        'category_names' => $categoryNames,
                        'external_contacts_count' => count($data)
                    ],
                    'User uploaded external contacts to existing categories: ' . implode(', ', $categoryNames)
                );
            }

            return response(['success'=>$data],200);
        }else{
            // Log validation failure
            $this->logActivity(
                'upload_external_contacts_failed',
                ExternalContact::class,
                null,
                null,
                [
                    'validation_errors' => $val->errors()->toArray()
                ],
                'External contact upload failed due to validation errors'
            );
            
            return response(['errors'=>$val->messages()]);
        }
    }
    public function categorylist(Request $request){
        $category=ExternalContactCategory::withCount('email')->get();
        return response($category);
    }

    public function delcategory(Request $request){
        $contact = ExternalContactCategory::find($request->id);
        
        // Capture old data before deletion for logging
        $oldData = [
            'id' => $contact->id,
            'category' => $contact->category,
            'email_count' => $contact->email()->count()
        ];
        
        $contact->email()->detach();
        $contact->delete();
        
        // Log the deletion activity
        $this->logActivity(
            'delete_external_contact_category',
            ExternalContactCategory::class,
            $request->id,
            $oldData,
            null,
            'User deleted external contact category: ' . $oldData['category']
        );
        
        return response('success', 200);
    }

    public function template(){
        $file = public_path().'/'.'contact-template.xls';
        
        // Log the template download activity
        $this->logActivity(
            'download_template',
            ExternalContact::class,
            null,
            null,
            [
                'template_name' => 'contact-template.xls',
                'template_path' => $file
            ],
            'User downloaded external contact template'
        );
        
        return response()->download($file);
    }
    public function contactsbycategory($category){
        return view('contacts.contacts',['category'=>$category]);
    }
    public function listexternalcontact(Request $request)
    {
        $categories=ExternalContactCategory::withCount('email')->where('id','=',$request->cat)->first();
        $total=$categories->email_count;

        $columns = array(
            0 =>'id',
            1 =>'fname',
            2 =>'email',
            3 =>'category',
        );
        $totalData = $total;
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search=$request->input('search.value');


        $contacts=ExternalContactCategory::with(['email'=>function($q) use ($start,$limit,$order,$dir){
            $q->offset($start)->limit($limit)->orderBy($order,$dir);
        }])->where('id','=',$request->cat)->first();
        if(!empty($search)){
            $contacts=ExternalContactCategory::with(['email'=>function($q) use ($search,$start,$limit,$order,$dir) {
                return $q->where('fname','LIKE',"%$search%")->orWhere('email','LIKE',"%$search%")->offset($start)->limit($limit)->orderBy($order,$dir);
            }])->offset($start)->limit($limit)->orderBy($order,$dir)->first();
            $totalData=count($contacts->email);
            $totalFiltered=count($contacts->email);

        }
        $data=[];

        if (!empty($contacts)){
            foreach ($contacts->email as $key=>$list){
                $nestedData['id']=$list->id;
                $nestedData['fname']=$list->fname;
                $email=$list->email;
                $nestedData['email']="***".substr($list->email,3,strlen($email)) ;
                $nestedData['category']=$contacts->category;
                $data[]=$nestedData;
            }

        }
        $json_data=array(
            "draw"=>intval($request->input('draw')),
            "recordsTotal"=>intval($totalData),
            "recordsFiltered"=>intval($totalFiltered),
            "data"=>$data
        );
        return response($json_data);


    }
}
