<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\tbl_epin;
use App\Models\tbl_topup;
use App\Models\tbl_income;
use App\Models\tbl_package;
use App\Models\tbl_epin_transactions;
use App\Models\tbl_shipment_details;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;

class EpinController extends Controller
{
    public function __construct()
    {
        $this->panel = 'admin.e-pin';
        date_default_timezone_set("Asia/Kolkata");
    }
    public function index()
    {
        $package_list = tbl_package::orderBy('created_at', 'DESC')->get();
        $epin_list = tbl_epin::orderBy('created_at', 'DESC')->get();
        return view($this->panel . '.list', compact('epin_list', 'package_list'));
    }

    public function new_joinings()
    {
        $epin_list = tbl_epin::join('users', 'users.epin_id', 'tbl_epin.epin_id')
            ->orderBy('users.created_at', 'DESC')
            ->where('tbl_epin.status', 'used')
            ->paginate(10);

        $packages = tbl_package::orderBy('created_at', 'DESC')->get();

        return view($this->panel . '.new_joinings', compact('epin_list', 'packages'));
    }

    public function new_joinings_search(Request $request)
    {
        $search =  $request->all();
        if (is_array($search) && sizeof($search)) {

            $query = tbl_epin::join('users', 'users.epin_id', 'tbl_epin.epin_id')
                ->orderBy('users.created_at', 'DESC')
                ->where('tbl_epin.status', 'used');


            $created_at_field = DB::raw("(STR_TO_DATE(users.created_at,'%Y-%m-%d'))");


            if ($search['from_date'] != "" && $search['to_date'] != "") {
                if ($search['from_date'] == $search['to_date']) {
                    $query->where($created_at_field, '=', date('Y-m-d', strtotime($search['from_date'])));
                } else {
                    $query->where($created_at_field, '>=', date('Y-m-d', strtotime($search['from_date'])))
                        ->where($created_at_field, '<=', date('Y-m-d', strtotime($search['to_date'])));
                }
            }

            if ($search['kit_type'] != '') {
                $query->where('kit_id', '=', $search['kit_type']);
                $display_stats = true;
                $packageDetail = tbl_package::where('package_id', $search['kit_type'])->first();
            }
            // if ($search['epin_status'] != '') {
            //   $query->where('status', '=', $search['epin_status']);
            // }

            // foreach ($search as $key => $attr) {
            //   if ($key == 'search' && $key != '' && $attr != '') {
            //     $query->where(function ($query) use ($attr, $search) {

            //       $query->orWhere('title', 'like', "%" . $attr . "%")
            //         ->orWhere('price', 'like', "%" . $attr . "%");
            //     });
            //   }
            // }
            $epins =   $query->paginate(10);
            return view($this->panel . '.new_joinings_row', compact('epins'))->render();
        }
    }

    public function search_transfer_epin(Request $request)
    {
        $search =  $request->all();
        $status = $search['id'];
        $user_id = Auth::user()->id;
        if (is_array($search) && sizeof($search)) {

            $tbl_epin_trans = tbl_epin_transactions::select('tbl_epin_transactions.*')->orderBy('created_at', 'DESC')
                ->join('users as u1', 'u1.id', 'tbl_epin_transactions.transfer_from_id')
                ->join('users as u2', 'u2.id', 'tbl_epin_transactions.transfer_to_id');

            if ($status == 'all') {
                $package_list = $tbl_epin_trans->where('tbl_epin_transactions.transfer_from_id', '=', $user_id)
                    ->orwhere('tbl_epin_transactions.transfer_to_id', '=', $user_id)->get();
            } else if ($status == 'sent') {
                $package_list = $tbl_epin_trans->where('tbl_epin_transactions.transfer_from_id', '=', $user_id)->get();
            } else if ($status == 'received') {
                $package_list = $tbl_epin_trans->where('tbl_epin_transactions.transfer_to_id', '=', $user_id)->get();
            }
            foreach ($search as $key => $attr) {
                if ($key == 'search' && $key != '' && $attr != '') {
                    $tbl_epin_trans->where(function ($tbl_epin_trans) use ($attr, $search) {

                        $tbl_epin_trans->orWhere('u1.user_id', 'like', "%" . $attr . "%")
                            ->orWhere('u1.name', 'like', "%" . $attr . "%")
                            ->orWhere('u2.user_id', 'like', "%" . $attr . "%")
                            ->orWhere('u2.name', 'like', "%" . $attr . "%");
                    });
                }
            }
            $package_list =   $tbl_epin_trans->paginate(10);
            return view('users.e-pin.transfer_row', ['package_list' => $package_list]);
        }
    }

