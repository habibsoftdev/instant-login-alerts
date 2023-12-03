<?php 
// includes/class-ipinfo-handler.php

class IPInfo_Handler{
    private $ipinfo_api_token;


    public function __construct($ipinfo_token=null)
    {
        $this->ipinfo_api_token = $ipinfo_token;

    }

    public function get_location($ip){

        $ch = curl_init();

        $ipinfo_api_url = 'https://ipinfo.io/'.$ip. '/json?token='.$this->ipinfo_api_token;

        curl_setopt($ch, CURLOPT_URL, $ipinfo_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if(curl_errno($ch)){
            return false;
        }

        curl_close($ch);

        $data = json_decode($response);

        return $data;

    }


}