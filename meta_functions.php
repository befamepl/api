<?php
function get_meta_type($data,$arr){
 $key = array_search($data, array_column($arr, 'key'));
 if($key !== false && $key !== ""){return $arr[$key]['value'];}
 return '';
}


function submit_api_order($order,$service_id,$original_link,$quantity,$type,$product_id){
	global $wpdb;
	 if(!empty($order)){
		 $order_mesg =""; $status ="";	
		 $order_arr = json_decode(json_encode($order),true);
		 if(!empty($order_arr['error'])){$order_id="0";$order_mesg	=$order_arr['error'];$status=1;} 
		 if(!empty($order_arr['order'])){$order_id=$order_arr['order'];$order_mesg ="Success";$status=2;}
		 $tablename2=$wpdb->prefix . "api_order_detail";
		 
		 // Use prepared statement to prevent SQL injection
		 $wpdb->insert($tablename2, array(
		     'service_id' => $service_id,
		     'order_id' => $order_id,
		     'link' => $original_link,
		     'status' => $status,
		     'quantity' => $quantity,
		     'type' => $type,
		     'mesg' => $order_mesg,
		     'product_id' => $product_id
		 ), array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%d'));
		}
	}
	
function get_service_name($service_id,$product_id){
    global $wpdb;
    
    // Use prepared statement to prevent SQL injection
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
        $product_id, 
        '%_service_parent%'
    ));
    
    if(empty($results)) {
        return "<span style='color:#FF0000'>Service not found</span>";
    }
    
    $service_val = json_decode(json_encode($results),True);
    $parent_id = $service_val[0]['meta_value']; 
    
    $tablename=$wpdb->prefix."api_credentials";
    $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id = %d", $parent_id));
    
    if(empty($api_data)) {
        return "<span style='color:#FF0000'>API credentials not found</span>";
    }
    
    $api_data = json_decode(json_encode($api_data),true);
    $api = new Api();
    $api->api_url=$api_data['api_url'];
    $api->api_key= $api_data['api_key'];
    
    // FOR SERVICES     
    $services = $api->services();
	    if(!empty($services)){
	    $service_data = json_decode(json_encode($services),True);
	    $service_name ="";
	    foreach($service_data as $row){
	        if($row['service']==$service_id){ 
	            $service_name = $row['name'];
	            break;
	        }
	    } 
	    if(!empty($service_name)){ 
	        return $service_name;
	    } else {
	        return "<span style='color:#FF0000'>Service not found in API</span>";
	    }
	} else {
	    return "<span style='color:#FF0000'>API connection failed</span>";
	}
}


function get_service_type($service_type){
global $wpdb;

// Use prepared statement to prevent SQL injection
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
    $service_type, 
    '%_service_type%'
));

if(!empty($results)){
$service_val = json_decode(json_encode($results),True);
return $service_val[0]['meta_value'];
}
return '';
}