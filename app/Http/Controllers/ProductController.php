<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
         public function __construct()
    {
        $this->middleware('auth');
    }
        private function validate_input(Request $request) {
          $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'files'=>'required_without:old_files',
            'files.*'=>'mimes:jpeg,jpg,png',
            'old_files'=>'required_without:files'
        ]);
 
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            echo json_encode(["status"=>false,"error"=>$error]);
            exit;
        }
 
        $data= $validator->valid();
        $data['user_id']= Auth::user()->id;
         return $data;
    }
           public function getDataTable(Request $request) {
       
     ## Read value
     $draw = $request->get('draw');
     $start = $request->get("start");
     $rowperpage = $request->get("length"); // Rows display per page

     $columnIndex_arr = $request->get('order');
     $columnName_arr = $request->get('columns');
     $order_arr = $request->get('order');
     $search_arr = $request->get('search');

     $columnIndex = $columnIndex_arr[0]['column']; // Column index
     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
     $searchValue = $search_arr['value']; // Search value

     // Total records
     $totalRecords = Product::select('count(*) as allcount')->count();
     $totalRecordswithFilter = Product::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

     // Fetch records
     $records = Product::orderBy($columnName,$columnSortOrder)
       ->where('name', 'like', '%' .$searchValue . '%')
       ->select('products.*')
       ->skip($start)
       ->take($rowperpage)
       ->get();

     $data_arr = array();
     
     foreach($records as $record){
$action='<button type="button" class="btn btn-success btn-sm" onclick="editData('.$record->id.')">Edit</button>
                    <button type="button" onclick="deleteData('.$record->id.')" class="btn btn-danger btn-sm" >Delete</button>';
        $data_arr[] = array(
          "id" => $record->id,
          "name" => $record->name,
          "Actions" => $action
        );
     }

     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
     );

     echo json_encode($response);
     exit;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product');
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
       $data= $this->validate_input($request);
         if(isset($data['files']))
         {
           unset($data['files']);
         }
         $product=Product::create($data);
         foreach ($request->file('files') as $file)
         {
            $productImage= $product->ProductImages();
            
            $fileName = time().rand(0, 1000).'.'.$file->extension();  
            $file->move(public_path('uploads/products'), $fileName);
            $productImage->create(["name"=>$fileName]);
         }
         echo json_encode(["status"=>1,"message"=>"Inserted Successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
      echo json_encode(["master"=>$product,"details"=>ProductImage::where('product_id',$product->id)->get()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data= $this->validate_input($request);
         if(isset($data['files']))
         {
           unset($data['files']);
         }
         $old_files=[];
         if(isset($data['old_files']))
         {
           $old_files=$data['old_files'];
           unset($data['old_files']);
         }
         $product->update($data);
         $product->ProductImages()->delete();
         if($request->hasFile('files'))
         {
         foreach ($request->file('files') as $file)
         {
            $productImage= $product->ProductImages();
            
            $fileName = time().rand(0, 1000).'.'.$file->extension();  
            $file->move(public_path('uploads/products'), $fileName);
            $productImage->create(["name"=>$fileName]);
         }
         }
         foreach ($old_files as $old_file)
         {
            $productImage= $product->ProductImages();
             $productImage->create(["name"=>$old_file]);
         }
          echo json_encode(["status"=>1,"message"=>"Updated Successfully"]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
          $product->ProductImages()->delete();
      echo $product->delete();
    }
}
