<?php
/*
Plugin Name: Maintenance Mode Lite
Description: Simple maintenance mode plugin with toggle and custom message. Non-logged-in users see the maintenance message, while admins have full access.
Version: 1.0
Author: Collins Elenwo
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings menu
 */
function mml_add_admin_menu()
{
    add_options_page(
        'Maintenance Mode Lite',
        'Maintenance Mode Lite',
        'manage_options',
        'maintenance-mode-lite',
        'mml_settings_page'
    );
}
add_action('admin_menu', 'mml_add_admin_menu');

/**
 * Register settings
 */
function mml_register_settings()
{
    register_setting('mml_settings_group', 'mml_enabled');
    register_setting('mml_settings_group', 'mml_message');
}
add_action('admin_init', 'mml_register_settings');

/**
 * Settings page markup
 */
function mml_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Maintenance Mode Lite</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mml_settings_group'); ?>
            <?php do_settings_sections('mml_settings_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Maintenance Mode</th>
                    <td>
                        <input type="checkbox" name="mml_enabled" value="1" <?php checked(get_option('mml_enabled'), 1); ?> />
                        <label for="mml_enabled">Turn on Maintenance Mode</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom Message</th>
                    <td>
                        <textarea name="mml_message" rows="5" cols="50"
                            placeholder="We are currently undergoing maintenance. Please check back later."><?php echo esc_textarea(get_option('mml_message')); ?></textarea>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Display maintenance message for non-logged-in users
 */
function mml_maintenance_mode()
{
    if (get_option('mml_enabled') && !current_user_can('manage_options') && !is_user_logged_in()) {
        wp_die(
            wpautop(esc_html(get_option('mml_message', 'We are currently undergoing maintenance. Please check back later.'))),
            'Maintenance Mode'
        );
    }
}
add_action('template_redirect', 'mml_maintenance_mode');
