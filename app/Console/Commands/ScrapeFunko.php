<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
//use Illuminate\Http\Request;
//use GuzzleHttp\Client;

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
        'animation',
        'apparel',
        'blind-boxes',
        'games',
        'heroes',
        'home-goods',
        'movies',
        'music',
        'rides',
        'sports',
        'television',
        'the-vault',
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
        foreach ($this->collections as $collection) {
            $this->scrape($collection);
            die();
        }
    }

    public static function scrape($collection) {
        $funko_url = 'https://www.funko.com/products/all/categories';
        $url = $funko_url.'/'.$collection;

        $client = new \GuzzleHttp\Client();
        $request = $client->get($url);
        $response = $request->getBody();

        //convert response to XML Element object
        $xml = new \SimpleXmlElement($response);

        //loop each item and display result
        foreach($xml->entry as $item) {
            echo '<h3> Question By: ' . $item->author->name . '</h3>';
            echo '<p> Question: '. $item->summary . '</p>';
        }
    }
}
