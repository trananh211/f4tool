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

    public function testFunction()
    {
        $info = WooInfo::getSessionStore();
        $woocommerce = WooInfo::getConnectStore($info['woo_link'], $info['consumer_key'], $info['consumer_secret']);
        echo "<pre>";
//        print_r($woocommerce->get('orders/3130'));
        /*$data = [
            'status' => 'processing'
        ];

        print_r($woocommerce->put('orders/3129', $data));*/
        print_r($woocommerce->get('system_status/tools'));
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

    /*Hàm thực hiện lấy review và thêm vào product có sẵn*/
    public function addReviewProduct($product_id)
    {
        $info = WooInfo::getSessionStore();
        if ($info)
        {
            $text = str_random(12);
            $woocommerce = WooInfo::getConnectStore($info['woo_link'], $info['consumer_key'], $info['consumer_secret']);
            $data = [
                'product_id' => $product_id,
                'review' => 'Nice album! '.$text.'!!!',
                'reviewer' => 'John Doe 2',
                'reviewer_email' => 'john.doe@example.com',
                'rating' => rand(4,5)
            ];
            $woocommerce->post('products/reviews', $data);
            return redirect()->back()->with('success','Import review success fully'.$product_id);
        }
        else
        {
            return redirect()->back()->with('error', 'Get error! Please try again later.');
        }
    }

    /*
     * Hàm thực hiện quét danh sách store woo hàng ngày.
     * Trả về tổng sản phẩm mới trong ngày hôm trước được tạo mới và lưu vào cơ sở dữ liệu
     * */
    public function scanStoreList()
    {
        $return = false;
        $stores = DB::table('woo_infos')
            ->select('id','woo_link','consumer_key','consumer_secret','external','grouped','simple','variable')
            ->where('status', '1')
            ->orderby('id','ASC')
            ->get();
        if (sizeof($stores) > 0) {
            //connect to server, then get all information
            foreach ($stores as $store){
                $url = $store->woo_link;
                $consumer_key = $store->consumer_key;
                $consumer_secret = $store->consumer_secret;
                $old_store = array(
                    'external' => $store->external,
                    'grouped' => $store->grouped,
                    'simple' => $store->simple,
                    'variable' => $store->variable
                );
                try{
                    $woocommerce = WooInfo::getConnectStore($url, $consumer_key, $consumer_secret);
                    $info = $woocommerce->get('reports/products/totals');
                    $info = json_decode(json_encode($info),true); //it will return you data in array
                    $new_store = array(
                        'external' => $info[0]['total'],
                        'grouped' => $info[1]['total'],
                        'simple' => $info[2]['total'],
                        'variable' => $info[3]['total']
                    );
                    //compare old data with new data
                    if (count(array_diff_assoc($old_store,$new_store)) > 0)
                    {
                        $data = $new_store;
                        $data['compare'] = 1;
                        DB::table('woo_infos')->where('id', $store->id)->update($data);
                    }
                    $return = true;
                }
                catch (Exception $e) {
                    $return = false;
                    \Log::info($e->getMessage());
                }

            }
        }
        return $return;
    }

    /*
     * Hàm thực hiện quét danh sách sản phẩm mới store woo hàng ngày.
     * Trả về list sản phẩm mới trong ngày hôm trước và lưu vào cơ sở dữ liệu
     * Hàm thực hiện 30p 1 lần
     * */
    public function scanProductNew()
    {
        $woo_info = new WooInfo();
        $return = false;
        $str = '';
        $stores = DB::table('woo_infos')
            ->select(
                'id','woo_link','consumer_key','consumer_secret','external','grouped','simple','variable',
                'try','compare'
                )
            ->where(['compare' => 1, 'status' => 1])
            ->limit(1)
            ->get()
            ->toArray();
        $stores = json_decode(json_encode($stores),true); //it will return you data in array
        if (sizeof($stores) > 0)
        {
            $list_all_product = DB::table('products')
                ->select('id','product_id')
                ->where('woo_info_id',$stores[0]['id'])
                ->get('id')
                ->toArray();
            $count_products = sizeof($list_all_product);
            $compare_product = array();
            $old_prd_array = array();
            foreach ($list_all_product as $product) {
                $prd_id = $product->product_id;
                $old_prd_array[$product->id] = $prd_id;
                if (!in_array( $prd_id, $compare_product)) {
                    $compare_product[$product->id] = $prd_id;
                }
            }
            //nếu xuất hiện sản phẩm bị trùng
            if (sizeof($old_prd_array) != sizeof($compare_product) && $count_products > 0)
            {
                $del_data = array_keys(array_diff_assoc($old_prd_array,$compare_product));
                $str .= "Store ".$stores[0]['woo_link']." có ".sizeof($del_data)." trùng nhau. Tool sẽ xóa các sản phẩm này.";
                DB::table('products')->whereIn('id', $del_data)->delete();
            }
            else //mọi việc hoàn toàn bình thường
            {
                $all_product = $stores[0]['external']+$stores[0]['grouped']+$stores[0]['simple']+$stores[0]['variable'];
                try {
                    //nếu phát hiện có sản phẩm mới
                    if ($all_product > $count_products)
                    {
                        $str .= "Store ".$stores[0]['woo_link']." phát hiện có sản phẩm mới.";
                        $url = $stores[0]['woo_link'];
                        $consumer_key = $stores[0]['consumer_key'];
                        $consumer_secret = $stores[0]['consumer_secret'];
                        $woocommerce = WooInfo::getConnectStore($url, $consumer_key, $consumer_secret);
                        $conditions = [
                            'status' => 'publish',
                            'page' => $stores[0]['try'],
                            'per_page' => 3,
                        ];
                        $lst_prd = $woocommerce->get('products',$conditions);
                        $data_prd = array();
                        foreach ($lst_prd as $new_product) {
                            if (in_array($new_product->id, $old_prd_array)) {
                                continue;
                            }
                            $data_prd[] = [
                                'woo_info_id' => $stores[0]['id'],
                                'product_id' => $new_product->id,
                            ];
                        }
                        if (sizeof($data_prd) > 0)
                        {
                            DB::table('products')->insert($data_prd);
                            $str .= "Thêm mới ".sizeof($data_prd)." vào database.";
                        }
                        $try = $stores[0]['try'] + 1;
                        DB::table('woo_infos')
                            ->where('id',$stores[0]['id'])
                            ->update([ 'try' => $try ]);
                    }
                    else
                    {
                        if ($stores[0]['try'] != 1 || $stores[0]['compare'] != 0) {
                            DB::table('woo_infos')
                                ->where('id',$stores[0]['id'])
                                ->update(['try' => 1, 'compare' => 0]);
                        }
                        $str .= "Store ".$stores[0]['woo_link']." chưa thêm sản phẩm mới. Tắt tìm kiếm sản phẩm mới.";
                    }
                    $return = true;
                } catch (\Exception $e) {
                    $return = false;
                    \Log::info($e->getMessage());
                }
            }
        } else {
            /*Trường hợp 2: nếu người dùng xóa sản phẩm ở woo store*/
            echo "kiem tra lại store";
        }
        \Log::info($str);
        return $return;
    }
}
