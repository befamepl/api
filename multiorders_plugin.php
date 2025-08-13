<?php
  /*
   Plugin Name: SMM API Wordpress
   Plugin URI: http://webviraltrends.com/smm
   description: This plugin will allow connecting your woocommerce powered WordPress store to integrate with the SMM panel using API. You can complete all orders from any SMM panel automatically. Maximum 4 panels can be added at one time. All orders will complete automatically from your SMM panel using an API key.
   Version: 4.0
   Author: tripchoni 
   Author URI: http://webviraltrends.com/smm
   License: Extended
   Created Date: 01-01-2021
   */

function ftp_menu_page() {
   add_menu_page('SMM API', 'SMM API', 'add_users', 'ftb', '_ftp_bunch', '', 6);  
}
add_action('admin_menu', 'ftp_menu_page');


/*error_reporting(E_ALL); // Error engine - always ON!

ini_set('ignore_repeated_errors', TRUE); // always ON

ini_set('display_errors', TRUE); // Error display - OFF in production env or real server

ini_set('log_errors', TRUE); // Error logging

ini_set('error_log', plugin_dir_path(__FILE__).'debug.log'); // Logging file

ini_set('log_errors_max_len', 1024); // Logging file size
*/

//create custom text table when plugin activate
custom_order_tbl();

