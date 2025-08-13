<?php
add_action( 'woocommerce_payment_complete', 'so_payment_complete' );
function so_payment_complete($order_id){
    
    $order = wc_get_order($order_id);
    echo "Hello world";
     if(!empty($order)){
         $user = $order->get_user();
                      $order_det=array();
                      foreach ($order->get_items() as $key => $lineItem) {
                      //$order_det = json_decode($lineItem,true);
                      
                      array_push($order_det,json_decode($lineItem,true));
                      
                      }
             $sex=[];
             $product_id=[];
             for($i=0;$i<count($order_det);$i++)
             {
                array_push($sex,$order_det[$i]['meta_data']);
             }

            $result = call_user_func_array('array_merge', $sex);
          //  print_r($result);
            // FOR GETTING QUANTITY AND LINK 
            $link = []; 
            $quantity =[];
            $the_word="quantity";
            $post_word="posts";
            $post_arr=[];
            $newArray = array();
            for($i=0;$i<count($result);$i++){
                if($result[$i]['key'] == 'custom_option'){
                   array_push($link,$result[$i]['value']);
                }
                if(strpos($result[$i]['key'], $the_word) !== false) {
                array_push($quantity,$result[$i]['value']);
                }
                if(strpos($result[$i]['key'], $post_word) !== false) {
                array_push($post_arr,$result[$i]['value']);
                }
            }
            
          global $wpdb;
           for($i=0;$i<count($order_det);$i++){ 
           // Use prepared statement to prevent SQL injection
           $results = $wpdb->get_results($wpdb->prepare(
               "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d", 
               $order_det[$i]['product_id']
           ));
           $service_val = json_decode(json_encode($results),True);
          
           $original_link=$link[$i];
           $myvalue = (!empty($quantity[$i]) ? $quantity[$i] : 0 );
           $arr = explode(' ',trim($myvalue));
           $real_quantity = $arr[0]*$order_det[$i]['quantity'];

           // Use prepared statement to prevent SQL injection
           $results = $wpdb->get_results($wpdb->prepare(
               "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
               $order_det[$i]['product_id'], 
               '%_Service%'
           ));
           $service_val = json_decode(json_encode($results),True);
          $service_id = get_post_meta($order_det[$i]['product_id'], '_Service', true);
                   
           // Use prepared statement to prevent SQL injection
           $result1 = $wpdb->get_results($wpdb->prepare(
               "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
               $order_det[$i]['product_id'], 
               '%_service_type%'
           ));
           $service_type = json_decode(json_encode($result1),True);
           $service_type = isset($service_type[0]['meta_value']) ? $service_type[0]['meta_value'] : '';

             // Use prepared statement to prevent SQL injection
             $results2 = $wpdb->get_results($wpdb->prepare(
                 "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
                 $order_det[$i]['product_id'], 
                 '%_service_parent%'
             ));
             $api_arr = json_decode(json_encode($results2),True);
             $api_id = isset($api_arr[0]['meta_value']) ? intval($api_arr[0]['meta_value']) : 0; 

           $tablename=$wpdb->prefix . "api_credentials";
           // Use prepared statement to prevent SQL injection
           $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id = %d", $api_id));
       $api_data = json_decode(json_encode($api_data),true);

           $api = new Api();
           $api->api_url=$api_data['api_url'];
           $api->api_key= $api_data['api_key'];

           
       $arr = $order_det[$i]['meta_data'];
       $product_id =  $order_det[$i]['product_id'];  
           switch ($service_type) {
            case 'default':
            $order = $api->order(array('service' =>$service_id, 'link' => $original_link, 'quantity' => $real_quantity));
            $type="Default";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
        break;

            case 'custom_comments':
            $comment = get_meta_type('custom_comment',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link,'comments' =>$comment, 'quantity'=>$real_quantity));
            $type="Custom Comment";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'mention_custom_list':
            // No quantity required
            $value = get_meta_type('mention_custom_list',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'usernames' => $value, 'quantity'=>$real_quantity));
            $type="Mention Custom List";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'mention_user_follower':
            $value = get_meta_type('mention_user_follower',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
            $type="Mention User Follower";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;


            case 'comment_likes':
            $value = get_meta_type('comment_likes',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
            $type="Comment Likes";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'drip_feed':
             $runs = get_meta_type('runs',$arr);
             $interval = get_meta_type('interval',$arr);
             $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'runs' => $runs, 'interval' => $interval));
             $type="Drip-Feed";
             submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'subscription':
            //No Link And Quantity Required
            $username = get_meta_type('username',$arr);
            //$posts = get_meta_type('posts',$arr);
            $posts = isset($post_arr[$i]) ? $post_arr[$i] : '';
            $original_link ="https://www.instagram.com/".$username;
             $order = $api->order(array('service' => $service_id,'min'=>$real_quantity,'max'=>$real_quantity ,'username' => $username, 'link' => $original_link, 'posts' => $posts));
            $type="Subscription";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

           }
             
           }
        }
  }



