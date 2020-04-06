<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{

    protected $designs;

    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }
    public function index()
    {
        $designs = $this->designs->all();

        return DesignResource::collection($designs);
    }

    public function update(Request $request, $id)
    {
        $design = Design::findOrFail($id);

        $this->authorize('update', $design);

        $this->validate($request, [
            'title'       => ['required', 'unique:designs,title,'.$id],
            'description' => ['required', 'string', 'min:20','max:140'],
            'tags'        => ['required']
        ]);


        $design->update([
            'title'       => $request->title,
            'description' => $request->description,
            'slug'        => Str::slug($request->title),
            'is_live'     => !$design->upload_successful ? false : $request->is_live
        ]);

        //apply the tags
        $design->retag($request->tags);

        return new DesignResource($design);

    }

    public function destroy($id)
    {
        $design = Design::findOrFail($id);
        $this->authorize('delete', $design);



        //delete the files asociated to the record
        foreach(['thumbnail', 'large', 'original'] as $size)
        {
            //check if the file exits
            if(Storage::disk($design->disk)->exists("uploads/designs/{$size}/".$design->image))
            {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/".$design->image);
            }
        }

        $design->delete();

        return response()->json([
            "message" => "Recurso eliminado"
        ], 200);

    }
}