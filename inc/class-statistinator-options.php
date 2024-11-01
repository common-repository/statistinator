<?php 

class Statistinator_Options {

    public function __construct() {
        //Add hooks
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'admin_init', array( $this, 'settings' ) );
        add_filter( 'removable_query_args', array( $this, 'query_args' ) );
    }

    // Create Menu
    public function menu() {
        add_options_page( 'Statistinator', 'Statistinator', 'manage_options', 'statistinator', array( $this, 'page' ) );
    }

    public function page() { 
        //Check if we got API Key
        if ( _isset( $_GET['key'] ) ) {
            $key = $_GET['key']; 
            // Send to server to check
            $post = wp_remote_post( Statistinator::URL . '/src/key.php', array(
                'headers'    => array( 'Referer' => get_site_url() ),
                'body'      => array( 'key' => $key ),
            ));

            // If ok, store as meta
            if ( $post['body'] == 'ok' ) {
                $message = 'API Key stored successfully!';
                $type = 'success';

                update_option( 'statistinator_key', $key );
            }
            else {
                $message = 'Could not confirm your API Key, please contact us at support@statistinator.com';
                $type = 'error';
            }
            // Show message
?>
<div class="notice notice-<?php echo $type; ?> is-dismissible"><p><?php echo $message; ?></p></div>
<?php
        }
?>
    <div class="wrap">
        <h2>Statistinator</h2>
    <?php
        // Check if API Key exists or not
        $key = get_option( 'statistinator_key' );
        if ( !_isset( $key ) ) {
?>
<div class='sts-key-wrap'>
    <form action="options.php" method="post">
        <h1>Create your <strong>API Key</strong> to Get Started!</h1>
        <?php settings_fields( "statistinator_email" ); ?>
        <?php do_settings_sections( "statistinator_email" ); ?>
        <p><input type="submit" name="submit" class="button button-primary" value="Send me my Key"></p>
    </form>
</div>
<?php
            return;        
        }
        // Init tabs
        $tabs = array(
            'graph' => 'Graph',
            'account' => 'Social Accounts',
            'authentication' => 'Authentication'
        );

        // Set active tab
        $active_tab = ( _isset( $_GET['tab']) ? $_GET['tab'] : key( $tabs ) );
    ?>
        <h2 class="nav-tab-wrapper">
        <?php
            // List tabs
            foreach ( $tabs as $slug => $title ) {
                $is_active = ( $slug == $active_tab ? 'nav-tab-active' : '' );
                echo "<a href='?page=statistinator&tab=$slug' class='nav-tab $is_active'>$title</a>";
            }
        ?>
        </h2>
<?php
        // Render specific page from Factory
        $tab = new Statistinator_Tab( $active_tab );
        $tab->render();
    }

    public function settings() {

        // Register sections
        add_settings_section( 'statistinator_account_section', 'Accounts', array(  'Statistinator_Tab_Account', 'description'), 'statistinator_account');
        add_settings_section( 'statistinator_authentication_section', 'Authentication', array(  'Statistinator_Tab_Authentication', 'description'), 'statistinator_authentication');
        add_settings_section( 'statistinator_email_section', '', __return_null() , 'statistinator_email');

        // Register fields
        add_settings_field( 'statistinator_email','Your Email:', array( $this, 'textbox' ), 'statistinator_email', 'statistinator_email_section' );
        register_setting( 'statistinator_email', 'statistinator_email', array( 'Statistinator', 'statistinator_email_validate' ) );

        $fields = array(
            'google' => 'Google Analytics',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'mailchimp' => 'Mailchimp',
            'youtube' => 'YouTube'
        );
        foreach ( $fields as $slug => $title ) {
            add_settings_field( 
                "statistinator_account[$slug]", 
                $title, 
                array( 'Statistinator_Tab_Account', 'textbox' ),
                'statistinator_account', 
                'statistinator_account_section',
                array( 'field' => "statistinator_account[$slug]",  'slug' => $slug)
            );
        }
        register_setting( 'statistinator_account', "statistinator_account" );
        foreach ( $fields as $slug => $title ) {
            add_settings_field( 
                "statistinator_authentication[$slug]", 
                $title, 
                array(  'Statistinator_Tab_Authentication', 'textbox' ),
                'statistinator_authentication', 
                'statistinator_authentication_section',
                array( 'field' => "statistinator_authentication[$slug]",  'slug' => $slug)
            );
        }
        register_setting( 'statistinator_authentication', "statistinator_authentication" );
    }


    // Remove from url string query
    public function query_args( $args ) {
        $args[] = 'google_token';
        $args[] = 'facebook_token';
        $args[] = 'twitter_token';
        $args[] = 'mailchimp_token';
        $args[] = 'youtube_token';
        return $args;
    }

    public function textbox() {
    ?>
        <input name='statistinator_email' id='statistinator_email' type='text' />
    <?php
    }

}
