<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Redirect;
use App\User; // this to add User Model
use Illuminate\Support\Facades\Hash; // this to check Hash Password
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function login(Request $request)
    {
    	if ($request->isMethod('post'))
    	{
    		$data = $request->input();
    		if (Auth::attempt(['email' => $data['email'], 'password' => $data['password'],'admin'=>'1']))
    		{
    			// Session::put('adminSession',$data['email']);
    			return redirect('admin/dashboard');
    		}
    		else
    		{
    			return redirect('admin')->with('error','Sai địa chỉ Email hoặc Mật khẩu');
    		}
    	}
    	return view('admin.admin_login');
    }

    public function dashboard()
    {
    	// if (Session::has('adminSession'))
    	// {
    	// 	return view('admin/dashboard');
    	// } 
    	// else 
    	// {
    	// 	return redirect('admin')->with('error','Bạn cần đăng nhập trước khi truy cập');
    	// }
    	return view('admin.dashboard');
    }

    public function setting()
    {
    	return view('/admin/setting');
    }

    public function register()
    {
        return view('/auth/register');
    }

    public function checkPass(Request $request) 
    {
        if (Auth::check())
        {
            $id = Auth::id();
            $data = $request->all();
            $current_pwd = $data['current_pwd'];
            $check_pwd = User::where(['id'=>$id])->first();
            if(Hash::check($current_pwd, $check_pwd->password)) {
                echo 'true';die();
            } else {
                echo 'false';die();
            }
        }
    }

    public function updatePass(Request $request)
    {
        if (Auth::check())
        {
            $id = Auth::id();
            $data = $request->all();
            $current_pwd = $data['current_pwd'];
            $check_pwd = User::where(['id'=>$id])->first()->password;
            if(Hash::check($current_pwd, $check_pwd)) {
                $password = bcrypt($data['pwd']);
                DB::table('users')->where('id',$id)->update(['password' => $password]);
                return redirect('/admin/setting')->with('success','Update password successfully!');
            } else {
                return redirect('/admin/setting')->with('error','Current password incorrect.');
            }
        }
        
    }

    public function logout()
    {
    	Session::flush();
    	return redirect('/admin')->with('success','Đăng xuất thành công');
    }

    /*spy shopify*/
    public function getSpyShopify()
    {
        return view('/admin/shopify');
    }

    public function shopifyGiveContent($domain,$page)
    {  
        $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );
        $domain = 'https://'.$domain;
        $best = '/collections/all?page='.$page.'&sort_by=best-selling';
        $url = $domain.$best;
        $html = file_get_contents($url, false, $context); //get the html returned from the following url
        $body = str_replace('href="/products','href="'.$domain.'/products',$html);
        echo $body;
    }

}
