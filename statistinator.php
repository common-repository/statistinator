<?php
/* 
 * Plugin name: Statistinator
 * Description: Your social media analytics in one place.
 * Author: Filippos Karailanidis
 * Version: 1.2.2
 */

define( 'STS_FILE',  __FILE__ );
define( 'STS_DIR', plugin_dir_path( __FILE__ ) );

include STS_DIR . 'inc/class-statistinator.php';
include STS_DIR . 'inc/class-statistinator-tab.php';

if ( class_exists( 'Statistinator' ) ) {
    $s = new Statistinator();
}

register_activation_hook( __FILE__, array('Statistinator', 'on_activation') );

//Custom useful function
function _isset( &$value ) {
    return ( isset( $value ) && !empty( $value ) );
}
