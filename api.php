<?php
class Api
{   
   
    public $api_url=''; // API URL

    public $api_key=''; // Your API key
    
     public function order($data) { // add order
        if(empty($this->api_url) || empty($this->api_key)) {
            return json_encode(array('error' => 'API URL or API Key is missing'));
        }
        
        $post = array_merge(array('key' => $this->api_key, 'action' => 'add'), $data);
        $response = $this->connect($post);
        
        if($response === false) {
            return json_encode(array('error' => 'Failed to connect to API'));
        }
        
        return json_decode($response);
    }

    public function status($order_id) { // get order status
        if(empty($this->api_url) || empty($this->api_key)) {
            return json_encode(array('error' => 'API URL or API Key is missing'));
        }
        
        if(empty($order_id)) {
            return json_encode(array('error' => 'Order ID is required'));
        }
        
        $response = $this->connect(array(
            'key' => $this->api_key,
            'action' => 'status',
            'order' => $order_id
        ));
        
        if($response === false) {
            return json_encode(array('error' => 'Failed to connect to API'));
        }
        
        return json_decode($response);
    }

    public function multiStatus($order_ids) { // get order status
        if(empty($this->api_url) || empty($this->api_key)) {
            return json_encode(array('error' => 'API URL or API Key is missing'));
        }
        
        if(empty($order_ids)) {
            return json_encode(array('error' => 'Order IDs are required'));
        }
        
        $response = $this->connect(array(
            'key' => $this->api_key,
            'action' => 'status',
            'orders' => implode(",", (array)$order_ids)
        ));
        
        if($response === false) {
            return json_encode(array('error' => 'Failed to connect to API'));
        }
        
        return json_decode($response);
    }

    public function services() { // get services
        if(empty($this->api_url) || empty($this->api_key)) {
            return json_encode(array('error' => 'API URL or API Key is missing'));
        }
        
        $response = $this->connect(array(
            'key' => $this->api_key,
            'action' => 'services',
        ));
        
        if($response === false) {
            return json_encode(array('error' => 'Failed to connect to API'));
        }
        
        return json_decode($response);
    }

    public function balance() { // get balance
        if(empty($this->api_url) || empty($this->api_key)) {
            return json_encode(array('error' => 'API URL or API Key is missing'));
        }
        
        $response = $this->connect(array(
            'key' => $this->api_key,
            'action' => 'balance',
        ));
        
        if($response === false) {
            return json_encode(array('error' => 'Failed to connect to API'));
        }
        
        return json_decode($response);
    }


    private function connect($post) {
        if(empty($this->api_url)) {
            return false;
        }
        
        // Validate URL
        if(!filter_var($this->api_url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        $_post = Array();
        if (is_array($post)) {
            foreach ($post as $name => $value) {
                $_post[] = $name.'='.urlencode($value);
            }
        }

        $ch = curl_init($this->api_url);
        if($ch === false) {
            return false;
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Add timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Add connection timeout
        
        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        $result = curl_exec($ch);
        
        if (curl_errno($ch) != 0 || $result === false) {
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $result;
    }
}
