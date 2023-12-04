<?php 
// includes/class-ipinfo-handler.php




class IPInfo_Handler {
    private $ipinfo_api_token;

    public function __construct($ipinfo_token = null) {
        $this->ipinfo_api_token = $ipinfo_token;
    }

    public function get_location($ip) {
        $ipinfo_api_url = 'https://ipinfo.io/' . $ip . '/json?token=' . $this->ipinfo_api_token;

        $response = wp_remote_get($ipinfo_api_url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        return $data;
    }
}