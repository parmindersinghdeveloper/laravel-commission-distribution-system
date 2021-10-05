<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;


class AddressLabelController extends Controller
{
    public function index()
    {
        return view('admin.Eshop.print_address_label');
    }

    public function search(Request $request)
    {
        $search =  $request->all();  
        if (is_array($search) && sizeof($search)) {

            $query = User::orderBy('created_at','ASC');


            $created_at_field = DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))");
      
      
            if ($search['from_date'] != "" && $search['to_date'] != "") {
              if ($search['from_date'] == $search['to_date']) {
                $query->where($created_at_field, '=', date('Y-m-d', strtotime($search['from_date'])))
                  ->where('user_type', 'customer');
              } else {
                $query->where($created_at_field, '>=', date('Y-m-d', strtotime($search['from_date'])))
                  ->where($created_at_field, '<=', date('Y-m-d', strtotime($search['to_date'])))
                  ->where('user_type', 'customer');
              }
            }
              if ($search['user_id'] != "") {
                  $query->where('user_id', $search['user_id']);
              }

            $user_list =   $query->paginate(10);
        return view('admin.Eshop.address_label',['data' => $user_list]);
    }
    }
}
