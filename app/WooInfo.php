<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WooInfo extends Model
{
    public $timestamps = true;
    //
    protected $table='woo_infos';

    protected function getConnectStore($url, $consumer_key, $consumer_secret)
    {
        $woocommerce = new Client(
            $url,
            $consumer_key,
            $consumer_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
            ]
        );
        return $woocommerce;
    }

    /*Lay thong tin session hien tai ve store*/
    protected function getSessionStore()
    {
        $session = false;
        if (\Session::has('woo_store'))
        {
            $session = \Session::get('woo_store');
        }
        return $session;
    }

    //Hiển thị thông tin cơ bản của 1 store và lưu vào session
    public static function setSessionStore($store_id)
    {
        $return = false;
        $info = WooInfo::firstOrCreate(['id' => $store_id]);
        if ($info)
        {
            $array = [];
            $array['woo_id'] = $store_id;
            $array['woo_name'] = $info->woo_name;
            $array['woo_link'] = $info->woo_link;
            $array['consumer_key'] = $info->consumer_key;
            $array['consumer_secret'] = $info->consumer_secret;
            $return = $array;
        }
        return $return;
    }

    // Triển khai code logic
    public static function saveStore()
    {
        $request = request();
        $woo_info = new WooInfo();

        $woo_info->woo_name = $request->storename;
        $woo_info->woo_link = $request->linkstore;
        $woo_info->consumer_key = $request->consumer_key;
        $woo_info->consumer_secret = $request->consumer_secret;
        $woo_info->status = 0;
        if ($woo_info->save()) {
            \Session::flash('success', 'Successfully add new store!');
        } else {
            \Session::flash('error', 'Error! Please try again an other time!');
        }
    }


    public function test(){
        $woocommerce = new Client(
            'http://localhost/wordpress/',
            'ck_f679ccd69721c8194042e9fc85026121d72c4a71',
            'cs_fbb978e46e5b08fbf5460656e16933498b767b43',
            [
                'wp_api' => true,
                'version' => 'wc/v3',
            ]
        );
//        echo "<pre>";
        $data = [
            'status' => 'processing',
            'meta_data' => [
                [
                    "key" => "ywot_tracking_code",
                    "value" => "LT123456789CN"
                ],
                [
                    "key" => "ywot_pick_up_date",
                    "value" => "2018-12-19"
                ],
                [
                    "key" => "ywot_carrier_name",
                    "value" => "https://t.17track.net/en#nums=LT075945772CN"
                ]
            ]
        ];

//        $woocommerce->put('orders/277', $data);
//        print_r($woocommerce->get('orders'));
//        die();
    }

    public function dashBoardStore($store_id, $url, $consumer_key, $consumer_secret)
    {
        $woocommerce = WooInfo::getConnectStore($url, $consumer_key, $consumer_secret);
//        echo "<pre>";
//        print_r($woocommerce->get('orders'));
    }

    public function getOderStore()
    {
        $info = WooInfo::getSessionStore();
        if ($info)
        {
            $yesterday = date("Y-m-d", strtotime( '-1 days' ));
            $woocommerce = WooInfo::getConnectStore($info['woo_link'], $info['consumer_key'], $info['consumer_secret']);
            $from = "".$yesterday."T00:01:00Z";
            $to   = "".$yesterday."T23:59:58Z";

//            $from = "2019-01-05T00:01:00Z";
//            $to   = "2019-01-06T23:59:58Z";
            $per_page = '10';
            $data = [
                'status' => array('processing','on-hold'),
                'order' => 'asc',
                'per_page' => $per_page,
                'after' => $from,
                'before' => $to

            ];
            $orders = $woocommerce->get('orders',$data);
            echo "<pre>";
            print_r($orders);
            die();
            return view('/woo/order')->with(compact('orders'));
        }
        else
        {
            return redirect()->back()->with('error', 'Get error! Please try again later.');
        }
    }

    /*Ham upload file excel*/
    public function doUploadExcel()
    {
        $request = request();
        $str_move_fail = '';
        $str_fail = '';
        $str_uploaded = '';
        $session = false;
        if (\Session::has('woo_store')) {
            $session = \Session::get('woo_store');
        }
        //kiểm tra xem có tồn tại file upload hay không
        if($request->hasFile('excel_file')) {
            $allowedfileExtension=['csv','xls','xlsx'];
            $files = $request->excel_file;
            // kiểm tra tất cả các files xem có đuôi mở rộng đúng không
            foreach($files as $key => $file) {
                $extension = $file->getClientOriginalExtension();
                $check=in_array($extension,$allowedfileExtension);
                //kiểm tra xem có đúng định dạng không
                if(!$check) {
                    $str_fail .= $file->getClientOriginalName().", ";
                    unset($files[$key]);
                }
            }
            $str_fail = substr(trim($str_fail), 0, -1);
            //kiểm tra nếu đúng định dạng
            if (sizeof($files) > 0)
            {

                $title = $request->excel_title;
                $note = $request->excel_note;
                $userId = Auth::user()->id;
                $user_name = Auth::user()->name;
                $data = [];
                foreach ($files as $file) {
                    $date = date("Y_m_d_His");
                    $file_name = $file->getClientOriginalName();
                    $name=$date.'_'.$file_name;
                    $path = public_path().'/files/'.$name;
                    if ($file->move(public_path().'/files/', $name))
                    {
                        $str_uploaded .= $file_name.", ";
                        $data[] = array(
                            'name' => $name,
                            'path' => $path,
                            'title' => $title,
                            'note' => $note,
                            'woo_info_id' => $session['woo_id'],
                            'user_id' => $userId,
                            'user_name' => $user_name,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s")
                        );
                    }
                    else
                    {
                        $str_move_fail .= $file_name.', ';
                    }
                }
                $str_move_fail = substr(trim($str_move_fail), 0, -1);
                $str_uploaded = substr(trim($str_uploaded), 0, -1);
                //Nếu move file thành công. Thực hiện lưu vào database
                if (sizeof($data) > 0)
                {
                    DB::table('excels')->insert($data);
                    $mess = "Upload successfuly files: ".$str_uploaded;
                    $mess .= (strlen($str_move_fail) > 0)? ' ---> But can not upload files: '.$str_move_fail : ' <---';
                    \Session::flash('success', $mess);
                }
                else
                {
                    \Session::flash('error', 'Fail to upload. Please try again this files : '.$str_move_fail);
                }
            }
            else
            {
                \Session::flash('error', 'Fail to upload. Only accept csv, xls, xlsx file. Wrong extension file : '.$str_fail);
            }
        }
    }

    /*Ham cron job lay order luu vao database */
    public function getOrderNew()
    {
        $lst_store = WooInfo::whereStatus(1);
        if (!empty($lst_store))
        {

            $message = 'Ton tai store';
        }
        else
        {
            $message = 'Khong ton tai store nao';
        }

        Log::info($message);
    }

}
