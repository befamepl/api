<?php
add_action( 'woocommerce_payment_complete', 'so_payment_complete' );
function so_payment_complete($order_id){
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    $order_det = array();
    foreach ($order->get_items() as $item_id => $item) {
        $meta_data = array();
        foreach ($item->get_meta_data() as $md) {
            $meta_data[] = $md->get_data();
        }
        $order_det[] = array(
            'meta_data'  => $meta_data,
            'product_id' => $item->get_product_id(),
            'quantity'   => $item->get_quantity(),
        );
    }

    global $wpdb;
    $tablename = $wpdb->prefix . "api_credentials";

    foreach ($order_det as $det) {
        $product_id = $det['product_id'];
        $arr = $det['meta_data'];

        $service_id   = get_post_meta($product_id, '_Service', true);
        $service_type = get_service_type($product_id);
        $api_id       = get_post_meta($product_id, '_service_parent', true);

        if (empty($api_id) || empty($service_id) || empty($service_type)) {
            continue;
        }

        $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id=%d", intval($api_id)), ARRAY_A);
        if (empty($api_data)) {
            continue;
        }

        $api = new Api();
        $api->api_url = $api_data['api_url'];
        $api->api_key = $api_data['api_key'];

        // Determine base quantity from any meta key containing 'quantity'
        $baseQuantityLabel = null;
        foreach ($arr as $md) {
            if (isset($md['key']) && stripos($md['key'], 'quantity') !== false) {
                $baseQuantityLabel = $md['value'];
                break;
            }
        }
        $baseQuantity = 0;
        if (!empty($baseQuantityLabel)) {
            $parts = preg_split('/\s+/', trim((string) $baseQuantityLabel));
            $baseQuantity = intval($parts[0]);
        }
        $real_quantity = intval($baseQuantity) * intval($det['quantity']);

        $original_link = get_meta_type('custom_option', $arr);

        switch ($service_type) {
            case 'default':
                if (empty($original_link)) { break; }
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Default', $product_id);
                break;

            case 'custom_comments':
                if (empty($original_link)) { break; }
                $comment = get_meta_type('custom_comment', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'comments' => $comment, 'quantity' => $real_quantity));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Custom Comment', $product_id);
                break;

            case 'mention_custom_list':
                if (empty($original_link)) { break; }
                $value = get_meta_type('mention_custom_list', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'usernames' => $value, 'quantity' => $real_quantity));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Mention Custom List', $product_id);
                break;

            case 'mention_user_follower':
                if (empty($original_link)) { break; }
                $value = get_meta_type('mention_user_follower', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Mention User Follower', $product_id);
                break;

            case 'comment_likes':
                if (empty($original_link)) { break; }
                $value = get_meta_type('comment_likes', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Comment Likes', $product_id);
                break;

            case 'drip_feed':
                if (empty($original_link)) { break; }
                $runs = get_meta_type('runs', $arr);
                $interval = get_meta_type('interval', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'runs' => $runs, 'interval' => $interval));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Drip-Feed', $product_id);
                break;

            case 'subscription':
                $username = get_meta_type('username', $arr);
                $posts = get_meta_type('posts', $arr);
                if (empty($username)) { break; }
                $original_link = 'https://www.instagram.com/' . ltrim($username, '@/');
                $orderResp = $api->order(array('service' => $service_id, 'min' => $real_quantity, 'max' => $real_quantity, 'username' => $username, 'link' => $original_link, 'posts' => $posts));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Subscription', $product_id);
                break;
        }
    }
}



add_action( 'woocommerce_order_status_completed', 'your_function', 10, 1);
function your_function($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    $order_det = array();
    foreach ($order->get_items() as $item_id => $item) {
        $meta_data = array();
        foreach ($item->get_meta_data() as $md) {
            $meta_data[] = $md->get_data();
        }
        $order_det[] = array(
            'meta_data'  => $meta_data,
            'product_id' => $item->get_product_id(),
            'quantity'   => $item->get_quantity(),
        );
    }

    global $wpdb;
    $tablename = $wpdb->prefix . "api_credentials";

    foreach ($order_det as $det) {
        $product_id = $det['product_id'];
        $arr = $det['meta_data'];

        $service_id   = get_post_meta($product_id, '_Service', true);
        $service_type = get_service_type($product_id);
        $api_id       = get_post_meta($product_id, '_service_parent', true);

        if (empty($api_id) || empty($service_id) || empty($service_type)) {
            continue;
        }

        $api_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tablename WHERE api_id=%d", intval($api_id)), ARRAY_A);
        if (empty($api_data)) {
            continue;
        }

        $api = new Api();
        $api->api_url = $api_data['api_url'];
        $api->api_key = $api_data['api_key'];

        $baseQuantityLabel = null;
        foreach ($arr as $md) {
            if (isset($md['key']) && stripos($md['key'], 'quantity') !== false) {
                $baseQuantityLabel = $md['value'];
                break;
            }
        }
        $baseQuantity = 0;
        if (!empty($baseQuantityLabel)) {
            $parts = preg_split('/\s+/', trim((string) $baseQuantityLabel));
            $baseQuantity = intval($parts[0]);
        }
        $real_quantity = intval($baseQuantity) * intval($det['quantity']);

        $original_link = get_meta_type('custom_option', $arr);

        switch ($service_type) {
            case 'default':
                if (empty($original_link)) { break; }
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Default', $product_id);
                break;

            case 'custom_comments':
                if (empty($original_link)) { break; }
                $comment = get_meta_type('custom_comment', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'comments' => $comment, 'quantity' => $real_quantity));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Custom Comment', $product_id);
                break;

            case 'mention_custom_list':
                if (empty($original_link)) { break; }
                $value = get_meta_type('mention_custom_list', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'usernames' => $value, 'quantity' => $real_quantity));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Mention Custom List', $product_id);
                break;

            case 'mention_user_follower':
                if (empty($original_link)) { break; }
                $value = get_meta_type('mention_user_follower', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Mention User Follower', $product_id);
                break;

            case 'comment_likes':
                if (empty($original_link)) { break; }
                $value = get_meta_type('comment_likes', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'username' => $value));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Comment Likes', $product_id);
                break;

            case 'drip_feed':
                if (empty($original_link)) { break; }
                $runs = get_meta_type('runs', $arr);
                $interval = get_meta_type('interval', $arr);
                $orderResp = $api->order(array('service' => $service_id, 'link' => $original_link, 'quantity' => $real_quantity, 'runs' => $runs, 'interval' => $interval));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Drip-Feed', $product_id);
                break;

            case 'subscription':
                $username = get_meta_type('username', $arr);
                $posts = get_meta_type('posts', $arr);
                if (empty($username)) { break; }
                $original_link = 'https://www.instagram.com/' . ltrim($username, '@/');
                $orderResp = $api->order(array('service' => $service_id, 'min' => $real_quantity, 'max' => $real_quantity, 'username' => $username, 'link' => $original_link, 'posts' => $posts));
                submit_api_order($orderResp, $service_id, $original_link, $real_quantity, 'Subscription', $product_id);
                break;
        }
    }
}