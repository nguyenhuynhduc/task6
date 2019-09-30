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
        add_action('admin_menu',array($this,'pageRestorePost'));
        add_action('admin_menu',array($this,'settingMenu'));
      //  do_action( 'my_action',array($this,'is_user_logged_in') );
        //get login get logout
        add_action( 'wp_login', array($this,'checkLogin'),99);
        add_action( 'wp_logout', array($this,'checkLogout'));
        add_action( 'save_post', array($this,'check_edit_post'), 10, 3);
        add_action( 'delete_user', array($this,'deleteUser') );
        add_action( 'edit_user_profile', array($this,'editUser'), 10, 1 );
        add_action( 'user_register',  array($this,'addUser'), 10, 1 );
        add_action( 'comment_post', array($this,'addComment'), 10, 2 );
        add_action( 'edit_comment', array($this,'editComment'));
        add_action( 'trashed_comment', array($this,'deleteComment'));
        if ( isset($_GET['action'] ) && $_GET['action'] == 'download_csv' )  {
            // Handle CSV Export
            add_action( 'admin_init', array($this,'excel_export')) ;
        }
    }
    function deleteComment( $comment_ID )
    {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $table_posts = $wpdb->prefix . "posts";
        $table_comments = $wpdb->prefix . "comments";
        $current_user = wp_get_current_user();
        $blogtime = current_time('mysql');
        $post = $wpdb->get_results("SELECT * FROM  $table_comments
                INNER JOIN $table_posts ON $table_comments.comment_post_ID = $table_posts.ID  
                WHERE comment_ID=$comment_ID");

        foreach ($post as $item) {
            $wpdb->insert($table_check_logs,
                array('id_user' => $current_user->ID,
                    'time_logs' => $blogtime,
                    'user_action' => 'Have Delete Comment in  ' . $item->post_title . ": " . $item->comment_content
                ));
        }
    }

    function editComment( $comment_ID )
    {
            global $wpdb;
            $table_check_logs = $wpdb->prefix . "check_logs";
            $table_posts = $wpdb->prefix . "posts";
            $table_comments = $wpdb->prefix . "comments";
            $current_user = wp_get_current_user();
            $blogtime = current_time('mysql');
            $post = $wpdb->get_results("SELECT * FROM  $table_comments
                INNER JOIN $table_posts ON $table_comments.comment_post_ID = $table_posts.ID  
                WHERE comment_ID=$comment_ID");

            foreach ($post as $item) {
                $wpdb->insert($table_check_logs,
                    array('id_user' => $current_user->ID,
                        'time_logs' => $blogtime,
                        'user_action' => 'Have Edit Comment in  ' . $item->post_title . ": " . $item->comment_content
                    ));
            }
    }
    function addComment( $comment_ID, $comment_approved )
    {
        if (1 === $comment_approved) {

            global $wpdb;
            $table_check_logs = $wpdb->prefix . "check_logs";
            $table_posts = $wpdb->prefix . "posts";
            $table_comments = $wpdb->prefix . "comments";
            $current_user = wp_get_current_user();
            $blogtime = current_time('mysql');


            $post = $wpdb->get_results("SELECT * FROM  $table_comments
                INNER JOIN $table_posts ON $table_comments.comment_post_ID = $table_posts.ID  
                WHERE comment_ID=$comment_ID");

            foreach ($post as $item) {
                $wpdb->insert($table_check_logs,
                    array('id_user' => $current_user->ID,
                        'time_logs' => $blogtime,
                        'user_action' => 'Have Comment in  ' . $item->post_title . ": " . $item->comment_content
                    ));
            }
        }
    }
















    //add
    function addUser( $user_id ){
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $table_user=$wpdb->prefix ."users";
        $current_user = wp_get_current_user();
        $blogtime = current_time ('mysql');
        $user_login=$wpdb->get_var("SELECT user_login FROM $table_user WHERE ID=$user_id");
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs'=>$blogtime,'user_action' => ' have inserted a user with user login is: '.$user_login));
    }
    function editUser( $user_id )
    {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $table_user=$wpdb->prefix ."users";
        $current_user = wp_get_current_user();
        $blogtime = current_time ('mysql');
        $user_login=$wpdb->get_var("SELECT user_login FROM $table_user WHERE ID=$user_id");
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs'=>$blogtime,'user_action' => '  have updated a user with user login is: ' .$user_login));
    }

    function deleteUser( $user_id ) {
        global $wpdb;
        $table_check_logs = $wpdb->prefix . "check_logs";
        $table_user=$wpdb->prefix ."users";
        $current_user = wp_get_current_user();
        $blogtime = current_time ('mysql');
        $user_login=$wpdb->get_var("SELECT user_login FROM $table_user WHERE ID=$user_id");
        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'time_logs'=>$blogtime,'user_action' => ' have deleted a  user with user login is: '.$user_login));
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
                    if ($check_post==null)
                    {
                        $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID,'id_post'=>$post_id, 'time_logs'=>$blogtime,'user_action' => 'inserted'));
                    }
                    else{
                        $check_delete =$wpdb->get_results('SELECT * FROM '.$table_posts.' WHERE ID='.$post_id.' AND post_status="trash";');
                        if ($check_delete!=null)
                        {
                            $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'id_post'=>$post_id,'time_logs'=>$blogtime,'user_action' => 'deleted'));

                        }
                        else{
                            $wpdb->insert($table_check_logs, array('id_user' => $current_user->ID, 'id_post'=>$post_id,'time_logs'=>$blogtime,'user_action' => 'updated'));
                        }
                    }
            }
     }
    }

