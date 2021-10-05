<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddCategories;
use App\Models\SubCategories;


class SubCategoriesController extends Controller
{
    public function index()
    {
        $category = AddCategories::all();
        $sub_category = SubCategories::select('subcategories.id','subcategories.sub_category','subcategories.category_image','subcategories.status','addcategories.category_name')
        ->join('addcategories','subcategories.main_category','=','addcategories.id')->get();
        return view('admin.Eshop.add_sub_category',['category'=>$category,'sub_category' => $sub_category]);
    }

    public function insert(Request $request)
    {
        if(isset($request->sub_category_id)){
            $member= SubCategories::find($request->sub_category_id);
        }else{
            $member=new SubCategories();
        }
        $member->main_category=$request->category_name;
        $member->sub_category=$request->sub_category_name;
        $member->status='testing';
        if(isset($request->photo))
        {
          $imageName =time().'.'.$request->photo->extension();
          $request->photo->move(public_path('Sub_Category_images'), $imageName);
          $member->category_image=$imageName;
        }
        if($member->save())
        {
            $category = SubCategories::select('subcategories.id','subcategories.sub_category','subcategories.category_image','subcategories.status','addcategories.category_name')
            ->join('addcategories','subcategories.main_category','=','addcategories.id')->get();
            $result = $this->view_table($category);
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
                <th>Sub Category Name</th>
                <th>Photo</th>
                <th>Status</th>
                <th>Action</th>
                </tr>
              </thead>
              <tbody class="main-banker-master-view">';
              foreach($data as $key => $member)
              {
                $table .= '<tr>
                <td>'.$member->category_name.'</td><td>'.$member->sub_category.'</td><td><button type="button" class="btn btn-primary modalBtn" data-toggle="modal" data-target="#documentImgModal" photo_id="'.$member->id.'">
                <img alt="'.$member->sub_category.'" src="'.url("public/Sub_Category_images/$member->category_image").'" width="80" height="60">
                </button></td><td>'.$member->status.'</td><td width="150">
                <a target="_blank" sub_category_id='.$member->id.' class="update" style="cursor: pointer;">
                  <i class="fa fa-pencil"></i> Edit
                </a>
                <a href="javascript:;" sub_category_id='.$member->id.' class="delete" style="cursor: pointer;">
                  <i class="fa fa-trash"></i> Delete
                </a></td></tr>';
              }
                      
        $table .= '</tbody></table></div></div>';
        return $table;
    }

    function view_image()
    {
        $photo_id=$_POST['photo_id'];
        $member=SubCategories::find($photo_id);
        $src=url('public/Sub_Category_images/'.$member['category_image']);
        $img = '<img src="'.$src.'" class="modalImage" style="max-height:100%;max-width:100%">';
        return ['response'=>'success','data'=>$img];
    }

    public function delete(Request $req)
    {
        $category = SubCategories::find($req->sub_category_id)->delete();
        if($category)
        {
            $category = SubCategories::select('subcategories.id','subcategories.sub_category','subcategories.category_image','subcategories.status','addcategories.category_name')
            ->join('addcategories','subcategories.main_category','=','addcategories.id')->get();
          $result = $this->view_table($category);
          return ['status'=>'success','result'=>$result];
        }
        else{
            return ['status' => 'fail'];
        }
    }

    public function update(Request $req)
    {
      $category_id = $req->sub_category_id;
      $category = SubCategories::find($category_id);
      If($category){
        return ['status'=>'success','data'=>$category];
      }else{
          return ['status'=>'fail','msg'=>'category Does Not Exist','data'=>$category_id];

      }
    }

}
