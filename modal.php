<?php
/** Plugin Name: Modal
** Author: Cuong Le
** Version: 1.0
** Description: Create a modal custom post type where authors can add content. Authors will associate the modal to a specific URL. The Modal will show the first time an user land in the page and won’t show again after assertive action by user. If the user declines the Modal, it will redirect to blackstone.com home page.
*/

register_activation_hook( __FILE__, 'author_modal_setup_table' );

function author_modal_setup_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'author_modal';
    $table_name_2 = $wpdb->prefix . 'author_modal_browser_fingerprint';

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      content text NOT NULL,
      associate_url mediumtext NOT NULL,
      PRIMARY KEY  (id)
    )";

    $sql_2 = "CREATE TABLE $table_name_2 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip varchar(100) NOT NULL,
        browser_version mediumtext NOT NULL,
        browser varchar(100) NOT NULL,
        PRIMARY KEY  (id)
    )";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql_2 );

}

function modal_enqueue_scripts() {
    wp_enqueue_script('modal', plugin_dir_url(__FILE__) . 'modal.js', array('jquery'), null, true);
    wp_enqueue_style('modal', plugin_dir_url(__FILE__) . 'modal.css');
}

add_action('wp_enqueue_scripts', 'modal_enqueue_scripts');

if ( ! function_exists ( 'author_modal_content_menu' ) ) {

    function author_modal_content_menu () {

    	add_menu_page (

    		__( 'Author Modal', 'author-mdodal' ),

    		'Author Modal',

    		'manage_options',

    		'custompage',

            'author_modal_content_menu_page'

    	);

    }

}

add_action ( 'admin_menu', 'author_modal_content_menu' );

if ( ! function_exists ( 'author_modal_content_menu_page' ) ) {

    function author_modal_content_menu_page () {
        
        $author = '

            <div>
        
                <label>Content</label>

        <textarea id="left-footer" name="left-footer" rows="10">' 
        
        . '</textarea>

        <button id="save-left-footer" class="submit success button">Save</button>

    </div>
        
        
        ';
    }

}

add_action( 'wp_ajax_save_browser_fingerprint', 'ajax_post_save_browser_fingerprint_handler' );

add_action( 'wp_ajax_nopriv_save_browser_fingerprint', 'ajax_post_save_browser_fingerprint_handler' );

if ( !function_exists ( 'ajax_post_save_browser_fingerprint_handler' ) ) {

    function ajax_post_save_browser_fingerprint_handler () {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal_browser_fingerprint';

        //$ip = $_SERVER['REMOTE_ADDR'];

        //$browser = $_POST['user_agent'];

        $browser_version = $_POST['app_version'];

        //file_put_contents(plugin_dir_path() . 'modal/log.txt', $browser_version);

        $wpdb->insert( $table_name, array( 'browser_version' => $browser_version ) );

        wp_die();

    }

}

?>