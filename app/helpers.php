<?php
	function helloWorld()
	{
	    return 'Hello, World!';
	}

	function create_api_url($query, $api_id, $api_key, $api_url, $timestamp = '')
    {
        $url = "";
        if (!$timestamp) $timestamp = gmdate("Y-m-d_H:i:s");
        if (trim($query) != '') {
            $url = $api_url."/".$query.'/api_id/'.$api_id.'/api_key/'.$api_key;  
            $signature = md5($url.'/timestamp/'.$timestamp);
            $url .= '/signature/'.$signature.'/timestamp/'.$timestamp;
        }
        return $url;
    }
