<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Illuminate\Support\Facades\DB;

class ScrapeAmazon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:amazon_product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Amazon';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $collections = [
        'Women Fashion : Clothing' => '/s?bbn=16225018011&rh=n%3A7141123011%2Cn%3A16225018011%2Cn%3A1040660&pf_rd_i=16225018011&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=e9a7a2cd-d373-460c-8c25-702b5e2acb03&pf_rd_r=MQZ6VQHV16S08X89JP6G&pf_rd_s=merchandised-search-4&pf_rd_t=101&ref=s9_acss_bw_cts_AEAISWF_T1_w',
        'wall-art' => '/s?bbn=16225011011&rh=n%3A%2116225011011%2Cn%3A3736081&dc&fst=as%3Aoff&pf_rd_i=16225011011&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=d8ae4b88-d235-4ceb-bb6d-2c10aea6fa47&pf_rd_r=SZD3Y18VPVC4T5K6R9HG&pf_rd_s=merchandised-search-4&pf_rd_t=101&qid=1487191426&rnid=16225011011&ref=s9_acss_bw_cts_AEVNHOME_T3_w',
    ];

    protected $amz = 'https://www.amazon.com';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $category = DB::table('categories')
            ->select('id','url_shop','amz_collection_id')
            ->where('status',0)
            ->get()->toArray();
        if (sizeof($category) > 0)
        {
            $category = json_decode(json_encode($category),true)[0]; //it will return you data in array
            $url_shop = $category['url_shop'];
            $this->log('1. ==> Scan collections Amazon <==');
            $link = $this->amz.$url_shop;
            $this->log('2. Scan link : '.$link);
            $data = $this->scrape($link);
            $this->checkLinkExist($data,$category['amz_collection_id'], $category['id']);
            $this->log('==> END Scan collections Amazon <==');
        }
        else
        {
            $this->log('0. Find 0 collection in database ready. Refresh to all collections');
        }
        $this->log("=====================================================================\n");

    }

    /*Ham log file theo date*/
    private static function log($str)
    {
        \Log::channel('daily')->info($str);
    }

    /*Lưu link product vào database Amazon*/
    private function checkLinkExist($data, $collection_id, $category_id)
    {
        $this->log('4. Check link before save to database');
        $links = $data['data'];
        if (sizeof($links) > 0)
        {
            $lst_old = DB::table('amz_products')
                ->select('product_id')
                ->where('amz_collection_id',$collection_id)
                ->pluck('product_id')
                ->toArray();
            $db = array();
            foreach ($links as $link) {
                if ($link['review'] <= 5) {
                    continue;
                }
                //tìm product_id amazon và đổi link sản phẩm thành link review
                $tmp = explode("/", $link['link']);
                $tmp[2] = 'product-reviews';
                $amz_product_id = trim($tmp[3]);
                /*Check amz_product_id đã tồn tại trong database hay chưa*/
                if (in_array($amz_product_id, $lst_old))
                {
                    continue;
                }
                $url = implode('/', $tmp);
                $db[] = [
                    'amz_collection_id' => $collection_id,
                    'category_id' => $category_id,
                    'product_id' => $amz_product_id,
                    'title' => trim(htmlentities($link['title'])),
                    'link_origin' => $url,
                    'link_page' => $url,
                    'created_at' => date("Y-m-d H:m:s"),
                    'updated_at' => date("Y-m-d H:m:s")
                ];
            }
            if (sizeof($db) > 0)
            {
                $this->log('4.1 Save new '.sizeof($db).' to database');
                DB::table('amz_products')->insert($db);
            }
            else
            {
                $this->log('4.1 All products in this page is already import before.');
            }
        }
        /*Nếu tồn tại page kế tiếp. Đổi link và chưa cập nhật page*/
        $this->log('5. Check next page in this category.');
        if (strlen($data['next_link']) > 0)
        {
            $update_cat = [
                'url_shop' => trim($data['next_link']),
                'updated_at' => date("Y-m-d H:m:s")
            ];
            $str = "5.1 Next page to next scan product";
        }
        else
        {
            $update_cat = [
                'status' => 1,
                'updated_at' => date("Y-m-d H:m:s")
            ];
            $str = "5.1 End page and stop this page";
        }
        DB::table('categories')->where('id',$category_id)->update($update_cat);
        $this->log($str);
    }

    //Hàm quét product của amazon
    private function scrape($url)
    {
        $data = array();
        $next_link = '';
        $client = new Client();
        $crawler = $client->request('GET', $url);
        //Kiểm tra xem đây có phải là link cuối cùng hay chưa
        if ($crawler->filter('ul.a-pagination li.a-last a')->count() > 0 ) {
            $next_link = $crawler->filter('ul.a-pagination li.a-last a')->attr('href');
            $this->log('3.1 Exist next link this collections');
        } else {
            $this->log('3.1 This is last link.');
        }

        //Quét toan bộ danh sách sản phẩm
        if ($crawler->filter('div.s-include-content-margin')->count() > 0) {
            $crawler->filter('div.s-include-content-margin')->each(function ($node,$i) use (&$data, $crawler) {
                $link = $node->filter('a.a-link-normal')->attr('href'); // se bao gom ca ID cua Amazon
                $title = trim($node->filter('.a-text-normal')->text());
                if ($node->filter('span.a-size-base')->count() > 0)
                {
                    $review = (int)trim($node->filter('span.a-size-base')->text());
                }
                else
                {
                    $review = 0;
                }
                $data[$i] = [
                    'review' => $review,
                    'title' => $title,
                    'link' => $link,
                ];
            });
            $this->log('3.2 Had found '.sizeof($data)." products in this collection");
        } else {
            $this->log('3.2 Had found 0 product in this collection');
        }

        $return = [
            'next_link' => $next_link,
            'data' => $data
        ];
        return $return;
    }
}