function custom_order_tbl() {

global $wpdb; //geting the acces to a wp tables
$tablename=$wpdb->prefix . "api_credentials"; //the name of a table with it's prefix
$table2 = $wpdb->prefix . "api_order_detail";
//checking if the table with the name we created a line above exists
if($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename) {
    //if it does not exists we create the table
    $sql="CREATE TABLE `$tablename`(
    `api_id` int(11) NOT NULL AUTO_INCREMENT,
    `api_url` varchar(500) DEFAULT NULL,
    `api_key` varchar(500) DEFAULT NULL,
    `panel_name` varchar(500) DEFAULT NULL,
    PRIMARY KEY (api_id)
    );";
    //wordpress function for updating the table
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
 }
 if($wpdb->get_var("SHOW TABLES LIKE '$table2'") != $table2) {
    //if it does not exists we create the table
    $sql="CREATE TABLE `$table2` ( `id` INT NOT NULL AUTO_INCREMENT, 
  `service_id` INT NULL , 
  `order_id` VARCHAR(20) NULL ,
  `link` TEXT NULL ,
  `status` TINYINT(2) NOT NULL DEFAULT '0' ,
  `quantity` VARCHAR(20) NULL ,
  `type` VARCHAR(20) NULL ,
  `mesg` VARCHAR(250) NULL ,
  `product_id` INT NULL ,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`))";
   //wordpress function for updating the table
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
 } 
}

function _ftp_bunch(){
  $myplugin_link = plugins_url( '', __FILE__ );
   $adminURL = admin_url().'admin.php?page=ftb';
   global $wpdb; //geting the acces to a wp tables
   $tablename=$wpdb->prefix . "api_credentials"; 
   $tablename2=$wpdb->prefix . "api_order_detail";  //the name of a table with it's prefix
   
   ?>
   <?php
if(isset($_POST['api_submit']))
{ 
  $panel_name = $_POST['panel_name'];
  $api_url = $_POST['api_url'];
  $api_key = $_POST['api_key'];
  
  $panel_name2 = $_POST['panel_name2'];
  $api_url2 = $_POST['api_url2'];
  $api_key2 = $_POST['api_key2'];
  
  $panel_name3 = $_POST['panel_name3'];
  $api_url3 = $_POST['api_url3'];
  $api_key3 = $_POST['api_key3'];
  
  $panel_name4 = $_POST['panel_name4'];
  $api_url4 = $_POST['api_url4'];
  $api_key4 = $_POST['api_key4'];

  $wpdb->query("INSERT INTO $tablename(`api_url`, `api_key`, `panel_name`) VALUES ('".$api_url."','".$api_key."','".$panel_name."')");
  $wpdb->query("INSERT INTO $tablename(`api_url`, `api_key`, `panel_name`) VALUES ('".$api_url2."','".$api_key2."','".$panel_name2."')");
  $wpdb->query("INSERT INTO $tablename(`api_url`, `api_key`, `panel_name`) VALUES ('".$api_url3."','".$api_key3."','".$panel_name3."')");
  $wpdb->query("INSERT INTO $tablename(`api_url`, `api_key`, `panel_name`) VALUES ('".$api_url4."','".$api_key4."','".$panel_name4."')");
  echo "<script>alert('Api has been inserted successfully!!');</script>";
}
else if(isset($_POST['api_update'])){
  $panel_name = $_POST['panel_name'];
  $api_url = $_POST['api_url'];
  $api_key = $_POST['api_key'];
  
  $panel_name2 = $_POST['panel_name2'];
  $api_url2 = $_POST['api_url2'];
  $api_key2 = $_POST['api_key2'];
  
  $panel_name3 = $_POST['panel_name3'];
  $api_url3 = $_POST['api_url3'];
  $api_key3 = $_POST['api_key3'];
  
  $panel_name4 = $_POST['panel_name4'];
  $api_url4 = $_POST['api_url4'];
  $api_key4 = $_POST['api_key4'];

  $wpdb->query("UPDATE $tablename SET `api_url` ='".$api_url."', `api_key` ='".$api_key."', `panel_name` ='".$panel_name."'  where api_id=1");
  $wpdb->query("UPDATE $tablename SET `api_url` ='".$api_url2."', `api_key` ='".$api_key2."', `panel_name` ='".$panel_name2."'  where api_id=2");
  $wpdb->query("UPDATE $tablename SET `api_url` ='".$api_url3."', `api_key` ='".$api_key3."', `panel_name` ='".$panel_name3."'  where api_id=3");
  $wpdb->query("UPDATE $tablename SET `api_url` ='".$api_url4."', `api_key` ='".$api_key4."', `panel_name` ='".$panel_name4."'  where api_id=4");
  echo "<script>alert('Api has been updated successfully!!');</script>";
}
  $api_data = $wpdb->get_row("SELECT * FROM $tablename where api_id=1");
  $api_data = json_decode(json_encode($api_data),true);
  $panel_name = $api_data['panel_name'];
  $api_url = $api_data['api_url']; 
  $api_key = $api_data['api_key'];
  
  $api_data = $wpdb->get_row("SELECT * FROM $tablename where api_id=2");
  $api_data = json_decode(json_encode($api_data),true);
  $panel_name2 = $api_data['panel_name'];
  $api_url2 = $api_data['api_url']; 
  $api_key2 = $api_data['api_key'];
  
  $api_data = $wpdb->get_row("SELECT * FROM $tablename where api_id=3");
  $api_data = json_decode(json_encode($api_data),true);
  $panel_name3 = $api_data['panel_name'];
  $api_url3 = $api_data['api_url']; 
  $api_key3 = $api_data['api_key'];
  
  $api_data = $wpdb->get_row("SELECT * FROM $tablename where api_id=4");
  $api_data = json_decode(json_encode($api_data),true);
  $panel_name4 = $api_data['panel_name'];
  $api_url4 = $api_data['api_url']; 
  $api_key4 = $api_data['api_key'];
?>
  <style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

</style>
</head>
<body>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script> 
<h1>SMM API</h1>
<p>Click on the buttons inside the tabbed menu:</p>

<div class="tab">
  <button class="tablinks" onclick="openfunc(event, 'api')" id='api_btn'>API Credentials</button>
  <button class="tablinks" onclick="openfunc(event, 'orders')">Orders</button>
</div>

<div id="api" class="tabcontent">
            <div class="row">
               <div class="col-sm-12">
                  <center><h2>API CREDENTIALS</h2></center>    
                  <hr>
               </div>
               <hr>
            </div>
            <div class="row">
               <div class="col-sm-12">
                  <form method="POST" action="#">
                      <h2>API 1</h2>
                  
                  <div class="form-group">
                    <label for="exampleInputEmail1">PANEL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="panel_name" placeholder="Enter PANEL NAME" value="<?php if(isset($panel_name)){ echo $panel_name; }?>" required>
                  </div>    


                  <div class="form-group">
                    <label for="exampleInputEmail1">API URL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="api_url" placeholder="Enter API URL" value="<?php if(isset($api_url)){ echo $api_url; }?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword1">API KEY</label>
                    <input type="text" class="form-control" name="api_key" id="exampleInputPassword1" placeholder="Enter API KEY" value="<?php if(isset($api_key)){ echo $api_key; }?>" required>
                    <small id="emailHelp" class="form-text text-muted" >We'll never share your Api key with anyone else.</small>
                  </div>
                  
                    
                  <h2>API 2</h2>

                  <div class="form-group">
                    <label for="exampleInputEmail1">PANEL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="panel_name2" placeholder="Enter PANEL NAME" value="<?php if(isset($panel_name2)){ echo $panel_name2; }?>" required>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1">API URL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="api_url2" placeholder="Enter API URL" value="<?php if(isset($api_url2)){ echo $api_url2; }?>" required>
                    
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">API KEY</label>
                    <input type="text" class="form-control" name="api_key2" id="exampleInputPassword1" placeholder="Enter API KEY" value="<?php if(isset($api_key2)){ echo $api_key2; }?>" required>
                    <small id="emailHelp" class="form-text text-muted" >We'll never share your Api key with anyone else.</small>
                  </div>
                  
                  
                  <h2>API 3</h2>
                  <div class="form-group">
                    <label for="exampleInputEmail1">PANEL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="panel_name3" placeholder="Enter PANEL NAME" value="<?php if(isset($panel_name3)){ echo $panel_name3; }?>" required>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1">API URL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="api_url3" placeholder="Enter API URL" value="<?php if(isset($api_url3)){ echo $api_url3; }?>" required>
                    
                  </div>

                  

                  <div class="form-group">
                    <label for="exampleInputPassword1">API KEY</label>
                    <input type="text" class="form-control" name="api_key3" id="exampleInputPassword1" placeholder="Enter API KEY" value="<?php if(isset($api_key3)){ echo $api_key3; }?>" required>
                    <small id="emailHelp" class="form-text text-muted" >We'll never share your Api key with anyone else.</small>
                  </div>
                  
                  
                  <h2>API 4</h2>
                  <div class="form-group">
                    <label for="exampleInputEmail1">PANEL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="panel_name4" placeholder="Enter PANEL NAME" value="<?php if(isset($panel_name4)){ echo $panel_name4; }?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputEmail1">API URL</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="api_url4" placeholder="Enter API URL" value="<?php if(isset($api_url4)){ echo $api_url4; }?>" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">API KEY</label>
                    <input type="text" class="form-control" name="api_key4" id="exampleInputPassword1" placeholder="Enter API KEY" value="<?php if(isset($api_key4)){ echo $api_key4; }?>" required>
                    <small id="emailHelp" class="form-text text-muted" >We'll never share your Api key with anyone else.</small>
                  </div>
                  
                  
                  <?php if(empty($api_data)){?>
                  <button type="submit" class="btn btn-primary" name="api_submit">Submit</button>
                  <?php } else{?>
                  <button type="submit" class="btn btn-primary" name="api_update">Update</button>
                  <?php
                   }
                  ?>
                </form>
               </div>
            </div>

</div>

<div id="orders" class="tabcontent">
 
           



<center><h2>Orders Data</h2></center>  
<hr>
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Order#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Service</th>
                <th>Link</th>
                <th>Quantity</th>
                <th>Message</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php $order_data = $wpdb->get_results("SELECT * FROM $tablename2");
         $order_data=json_decode(json_encode($order_data),true);
        foreach($order_data as $row){?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= @date('d-m-Y',strtotime($row['created_at']))?></td>
                <td><?= $row['type']?></td>
                <td><?= get_service_name($row['service_id'],$row['product_id']); ?> </td>
                <td><a href="<?= $row['link']?>" target="_blank"><?= $row['link']?></a></td>
                <td><?= $row['quantity']?></td>
                <td><?= $row['mesg'] ?></td>
        <td><h5><?php if($row['status']==1){?><span class='label label-danger'>Failed</span><?php }else{?> <span class='label label-success'>Success</span> <?php } ?></h5></td>                  
       </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Order#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Service</th>
                <th>Link</th>
                <th>Quantity</th>
                <th>Message</th>
                <th>Status</th>
            </tr>
        </tfoot>
    </table>
</div>



<script>
function openfunc(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
    $('#example').DataTable();
</script> 
  
 <?php
    //  }
}





//woocommerce_quantity_input(array('input_value' => @$_POST['quantity']));
// HITTING API AFTER PAYMENT

add_action( 'wp_head', 'check_gateway' );
function check_gateway(){
?>
<style>
.link_txtbox{}
.link_lbl{font-family: 'Montserrat', sans-serif;font-weight: 700;font-style: inherit;font-size: 14px;color: #282828;}
.section .row {margin-top:0px !important;}
.woocommerce div.product form.cart .variations {margin-bottom: 0;}
.woocommerce div.product form.cart .button {margin-left: ;}
.woocommerce div.product form.cart .variations select{
  
}
</style>
<script>
jQuery(function($) {$('.link_bc:gt(0)').remove();$('.quantity').hide();
$('.link_img_div:gt(0)').remove();
//https://www.instagram.com/p/BO2fy1sDIV_/
$(".link_txtbox").on('change', function() {
    var Url = $('.link_txtbox').val();
    $('.link_img').attr('src','https://i.gifer.com/2xkV.gif');
    $.ajax({
    type: 'GET',
    url: 'https://api.instagram.com/oembed?callback=&url='+Url, //You must define 'Url' for yourself
    cache: false,
    dataType: 'json',
    jsonp: false,
    success: function (data) {
       //var MediaID = data.media_id;
       if(data.thumbnail_url != "No URL Match"){
        $('.link_img').attr('src',data.thumbnail_url);}if(data.thumbnail_url=="No URL Match"){} 
        }
  });
});

/*$('#custom_comment').keypress(function(event) {  
     var quantity = $("#quantity").val();
         if(quantity!=""){
         var comment_quantity= $("#comment_quantity").html();
         if(event.which == 13) {                    
            $("#comment_quantity").html(parseInt(comment_quantity)+1);
            if(comment_quantity >= quantity){
            alert("Quantity Exceed");
            return false;
            }
         }
     }
     else{
         alert("Select Quantity First");
     }
});*/     




});
</script>
<?php
}

add_action('admin_footer', 'load_custom_script' ); 
function load_custom_script() {
  ?>
<script>
(function($) {
  $(document).ready(function(){
    $("#api_btn").addClass('active');
    $("#api").css('display','block');

    $("#_service_type").change(function(){
    if($(this).val()==1){alert("Custom Comments");}   
});

});
$(".variations").css('margin-left','-104px');


/*var data = {
        action: 'my_action',
        panel_id: $('#_service_parent').val()
    };
    $.post(ajaxurl, data, function(response) {
        $("#_Service").html(response);
    });*/


$("#_service_parent").on('change', function() {
    var data = {
        action: 'my_action',
        panel_id: $(this).val()
    };
    $.post(ajaxurl, data, function(response) {
        $("#_Service").html(response);
    });
});


}(jQuery));


</script>
<?php
}
include 'api.php';
include 'payment_code.php';
include 'meta_functions.php';
include 'admin_custom_field.php';
include 'cart_functionality.php';