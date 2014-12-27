<?php
/*
Plugin Name: Custom Menu for Logged and not Logged Users
Description: Come across situations where you want to show different navigation menus to logged in and logged out users..
Version: 2.4.7
Author: James Jara
Author URI:  https://jamesjara.com/
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
function setup( ) {
    add_options_page( 'Custom Menu', 'Custom Menu', 'manage_options', 'especialmenujara', 'adminpage' );
    add_action( 'admin_init', 'register_settings' );
}
function register_settings( ) {
    register_setting( 'especialmenujara-settings-group', 'loggedin_menu' );
    register_setting( 'especialmenujara-settings-group', 'loggedout_menu' );
}
add_action( 'admin_menu', 'setup' );
function nav_menu_drop_down( $name, $selected = '', $print = TRUE ) {
    // array of menu objects
    $menus = wp_get_nav_menus();
    $out   = '';
    // No menu found.
    if ( empty( $menus ) or is_a( $menus, 'WP_Error' ) ) {
        // Give some feedback …
        $out .= __( 'There are no menus.', 't5_nav_menu_per_post' );
        // … and make it usable …
        if ( current_user_can( 'edit_theme_options' ) ) {
            $out .= sprintf( __( ' <a href="%s">Create one</a>.', 't5_nav_menu_per_post' ), admin_url( 'nav-menus.php' ) );
        }
        // … and stop.
        $print and print $out;
        return $out;
    }
    // Set name and ID to let you use a <label for='id_$name'>
    $out = "<select name='$name' id='id_$name'>\n";
    foreach ( $menus as $menu ) {
        // Preselect the active menu
        $active = $selected == $menu->slug ? 'selected' : '';
        // Show the description
        $title  = empty( $menu->description ) ? '' : esc_attr( $menu->description );
        $out .= "\t<option value='$menu->slug' $active $title>$menu->name</option>\n";
    }
    $out .= '</select>';
    $print and print $out;
    return $out;
}
function adminpage( )
{
?>
<div class="wrap">
<h2>Custom Menu for Logged and not Logged Users</h2>
<h3>Custom Menu Options</h3>
<form method="post" action="options.php">
<?php settings_fields( 'especialmenujara-settings-group' ); ?>
   <table class="form-table">
        <tr valign="top">
        <th scope="row">Logged Out menu</th>
        <td><?php nav_menu_drop_down( 'loggedout_menu', get_option( 'loggedout_menu' ), true ); ?></td>
        </tr>
        <tr valign="top">
        <th scope="row">Logged In menu</th>
        <td><?php nav_menu_drop_down( 'loggedin_menu', get_option( 'loggedin_menu' ), true ); ?></td>
        </tr>          
    </table>    
    <?php submit_button(); ?>
</form>  
</div>
<?php
}
function my_wp_nav_menu_args( $args = '' )
{
    if ( is_user_logged_in() ) {
        $args['menu'] = get_option( 'loggedin_menu' );
    } else {
        $args['menu'] = get_option( 'loggedout_menu' );
    }
    return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );