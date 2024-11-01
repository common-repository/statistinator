<?php

class Statistinator {

    const URL='https://statistinator.com';
    const AUTH_URL='https://statistinator.com/auth';
    const AJAX_URL='https://statistinator.com/src/ajax.php';

    public function __construct() {

        //Include options
        include STS_DIR . 'inc/class-statistinator-options.php';        
        $options = new Statistinator_Options();

        //Setup hooks
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'add_option_statistinator_authentication', array( $this, 'update_authentication' ), 10, 2 );
        add_action( 'add_option_statistinator_account', array( $this, 'update_account' ), 10, 2 );
        add_action( 'update_option_statistinator_authentication', array( $this, 'update_authentication' ), 10, 2 );
        add_action( 'update_option_statistinator_account', array( $this, 'update_account' ), 10, 2 );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
    }

    // On plugin activation
    public static function on_activation() {
        $status = array(
            'global' => 'initial',
            'google' => 'initial',
            'facebook' => 'initial',
            'twitter' => 'initial',
            'mailchimp' => 'initial',
            'youtube' => 'initial',
        );
        update_option( 'statistinator_status', $status );
    }

    //Enqueue scripts
    public function enqueue() {
        $status = get_option( 'statistinator_status' );
        $enabled = ($status['global'] == 'enabled');
        $screen = get_current_screen();
        //Admin Page
        if ( $screen->base == 'settings_page_statistinator' ) {
            wp_enqueue_style( 'sts-admin-style', plugins_url( 'css/admin.css', STS_FILE ) );

            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );

            $tab = ( _isset($_GET['tab']) ? $_GET['tab'] : 'graph' );
            switch( $tab ) {
            case 'graph': 
                if ( $enabled ) {
                    $data = self::get_data();
                    wp_enqueue_script( 'sts-chart', plugins_url( 'js/Chart.js', STS_FILE ) );
                    wp_enqueue_script( 'sts-graph', plugins_url( 'js/graph.js', STS_FILE ) );
                    wp_localize_script( 'sts-graph', 'graph_php', array( 'data' => $data, 'status' => $status ) );
                }
                break;
            case 'authentication':
                wp_enqueue_style( 'sts-switch-style', plugins_url( 'css/switch.css', STS_FILE ) );
                wp_enqueue_script( 'sts-authentication', plugins_url( 'js/authentication.js', STS_FILE ) );
                break;
            case 'account':
                wp_enqueue_script( 'sts-account', plugins_url( 'js/account.js', STS_FILE ) );
                wp_localize_script( 'sts-account', 'php', array( 'sts_ajaxurl' => Statistinator::AJAX_URL ) );
                break;
            }
        }
        //Dashboard
        if ( $screen->base == 'dashboard' && $enabled ) {
            wp_enqueue_style( 'sts-admin-style', plugins_url( 'css/admin.css', STS_FILE ) );

            $data = self::get_data();
            wp_enqueue_script( 'sts-chart', plugins_url( 'js/Chart.js', STS_FILE ) );
            wp_enqueue_script( 'sts-graph', plugins_url( 'js/graph.js', STS_FILE ) );
            wp_localize_script( 'sts-graph', 'graph_php', array( 'data' => $data , 'status' => $status ) );
        }
    }

    // On Update Authentication
    public function update_authentication( $old_value, $value ) {
        
        // Manage Status
        $status = get_option( 'statistinator_status' );
        $account = get_option( 'statistinator_account' );
        $initial = true;

        foreach ( $value as $key => $val ) {
            // Individual statuses
            if ( !empty( $val ) ) {
                if ( $status[ $key ] == 'initial' ) {
                    $status[ $key ] = 'authenticated';
                    if ( _isset( $account[ $key ] ) ) {
                        $status[ $key ] = 'enabled';
                    }
                }
                $initial = false;
            }
            else {
                $status[ $key ] = 'initial';
            }
        }
        if ( $initial ) {
            // If no auth status, global=initial
            $status['global'] = 'initial';
        }
        else if ( $status['global'] == 'initial' ) {
            // If no initial, but was initial, change to authenticated
            $status['global'] = 'authenticated';
        }

        update_option( 'statistinator_status', $status);
    }

    // On Update Account
    public function update_account( $old_value, $value ) {

        // Manage Status
        $status = get_option( 'statistinator_status' );
        $initial = true;

        foreach ( $value as $key => $val ) {
            // Individual statuses
            if ( !empty( $val ) ) {
                $status[ $key ] = 'enabled';
                $initial = false;
            }
            else {
                $status[ $key ] = 'authenticated';
            }
        }
        // If no auth status, global=initial
        if ( $initial ) {
            $status['global'] = 'authenticated';
        }
        else if ( $status['global'] == 'authenticated' ) {
            // If no initial, but was initial, change to authenticated
            $status['global'] = 'enabled';
        }

        update_option( 'statistinator_status', $status);

        // Get data stored
        $social = array(
            'google' => $value['google']['view'],
            'facebook' => $value['facebook']['id'],
            'twitter' => $value['twitter'],
            'mailchimp' => $value['mailchimp']['id'],
            'youtube' => $value['youtube']['id'],
        );

        // Post it to server
        $post = wp_remote_post( Statistinator::URL . '/src/store.php', array(
            'headers'    => array( 'Referer' => get_site_url() ),
            'body'      => array( 'social' => json_encode( $social ) ),
        ));

        // Force fetch new data
        $data = self::get_data(true);

    }

    //Add Dashboard Widget
    public function add_dashboard_widget() {
        wp_add_dashboard_widget( 'statistinator', 'Statistinator', array( $this, 'dashboard_widget' ) );
    }
    public function dashboard_widget() {
        $key = get_option('statistinator_key');
    ?>
        <?php if ( _isset( $key ) ) : ?>
        <div class='sts-graph-container'>
            <canvas id="sts-graph" width="400" height="400"></canvas>
        </div>
        <?php else: ?>
        <div class='sts-warning-wrapper'>
            <h2>Looking for your <strong>Statistinator</strong> graph?</h2>
            <p>Because of some changes, you must now create an API Key.</p>
            <p>Don't worry! All your data is still safe and sound.</p>
            <h2><a href='options-general.php?page=statistinator'>Click here to create your API Key</a></h2>
        </div>
        <?php endif; ?>
    <?php
    }

    //Data handling
    
    //Latest data for UI
    public static function get_latest_data() {
        $data = self::get_data();
        $last = array_map( function( $value ) { if (is_array( $value )) return array_pop( $value ); }, $data );
        unset( $last['fetched'] );
        if ( empty( $last ) ) {
            $last = array( 
                'google' => '',
                'facebook' => '',
                'twitter' => '',
                'mailchimp' => '',
                'youtube' => '',
            );
        }
        return $last;
    }

    //Get data from db or remote
    public static function get_data( $force = false ) {
        // Get db data
        $data = get_option( 'statistinator_data', array() );

        // If empty or expired fetch
        if ( empty( $data ) || self::expired_data( $data ) || $force ) {
            $data = self::fetch_data();
            if ( !empty( $data ) ) {
                $data['fetched'] = date('r', time() );
                update_option( 'statistinator_data', $data );
            }
        }

        return $data;
    }

    //Is data expired
    public static function expired_data( $data ) {
        if ( !_isset( $data['fetched'] ) ) {
            return true;
        }
        // Get day of fetched date
        $day = date( 'm/d/Y', strtotime( $data['fetched'] ) );
        $today = date( 'm/d/Y', strtotime('now') );

        // Compare timestamps
        $expired = ( $day != $today );

        return $expired;
    }

    //Get data remote
    public static function fetch_data() {
        // Fetch graph data from server 
        $data = wp_remote_get( Statistinator::URL . '/src/get.php', array(
            'headers'    => array( 'Referer' => get_site_url() ),
        ));
        $data = json_decode( $data['body'] );

        // Sort by date
        usort($data, function($a, $b) {
            $ad = strtotime($a->date);
            $bd = strtotime($b->date);
            return $ad - $bd;
        });

        // Format to send                                                       
        $final = array();                                                       
        foreach ($data as $row) 
            foreach ($row as $col => $val)                                      
                $final[ $col ][] = $val;   

        // If data exists
        if ( !empty( $final ) ) {
            // Format dates
            foreach( $final['date'] as &$str ) {
                $str = date('m/d', strtotime($str));
            }

            //Add padding if needed
            $count = count( $final['date'] );
            if ( $count < 30 ) {
                foreach ($final as $key => &$array) {
                    $array = array_pad( $array, -30, '' );
                }
            }
        }
        return $final;
    }

    // Set up by Statistinator_Options as sanitized callback
    public static function statistinator_email_validate( $data ) {
        if ( _isset( $data ) ) {
            // Sanitize input
            $email = filter_var( $data ,FILTER_SANITIZE_EMAIL );   
            // Ajax call to server
            $post = wp_remote_post( Statistinator::URL . '/src/key.php', array(
                'headers'    => array( 'Referer' => get_site_url() ),
                'body'      => array( 'email' => $email, 'path' => $_SERVER['HTTP_REFERER'] ),
            ));
            //TODO Make sure email got sent OK. Send error/ok from server
            if ( $post['body'] == 'ok' ) {
                $message = 'Your API Key is on its way! Don\'t forget to check your spam folder.';
                $type = 'updated';
            }
            else {
                $message = 'Could not send email, please contact us at support@statistinator.com';
                $type = 'error';
            }
        }
        add_settings_error('statistinator_email','statistinator_email', $message, $type);
        return false;
    }

}
