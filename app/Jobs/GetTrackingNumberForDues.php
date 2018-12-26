<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class GetTrackingNumberForDues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $excel = $this->getFileExcel();
    }

    private function getFileExcel()
    {

        $files = DB::table('excels')->select('id','name')->where('status',0)->get();
        if (!empty($files))
        {
            $string = '';
            foreach ($files as $file) {
                $path = public_path().'/files/'.$file->name;
                $string .= $path." \n ";
            }
        }


        \Log::info($string);
//        $fileData = Excel::load($this->filePath, function($reader) {
//        })->get();
//
//        //Whatever you want to do with the file here, for eg. create a DB entry
//        return 'done';
    }
}
