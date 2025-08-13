<?php
function get_meta_type($data,$arr){
 $key = array_search($data, array_column($arr, 'key'));
 if($key!=""){return $arr[$key]['value'];}
}


function submit_api_order($order,$service_id,$original_link,$quantity,$type,$product_id){
	global $wpdb;
	 if(!empty($order)){
		 $order_mesg =""; $status ="";	
		 $order_arr = json_decode(json_encode($order),true);
		 if(!empty($order_arr['error'])){$order_id="0";$order_mesg	=$order_arr['error'];$status=1;} 
		 if(!empty($order_arr['order'])){$order_id=$order_arr['order'];$order_mesg ="Success";$status=2;}
		 $tablename2=$wpdb->prefix . "api_order_detail";
		 $wpdb->query("INSERT INTO $tablename2 (`service_id`, `order_id`, `link`, `status`, `quantity`, `type`,`mesg`,`product_id`)
		 VALUES ('".$service_id."','".$order_id."','".$original_link."','".$status."','".$quantity."','".$type."','".$order_mesg."','".$product_id."')");
		}
	}
	
function get_service_name($service_id,$product_id){
    global $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = ".$product_id." and meta_key like '%_service_parent%'", OBJECT );
    $service_val = json_decode(json_encode($results),True);
    $parent_id = $service_val[0]['meta_value']; 
    
    $tablename=$wpdb->prefix."api_credentials";
    $api_data = $wpdb->get_row("SELECT * FROM $tablename where api_id=".$parent_id."");
    $api_data = json_decode(json_encode($api_data),true);
    $api = new Api();
    $api->api_url=$api_data['api_url'];
    $api->api_key= $api_data['api_key'];
    // FOR SERVICES     
    $services = $api->services();
	    if(!empty($services)){
	    $service_data = json_decode(json_encode($services),True);
	    $service_name ="";
	    foreach($service_data as $row){if($row['service']==$service_id){ $service_name = $row['name'];}} 
	    if(!empty($service_name)){ echo $service_name;}else{echo "<span style='color:#FF0000'>Api Missing</span>";}
	}
}


function get_service_type($service_type){
global $wpdb;
$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = ".$service_type." and meta_key like '%_service_type%'", OBJECT );    
if(!empty($results)){
$service_val = json_decode(json_encode($results),True);
return $service_val[0]['meta_value'];}
}