<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Runner\Exception;


class MailEditor extends Model
{
    //
    protected $table='mail_editors';
    protected $fillable=['name','subject','type','content'];
    public function parse($data)
    {

        $parsed = preg_replace_callback('/{(.*?)}/', function ($matches) use ($data) {
            list($shortCode, $index) = $matches;

            if( isset($data[$index]) ) {
                if (in_array($data[$index],['Mr','Mr.','mr','Mr','MR','MR.','MSTR.'])){
                    return 'Mr.';
                }elseif(in_array($data[$index],['Mrs.','mrs','Mrs','MRS','MRS.','Miss','Ms','MS','ms','ms.','MS.'])){
                    return 'Ms./Mrs.';
                }else {
                    return $data[$index];
                }
            } else {
                throw new Exception("Shortcode {$shortCode} not found in template id {$this->id}", 1);
            }

        }, $this->content);

        return $parsed;
    }
    public function poststay(){
        return $this->hasOne('\App\Models\PostStay','template_id');
    }
    public function configprestay(){
        return $this->hasOne('\App\Models\ConfigPrestay','template_id');
    }
    public function miss(){
        return $this->hasOne('\App\Models\MissYou','template_id');
    }
    public function campaign(){
        return $this->belongsToMany('\App\Models\Campaign','campaign_template','template_id','campaign_id');
    }
    public function birthday(){
        return $this->hasOne('\App\Models\Birthday','template_id');
    }
}
