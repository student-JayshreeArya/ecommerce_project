<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TempImagesController extends Controller
{
    public function create(Request $request){
        $image = $request->image;

        if (!empty($image)){
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp', $newName);
            //this will save temporary image in a temporary file name temp

            return response()->json([
                'status' => true,
                'image' => $tempImage->id,
                'message' => 'Image Uploaded successfully'
            ]);
        }
    }
}
