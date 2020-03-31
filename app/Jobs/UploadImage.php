<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImage implements ShouldQueue
{
    protected $desing;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design=$design;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $disk = $this->desing->disk;
        $filename=$this->design
        $original_file=storage_path().'/uploads/original'.$this->design->image;

        try{
            //create the Large Image and save to tmp disk
            Image::make($original_file)
             ->fit(800,600, function($constraint) {
                 $constraint->aspectRadio();
             })
             ->save($large = storage_path('uploads/large'.$this->design->image));

             //Create the thumbnail image
            Image::make($original_file)
            ->fit(250,200, function($constraint){
                $constraint->aspectRadio();
            })
            ->save($large = storage_path('uploads/thumbnail/'.$this->design->image));

            //Store images to permanent disk
            //original image
            if (Storage::disk($disk)->put('uploads/designs/original'.$this->design->image, fopen($original_file, 'r+')))
            {
                File::delete($original_file);
            }


        }catch(\Exception $e)
        {
            \Log::error($e->getMessage());
        }
    }
}