    public function detailed_purchase_history($id)
    {

        $tbl_epin = tbl_epin_transactions::where('id', $id)->orderby('created_at', 'DESC')->first();
        $date = $tbl_epin->created_at;
        $ids = Auth::user()->id;
        $deserialisedIds = json_decode($tbl_epin->kit_code);
        $epins = tbl_epin::whereIn('epin_id', $deserialisedIds)
            ->orderby('updated_at', 'DESC')->get();
        return view('users.e-pin.detailed-purc-epin', ['tbl_epin' => $tbl_epin, 'epins' => $epins]);
    }

    public function detailed_tfr_history($id)
    {

        $tbl_epin = tbl_epin_transactions::where('id', $id)->orderby('created_at', 'DESC')->first();
        $date = $tbl_epin->created_at;
        $ids = Auth::user()->id;
        $deserialisedIds = json_decode($tbl_epin->kit_code);
        $epins = tbl_epin::whereIn('epin_id', $deserialisedIds)
            ->orderby('updated_at', 'DESC')->get();
        return view('users.e-pin.detailed-transfer-epin', ['tbl_epin' => $tbl_epin, 'epins' => $epins]);
    }


    public function pending()
    {
        $epins = tbl_epin::select('tbl_epin.*', DB::raw('count(*) as sold_to_total'))->where('status', 'sold_unused')
            ->groupBy('sold_to')->paginate(10);
        return view($this->panel . '.pending', compact('epins'));
    }
    public function pending_search(Request $request)
    {
        $search =  $request->all();
        $packageDetail = false;
        $display_stats = false;

        if (is_array($search) && sizeof($search)) {

            $query = tbl_epin::select('tbl_epin.*', DB::raw('count(*) as sold_to_total'))
                ->where('tbl_epin.status', 'sold_unused')
                ->groupBy('tbl_epin.sold_to')
                ->join('users', 'users.id', 'tbl_epin.sold_to');


            $created_at_field = DB::raw("(STR_TO_DATE(tbl_epin.created_at,'%Y-%m-%d'))");


            // if ($search['from_date'] != "" && $search['to_date'] != "") {
            //   if ($search['from_date'] == $search['to_date']) {
            //     $query->where($created_at_field, '=', date('Y-m-d', strtotime($search['from_date'])));
            //   } else {
            //     $query->where($created_at_field, '>=', date('Y-m-d', strtotime($search['from_date'])))
            //       ->where($created_at_field, '<=', date('Y-m-d', strtotime($search['to_date'])));
            //   }
            // }




            if ($search['user_id'] != '') {
                $query->where('users.user_id', '=', $search['user_id']);
            }

            foreach ($search as $key => $attr) {
                if ($key == 'search' && $key != '' && $attr != '') {
                    $query->where(function ($query) use ($attr, $search) {

                        $query->orWhere('tbl_epin.title', 'like', "%" . $attr . "%");
                    });
                }
            }
            $epins =   $query->paginate(10);
            return view($this->panel . '.pending_row', compact('epins'))->render();
        }
    }
    public function transactions()
    {
        $transactions = tbl_epin_transactions::orderBy('created_at', 'ASC')->paginate(10);
        $packages = tbl_package::orderBy('created_at', 'DESC')->get();
        return view($this->panel . '.transactions', compact('transactions', 'packages'));
    }
    public function transaction_search(Request $request)
    {
        $search =  $request->all();
        $packageDetail = false;
        $display_stats = false;

        if (is_array($search) && sizeof($search)) {

            $query = tbl_epin_transactions::orderBy('created_at', 'DESC');


            $created_at_field = DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))");


            if ($search['from_date'] != "" && $search['to_date'] != "") {
                if ($search['from_date'] == $search['to_date']) {
                    $query->where($created_at_field, '=', date('Y-m-d', strtotime($search['from_date'])));
                } else {
                    $query->where($created_at_field, '>=', date('Y-m-d', strtotime($search['from_date'])))
                        ->where($created_at_field, '<=', date('Y-m-d', strtotime($search['to_date'])));
                }
            }