//export Excel
    function excel_export() {
        // Check for current user privileges
        if( !current_user_can( 'manage_options' ) ){ return false; }
        // Check if we are in WP-Admin
        if( !is_admin() ){ return false; }
        // Nonce Check
        $blogtime = current_time ('mysql');
        ob_start();
        $domain = $_SERVER['SERVER_NAME'];
        $filename = 'logs-' . $domain . '-' . $blogtime . '.xls';

        $header_row = array(
            'id',
            'User',
            'Action',
            'Time'

        );
        $data_rows = array();
        global $wpdb;
        $table_check_logs =$wpdb->prefix ."check_logs";
        $table_user=$wpdb->prefix ."users";
        $table_posts=$wpdb->prefix ."posts";
        $users =  $user = $wpdb->get_results("SELECT * FROM $table_check_logs 
                INNER JOIN $table_user ON $table_user.ID = $table_check_logs.id_user  
                WHERE user_action != '' ORDER BY time_logs DESC
                                
                                ");
        foreach ( $users as $user ) {
            if($user->id_post!=null)
            {
                $post_type=$wpdb->get_var("SELECT post_type FROM $table_posts WHERE ID=$user->id_post");
                $post_title=$wpdb->get_var("SELECT post_title FROM $table_posts WHERE ID=$user->id_post");
            }
            else{
                $post_type="";
            }
            if ($user->id_post==null)
            {
                $action="Have ".$user->user_action;
            }
            else
            {
                $action="Have ".$user->user_action. " a ".$post_type.": ".$post_title;
            }
            $row = array(
                $user->id,
                $user->user_nicename,
                $action,
                $user->time_logs,
            );
            $data_rows[] = $row;
        }
        $fh = @fopen( 'php://output', 'w' );
        fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Content-Description: File Transfer' );
        header( 'Content-type: text/xls' );
        header( "Content-Disposition: attachment; filename={$filename}" );
        header( 'Expires: 0' );
        header( 'Pragma: public' );
        fputcsv( $fh, $header_row );
        foreach ( $data_rows as $data_row ) {
            fputcsv( $fh, $data_row );
        }
        fclose( $fh );

        ob_end_flush();

        die();
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
    public function pageRestorePost()
    {
        add_pages_page('restorePost',
            'Restore Post',
            'manage_options',
            'restorePost',
            array($this,'getRestorePost'));
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
    function getRestorePost()
    {
        require_once ABSPATH ."/wp-content/plugins/CheckLogin/restorePost.php";
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


add_action('init', 'do_output_buffer');
function do_output_buffer() {
    ob_start();
}




