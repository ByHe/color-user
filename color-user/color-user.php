<?php
/*
    Plugin Name: Color User
    Plugin URI: http://data.egyweb.se/coloruser
    Description: Paint users
    Author: Henrik Bygren
    Version: 1.0
    Text Domain: color-user
    Author URI: http://data.egyweb.se/
 */

include_once('php/cu-model.php');

// För att kunna uppdatera tabellen senare
global $cu_db_version;
$cu_db_version = "1.2";

// Före och efter för plugin. När den installeras och avinstalleras.
register_activation_hook(__FILE__,'cu_install');
register_deactivation_hook(__FILE__, 'cu_uninstall' );

// Ser till att css kommer in på wp. Allla sidor, men det går att styra till specifika.
wp_register_style( 'cu-style', plugins_url( '/css/cu_styleSheet.css', __FILE__ ));
wp_enqueue_style( 'cu-style' );


// Set till så att javascript-filen kommer in på alla sidor. Går även att styra till specifika sidor.
wp_register_script( 'cu-script', plugin_dir_url(__FILE__) . 'js/cu-script.js');
wp_enqueue_script( 'cu-script' );

// Skickar sökvägen för AJAX-anrop. WP har en sida som behandlar alla anrop. Kommer mer om detta längre ner.
wp_localize_script( 'cu-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

// Vilken funktion skall anropas vid en request.
add_action( 'wp_ajax_server_response', 'ajax_call' );
add_action( 'wp_ajax_nopriv_server_response', 'ajax_call' ); // Ej inloggad

// Short kod, vilken funktion skall anropas.
add_shortcode( 'cu_color_user', 'cu_view');

/**
 * Anropas via ett request (ajax).
 * ajaxRequest.send("action=server_response ....");
 */
function ajax_call(){

    if (isset($_POST['todo'])) {
        switch ($_POST['todo']) {
            case 'get_users':
                echo getUsers(); // ligger i php/cu-model.php
                wp_die();
                break;
            case 'set_color':
                echo setColor($_POST['u_id'], $_POST['color']); // ligger i php/cu-model.php
                wp_die();
                break;
            default:
                echo "ErroR";
        }
    }
}

/**
 * Namnges i add_shortcode( 'cu_color_user', 'cu_view');
 * [cu_color_user]
 * @return string kod som shorcode skall "ersättas" med.
 */
function cu_view(){
    $html = "<section id = 'color-panel'>
             </section>
             <script>init();</script>";
    return $html;
}

/**
 * Körs vid aktivering av plugin.
 * Namnges i register_activation_hook(__FILE__,'cu_install');
 */
function cu_install(){
    global $wpdb;
    global $cu_db_version;

    $table_name = $wpdb->prefix . "color";
    $wp_users = $wpdb->prefix . "users";

    $sql_create = "CREATE TABLE $table_name (
                      u_id bigint(20) unsigned NOT NULL,
                      color VARCHAR(25) DEFAULT 'black',
                      PRIMARY KEY (u_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_create);

    $sql_insert_users = "INSERT INTO $table_name (u_id) SELECT ID FROM $wp_users ORDER BY ID";
    $wpdb ->query($sql_insert_users);

    add_option("cu_db_version", $cu_db_version);
}

/**
 * Körs vid avinstallation av plugin.
 * Namnges i register_deactivation_hook(__FILE__, 'cu_uninstall' );
 */
function cu_uninstall(){
    global $wpdb;

    $table_name = $wpdb->prefix . "color";

    $sql = "DROP TABLE IF EXISTS $table_name";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

