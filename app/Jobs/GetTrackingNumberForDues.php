<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

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

    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    private function getFileExcel()
    {
        $files = DB::table('excels')->select('id', 'name', 'path','woo_info_id')
            ->where('status', 0)->get();
        if (!empty($files)) {
            $success = [];
            $fail = [];
            $fail_format = []; $format = true;
            $data = [];
            foreach ($files as $file) {
                $path = trim($file->path);
                $woo_info_id = $file->woo_info_id;
                if (file_exists(trim($path))) {
//                    echo 'ton tai file. ' . $path . " \n<br>";
                    $trackings = (new FastExcel)->import($path);
                    if (!empty($trackings)) {
                        foreach ($trackings as $value) {
                            if (array_key_exists('order_id', $value) && array_key_exists('tracking', $value)) {
                                if ($value['order_id'] == '') {
                                    continue;
                                }

                                $data[$value['order_id']] = array(
                                    'order_id' => $value['order_id'],
                                    'tracking' => $value['tracking'],
                                    'woo_info_id' => $woo_info_id,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                );
                            } else {
                                $format = false;
                                $fail_format[] = $file->id;
                                break;
                            }

                        }
                        $success[] = $file->id;
                    }
                } else {
                    $fail[] = $file->id;
                }
            }

            if (!empty($data)) {
                try {
                    $result = DB::table('trackings')->insert($data);
                } catch (\Exception $exception) {
                    $result = false;
                }
                if (!$result) {
                    $fail = array_merge($fail, $success);
                    $success = [];
                }
            }
            //neu xay ra loi trong qua trinh doc file excel
            if (!empty($fail)) {
                DB::table('excels')->whereIn('id', $fail)
                    ->update(['status' => 2]);
            }
            //neu xay ra loi sai dinh dang file title
            if (!empty($fail_format)) {
                foreach ($success as $k => $v)
                {
                    if (in_array($v,$fail_format))
                    {
                        unset($success[$k]);
                    }
                }
                DB::table('excels')->whereIn('id', $fail_format)
                    ->update(['status' => 3]);
            }

            //cap nhat lai status file excel
            if (!empty($success)) {
                DB::table('excels')->whereIn('id', $success)
                    ->update(['status' => 1]);
            }
        }
//        \Log::info($string);
    }
}
