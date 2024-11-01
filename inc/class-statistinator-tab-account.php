<?php 

class Statistinator_Tab_Account {

    public function __construct() {
    }

    public function render() {
?>
        <form action="options.php" method="post">
            <?php settings_fields( "statistinator_account" ); ?>
            <?php do_settings_sections( "statistinator_account" ); ?>

            <?php submit_button(); ?>
        </form>
<?php
    }

    public static function description() {
    ?>
        <p>Here you can fill in the specific Accounts from which data will be gathered.</p>
    <?php
    }

    public static function textbox( $args ) {
        extract( $args );

        $status = get_option( "statistinator_status" );

        $value = get_option( "statistinator_account", array() );
        $value = ( _isset( $value[ $slug ] ) ? $value[ $slug ] : '' );

        if ( $status[ $slug ] == 'initial' ) {
            echo "Not Enabled";
            return;
        }

        switch ( $slug ) {
        case 'google':
            $stages = array( 
                'account'	=> 'Account',
                'property'	=> 'Property',
                'view'		=> 'View'
            );
            if ( empty( $value ) ) {
                $value = array(
                    'account'	=> '',
                    'account_name'	=> '',
                    'property'	=> '',
                    'property_name'	=> '',
                    'view'		=> '',
                    'view_name'		=> ''
                );
            }

            echo "<p class='description'>Select your Website from your Google Analytics.</p>";
            echo "<table>";
            foreach ( $stages as $stage => $title ) {
                echo "<tr>";
                echo "<td>$title</td>";
                echo "<td><select class='set_name' id='statistinator_account_{$slug}_$stage' name='{$field}[$stage]' ><option value='{$value[ $stage ]}'>{$value[ $stage . '_name' ]}</option></select>"; 
                echo "<input type='hidden' class='get_name' id='statistinator_account_{$slug}_{$stage}_name' name='{$field}[{$stage}_name]' value='{$value[ $stage . '_name' ]}' />";
                echo "</td></tr>";
            }

            echo "</table>";
            break;

        case 'facebook':
            if ( empty( $value ) ) {
                $value = array(
                    'id'	=> '',
                    'name'	=> '',
                );
            }
            echo "<input type='text' id='statistinator_account_{$slug}_name' name='{$field}[name]' value='{$value['name']}' />";
            echo "<input type='hidden' id='statistinator_account_{$slug}_id' name='{$field}[id]' value='{$value['id']}' />";
            echo "<p class='description'>Enter your Facebook Page and select it from the Search Results</p>";
            break;

        case 'twitter':
            echo "<input type='text' id='statistinator_account_{$slug}' name='$field' value='$value' />";
            echo "<p class='description'>Enter your Twitter Screen Name (eg. @wordpress)</p>";
            break;

        case 'mailchimp':
            if ( empty( $value ) ) {
                $value = array(
                    'id'	=> '',
                    'name'	=> '',
                );
            }
            echo "<select class='set_name' id='statistinator_account_{$slug}_id' name='{$field}[id]' ><option value='{$value['id']}'>{$value['name']}</option></select>";
            echo "<input type='hidden' class='get_name' id='statistinator_account_{$slug}_name' name='{$field}[name]' value='{$value['name']}' />";
            echo "<p class='description'>Select your Mailchimp Mailing List</p>";
            break;

        case 'youtube':
            if ( empty( $value ) ) {
                $value = array(
                    'id'	=> '',
                    'name'	=> '',
                );
            }
            echo "<select class='set_name' id='statistinator_account_{$slug}_id' name='{$field}[id]' ><option value='{$value['id']}'>{$value['name']}</option></select>";
            echo "<input type='hidden' class='get_name' id='statistinator_account_{$slug}_name' name='{$field}[name]' value='{$value['name']}' />";
            echo "<p class='description'>Select your YouTube Channel</p>";
            break;

        }
    }
}
