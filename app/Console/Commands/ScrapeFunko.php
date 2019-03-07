<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Illuminate\Support\Facades\DB;

class ScrapeFunko extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:funko';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Funko POP! Vinyl Scraper';

    protected $collections = [
        'wall-art' => 's?bbn=16225011011&rh=n%3A%2116225011011%2Cn%3A3736081&dc&fst=as%3Aoff&pf_rd_i=16225011011&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=d8ae4b88-d235-4ceb-bb6d-2c10aea6fa47&pf_rd_r=SZD3Y18VPVC4T5K6R9HG&pf_rd_s=merchandised-search-4&pf_rd_t=101&qid=1487191426&rnid=16225011011&ref=s9_acss_bw_cts_AEVNHOME_T3_w',
    ];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $category = DB::table('categories')
            ->select('id','category_slug','url_shop')
            ->where('status',0)
            ->get()->toArray();
        if (sizeof($category) > 0)
        {
            $category = json_decode(json_encode($category),true)[0]; //it will return you data in array
            $data = array_diff( $this->scrape($category['category_slug'],$category['url_shop']), array( '' ) );
            if (sizeof($data) > 0)
            {
                $this->checkLinkExist($data,$category['id']);
            }
        }
        else
        {
            echo 'Da het category de quet. Save review nào'."\n";
//            $this->saveReview();
            $data = $this->scanReview('aaa');

        }
    }

    /*Hàm quét link tu shop www.personalcreations.com */
    public static function scrape($category , $url_shop) {
        $url = $url_shop."/".$category;
        $data = array();
        $client = new Client();
        $crawler = $client->request('GET', (string)$url);
        return $crawler->filter('.reviewHolder')->each(function ($node) {
            $review = $node->filter('.numberReviews')->text();
            if( strlen($review) >= 0 && (int)$review >= 5) {
                $href = $node->filter('a')->attr('href');
                return $link = explode("?productgroup",$href)[0];
                $data[] = $link;
            }
        });
        return $data;
    }

    /*Hàm check link co tồn tại trước đó hay chưa*/
    public static function checkLinkExist($data, $category_id)
    {
        $lst_link = DB::table('reviewlinks')->select('link')->where('category_id', $category_id)
            ->get()->toArray();
        $db_save = array();
        //không cần kiểm tra. Lưu hết vào db
        foreach ($data as $link)
        {
            $db_save[] = [
                'category_id' => $category_id,
                'link' => $link
            ];
        }
        DB::table('reviewlinks')->insert($db_save);
        DB::table('categories')->where('id',$category_id)->update(['status'=>1]);
    }

    /*Hàm lưu review vào database*/
    private function saveReview()
    {
        $lst_link = DB::table('reviewlinks')
            ->select('link','id')
            ->where('status',0)
            ->limit(1)
            ->get()->toArray();
        if (sizeof($lst_link) > 0)
        {
            $lst_link = json_decode(json_encode($lst_link),true)[0]; //it will return you data in array
            $link = $lst_link['link'];
            $data = $this->scanReview($link);

        }
    }

    /*Hàm thực hiện quét review thông qua link*/
    public static function scanReview($link)
    {
        $number = '12';
        $link = 'https://www.amazon.com/SE-MIU-Bohemian-Vintage-Printed/dp/B07MKRGYGT';
//        $link = 'https://www.amazon.com/SE-MIU-Bohemian-Vintage-Printed/product-reviews/B07MKRGYGT/reviewerType=all_reviews?pageNumber='.$number;
        echo $link."\n";
        $client = new Client();
        $crawler = $client->request('GET', (string)$link);
        $crawler->filter('div.review')->each(function ($node) {
            $star = $node->filter('span.a-icon-alt')->text();
            $title = $node->filter('a.review-title')->text();
            echo $star."--".$title."\n";
//            var_dump($title);
//            var_dump($node);
        });

        if ($crawler->filter('li.a-last a')->count() > 0 ) {
            $next_page = $crawler->filter('li.a-last a')->attr('href');
            echo $next_page;
        }
            else
            {
                echo 'da la page cuoi cung'."\n";
            }

    }
}
