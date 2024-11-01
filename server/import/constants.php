<?php 
 
if (!defined('__CONSTANTS_INC')) {  
define('__CONSTANTS_INC', 1);  
 
include_once('jlhconst.php');  
 
$eserverHostname = J_HOST;  
$eserverName = "server";
$domainSettings =J_DOMAIN_SET_PATH;  
$server_directory =J_DIR_PATH;  
$ssl =J_CONF_SSL;  
 
$install_directory = $server_directory."/".$eserverName;
$push_api_path = "http://s99.velaio.com/pushapi/";
 
// Set advanced settings, ie. timers  
 
$connection_timeout = 60;
$connection_timeout_mobile  = 43200;
$keep_alive_timeout = 30;
$guest_login_timeout= 60;
$chat_refresh_rate = 6;
$user_panel_refresh_rate = 10;
$sound_alert_new_message = 1;
$status_indicator_img_type = "gif";
$invitation_position = "right";
$sound_alert_new_pro_msg =1;
 
} /* __CONSTANTS_INC */
 
?>