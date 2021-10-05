<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tbl_package;
use App\Models\JoiningCommissionModel;


class JoiningCommissionController extends Controller
{
    public function index()
    {
        $package = tbl_package::all();
        $data = JoiningCommissionModel::all();
        return view('admin.userful.joining_comm',compact('package','data'));
    }

    public function get_kit_price()
    {
        $id = $_GET['id'];
        if($id != "All"){
            $package = tbl_package::where('package_id',$id)->get('price');
            return $package;
        }
        else{
            $package = tbl_package::all()->sum('price');
            return $package;
        }
    }

    public function insert(Request $req)
    {
        $new = new JoiningCommissionModel();
        $new->kit = $req->kit;
        $new->commission_type = $req->commission_type;
        $new->commission_amount = $req->enter_commission;
        if(isset($req->TDScheck))
        {
            $new->TDS = $req->TDS;
        }

        $new->admin_charges = $req->admin_charges;
        if($new->save())
        {
            
            $result = $this->view();
            return ['status' => 'success','result' => $result];
        }
        else{
            return ['status' => 'fail'];
        }
    }

    public function delete(Request $req)
    {
        $id = $req->id;
        $data = JoiningCommissionModel::find($id)->delete();
        if($data)
        {
            $result = $this->view();
            return ['status' => 'success','result' => $result];
        }
        else{
            return ['status' => 'fail'];
        }

    }

    public function view()
    {
        $data = JoiningCommissionModel::all();
        return view('admin.userful.joinig_comm_table',['data' => $data])->render();
        
    }
}
