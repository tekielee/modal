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
      display tinyint(1) NOT NULL DEFAULT 1,
      PRIMARY KEY  (id)
    )";

    $sql_2 = "CREATE TABLE $table_name_2 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip varchar(100) NOT NULL,
        browser_version mediumtext NOT NULL,
        PRIMARY KEY  (id)
    )";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql_2 );

}

add_action ( 'admin_enqueue_scripts', 'author_modal_scripts' );

if ( ! function_exists ( 'author_modal_scripts' ) ) {

    function author_modal_scripts ( $hook ) {

        if ( $hook != 'toplevel_page_custompage' ) {

            return;

        }

        wp_enqueue_script('modal', plugin_dir_url(__FILE__) . 'modal-admin.js', array('jquery'), null, true);

    }

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

if ( ! function_exists ( 'get_associate_urls_list' ) ) {

    function get_associate_urls_list() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal';

        $associate_urls = $wpdb->get_results( "SELECT id, associate_url FROM {$table_name}" );

        $select = '<select id="associate-url" name="associate-url">';

            foreach ( $associate_urls as $associate_url ) {

                $select .= '<option value="' . $associate_url->id . '">' . $associate_url->associate_url . '</option>';

            }

            $select .= '</select>';

            return $select;

        return $associate_urls;

    }

}

add_action ( 'admin_menu', 'author_modal_content_menu' );

if ( ! function_exists ( 'author_modal_content_menu_page' ) ) {

    function author_modal_content_menu_page () {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal';

        $content = $wpdb->get_results( "SELECT content, associate_url FROM {$table_name} limit 1" );
        
        $author = '

            <div>

                <label>Select Associate URL</label>

            ' . get_associate_urls_list() . ' 

                <br/>
        
                <label>Content</label>

                <textarea id="author-content" name="author-content" cols="100" rows="10">'. wp_unslash($content[0]->content) .'</textarea>

                <br/>

                <label>Url</label>

                <input id="associate-url" name="associate-url" value="'. $content[0]->associate_url .'"style="width:100%;" />

                <br/>

                <button id="save-author-content" class="submit success button">Save</button>

                <br/>

                <div id="author-content-message"></div>

            </div>
        
        
        ';

        echo $author;
    }

}

add_action( 'wp_ajax_save_author_content', 'ajax_post_save_author_content_handler' );

if ( !function_exists ( 'ajax_post_save_author_content_handler' ) ) {

    function ajax_post_save_author_content_handler () {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal';

        $content = $_POST['content'];

        $associate_url = $_POST['associate_url'];

        $wpdb->insert( $table_name, 

        array(
            'content' => $content, 
            'associate_url' => $associate_url 
        ),
        array(
            '%s',
            '%s',
        ) );

        echo 'Content saved';

    }

}

add_action( 'wp_ajax_save_browser_fingerprint', 'ajax_post_save_browser_fingerprint_handler' );

add_action( 'wp_ajax_nopriv_save_browser_fingerprint', 'ajax_post_save_browser_fingerprint_handler' );

if ( !function_exists ( 'ajax_post_save_browser_fingerprint_handler' ) ) {

    function ajax_post_save_browser_fingerprint_handler () {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal_browser_fingerprint';

        $ip = $_SERVER['REMOTE_ADDR'];

        $browser_version = $_POST['app_version'];

        $total_query = "SELECT COUNT(*) FROM {$table_name} where ip = '{$ip}' and browser_version = '{$browser_version}'";

        $total = $wpdb->get_var( $total_query );

        if ( (int)$total === 0 ) {

            $wpdb->insert( $table_name, 
                array( 
                    'ip' => $ip, 
                    'browser_version' => $browser_version 
                ),
                array(
                    '%s',
                    '%s',
                )
            );

        }

        echo $total;

        wp_die();

    }

}

add_action( 'rest_api_init', 'modal_author_routes' );

function modal_author_routes() {
    // Register the routes
    register_rest_route(
        'modal-api/v1',
        '/browser-inf/',
        array(
            'methods'  => 'GET',
            'callback' => 'browser_inf_callback',
            'permission_callback' => '__return_true'
        )
    );

    register_rest_route(
        'modal-api/v1',
        '/author-content/',
        array(
            'methods'  => 'GET',
            'callback' => 'author_content_callback',
            'permission_callback' => '__return_true'
        )
    );
}

if (!function_exists('browser_inf_callback')) {

    function browser_inf_callback( $data ) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal_browser_fingerprint';

        $ip = $_SERVER['REMOTE_ADDR'];

        $browser_version = $_GET['app_version'];

        $total_query = "SELECT COUNT(*) FROM {$table_name} where ip = '{$ip}' and browser_version = '{$browser_version}'";

        $total = $wpdb->get_var( $total_query );

        echo json_encode(array('count' => $total));
    
    }

}

if (!function_exists('author_content_callback')) {

    function author_content_callback( $data ) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'author_modal_browser_fingerprint';

        $ip = $_SERVER['REMOTE_ADDR'];

        $browser_version = $_GET['app_version'];

        $total_query = "SELECT COUNT(*) FROM {$table_name} where ip = '{$ip}' and browser_version = '{$browser_version}'";

        $total = $wpdb->get_var( $total_query );

        echo json_encode(array('count' => $total));
    
    }

}
?>