add_action( 'woocommerce_order_status_completed', 'your_function', 10, 1);
function your_function($order_id) {
    
    $order = wc_get_order($order_id);
     if(!empty($order)){
         $user = $order->get_user();
                      $order_det=array();
                      foreach ($order->get_items() as $key => $lineItem) {
                      //$order_det = json_decode($lineItem,true);
                      
                      array_push($order_det,json_decode($lineItem,true));
                      
                      }
             $sex=[];
             $product_id=[];
             for($i=0;$i<count($order_det);$i++)
             {
                array_push($sex,$order_det[$i]['meta_data']);
             }

            $result = call_user_func_array('array_merge', $sex);
          //  print_r($result);
            // FOR GETTING QUANTITY AND LINK 
            $link = []; 
            $quantity =[];
            $the_word="quantity";
            $post_word="posts";
            $post_arr=[];
            $newArray = array();
            for($i=0;$i<count($result);$i++){
                if($result[$i]['key'] == 'custom_option'){
                   array_push($link,$result[$i]['value']);
                }
                if(strpos($result[$i]['key'], $the_word) !== false) {
                array_push($quantity,$result[$i]['value']);
                }
                if(strpos($result[$i]['key'], $post_word) !== false) {
                array_push($post_arr,$result[$i]['value']);
                }
            }
            
          global $wpdb;
           for($i=0;$i<count($order_det);$i++){ 
           // Use prepared statement to prevent SQL injection
           $results = $wpdb->get_results($wpdb->prepare(
               "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d", 
               $order_det[$i]['product_id']
           ));
           $service_val = json_decode(json_encode($results),True);
          
           $original_link=$link[$i];
           $myvalue = (!empty($quantity[$i]) ? $quantity[$i] : 0 );
           $arr = explode(' ',trim($myvalue));
           $real_quantity = $arr[0]*$order_det[$i]['quantity'];


           
           // Use prepared statement to prevent SQL injection
           $results = $wpdb->get_results($wpdb->prepare(
               "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
               $order_det[$i]['product_id'], 
               '%_Service%'
           ));
           $service_val = json_decode(json_encode($results),True);
          $service_id = get_post_meta($order_det[$i]['product_id'], '_Service', true);
           
           // Use prepared statement to prevent SQL injection
           $result1 = $wpdb->get_results($wpdb->prepare(
               "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
               $order_det[$i]['product_id'], 
               '%_service_type%'
           ));
           $service_type = json_decode(json_encode($result1),True);
           $service_type = isset($service_type[0]['meta_value']) ? $service_type[0]['meta_value'] : '';

             // Use prepared statement to prevent SQL injection
             $results2 = $wpdb->get_results($wpdb->prepare(
                 "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
                 $order_det[$i]['product_id'], 
                 '%_service_parent%'
             ));
             $api_arr = json_decode(json_encode($results2),True);
             $api_id = isset($api_arr[0]['meta_value']) ? intval($api_arr[0]['meta_value']) : 0; 

           $tablename=$wpdb->prefix . "api_credentials";
           // Use prepared statement to prevent SQL injection
           $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id = %d", $api_id));
       $api_data = json_decode(json_encode($api_data),true);

           $api = new Api();
           $api->api_url=$api_data['api_url'];
           $api->api_key= $api_data['api_key'];

           
       $arr = $order_det[$i]['meta_data'];
       $product_id =  $order_det[$i]['product_id'];  
           switch ($service_type) {
            case 'default':
            $order = $api->order(array('service' =>$service_id, 'link' => $original_link, 'quantity' => $real_quantity));
            $type="Default";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
        break;

            case 'custom_comments':
            $comment = get_meta_type('custom_comment',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link,'comments' =>$comment, 'quantity'=>$real_quantity));
            $type="Custom Comment";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'mention_custom_list':
            // No quantity required
            $value = get_meta_type('mention_custom_list',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'usernames' => $value, 'quantity'=>$real_quantity));
            $type="Mention Custom List";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'mention_user_follower':
            $value = get_meta_type('mention_user_follower',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
            $type="Mention User Follower";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;


            case 'comment_likes':
            $value = get_meta_type('comment_likes',$arr);
            $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
            $type="Comment Likes";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'drip_feed':
             $runs = get_meta_type('runs',$arr);
             $interval = get_meta_type('interval',$arr);
             $order = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'runs' => $runs, 'interval' => $interval));
             $type="Drip-Feed";
             submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

            case 'subscription':
            //No Link And Quantity Required
            $username = get_meta_type('username',$arr);
            //$posts = get_meta_type('posts',$arr);
            $posts = isset($post_arr[$i]) ? $post_arr[$i] : '';
            $original_link ="https://www.instagram.com/".$username;
             $order = $api->order(array('service' => $service_id,'min'=>$real_quantity,'max'=>$real_quantity ,'username' => $username, 'link' => $original_link, 'posts' => $posts));
            $type="Subscription";
            submit_api_order($order,$service_id,$original_link,$real_quantity,$type,$product_id);
            break;

           }
             
           }
        }
  }