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
      //  do_action( 'my_action',array($this,'is_user_logged_in') );
        //get login get logout
        add_action( 'wp_login', array($this,'checkLogin'),99);
        add_action( 'wp_logout', array($this,'checkLogout'));
        add_action( 'save_post', array($this,'check_edit_post'), 10, 3);
        add_action( 'delete_user', array($this,'my_delete_user') );
        add_action( 'edit_user_profile', array($this,'custom_user_profile_fields'), 10, 1 );
        add_action( 'user_register',  array($this,'myplugin_registration_save'), 10, 1 );
    }
    function myplugin_registration_save( $user_id ){
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $current_user = wp_get_current_user();
        $blogtime = current_time ('mysql');
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs'=>$blogtime,'user_action' => 'registration User'));
    }
    function custom_user_profile_fields( $profileuser )
    {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $current_user = wp_get_current_user();
        $blogtime = current_time ('mysql');
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs'=>$blogtime,'user_action' => 'update User'));
    }

    function my_delete_user( $user_id ) {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $current_user = wp_get_current_user();
        $blogtime = current_time ('mysql');
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs'=>$blogtime,'user_action' => 'delete User'));

    }
    function check_edit_post($post_id, $post, $update )
    {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) { // keine Aktion bei Autosave
         }else{
            if ($update)
            {
                global $wpdb;
                $table_check_logs = $wpdb->prefix . "check_logs";
                $table_posts = $wpdb->prefix . "posts";
                $current_user = wp_get_current_user();
                $blogtime = current_time ('mysql');
                $check_post=$wpdb->get_results('SELECT * FROM '.$table_check_logs.' WHERE id_post='.$post_id);
                if($post->post_type=="page"){
                    if ($check_post==null)
                    {
                        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID,'id_post'=>$post_id, 'time_logs'=>$blogtime,'user_action' => 'insert page'));
                    }
                    else{
                        $check_delete =$wpdb->get_results('SELECT * FROM '.$table_posts.' WHERE ID='.$post_id.' AND post_status="trash";');
                        if ($check_delete!=null)
                        {
                            $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'id_post'=>$post_id,'time_logs'=>$blogtime,'user_action' => 'delete page'));

                        }
                        else{
                            $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'id_post'=>$post_id,'time_logs'=>$blogtime,'user_action' => 'update page'));

                        }

                    }
                }
                else if($post->post_type=="post"){
                    if ($check_post==null)
                    {
                        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID,'id_post'=>$post_id, 'time_logs'=>$blogtime,'user_action' => 'insert post'));
                    }
                    else{
                        $check_delete =$wpdb->get_results('SELECT * FROM '.$table_posts.' WHERE ID='.$post_id.' AND post_status="trash";');
                        if ($check_delete!=null)
                            $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'id_post'=>$post_id,'time_logs'=>$blogtime,'user_action' => 'delete post'));
                        else
                            $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'id_post'=>$post_id,'time_logs'=>$blogtime,'user_action' => 'update post'));

                    }
                }

            }
     }

    }

    //get time login
    function  checkLogin($login) {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $user = get_user_by('login',$login);
        $blogtime = current_time ('mysql');
        $wpdb->insert($table_check_logs, array('id_user' => $user->ID,'time_logs'=>$blogtime,'user_action' => 'Login'));
    }
    function checkLogout()
    {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $current_user = wp_get_current_user();
        $blogtime = current_time('mysql');
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs' => $blogtime, 'user_action' => 'Logout'));
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





$table_check_logs =$wpdb->prefix ."check_logs";
if ($wpdb->get_var("SHOW TABLES LIKE '".$table_check_logs."'")!=$table_check_logs)
{
    $sql="CREATE TABLE ".$table_check_logs."(
    id INT NOT NULL AUTO_INCREMENT,
    id_post int,
    time_logs datetime,
    id_user INT  ,
    user_action varchar(255)  ,
   PRIMARY KEY ( id )
    );";
    dbDelta($sql);
    $table_check_post=$wpdb->prefix ."posts";
    $user = $wpdb->get_results("SELECT * FROM $table_check_post");
    foreach ($user as $item)
    {
        $wpdb->insert($table_check_logs,array('id_post'=>$item->ID));
    }
}


if (class_exists('CheckLogin'))
{
    $CheckLogin = new CheckLogin();
}

register_activation_hook(__FILE__,array($CheckLogin,'active_plugin'));

register_deactivation_hook(__FILE__,array($CheckLogin,'deactive'));






