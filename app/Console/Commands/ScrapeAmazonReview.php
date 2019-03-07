<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Illuminate\Support\Facades\DB;

class ScrapeAmazonReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:amazon_review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Amazon Review';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    protected $amz = 'https://www.amazon.com';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getReview();
    }

    /*Ham log file theo date*/
    private static function log($str)
    {
        \Log::channel('daily')->info($str);
    }

    private function getReview()
    {
        $categories = DB::table('amz_products')
            ->select('id','amz_collection_id','category_id','link_page')
            ->where('status',0)
            ->limit(1)
            ->orderBy('id','ASC')
            ->get()->toArray();
        $this->log('1. ==> Scan collections Amazon Review Product <==');
        if (sizeof($categories) > 0) {
            $insert_review = [];
            $insert_customer = [];
            $update_status = [];
            $str_update_link = '';
            foreach ($categories as $category) {
                $result = $this->scanReview(
                    $this->amz.$category->link_page,
                    $category->amz_collection_id,
                    $category->category_id
                );
                print_r($result);die();
                if (sizeof($result['data']) > 0) {
                    $insert_review = array_merge($insert_review,$result['data']);
                    $insert_customer = array_merge($insert_customer,$result['customer']);
                }
                /*Nếu sản phẩm đã hết review -> đổi trạng thái status = 1*/
                if (strlen($result['link']) == 0)
                {
                    $update_status[] = $category->id;
                }
                else
                {
                    $str_update_link .=
                        'UPDATE amz_products SET (link_page = "'.$result['link'].'") WHERE id ='.$category->id.';';
                }
            }
            /*Lưu data*/
            if (sizeof($insert_review) > 0)
            {
                DB::table('amz_reviews')->insert($insert_review);
                DB::table('amz_customers')->insert($insert_customer);
            }
            if ( sizeof($update_status) > 0)
            {
                DB::table('amz_products')->where('id',$update_status)->update(['status'=> 1]);
            }
            print_r($insert_review);
        }
        else
        {
            $this->log('1.1 ==> All product review had scan Review Product <==');
        }
        $this->log('============================================================'."\n");
    }

    /*Hàm thực hiện quét review của sản phẩm amazon*/
    private function scanReview($url,$collection_id, $category_id)
    {
        $next_link = '';
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $update_link = array();
        //Kiểm tra xem đây có phải là link cuối cùng hay chưa
        if ($crawler->filter('ul.a-pagination li.a-last a')->count() > 0 ) {
            $next_link = trim($crawler->filter('ul.a-pagination li.a-last a')->attr('href'));
            $this->log('2.1 Exist next link this collections');
        } else {
            $this->log('2.1 This is last link.');
        }

        $data = array();
        $customers = array();
        //Quét toan bộ review sản phẩm
        if ($crawler->filter('div.review')->count() > 0)
        {
            $crawler->filter('div.review')->each(function ($node,$i) use (
                $category_id, $collection_id, &$data, $crawler ) {
                $star = (int) substr($node->filter('span.a-icon-alt')->text(), 0, 1);
                if ($star >= 3)
                {
                    $name = trim($node->filter('.a-profile-name')->text());
                    $title = trim($node->filter('.review-title span.cr-original-review-content')->text());
                    $content = trim($node->filter('.review-text-content')->text());
                    $data[$i] = [
                        'star' => $star,
                        'title' => $title,
                        'content' => $content,
                        'amz_collection_id' => $collection_id,
                        'category_id' => $category_id,
                        'created_at' => date("Y-m-d H:m:s"),
                        'updated_at' => date("Y-m-d H:m:s")
                    ];
//                    $customers[$i] = [
//                        'name' => $name,
//                        'created_at' => date("Y-m-d H:m:s"),
//                        'updated_at' => date("Y-m-d H:m:s")
//                    ];
                }
            });
            $str_3 = '2.2 Had found '.sizeof($data).' reviews in this product.';
        } else {
            $str_3 = '2.2 No review in this product.';
        }
        $this->log($str_3);
        $result = [
            'link' => $next_link,
            'data' => $data,
            'customer' => $customers
        ];
        return $result;
    }
}
