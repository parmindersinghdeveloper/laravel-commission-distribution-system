<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;
use App\Models\Options;
use Illuminate\Http\Request;

class AddressMasterController extends Controller
{
    function addressmaster()
    {   
        $address1=Options::where('option_name','address1')->get();
        $address2=Options::where('option_name','address2')->get();
        $country=Options::where('option_name','country')->get();
        $state=Options::where('option_name','state')->get();
        $pincode=Options::where('option_name','pincode')->get();
        $city=Options::where('option_name','city')->get();
        $mobile1=Options::where('option_name','mobile1')->get();
        $mobile2=Options::where('option_name','mobile2')->get();
        $whatsappNo=Options::where('option_name','whatsappNo')->get();
        $adminEmailId=Options::where('option_name','adminEmailId')->get();
        
        $test=['address1'=>$address1[0],'address2'=>$address2[0],'country'=>$country[0],'state'=>$state[0],'pincode'=>$pincode[0],'city'=>$city[0],'mobile1'=>$mobile1[0],'mobile2'=>$mobile2[0],'whatsappNo'=>$whatsappNo[0],'adminEmailId'=>$adminEmailId[0]];
        $countries=Countries::all();
        $states = States::where('country_id',$country[0]['option_value'])->get();
        $cities = Cities::where('state_id',$state[0]['option_value'])->get();
        return view('admin.address_master',['countries' => $countries,'states'=>$states,'cities'=>$cities,'data'=>$test]);
    }

    public function getState()
    {
        $country_id = $_POST['country_id'];
        $states = States::where('country_id',$country_id)->get();
        $output= '';
        if(count($states) != 0)
        {
            foreach($states as $state)
            {
                $output .= '<option value="'.$state->id.'">'.$state->name.'</option>'; 
            }
            return ['status'=>'success','data'=>$output];
        }
        else{
            return ['status'=>'fail'];
        }
    }

    public function getCity()
    {
        $state_id = $_POST['state_id'];

        $cities = Cities::where('state_id',$state_id)->get();
        $output = '';
        if(count($cities) != 0)
        {
            foreach($cities as $city)
            {
                $output .= '<option value="'.$city->id.'">'.$city->name.'</option>'; 
            }
            return ['response'=>'success','data'=>$output];
        }
        else{
            return ['response'=>'fail'];
        }
    }

    public function addressmasterUpdate(Request $request)
    {
        $flag=true;
        $temp=['address1','address2','country','state','pincode','city','mobile1','mobile2','whatsappNo','adminEmailId'];
        foreach($temp as $item)
        {
            $sample=Options::where('option_name',$item)->get();
            $sampleId=Options::find($sample[0]->id);
            $sampleId->option_value=$request->$item;
            $sampleId->option_type='text';
            $sampleId->page_name='Address Master';
            $sampleId->section_name='Contact us';
            $check=$sampleId->save();
            if(!$check)
            {
                $flag=false;
            }
        }
        if($flag)
        {
            return ['response'=>'success'];
        }
    }

    
}
