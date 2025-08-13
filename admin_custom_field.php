<?php
add_action('wp_ajax_my_action', 'my_action_callback');

function my_action_callback() {
    global $wpdb; // this is how you get access to the database
    
    // Validate and sanitize the panel_id
    if(!isset($_POST['panel_id']) || !is_numeric($_POST['panel_id'])) {
        echo "<option>Invalid Panel ID</option>";
        exit();
    }
    
    $panel_id = intval($_POST['panel_id']);
    $tablename=$wpdb->prefix."api_credentials";
    
    // Use prepared statement to prevent SQL injection
    $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id = %d", $panel_id));
    
    if(empty($api_data)) {
        echo "<option>API credentials not found</option>";
        exit();
    }
    
    $api_data = json_decode(json_encode($api_data),true);
    $api = new Api();
    $api->api_url=$api_data['api_url'];
    $api->api_key= $api_data['api_key'];
    // FOR SERVICES     
    $services = $api->services();
    
    if(empty($services)) {
        echo "<option>Failed to fetch services</option>";
        exit();
    }
    
    $service_data = json_decode(json_encode($services),True);
    $option_arr = array();
    foreach($service_data as $row){
        if(isset($row['service']) && isset($row['name'])) {
            $option_arr[] = array($row['service']=>$row['name']);
        }
    }
    $newArray = array();
    foreach($option_arr as $array) {
        foreach($array as $k=>$v) {
            $newArray[$k] = $v; 
        }
    }
    $abc="<option>Select Service</option>";
    foreach($newArray as $key =>$row){
    $abc.= "<option value=".esc_attr($key).">".esc_html($row)."</option>";   
    }
    echo $abc;
    exit();
    }


// SERVICES DROPDOWN CODE
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );
function woo_add_custom_general_fields() {
    global $wpdb;
    $newArray = array();
    $post_id = 0;
    
        if(isset($_GET['post']) && is_numeric($_GET['post'])){
            $post_id = intval($_GET['post']);
        
        // Use prepared statement to prevent SQL injection
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s", 
            $post_id, 
            '%_service_parent%'
        ));
        
        if(sizeof($results)>0){
        $service_val = json_decode(json_encode($results),True);
        $parent_id = intval($service_val[0]['meta_value']); 
        // FOR API KEY
        global $wpdb;
        $tablename=$wpdb->prefix . "api_credentials";
        
        // Use prepared statement to prevent SQL injection
        $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id = %d", $parent_id));
        
        if(!empty($api_data)) {
        $api_data = json_decode(json_encode($api_data),true);
        $api = new Api();
        $api->api_url=$api_data['api_url'];
        $api->api_key= $api_data['api_key'];
        // FOR SERVICES     
        $services = $api->services();
        if(!empty($services)) {
        $service_data = json_decode(json_encode($services),True);
        $option_arr = array();
        foreach($service_data as $row){
            if(isset($row['service']) && isset($row['name'])) {
                $option_arr[] = array($row['service']=>$row['name']);
            }
        }
        foreach($option_arr as $array) {
            foreach($array as $k=>$v) {
                $newArray[$k] = $v; 
            }
        }
        }
        }
        }
    }
	echo "<p>For using below service dropdown product type will be variable product</p>";
     //SERVICE TYPE
     $service_type_arr =array('default'=>'Default',
    	'custom_comments'=>'Custom Comments',
    	//'mention_custom_list'=>'Mentions Custom List',
    	//'mention_user_follower'=>'Mentions User Followers',
    	//'mention_package'=>'Package',
    	'drip_feed'=>'Drip-Feed',
    	'subscription'=>'Subscriptions'
    	//'comment_likes'=>'Comment-Likes'
    );
    echo '<div class="options_group">';
    
        $tablename=$wpdb->prefix . "api_credentials";
    $api_data = $wpdb->get_results("SELECT * FROM $tablename");
    $api_data = json_decode(json_encode($api_data),true);
    
    $service_parent = [];
    foreach($api_data as $row => $value)
    { $service_parent[$value['api_id']] = $value['panel_name'];}
    
    array_unshift($service_parent, 'Select Panel');

    $field_title = get_post_meta( $post_id, '_field_title', true );
    $field_description = get_post_meta( $post_id, '_field_description', true );

    $args1 = array(
        'id' => '_field_title',
        'label' => __( 'Field Title', 'cfwc' ),
        'class' => 'cfwc-custom-field',
        'desc_tip' => true,
        'description' => __( 'Enter the text that will be displayed for the user while adding the link .', 'ctwc' ),
        'value' => $field_title
    );

    woocommerce_wp_text_input( $args1 );

    $args2 = array(
        'id' => '_field_description',
        'label' => __( 'Field Title', 'cfwc' ),
        'class' => 'cfwc-custom-field',
        'desc_tip' => true,
        'description' => __( 'Enter the text that will be displayed for the user while adding the link .', 'ctwc' ),
        'value' => $field_description,
        'rows' => 4
    );

    woocommerce_wp_textarea_input( $args2 );

    //SERVICES
    echo '<div class="options_group">';

    woocommerce_wp_select( array( // Text Field type
        'id'          => '_service_parent',
        'label'       => __( 'Panel', 'woocommerce' ),
        'description' => __( 'Choose Panel.', 'woocommerce' ),
        'desc_tip'    => true,
        'options'     => $service_parent
    ) );

    echo '</div>';
    
    woocommerce_wp_select( array( // Text Field type
        'id'          => '_service_type',
        'label'       => __( 'Service Type', 'woocommerce' ),
        'description' => __( 'Choose Service Type.', 'woocommerce' ),
        'desc_tip'    => true,
        'options'     => $service_type_arr
    ) );
	echo '</div>';


    //SERVICES
    echo '<div class="options_group">';

    woocommerce_wp_select( array( // Text Field type
        'id'          => '_Service',
        'label'       => __( 'Service', 'woocommerce' ),
        'description' => __( 'Choose Service.', 'woocommerce' ),
        'desc_tip'    => true,
        'options'     => $newArray
    ) );
    echo '</div>';
}





// Save Fields values to database when submitted (Backend)
add_action( 'woocommerce_process_product_meta', 'woo_save_custom_general_fields');
function woo_save_custom_general_fields( $post_id ){
    $posted_field_value = @$_POST['_field_title'];
    if( ! empty( $posted_field_value ) )
        update_post_meta( $post_id, '_field_title', esc_attr( $posted_field_value ) );
    $posted_field_value = @$_POST['_field_description'];
    if( ! empty( $posted_field_value ) )
        update_post_meta( $post_id, '_field_description', esc_attr( $posted_field_value ) );
    // Saving "Conditions" field key/value
    $posted_field_value = @$_POST['_Service'];
    if( ! empty( $posted_field_value ) )
        update_post_meta( $post_id, '_Service', esc_attr( $posted_field_value ) );

    $posted_field_value = @$_POST['_service_type'];
    if( ! empty( $posted_field_value ) )
         update_post_meta( $post_id, '_service_type', esc_attr( $posted_field_value ) );
         
    $posted_field_value = @$_POST['_service_parent'];
    if( ! empty( $posted_field_value ) )
         update_post_meta( $post_id, '_service_parent', esc_attr( $posted_field_value ) );
}
//END SERVICES DROPDOWN CODE