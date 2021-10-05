<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddCategories;

class AddCategoriesController extends Controller
{
    public function index()
    {
        $data = AddCategories::all();
        return view('admin.Eshop.add_categories',['data'=>$data]);
    }

    public function insert(Request $request)
    {
        if(isset($request->category_id)){
            $member= AddCategories::find($request->category_id);
        }else{
            $member=new AddCategories();
        }
        $member->category_name=$request->category_name;
        $member->display=$request->display;
        $member->status='testing';
        if(isset($request->photo))
        {
          $imageName =time().'.'.$request->photo->extension();
          $request->photo->move(public_path('Category_images'), $imageName);
          $member->category_image=$imageName;
        }
        if($member->save())
        {
            $event = AddCategories::all();
            $result = $this->view_table($event);
            return ['status'=>'success','result'=>$result];
        }
        else{
            return ['status'=>'fail'];
        }
    }

    public function view_table($data)
    {
        $table = '<div id="table_data" class="result_append">
        <div class="table-responsive  c-table-responsive">
            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%" style="border: 1px solid #dee2e6 !important;">
              <thead>
                <tr>
                <th>Category Name</th>
                <th>Photo</th>
                <th>Display</th>
                <th>Status</th>
                <th>Action</th>
                </tr>
              </thead>
              <tbody class="main-banker-master-view">';
              foreach($data as $key => $member)
              {
                $table .= '<tr>
                <td>'.$member->category_name.'</td><td><button type="button" class="btn btn-primary modalBtn" data-toggle="modal" data-target="#documentImgModal" photo_id="'.$member->id.'">
                <img alt="'.$member->category_name.'" src="'.url("public/Category_images/$member->category_image").'" width="80" height="60">
                </button></td><td>'.$member->display.'</td>
                <td>'.$member->status.'</td><td width="150">
                <a target="_blank" category_id='.$member->id.' class="update" style="cursor: pointer;">
                  <i class="fa fa-pencil"></i> Edit
                </a>
                <a href="javascript:;" category_id='.$member->id.' class="delete" style="cursor: pointer;">
                  <i class="fa fa-trash"></i> Delete
                </a></td></tr>';
              }
                      
        $table .= '</tbody></table></div></div>';
        return $table;
    }

    function view_image()
    {
        $photo_id=$_POST['photo_id'];
        $member=AddCategories::find($photo_id);
        $src=url('public/Category_images/'.$member['category_image']);
        $img = '<img src="'.$src.'" class="modalImage" style="max-height:100%;max-width:100%">';
        return ['response'=>'success','data'=>$img];
    }

    public function delete(Request $req)
    {
        $category_id = AddCategories::find($req->category_id)->delete();
        if($category_id)
        {
          $category = AddCategories::all();
          $result = $this->view_table($category);
          return ['status'=>'success','result'=>$result];
        }
        else{
            return ['status' => 'fail'];
        }
    }

    public function update(Request $req)
    {
      $category_id = $req->category_id;
      $category = AddCategories::find($category_id);
      If($category){
        return ['status'=>'success','data'=>$category];
      }else{
          return ['status'=>'fail','msg'=>'News Does Not Exist','data'=>''];

      }
    }
}
