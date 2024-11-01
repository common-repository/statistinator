<?php 

class Statistinator_Tab_Authentication {

    public function __construct() {
    }

    public function render() {
?>
        <form action="options.php" method="post">
            <?php settings_fields( "statistinator_authentication" ); ?>
            <?php do_settings_sections( "statistinator_authentication" ); ?>
        </form>
<?php
    }

    public static function description() {
    ?>
        <p>Here you can grant Statistinator access to your data. Authenticate the social media modules you wish to use. After that, you need to set up your <a href="?page=statistinator&tab=account">Social Accounts</a>.
    <?php
    }

    public static function textbox( $args ) {
        global $wp;
        extract( $args );

        $url = Statistinator::AUTH_URL . "/$slug.php";
        $checked = "";
        $value = "";

        //No Authentication
        $option = get_option( 'statistinator_authentication', array() );
        if ( _isset( $_GET['clear'] ) && $_GET['clear'] == $slug ) {
            $option[ $slug ] = '';
            update_option( 'statistinator_authentication', $option );
            unset( $_GET['clear'] );
        }

        //On Authentication
        if ( _isset( $_GET["{$slug}_token"] ) && !_isset( $_GET['settings-updated'] ) ) {
            $status = $_GET["{$slug}_token"];
            if ( $status == 'ok' ) {
                //Store immediately 
                $option[ $slug ] = 'ok';
                update_option( 'statistinator_authentication', $option );
            }
        }

        //After Authentication
        if ( _isset( $option[ $slug ] ) ) {
            $url = "?page=statistinator&tab=authentication&clear=$slug";
            $checked = 'checked="checked"';
            $value = $option[ $slug ];
        }
    ?>
        <div class="checkbox-switch">
            <input type="checkbox" <?= $checked ?> onclick="window.location.assign('<?= $url ?>')" class="input-checkbox" />
            <div class="checkbox-animate"></div>
        </div>
        <input type='hidden' name='<?= $field ?>' value='<?= $value ?>' />
    <?php
    }


}