            if ($search['kit_type'] != '') {
                $query->where('kit_id', '=', $search['kit_type']);
                $display_stats = true;
                $packageDetail = tbl_package::where('package_id', $search['kit_type'])->first();
            }


            if ($search['transfer_from_id'] != '') {
                $query->where('transfer_from_id', '=', $search['transfer_from_id']);
            }

            if ($search['transfer_to_id'] != '') {
                $query->where('transfer_to_id', '=', $search['transfer_to_id']);
            }

            foreach ($search as $key => $attr) {
                if ($key == 'search' && $key != '' && $attr != '') {
                    $query->where(function ($query) use ($attr, $search) {

                        $query->orWhere('title', 'like', "%" . $attr . "%");
                    });
                }
            }
            $transactions =   $query->paginate(10);
            return view($this->panel . '.transactions_row', compact('transactions'))->render();
        }
    }
    public function sale()
    {
        $pacakges = tbl_package::get();
        return view($this->panel . '.sale', compact('pacakges'));
    }


    public function status()
    {
        return view($this->panel . '.status');
    }

    public function status_search(Request $request)
    {
        $epin_code = $request->epin_code;
        $epins = tbl_epin::where("epin_code", $epin_code)

            ->join("tbl_package as pkg", "tbl_epin.kit_id", "=", "pkg.package_id")
            ->select("tbl_epin.*", "pkg.title")->first();

        return view($this->panel . '.statusrow', ['epins' => $epins])->render();
    }

    public function sale_store(Request $request)
    {
        $requestData =  $request->all();

        // $pacakges = tbl_package::get();
        $sold_to = $requestData['sold_to'];

        $kit_id = $requestData['kit_id'];
        $no_of_epins = $requestData['no_of_epins'];
        $mode_of_payment = $requestData['mode_of_payment'];
        $payment_date = $requestData['payment_date'];
        $bank_remarks = $requestData['bank_remarks'];

        $bank_name = $requestData['bank_name'];
        $total_amount = $requestData['total_amount'];




        //save Transaction
        $package_detail  = tbl_package::where('package_id', $kit_id)->first();
        $new_Trans = new tbl_epin_transactions();
        $new_Trans->transfer_from_id = 'Admin';
        $new_Trans->transfer_to_id = $sold_to;
        $new_Trans->no_of_epins = $no_of_epins;

        $new_Trans->kit_id = $kit_id;

        $new_Trans->amount = $total_amount;

        $new_Trans->address = $requestData['address'];

        $new_Trans->state = $requestData['state'];

        $new_Trans->city = $requestData['city'];

        $new_Trans->mode_of_payment = $mode_of_payment;

        $new_Trans->bank_name = $bank_name;
        //save Transaction

        $ids = array();

        for ($i = 0; $i < $no_of_epins; $i++) {
            $epin = tbl_epin::where('kit_id', $kit_id)->where('status', 'unused')->first();
            if ($epin) {
                array_push($ids,  $epin->epin_id);
                $epin->sold_to = $sold_to;
                $epin->status = 'sold_unused';
                $epin->mode_of_payment = $mode_of_payment;
                $epin->payment_date = $payment_date;
                $epin->bank_remarks = $bank_remarks;
                $epin->bank_name = $bank_name;
                $epin->save();
            }
            $new_Trans->kit_code = json_encode($ids);
            $new_Trans->save();
        }


        return [
            'status' => 'success',
            'data' => ''
        ];
    }



    public function search(Request $request)
    {
        $search =  $request->all();
        $packageDetail = false;
        $display_stats = false;

        if (is_array($search) && sizeof($search)) {

            $query = tbl_epin::orderBy('created_at', 'DESC');
            $created_at_field = DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))");
            if ($search['from_date'] != "" && $search['to_date'] != "") {
                if ($search['from_date'] == $search['to_date']) {
                    $query->where($created_at_field, '=', date('Y-m-d', strtotime($search['from_date'])));
                } else {
                    $query->where($created_at_field, '>=', date('Y-m-d', strtotime($search['from_date'])))
                        ->where($created_at_field, '<=', date('Y-m-d', strtotime($search['to_date'])));
                }
            }

            if ($search['kit_type'] != '') {
                $query->where('kit_id', '=', $search['kit_type']);
                $display_stats = true;
                $packageDetail = tbl_package::where('package_id', $search['kit_type'])->first();
            }

            foreach ($search as $key => $attr) {
                if ($key == 'search' && $key != '' && $attr != '') {
                    $query->where(function ($query) use ($attr, $search) {

                        $query->orWhere('title', 'like', "%" . $attr . "%")
                            ->orWhere('price', 'like', "%" . $attr . "%");
                    });
                }
            }
            $epins =   $query->get();
            return view($this->panel . '.show', compact('epins', 'display_stats', 'packageDetail'))->render();
        }
    }

    public function load_data_table(Request $request)
    {
        $search =  $request->all();

        $data = [
            "draw" => (int) $search['draw'],
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];
        if (is_array($search) && sizeof($search)) {

            $query = tbl_epin::orderBy('created_at', 'DESC');

            // foreach ($search as $key => $attr) {
            //   if ($key == 'search' && $key != '' && $attr != '') {
            //     $query->where(function ($query) use ($attr, $search) {

            //       $query->orWhere('title', 'like', "%" . $attr . "%");
            //     });
            //   }
            // }
            $keys = ['title'];
            $package_list =   $query->paginate(10);
            // $data['draw'] = $package_list->currentPage();
            $data['recordsTotal'] = $package_list->total();
            $data['recordsFiltered'] = $package_list->count();
            $new_array = [];
            foreach ($package_list as $package) {
                $sub_val = [];
                foreach ($keys as $key_) {
                    array_push($sub_val, $package->{$key_});
                }
                array_push($new_array, $sub_val);
            }
            $data['data'] = $new_array;
        }
        return $data;
    }

    public function manage_form(Request $request)
    {
        $requestData = $request->all();
        $epin = false;
        $edit_mode = false;
        $pacakges = tbl_package::get();
        if (isset($request->id)) {
            $epin = tbl_epin::where('epin_id', $request->id)->first();
            $edit_mode = true;
        }
        return view($this->panel . '.form', compact('epin', 'pacakges', 'edit_mode'));
    }

    public function delete_epin(Request $request)
    {
        $requestData = $request->all();
        $epin_id = $requestData['epin_id'];
        $epin_trans_id = $requestData['epin_trans_id'];
        $tbl_epin = tbl_epin::where('epin_id', $epin_id)->delete();
        $transactions = tbl_epin_transactions::Where('kit_code', 'like', "%" . $epin_id . "%")->get();

        foreach ($transactions as $key => $items) {
            $epin_ids = json_decode($items->kit_code);
            $count = count($items->getEpinNo);
            if ($count == 0) {
                $delete = tbl_epin_transactions::where('id', $items->id)->delete();
            }
        }
        return [
            'status' => 'success',
            'msg' => 'Epin is deleted successfully'
        ];
    }

    public function purchase_pin_view(Request $request)
    {
        $packages = tbl_package::get();
        $bal = $this->get_ewallet_balance();
        return view('users.e-pin.purchase-epin', compact('packages', 'bal'));
    }

    public function purchase_store(Request $request)
    {
        
        $request_data = $request->all();

        //save Transaction
        $package_detail  = tbl_package::where('package_id', $request_data['kit_id'])->first();

        $new_Trans = new tbl_epin_transactions();

        $new_Trans->transfer_from_id = 'Admin';

        $new_Trans->transfer_to_id = Auth::user()->id;

        $new_Trans->no_of_epins = $request_data['no_of_epins'];

        $new_Trans->kit_id = $request_data['kit_id'];

        $new_Trans->amount = $request_data['total_amount'];

        $new_Trans->address = Auth::user()->address;

        $new_Trans->state = Auth::user()->getState->name;

        $new_Trans->city = Auth::user()->getCity->name;

        $new_Trans->mode_of_payment = 'ewallet';

        //save Transaction

        $ids = array();

        for ($i = 0; $i < $request_data['no_of_epins']; $i++) {
            $epin = tbl_epin::where('kit_id', $request_data['kit_id'])->whereNull('sold_to')->where('status', 'unused')->first();
            if ($epin) {
                array_push($ids,  $epin->epin_id);
                $epin->sold_to = Auth::user()->id;
                $epin->status = 'sold_unused';
                $epin->mode_of_payment = 'ewallet';
                $epin->payment_date = date('Y-m-d');
                $epin->save();
            }
            $new_Trans->kit_code = json_encode($ids);
            $new_Trans->save();
        }

        $income_type = 'ewallet';

        $insert_data = new tbl_income();
        $insert_data->user_id = 'VEU1001';
        $insert_data->ref_id = strtoupper(Auth::user()->user_id);
        $insert_data->amt = $request_data['total_amount'];
        $insert_data->income_type = $income_type;
        $insert_data->credited = '1';
        $insert_data->credit_date = date('Y-m-d');
        $insert_data->payout_processed = '1';
        $insert_data->type2 = "credit";
        $insert_data->description = 'E-pin purchased By ' . strtoupper(Auth::user()->user_id) . ' from ewallet';
        if ($insert_data->save()) {
            $insert_data = new tbl_income();
            $insert_data->user_id = strtoupper(Auth::user()->user_id);
            $insert_data->ref_id = 'VEU1001';
            $insert_data->amt = $request_data['total_amount'];
            $insert_data->type2 = "debit";
            $insert_data->income_type = $income_type;
            $insert_data->credited = '1';
            $insert_data->credit_date = date('Y-m-d');
            $insert_data->payout_processed = '1';
            $insert_data->description = 'E-pin purchased from ewallet';
            if ($insert_data->save()) {
                $bal = $this->get_ewallet_balance();
                return ['status' => 'success', 'msg' => 'Amount Transferd Successfully', 'bal' => $bal];
            } else {
                return ['status' => 'fail', 'msg' => 'Something Went Worng Please Contact Developer\'s Team'];
            }
        } else {
            return ['status' => 'fail', 'msg' => 'Something went worng please try after sometime'];
        }
    }

    public function topup_pin_view(Request $request)
    {
        $packages = tbl_package::get();
        return view('users.e-pin.topup-pins', compact('packages'));
    }

    public function topup_pin_store(Request $request)
    {
        $request_data = $request->all();
        $user = User::where('user_id', $request_data['user_id'])->where('status', '1')->first();
        if ($user) {
            $old_epin_id = $user->epin_id;
            $old_package = tbl_epin::where('epin_id', $old_epin_id)->first();
            $epin = tbl_epin::where('sold_to', Auth::user()->id)
                ->where('status', 'sold_unused')
                ->where('kit_id', $request_data['kit_id'])->first();
            if ($epin) {
                $new_epin_id = $epin->epin_id;
                $new_kit_id = $epin->kit_id;
                $new_kit_pv = $epin->kit_pv;
                $new_kit_price = $epin->kit_price;
                $new_package_name = $epin->getPackage->title;
                $old_package_name = $old_package->title;
                $old_kit_id = $old_package->kit_id;
                $old_kit_pv = $old_package->kit_pv;
                $old_kit_price = $old_package->kit_price;
                if ($old_kit_price < $new_kit_price) {
                    $user->epin_id = $new_epin_id;
                    $user->package_id = $new_kit_id;
                    if ($user->save()) {
                        $epin->status = "used";
                        $epin->sponser_id = strtoupper(Auth::user()->user_id);
                        $epin->joiner_id = strtoupper($request_data['user_id']);
                        $epin->save();
                        $topup_insert = new tbl_topup();
                        $topup_insert->old_package_id = $old_kit_id;
                        $topup_insert->old_package_price = $old_kit_price;
                        $topup_insert->old_package_pv = $old_kit_pv;
                        $topup_insert->old_epin_id = $old_epin_id;
                        $topup_insert->old_package_name = $old_package_name;
                        $topup_insert->new_package_name = $new_package_name;
                        $topup_insert->new_package_id = $new_kit_id;
                        $topup_insert->new_package_price = $new_kit_price;
                        $topup_insert->new_package_pv = $new_kit_pv;
                        $topup_insert->new_epin_id = $new_epin_id;
                        $topup_insert->user_id = $request_data['user_id'];
                        $topup_insert->ref_id = Auth::user()->user_id;
                        if ($topup_insert->save()) {
                            return ['status' => 'success', 'msg' => "Topup successfull"];
                        } else {
                            return ['status' => 'fail', 'msg' => 'Something Went Wrong'];
                        }
                    } else {
                        return ['status' => 'fail', 'msg' => 'Something Went Wrong'];
                    }
                } else {
                    return ['status' => 'fail', 'msg' => 'downgrade of package not allowed'];
                }
            } else {
                return ['status' => 'fail', 'msg' => 'E-pin not available'];
            }
        } else {
            return ['status' => 'fail', 'msg' => 'user does not exist or inactive'];
        }
    }

    public function topup_wallet_view(Request $request)
    {
        $packages = tbl_package::get();
        $bal = $this->get_ewallet_balance();
        return view('users.e-pin.topup-wallet', compact('packages', 'bal'));
    }

    public function topup_wallet_store(Request $request)
    {
        $request_data = $request->all();
        $bal = $this->get_ewallet_balance();
        $kit_id = $request_data['kit_id'];
        $user = User::where('user_id', $request_data['user_id'])->where('status', '1')->first();
        if ($user) {
            $old_epin_id = $user->epin_id;
            $old_package = tbl_epin::where('epin_id', $old_epin_id)->first();
            // $epin = tbl_epin::where('sold_to',Auth::user()->id)
            //                     ->where('status','sold_unused')
            //                     ->where('kit_id',$request_data['kit_id'])->first();
            $package  = tbl_package::where('package_id', $kit_id)->first();
            if ($package) {
                if ($old_package->kit_price < $package->price) {
                    if ($bal >= $request_data['kit_price']) {
                        $findKitDetals = tbl_package::where('package_id', $kit_id)->first();
                        if ($findKitDetals) {
                            $temp1 = rand(100000, 999999);
                            $temp2 = rand(1000000, 9999999);
                            $temp3 = rand(100000, 999999);
                            $c = $findKitDetals->short_name;
                            $newCode = 'V' . $temp1 . 'E' . $temp2 . 'U' . $temp3 . $c;
                            $epn_model                    =   new tbl_epin();
                            $epn_model                    =   Auth::user()->id;
                            $epn_model->epin_code         =   $newCode;
                            $epn_model->kit_price         =   $findKitDetals->price;
                            $epn_model->kit_pv            =   $findKitDetals->pv;
                            $epn_model->kit_id            =   $kit_id;
                            if ($epn_model->save()) {
                                $new_epin_id              = $epn_model->epin_id;
                                $new_kit_id               = $epn_model->kit_id;
                                $new_kit_pv               = $epn_model->kit_pv;
                                $new_kit_price            = $epn_model->kit_price;
                                $new_package_name         = $package->title;
                                $old_package_name         = $old_package->getPackage->title;
                                $old_kit_id               = $old_package->kit_id;
                                $old_kit_pv               = $old_package->kit_pv;
                                $old_kit_price            = $old_package->kit_price;
                                if ($old_kit_price < $new_kit_price) {
                                    $user->epin_id        = $new_epin_id;
                                    $user->package_id     = $new_kit_id;
                                    if ($user->save()) {
                                        $epn_model->status              = "used";
                                        $epn_model->sponser_id          = strtoupper(Auth::user()->user_id);
                                        $epn_model->joiner_id           = strtoupper($request_data['user_id']);
                                        $epn_model->save();
                                        $topup_insert                   = new tbl_topup();
                                        $topup_insert->old_package_id   = $old_kit_id;
                                        $topup_insert->old_package_price = $old_kit_price;
                                        $topup_insert->old_package_pv   = $old_kit_pv;
                                        $topup_insert->old_epin_id      = $old_epin_id;
                                        $topup_insert->old_package_name = $old_package_name;
                                        $topup_insert->new_package_name = $new_package_name;
                                        $topup_insert->new_package_id   = $new_kit_id;
                                        $topup_insert->new_package_price = $new_kit_price;
                                        $topup_insert->new_package_pv   = $new_kit_pv;
                                        $topup_insert->new_epin_id      = $new_epin_id;
                                        $topup_insert->user_id          = $request_data['user_id'];
                                        $topup_insert->ref_id           = Auth::user()->user_id;
                                        if ($topup_insert->save()) {
                                            $income_type = 'ewallet';
                                            $insert_data = new tbl_income();
                                            $insert_data->user_id = strtoupper('veu1001');
                                            $insert_data->ref_id = strtoupper(Auth::user()->user_id);
                                            $insert_data->amt = $new_kit_price;
                                            $insert_data->income_type = $income_type;
                                            $insert_data->credited = '1';
                                            $insert_data->credit_date = date('Y-m-d');
                                            $insert_data->payout_processed = '1';
                                            $insert_data->type2 = "credit";
                                            $insert_data->description = "Epin purchased from ewallet by " . Auth::user()->user_id . " for " . $request_data['user_id'] . " Topup package";
                                            if ($insert_data->save()) {
                                                $insert_data = new tbl_income();
                                                $insert_data->user_id = strtoupper(Auth::user()->user_id);
                                                $insert_data->ref_id = strtoupper('veu1001');
                                                $insert_data->amt = $new_kit_price;
                                                $insert_data->type2 = "debit";
                                                $insert_data->income_type = $income_type;
                                                $insert_data->credited = '1';
                                                $insert_data->credit_date = date('Y-m-d');
                                                $insert_data->payout_processed = '1';
                                                $insert_data->description = "Epin purchased from ewallet by " . Auth::user()->user_id . " for " . $request_data['user_id'] . " Topup package";
                                                if ($insert_data->save()) {
                                                    return ['status' => 'success', 'msg' => 'Topup successfull'];
                                                } else {
                                                    return ['status' => 'fail', 'msg' => 'Something Went Worng Please Contact Developer\'s Team'];
                                                }
                                            } else {
                                                return ['status' => 'fail', 'msg' => 'Something went worng please try after sometime'];
                                            }
                                        } else {
                                            return ['status' => 'fail', 'msg' => 'Something Went Wrong'];
                                        }
                                    } else {
                                        return ['status' => 'fail', 'msg' => 'Something Went Wrong'];
                                    }
                                } else {
                                    return ['status' => 'fail', 'msg' => 'downgrade of package not allowed'];
                                }
                            }
                        }
                    }
                }
            }
        } else {
            return ['status' => 'fail', 'msg' => 'user does not exist or inactive'];
        }
    }


    public function topup_history_view(Request $request)
    {
        $packages = tbl_package::get();
        $topups = tbl_topup::where('ref_id', Auth::user()->user_id)->orWhere('user_id', Auth::user()->user_id)->get();
        return view('users.e-pin.topup-history', compact('packages', 'topups'));
    }

    public function topup_history_search(Request $request)
    {
        $kit_id = $request->kit_id;


        if ($kit_id != "") {
            $topup = tbl_topup::Where('new_package_id', $kit_id)->where('ref_id', Auth::user()->user_id)->orWhere('user_id', Auth::user()->user_id);
            // $topup->where(function ($topup) use ($kit_id) {

            //   });
        } else {
            $topup = tbl_topup::where('ref_id', Auth::user()->user_id)->orWhere('user_id', Auth::user()->user_id);
        }
        $topups = $topup->get();
        return view('users.e-pin.topup-history-row', compact('topups'));
    }

    public function show(Request $request)
    {
        $requestData = $request->all();
        $display_stats = false;
        $epin_trans_id = "";
        $epin_type = $requestData['load_epins_type'];
        if ($epin_type == "delete") {
            $epin_trans_id = $requestData['load_epins_trans_id'];
            $del = true;
            $tbl_trans = tbl_epin_transactions::where('id', $epin_trans_id)->first();
            // $epin_ids = json_decode($tbl_trans->kit_code);
            $epins = $tbl_trans->getEpinNo;
        } else {
            $packageDetail = tbl_package::where('package_id', $requestData['load_epins_kit_id'])->first();
            $del = false;
            if ($packageDetail) {
                if ($epin_type == 'unused') {
                    $epins = $packageDetail->getUnusedEpins;
                } else if ($epin_type == 'used') {
                    $epins = $packageDetail->getUsedEpins;
                } else if ($epin_type == 'sold_unused') {
                    $epins = $packageDetail->getSoldEpins;
                }
            }
        }
        return view($this->panel . '.show', compact('epins', 'epin_type', 'display_stats', 'del', 'epin_trans_id'));
    }

    public function user_show_show(Request $request)
    {
        $requestData = $request->all();
        $display_stats = false;
        $packageDetail = tbl_package::where('package_id', $requestData['load_epins_kit_id'])->first();
        $epin_type = $requestData['load_epins_type'];
        if ($packageDetail) {
            if ($epin_type == 'unused') {
                $epins = $packageDetail->getUnusedEpins;
            } else if ($epin_type == 'used') {
                $epins = $packageDetail->getUsedEpins;
            } else if ($epin_type == 'sold_unused') {
                $epins = $packageDetail->getSoldEpins;
            }
            return view($this->panel . '.show', compact('epins', 'epin_type', 'display_stats'));
        }
    }

    public function store(Request $request)
    {

        $requestData = $request->all();

        $no_of_pins                         = $requestData['no_of_pins'];
        $kit_id                             = $requestData['kit_id'];
        $findKitDetals = tbl_package::where('package_id', $kit_id)->first();

        for ($i = 0; $i < $no_of_pins; $i++) {
            if ($findKitDetals) {
                $temp1 = rand(100000, 999999);
                $temp2 = rand(1000000, 9999999);
                $temp3 = rand(100000, 999999);
                $c = $findKitDetals->short_name;
                $newCode = 'V' . $temp1 . 'E' . $temp2 . 'U' . $temp3 . $c;
                $epn_model     =   new tbl_epin();
                $epn_model->epin_code         =   $newCode;
                $epn_model->kit_price         =   $findKitDetals->price;
                $epn_model->kit_pv            =   $findKitDetals->pv;
                $epn_model->kit_id            =   $kit_id;
                $epn_model->save();
            }
        }





        if ($epn_model) {
            return [
                'status' => 'success',
                'data' => ''
            ];
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Enquiry  $Enquiry
     * @return \Illuminate\Http\Response
     */
    public function activeinactive($id)
    {
        $user = tbl_epin::where('id', $id)->first();
        if ($user->status == 1) {
            $user->status = 0;
        } else {
            $user->status = 1;
        }
        if ($user->save())
            echo "success";
        else
            echo "fail";
    }

    public function destroy($id, Request $request)
    {
        // $check = tbl_bandscore_category::where('category_id', $id)->first();
        // if($check){
        //     return ['status' => 'fail'];
        // }else{
        $delete = tbl_epin::find($id);
        $delete->delete();
        if ($delete->trashed()) {
            return ['status' => 'success'];
        }
        // }

    }
    public function get_ewallet_balance()
    {
        $user_id = Auth::user()->user_id;
        $user = tbl_income::select(DB::raw('sum(CASE WHEN wallet_type  = "ewallet" AND type2 = "credit"  THEN amt ELSE CASE WHEN wallet_type = "ewallet" AND type2 = "debit" THEN -amt ELSE 0 END END) AS ewallet_bal'))
            ->where('user_id', $user_id)->first();
        if ($user) {
            $bal = $user->ewallet_bal;
        } else {
            $bal = 0;
        }
        return $bal;
    }

    public function deliveryDetail()
    {   
        $user=Auth::user()->user_id;
        $data=User::where('user_id',$user)->first();
        $data->getPackage;
        return view('users.e-pin.delivery_detail',['data'=>$data]);
    }

    public function shipmentDetails()
    {
        $user=Auth::user()->user_id;
        $member=tbl_shipment_details::where('user_id',$user)->first();
        if($member)
        {
           return ['response'=>'exist','data'=>$member]; 
        }
        else
        {
            return ['response'=>'fail']; 
        }
    }
    public function shipmentDetailsUpdate(Request $request)
    {   
        if(isset($request->userid))
        {
            $member=tbl_shipment_details::where('user_id',$request->userid)->first();
        }
        else
        {
            $member=new tbl_shipment_details();
            $member->user_id=Auth::user()->user_id;
        }
        $member->name=$request->name;
        $member->email=$request->email;
        $member->mobile_number=$request->mobile;
        $member->address=$request->address;
        $member->pincode=$request->pincode;
        if($member->save())
        {
            $this->purchase_store($request);
        }
    }
}
