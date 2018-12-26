<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Session;
use Redirect;
use DB;
use App\User; // this to add User Model
use App\WooInfo;
use Validator;

use Illuminate\Support\Facades\Hash; // this to check Hash Password
use Automattic\WooCommerce\Client;
use App\Jobs\GetTrackingNumberForDues;

class WooController extends Controller
{

    /*woocommerce api*/
    public function getIndex()
    {
        $stores = WooInfo::get();
        return view('/woo/woocommerce',['stores' => $stores]);
    }

    public function newStore()
    {
        return view('/woo/add_new_store');
    }

    public function addNewStore(Request $request)
    {
        $woo_info = new WooInfo();

        \DB::beginTransaction();
        try {
            $woo_info->saveStore();
            \DB::commit(); // if there was no errors, your query will be executed
        } catch (\Exception $e) {
            \DB::rollback(); // either it won't execute any statements and rollback your database to previous state
        }
        return redirect('/woo/woocommerce');
    }

    public function getDashboardStore($store_id)
    {
        $woo_info = new WooInfo();
        $info = $woo_info->setSessionStore($store_id);
        if ($info)
        {
            Session::put('woo_store', $info);
            $test = $woo_info->dashBoardStore($store_id,$info['woo_link'], $info['consumer_key'], $info['consumer_secret']);
            return view('/woo/woo_dashboard');
        }
        else
        {
            return redirect('/woo/woocommerce')->with('error','Error! Please try again');
        }
    }

    public function getOderStore()
    {
        $woo_info = new WooInfo();
        return $woo_info->getOderStore();
    }

    public function getProduction()
    {
//        GetTrackingNumberForDues::dispatch('thong bao hay check ngay di');
        $date = \Carbon\Carbon::today()->subDays(30);
        $files = DB::table('excels')
            ->select('name', 'title', 'note', 'status', 'created_at')
            ->where('created_at', '>=', date($date))
            ->orderby('id','DESC')
            ->get();
        return view('woo/production')->with(compact('files'));
    }

    public function excelUploadPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required',
            'excel_title' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('error','File : Required or Wrong format. (Only accep csv, xlsx, xls file) -  Tittle: required');
        } else {
            \DB::beginTransaction();
            try {
                $woo = new WooInfo();
                $woo->doUploadExcel();
                //sau khi thuc hien luu database. Gọi job trong queue
//                GetTrackingNumberForDues::dispatch('upload thanh cong');

                \DB::commit(); // if there was no errors, your query will be executed
            } catch (\Exception $e) {
                \DB::rollback(); // either it won't execute any statements and rollback your database to previous state
            }
            return redirect('/woo/production');
        }
    }
}
