<?php
//LINK PRODUCT PAGE CODE
function kia_custom_option(){
  global $product;  
  $id = is_object($product) && method_exists($product, 'get_id') ? $product->get_id() : $product->id;
  $field_title = get_post_meta( $id, '_field_title', true );
    $field_description = get_post_meta( $id, '_field_description', true );

  // DEFAULT
    if(get_service_type($id)!='subscription'){
    $value = isset( $_POST['_custom_option'] ) ? sanitize_text_field( $_POST['_custom_option'] ) : '';
    echo '<div class="wapf-field-container wapf-field-text" style="width:100%;" for="5f0d89fd59f7d">

            <div class="wapf-field-label wapf--above"><label><span>'.$field_title.'</span> <abbr class="required" title="required">*</abbr></label></div><div class="wapf-field-description">
            '.nl2br($field_description) .'
            </div>
            <div class="wapf-field-input">
                
<input type="text" name="_custom_option" class="wapf-input" value="'.$value .'" required="">            </div>

            
            
        </div>';

    }
    //
    // CUSTOM COMMENT
    if(get_service_type($id)=='custom_comments'){
    $value2 = isset( $_POST['_custom_comment'] ) ? sanitize_text_field( $_POST['_custom_comment'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><textarea name="_custom_comment" class="link_txtbox" id="custom_comment">%s</textarea><span id="comment_quantity" style="display:none">1</span></div></div>', __( 'Comment  ', 'kia-plugin-textdomains' ), esc_textarea( $value2 ) );}

    //MENTION CUSTOM LIST
    if(get_service_type($id)=='mention_custom_list'){
    $value2 = isset( $_POST['_mention_custom_list'] ) ? sanitize_text_field( $_POST['_mention_custom_list'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_mention_custom_list" value="%s" class="link_txtbox" /></div></div>', __( 'MentionCustomList', 'plugin-mention-custom-list' ), esc_attr( $value2 ) );}

    //MENTION USER FOLLOWER
    if(get_service_type($id)=='mention_user_follower'){
    $value2 = isset( $_POST['_mention_user_follower'] ) ? sanitize_text_field( $_POST['_mention_user_follower'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_mention_user_follower" value="%s" class="link_txtbox"  /></div></div>', __( 'Username', 'plugin-mention-user-follower' ), esc_attr( $value2 ) );}

    //COMMENT LIKES
    if(get_service_type($id)=='comment_likes'){
    $value2 = isset( $_POST['_comment_likes'] ) ? sanitize_text_field( $_POST['_comment_likes'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_comment_likes" value="%s" class="link_txtbox" /></div></div>', __( 'Username', 'plugin-comment-likes' ), esc_attr( $value2 ) );}

    //DRIPFEED
    if(get_service_type($id)=='drip_feed'){
    $value2 = isset( $_POST['_runs'] ) ? sanitize_text_field( $_POST['_runs'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_runs" type="number" value="%s" class="link_txtbox" /></div></div>', __( 'Runs', 'plugin-runs' ), esc_attr( $value2 ) );
  
    $value3 = isset( $_POST['_interval'] ) ? sanitize_text_field( $_POST['_interval'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_interval" type="number" value="%s" class="link_txtbox" /></div></div>', __( 'Interval', 'plugin-interval' ), esc_attr( $value3 ) );
  }

  //SUBSCRIPTION
  if(get_service_type($id)=='subscription'){
    $value2 = isset( $_POST['_username'] ) ? sanitize_text_field( $_POST['_username'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_username" type="text" value="%s"  class="link_txtbox"/></div></div>', __( 'Username', 'plugin-username' ), esc_attr( $value2 ) );
  
   }
   //PACKAGE
   if(get_service_type($id)=='mention_package'){
    $value2 = isset( $_POST['_package'] ) ? sanitize_text_field( $_POST['_package'] ) : '';
    printf( '<div class="row"><div class="col-md-6 col-sm-6 col-xs-6"><label class="link_lbl">%s</label>&nbsp;&nbsp;</div><div class="col-md-6 col-sm-6 col-xs-6"><input name="_package" type="text" value="%s"  class="link_txtbox"/></div></div>', __( 'Package', 'plugin-package' ), esc_attr( $value2 ) );
   }

   



}
add_action( 'woocommerce_before_add_to_cart_button', 'kia_custom_option', 9 );



function kia_add_to_cart_validation($passed, $product_id, $qty){
    if( isset( $_POST['_custom_option'] ) && sanitize_text_field( $_POST['_custom_option'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Link.', 'kia-plugin-textdomain' ), $product->get_name() ), 'error' );
        return false;
    }
    // Custom Comment
    if(get_service_type($product_id)=='custom_comments'){
    if( isset( $_POST['_custom_comment'] ) && sanitize_text_field( $_POST['_custom_comment'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Comment.', 'kia-plugin-textdomains' ), $product->get_name() ), 'error' );
        return false;
    }}
    // Mention Custom List
    if(get_service_type($product_id)=='mention_custom_list'){
    if( isset( $_POST['_mention_custom_list'] ) && sanitize_text_field( $_POST['_mention_custom_list'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Mention Custom list.', 'plugin-mention-custom-list' ), $product->get_name() ), 'error' );
        return false;
    }}

    //Mention User Follower
    if(get_service_type($product_id)=='mention_user_follower'){
    if( isset( $_POST['_mention_user_follower'] ) && sanitize_text_field( $_POST['_mention_user_follower'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Mention User Follower.', 'plugin-mention-user-follower' ), $product->get_name() ), 'error' );
        return false;
    }}
    //Mention Comment Likes
    if(get_service_type($product_id)=='comment_likes'){
    if( isset( $_POST['_comment_likes'] ) && sanitize_text_field( $_POST['_comment_likes'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Comment Likes.', 'plugin-comment-likes' ), $product->get_name() ), 'error' );
        return false;
    }}



    //DripFeed
    if(get_service_type($product_id)=='drip_feed'){
      if( isset( $_POST['_runs'] ) && sanitize_text_field( $_POST['_runs'] ) == '' ){
          $product = wc_get_product( $product_id );
          wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Runs.', 'plugin-runs' ), $product->get_name() ), 'error' );
          return false;
      }
    if( isset( $_POST['_interval'] ) && sanitize_text_field( $_POST['_interval'] ) == '' ){
          $product = wc_get_product( $product_id );
          wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Interval.', 'plugin-interval' ), $product->get_name() ), 'error' );
          return false;
      }
    }

    //Subscription
    if(get_service_type($product_id)=='subscription'){
      if( isset( $_POST['_username'] ) && sanitize_text_field( $_POST['_username'] ) == '' ){
          $product = wc_get_product( $product_id );
          wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Username.', 'plugin-username' ), $product->get_name() ), 'error' );
          return false;
      }

      if( isset( $_POST['_posts'] ) && sanitize_text_field( $_POST['_posts'] ) == '' ){
          $product = wc_get_product( $product_id );
          wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Posts.', 'plugin-posts' ), $product->get_name() ), 'error' );
          return false;
      }
  }

  //Package
    if(get_service_type($product_id)=='mention_package'){
      if( isset( $_POST['_package'] ) && sanitize_text_field( $_POST['_package'] ) == '' ){
          $product = wc_get_product( $product_id );
          wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter Package.', 'plugin-package' ), $product->get_name() ), 'error' );
          return false;
      }
  }
  return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'kia_add_to_cart_validation', 10, 3 );


function kia_add_cart_item_data( $cart_item, $product_id ){
 
    if( isset( $_POST['_custom_option'] ) ) {
        $cart_item['custom_option'] = sanitize_text_field( $_POST['_custom_option'] );
    }
    // CUSTOM COMMENT
    if(get_service_type($product_id)=='custom_comments'){
    if( isset( $_POST['_custom_comment'] ) ) {
        $cart_item['custom_comment'] = $_POST['_custom_comment'];
    }}
    //MENTION CUSTOM LIST
    if(get_service_type($product_id)=='mention_custom_list'){
    if( isset( $_POST['_mention_custom_list'] ) ) {
        $cart_item['mention_custom_list'] = sanitize_text_field( $_POST['_mention_custom_list'] );
    }}
  // MENTION USER FOLLOWER
  if(get_service_type($product_id)=='mention_user_follower'){
    if( isset( $_POST['_mention_user_follower'] ) ) {
        $cart_item['mention_user_follower'] = sanitize_text_field( $_POST['_mention_user_follower'] );
    }}
  //COMMENT LIKES
  if(get_service_type($product_id)=='comment_likes'){
    if( isset( $_POST['_comment_likes'] ) ) {
        $cart_item['comment_likes'] = sanitize_text_field( $_POST['_comment_likes'] );
    }}
    // DRIP FEED
    if(get_service_type($product_id)=='drip_feed'){
    if( isset( $_POST['_runs'] ) ) {
        $cart_item['runs'] = sanitize_text_field( $_POST['_runs'] );
    }
    if( isset( $_POST['_interval'] ) ) {
        $cart_item['interval'] = sanitize_text_field( $_POST['_interval'] );
    }}

    //SUBSCRIPTION

    if(get_service_type($product_id)=='subscription'){
        if( isset( $_POST['_username'] ) ) {
            $cart_item['username'] = sanitize_text_field( $_POST['_username'] );
        }
      
        if( isset( $_POST['_posts'] ) ) {
            $cart_item['posts'] = sanitize_text_field( $_POST['_posts'] );
        }
    }

  //PACKAGE
  if(get_service_type($product_id)=='mention_package'){
    if( isset( $_POST['_package'] ) ) {
        $cart_item['package'] = sanitize_text_field( $_POST['_package'] );
    }}  
   return $cart_item;
 
}
add_filter( 'woocommerce_add_cart_item_data', 'kia_add_cart_item_data', 10, 2 );


function kia_get_cart_item_from_session( $cart_item, $values ) {
  // DEFAULT
    if ( isset( $values['custom_option'] ) ){
        $cart_item['custom_option'] = $values['custom_option'];
    }
    // CUSTOM COMMENT
  if ( isset( $values['custom_comment'] ) ){
        $cart_item['custom_comment'] = $values['custom_comment'];
    }
    // COMMENT LIST
    if ( isset( $values['mention_custom_list'] ) ){
        $cart_item['mention_custom_list'] = $values['mention_custom_list'];
    }
    // MENTION USER FOLLOWER
    if ( isset( $values['mention_user_follower'] ) ){
        $cart_item['mention_user_follower'] = $values['mention_user_follower'];
    }
    // COMMENT LIKES
    if ( isset( $values['comment_likes'] ) ){
        $cart_item['comment_likes'] = $values['comment_likes'];
    }
    // DRIPFEED
    if ( isset( $values['runs'] ) ){
        $cart_item['runs'] = $values['runs'];
    }

    if ( isset( $values['interval'] ) ){
        $cart_item['interval'] = $values['interval'];
    }

    // SUBSCRIPTION

    if ( isset( $values['username'] ) ){
        $cart_item['username'] = $values['username'];
    }

    if ( isset( $values['posts'] ) ){
        $cart_item['posts'] = $values['posts'];
    }
    // PACKAGE
    if ( isset( $values['package'] ) ){
        $cart_item['package'] = $values['package'];
    }
  return $cart_item;
}

add_filter( 'woocommerce_get_cart_item_from_session', 'kia_get_cart_item_from_session', 20, 2 );

function kia_add_order_item_meta( $item_id, $values ) {
  // DEFAULT
    if ( ! empty( $values['custom_option'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'custom_option', $values['custom_option'] );           
    }
    // CUSTOM COMMENT
    if ( ! empty( $values['custom_comment'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'custom_comment', $values['custom_comment'] );           
    }
    // MENTION CUSTOM LIST
    if ( ! empty( $values['mention_custom_list'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'mention_custom_list', $values['mention_custom_list'] );           
    }
    // MENTION USER FOLLOWER
    if ( ! empty( $values['mention_user_follower'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'mention_user_follower', $values['mention_user_follower'] );           
    }
    // COMMENT LIKES
    if ( ! empty( $values['comment_likes'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'comment_likes', $values['comment_likes'] );           
    }
    //DRIPFEED
    if ( ! empty( $values['runs'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'runs', $values['runs'] );           
    }

    if ( ! empty( $values['interval'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'interval', $values['interval'] );           
    }


    //SUBSCRIPTION
    if ( ! empty( $values['username'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'username', $values['username'] );           
    }

    if ( ! empty( $values['posts'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'posts', $values['posts'] );           
    }

    // PACKAGE
    if ( ! empty( $values['package'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'package', $values['package'] );           
    }
    
}
add_action( 'woocommerce_add_order_item_meta', 'kia_add_order_item_meta', 10, 2 );
 
 
 function kia_get_item_data( $other_data, $cart_item ) {
  // DEFAULT
    if ( isset( $cart_item['custom_option'] ) ){
 
        $other_data[] = array(
            'name' => __( 'Link', 'kia-plugin-textdomain' ),
            'value' => sanitize_text_field( $cart_item['custom_option'] )
        );
 
    }
    // CUSTOM COMMENT
  if ( isset( $cart_item['custom_comment'] ) ){
      $other_data[] = array(
            'name' => __( 'Comment', 'kia-plugin-textdomains' ),
            'value' => sanitize_text_field( $cart_item['custom_comment'] )
        );
       }
    // MENTION CUSTOM LIST   
    if ( isset( $cart_item['mention_custom_list'] ) ){
      $other_data[] = array(
            'name' => __( 'MentionCustomList', 'plugin-mention-custom-list' ),
            'value' => sanitize_text_field( $cart_item['mention_custom_list'] )
        );
       }
       
    //MENTION USER FOLLOWER   
    if ( isset( $cart_item['mention_user_follower'] ) ){
      $other_data[] = array(
            'name' => __( 'Username', 'plugin-mention-user-follower' ),
            'value' => sanitize_text_field( $cart_item['mention_user_follower'] )
        );
       }
     //COMMENT LIKES  
     if ( isset( $cart_item['comment_likes'] ) ){
      $other_data[] = array(
            'name' => __( 'Username', 'plugin-comment-likes' ),
            'value' => sanitize_text_field( $cart_item['comment_likes'] )
        );
       }
       //DRIPFEED
      if ( isset( $cart_item['runs'] ) ){
     $other_data[] = array(
            'name' => __( 'Runs', 'plugin-runs' ),
            'value' => sanitize_text_field( $cart_item['runs'] )
        );
       }

       if ( isset( $cart_item['interval'] ) ){
      $other_data[] = array(
            'name' => __( 'Interval', 'plugin-interval' ),
            'value' => sanitize_text_field( $cart_item['interval'] )
        );
       }        

       //SUBSCRIPTION
      if ( isset( $cart_item['username'] ) ){
     $other_data[] = array(
            'name' => __( 'Username', 'plugin-username' ),
            'value' => sanitize_text_field( $cart_item['username'] )
        );
       }

       if ( isset( $cart_item['posts'] ) ){
      $other_data[] = array(
            'name' => __( 'Posts', 'plugin-posts' ),
            'value' => sanitize_text_field( $cart_item['posts'] )
        );
       }

       //PACKAGE
       if ( isset( $cart_item['package'] ) ){
      $other_data[] = array(
            'name' => __( 'Package', 'plugin-package' ),
            'value' => sanitize_text_field( $cart_item['package'] )
        );
       }

  
    return $other_data;
 
}
add_filter( 'woocommerce_get_item_data', 'kia_get_item_data', 10, 2 );


function kia_order_item_product( $cart_item, $order_item ){
  //DEFAULT
  if( isset( $order_item['custom_option'] ) ){
        $cart_item_meta['custom_option'] = $order_item['custom_option'];
    }
    // CUSTOM COMMENT
  if( isset( $order_item['custom_comment'] ) ){
    $cart_item_meta['custom_comment'] = $order_item['custom_comment'];
    }
    // MENTION CUSTOM LIST
  if( isset( $order_item['mention_custom_list'] ) ){
    $cart_item_meta['mention_custom_list'] = $order_item['mention_custom_list'];
    }
    // MENTION USER FOLLOWER
    if( isset( $order_item['mention_user_follower'] ) ){
    $cart_item_meta['mention_user_follower'] = $order_item['mention_user_follower'];
    }
    // COMMENT LIKES
    if( isset( $order_item['comment_likes'] ) ){
    $cart_item_meta['comment_likes'] = $order_item['comment_likes'];
    }
    //DRIPFEED
    if( isset( $order_item['runs'] ) ){
    $cart_item_meta['runs'] = $order_item['runs'];
    }

    if( isset( $order_item['interval'] ) ){
    $cart_item_meta['interval'] = $order_item['interval'];
    }

    //SUBSCRIPTION
    if( isset( $order_item['username'] ) ){
    $cart_item_meta['username'] = $order_item['username'];
    }

    if( isset( $order_item['min'] ) ){
    $cart_item_meta['min'] = $order_item['min'];
    }
    if( isset( $order_item['max'] ) ){
    $cart_item_meta['max'] = $order_item['max'];
    }

    if( isset( $order_item['posts'] ) ){
    $cart_item_meta['posts'] = $order_item['posts'];
    }
    
    // PACKAGE
    if( isset( $order_item['package'] ) ){
    $cart_item_meta['package'] = $order_item['package'];
    }

  return $cart_item;
}
add_filter( 'woocommerce_order_item_product', 'kia_order_item_product', 10, 2 );


function kia_order_again_cart_item_data( $cart_item, $order_item, $order){
  // DEFAULT
    if( isset( $order_item['custom_option'] ) ){
        $cart_item_meta['custom_option'] = $order_item['custom_option'];
    }
    // CUSTOM COMMENT
    if( isset( $order_item['custom_comment'] ) ){
        $cart_item_meta['custom_comment'] = $order_item['custom_comment'];
    }
    // MENTIION CUSTOM LIST
    if( isset( $order_item['mention_custom_list'] ) ){
        $cart_item_meta['mention_custom_list'] = $order_item['mention_custom_list'];
    }
    // MENTION USER FOLLOWER
    if( isset( $order_item['mention_user_follower'] ) ){
        $cart_item_meta['mention_user_follower'] = $order_item['mention_user_follower'];
    }
    // COMMENT LIKES
    if( isset( $order_item['comment_likes'] ) ){
        $cart_item_meta['comment_likes'] = $order_item['comment_likes'];
    }
    //DRIPFEED
    if( isset( $order_item['runs'] ) ){
        $cart_item_meta['runs'] = $order_item['runs'];
    }
    if( isset( $order_item['interval'] ) ){
        $cart_item_meta['interval'] = $order_item['interval'];
    }

    //SUBSCRIPTION
    if( isset( $order_item['username'] ) ){
        $cart_item_meta['username'] = $order_item['username'];
    }

    if( isset( $order_item['posts'] ) ){
        $cart_item_meta['posts'] = $order_item['posts'];
    }

    // PACKAGE
    if( isset( $order_item['package'] ) ){
        $cart_item_meta['package'] = $order_item['package'];
    }
  return $cart_item;
}
add_filter( 'woocommerce_order_again_cart_item_data', 'kia_order_again_cart_item_data', 10, 3 );
?>