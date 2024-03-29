<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use ILluminate\Support\Facades\File;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::latest();
        if (!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }     //for making the search of category easy
        $categories = $categories->paginate(10);
        return view('admin.category.list', compact('categories'));
    }

    public function create(){
        return view('admin.category.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',   
            //in categories table slug must be unique, but if not then passes an error
        ]);

        if($validator->passes()){
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            //Save images here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);   //info about temporary image in var tempimage

                //making unique name by again creating extention of image
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                //name of the image will be acc to this category id with jpg extension or other

                //copy image
                $sPath = public_path().'/temp/'.$tempImage->name;
                //source path to know where our temp file is stored
                $dPath = public_path().'/uploads/category/'.$newImageName;
                //destination path where actual image have to be saved
                File::copy($sPath, $dPath);

                //Generate image thumbnail
                $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                $img->resize(450, 600);
                $img->save($dPath); 

                $category->image = $newImageName;
                $category->save();
            }

            $request->session()->flash('success','Category added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(){
        
    }

    public function update(){
        
    }
    
    public function destroy(){
        
    }
}
