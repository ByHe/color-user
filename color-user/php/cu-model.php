<?php
/**
 * Created by PhpStorm.
 * User: henrikbygren
 * Date: 2018-02-07
 * Time: 14:10
 */

function getUsers(){
    global $wpdb;

    // Tabellen color med prefix
    $tblColor = $wpdb->prefix . "color";

    /* Bygger upp sql frågan */
    $sqlkod = "SELECT $tblColor.*, $wpdb->users.display_name  FROM $tblColor JOIN $wpdb->users WHERE $tblColor.u_id = $wpdb->users.ID";

    $results = $wpdb->get_results($sqlkod);

    return json_encode($results);
}

function setColor($u_id, $color){
    global $wpdb;
    $status = "OK";

    // Tabellen color med prefix
    $tblColor = $wpdb->prefix . "color";

    $sqlkod = $wpdb->prepare("UPDATE $tblColor SET color = %s WHERE u_id = %d",array($color, $u_id));

    /* Kör frågan mot databasen wordpress och tabellen wp_color */
    if(!$wpdb->query($sqlkod))
        $status = "Error";

    $wpdb->flush();

    return $status;
}