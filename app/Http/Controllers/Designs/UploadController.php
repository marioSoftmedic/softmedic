<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{

    public function upload(Request $request)
    {
        //validate el request
        $this->validate($request, [
            'image'=>['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]);

        //get the image
        $image=$request->file('image');
        $image_path=$image->getPathname();

        //get the original file name and replace any spaces whith _
        //bussiness cards.png = timestamp()_bussiness_cards.png
        $filename=time()."_".preg_replace('/\s+/','_',strtolower($image->getClientOriginalName()));

        //move the image to the temporary location (tmp)
        $tmp=$image->storeAs('upload/original', $filename, 'tmp');

        //create the database record for the design
        $design = auth()->user()->designs()->create([
            'image'=>$filename,
            'disk'=>config('site.upload_disk')
        ]);

        //dispatch a jot to handel the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);
    }
}
