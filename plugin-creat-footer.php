<?php

/*
Plugin Name: Check Login
Plugin URI:
Description: Check Login
Version: 1.0.0
Author: DUC NGUYEN
Author URI: http://localhost:8080/task2/
 */

defined('ABSPATH') or die("HEY, WHAT DO YOU DOING?, you silly human!");

class CheckLogin
{

    public function  __construct()
    {
        add_action('admin_menu',array($this,'settingMenu'));
        do_action( 'my_action',array($this,'is_user_logged_in') );

        //get login get logout
        add_action( 'wp_login', array($this,'checkLogin'));
        add_action( 'wp_logout', array($this,'checkLogout'));
    }

    //get time login
    function  checkLogin() {
        $blogtime = current_time ('mysql');
        setcookie( 'timeLogin', $blogtime, 1 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
    }
    function checkLogout()
    {

//        //get ip
////        if (! empty ( $_SERVER [ 'HTTP_CLIENT_IP' ])) {
////        // ip từ chia sẻ internet
////        $ip  =  $_SERVER [ 'HTTP_CLIENT_IP' ];
////        }elseif (! empty ( $_SERVER [ 'HTTP_X_FORWARDED_FOR' ])) {
////            // ip truyền từ proxy
////            $ip  =  $_SERVER [ 'HTTP_X_FORWARDED_FOR' ];
////        }else {
////            $ip  =  $_SERVER [ 'REMOTE_ADDR' ];
////        }
////            //$ip = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');

    if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    $ip = filter_var( $ip, FILTER_VALIDATE_IP );
    $ip = ( $ip === false ) ? '0.0.0.0' : $ip;


        global $wpdb;

        //get time now with mysql
        $blogtime = current_time ('mysql');
        $table_check_login =$wpdb->prefix ."check_login";
        $current_user = wp_get_current_user();
        $wpdb->insert($table_check_login,array('username'=> $current_user->user_login,'ip'=>$ip,'roles'=> $current_user->roles[0],'timeLogin'=>$_COOKIE['timeLogin'] ,'timeLogout'=>$blogtime));
    }

    //create menu
    public function settingMenu()
    {
        add_menu_page('Check Login',
        'Check Login',
        'manage_options',
        'checkLogin',
        array($this,'exampleMenu'),
        '',
        6
    );
    }

    //design menu
    function exampleMenu()
    {
        require_once ABSPATH ."/wp-content/plugins/CheckLogin/CheckLogin.php";
    }


    //add options when actice
    function active_plugin()
    {

    }

    //update options when deactive
    function deactive(){

    }
}


require_once ABSPATH . "wp-admin/includes/upgrade.php";
global $wpdb;
$table_check_login =$wpdb->prefix ."check_login";
if ($wpdb->get_var("SHOW TABLES LIKE '".$table_check_login."'")!=$table_check_login)
{
    $sql="CREATE TABLE ".$table_check_login."(
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(40)  ,
     roles VARCHAR(40)  ,
    ip VARCHAR(40)  ,
    timeLogin longtext  ,
    timeLogout longtext  ,
   PRIMARY KEY ( id )
    );";
    dbDelta($sql);
}

if (class_exists('CheckLogin'))
{
    $CheckLogin = new CheckLogin();
}

register_activation_hook(__FILE__,array($CheckLogin,'active_plugin'));

register_deactivation_hook(__FILE__,array($CheckLogin,'deactive'));






