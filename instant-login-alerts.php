<?php
/*
Plugin Name: Instant Login Alerts 
Plugin URI: https://giantwpsolutions.com
Description: Enhance your WordPress website's security with Instant Login Alerts. This essential plugin provides you with real-time email notifications whenever someone logs into your WP-admin area. Stay informed and vigilant against unauthorized access to your website, giving you the peace of mind you deserve. With Instant Login Alerts, you're always in control of your WordPress security.
Version: 1.1.0
Requires at least: 5.3
Author: Habibur Rahman
Author URI: https://habibr.me 
License: GPLv2 or later
Text Domain: instant-login-alerts
Domain Path: /languages/
*/

// Avoid direct access to plugin file
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class InstantLoginAlerts {

    // Constants for option names
    const OPTION_NEW_USER_ALERT = 'alert_on_create_user';
    const OPTION_LOGIN_ADMIN_EMAIL = 'alert_user_login_admin_email';
    const OPTION_OTHER_EMAIL_CONFIRMATION = 'alert_other_email_confirmation';
    const OPTION_OTHER_EMAIL = 'alert_other_email';
    const OPTION_USER_LOC = 'user_loc';

    private $ipinfo_handler;
    private $email_alert_handler;

    /**
     * Constructor.
     */
    public function __construct() {
        // Initialize IPInfo and Email Handler Class
        require_once plugin_dir_path(__FILE__) . 'includes/class-ipinfo-handler.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-email-alert.php';

        $token = get_option(self::OPTION_USER_LOC);
        $this->email_alert_handler = new Email_Alert_Handler();
        $this->ipinfo_handler = new IPInfo_Handler($token);

        // Add hooks and actions
        add_action('plugin_loaded', array($this, 'wpila_plugin_bootstraping'));
        add_action('activated_plugin', array($this, 'wpila_plugin_redirect_settings'));
        add_action('admin_menu', array($this, 'wpila_add_submenu_page'));
        add_action('admin_enqueue_scripts', array($this, 'wpila_plugin_asset_register'));
        add_action('wp_login', array($this, 'notify_admin_on_login'), 99, 2);
        add_action('user_register', array($this, 'notify_on_new_admin_user'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wpila_plugin_actions'));
    }

    /**
     * Plugin Text Domain Loading
     */
    public function wpila_plugin_bootstraping() {
        load_plugin_textdomain('instant-login-alerts', false, plugin_dir_path(__FILE__) . '/languages');
    }

    /**
     * Plugin Redirect to settings and action link
     *
     * @param string $plugin
     */
    public function wpila_plugin_redirect_settings($plugin) {
        if (plugin_basename(__FILE__) == $plugin) {
            wp_redirect(admin_url('tools.php?page=wpila_settings'));
            die();
        }
    }

    /**
     * Add actions links to the plugin
     *
     * @param array $links
     * @return array
     */
    public function wpila_plugin_actions($links) {
        $links[] = sprintf("<a href='%s'> %s </a>", esc_url(admin_url('tools.php?page=wpila_settings')), esc_html__('Settings', 'instant-login-alerts'));
        return $links;
    }

    /**
     * Plugin Asset Register callback function
     */
    public function wpila_plugin_asset_register() {
        // Enqueue the JavaScript file
        wp_enqueue_script('wplia-js', plugin_dir_url(__FILE__) . '/assets/js/main.js', array('jquery'), '1.0', true);
        // Enqueue the CSS file
        wp_enqueue_style('wpila-css', plugin_dir_url(__FILE__) . '/assets/css/style.css', array(), '1.0');
        wp_enqueue_style('wpila-email-css', plugin_dir_url(__FILE__) . '/assets/css/email.css', array(), '1.0');
    }

    /**
     * Callback function to add the submenu
     */
    public function wpila_add_submenu_page() {
        add_submenu_page(
            'tools.php',
            'Instant Login Alerts Settings',
            'Instant Login Alerts',
            'manage_options',
            'wpila_settings',
            array($this, 'wpila_settings_page')
        );
    }

    /**
     * Display the settings page
     */
    public function wpila_settings_page() {
        if (isset($_POST['submit'])) {
            $alert_on_create_user = isset($_POST['alert_on_new_user']) ? 1 : 0;
            update_option(self::OPTION_NEW_USER_ALERT, $alert_on_create_user);

            $alert_user_login_admin_email = isset($_POST['wpila_admin_email']) ? 1 : 0;
            update_option(self::OPTION_LOGIN_ADMIN_EMAIL, $alert_user_login_admin_email);

            $alert_other_email_confirmation = isset($_POST['wpila_other_email']) ? 1 : 0;
            update_option(self::OPTION_OTHER_EMAIL_CONFIRMATION, $alert_other_email_confirmation);

            $alert_user_login_other_email = isset($_POST['alert_other_email']) ? sanitize_email($_POST['alert_other_email']) : '';
            update_option(self::OPTION_OTHER_EMAIL, $alert_user_login_other_email);

            if ($alert_other_email_confirmation === 0) {
                delete_option(self::OPTION_OTHER_EMAIL);
            }

            $user_location_token = isset($_POST['user_loc']) ? sanitize_text_field($_POST['user_loc']) : '';
            update_option(self::OPTION_USER_LOC, $user_location_token);
        }

        // Retrieve saved options
        $alert_on_new_user = get_option(self::OPTION_NEW_USER_ALERT);
        $alert_login_admin_email = get_option(self::OPTION_LOGIN_ADMIN_EMAIL);
        $alert_other_confirmation = get_option(self::OPTION_OTHER_EMAIL_CONFIRMATION);
        $alert_other_email_address = get_option(self::OPTION_OTHER_EMAIL);
        $user_location_token_api = get_option(self::OPTION_USER_LOC);

        // Checkbox Checked on save
        $checked = $alert_on_new_user ? 'checked' : '';
        $checked2 = $alert_login_admin_email ? 'checked' : '';
        $checked3 = $alert_other_confirmation ? 'checked' : '';

        // Display Section One of the form
        ?>
        <div class="wrap">
            <h2> <?php _e('Instant Login Alert Settings', 'instant-login-alerts'); ?></h2>
            <section>
                <form method="post" action="<?php echo esc_url(admin_url('tools.php?page=wpila_settings')); ?>">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('Alert on New Administrative User Create: ', 'instant-login-alerts'); ?>
                            </th>
                            <td>
                                <input type="checkbox" name="alert_on_new_user" id="alert_on_new_user" value="1" <?php echo esc_attr($checked); ?> />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('Alert Email', 'instant-login-alerts'); ?>
                            </th>
                            <td>
                                <label for="wpila_admin_email">
                                    <input type="checkbox" value="1" name="wpila_admin_email" id="wpila_admin_email" <?php echo esc_attr($checked2); ?>> <?php _e('Admin Email', 'instant-login-alerts'); ?>
                                </label> <br>
                                <label for="wpila_other_email"><input type="checkbox" value="1" name="wpila_other_email" id="wpila_other_email" <?php echo esc_attr($checked3); ?>> <?php _e('Others Email', 'instant-login-alerts'); ?></label>
                            </td>
                        </tr>
                    </table>

                    <div class="wpila-other">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Your Alert Email Address ', 'instant-login-alerts'); ?> </th>
                                <td>
                                    <input type="email" name="alert_other_email" id="alert_other_email" value="<?php echo esc_attr($alert_other_email_address); ?>" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="wpila">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Get User Location Info', 'instant-login-alerts'); ?> </th>
                                <td>
                                    <input type="user_loc" name="user_loc" id="user_loc" value="<?php echo esc_attr($user_location_token_api); ?>" />
                                </td>
                            </tr>
                        </table>
                        <p class="font-italic"> <?php _e("To Get Location Info insert your <a target='_blank' src='https://ipinfo.io/account/token'> IPInfo</a> Token (It's Free)", "instant-login-alerts"); ?></p>
                    </div>

                    <?php submit_button("Save Changes", 'primary', 'submit'); ?>

                </form>
            </section>
        </div>
        <?php
    }

    /**
     * Notify admin on user login
     */
    public function notify_admin_on_login() {

        $user = wp_get_current_user();
        $user_login = $user->user_login;
        $recipient_email = get_option('alert_other_email');
        $recipient_admin_email = get_option('admin_email');
        $loginTime = time();
        $user_ip =  filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        $token = get_option('user_loc');
        $user_location = $this->ipinfo_handler->get_location($user_ip);
        $alert_other_email_confirmation = get_option('alert_other_email_confirmation');
        $alert_login_admin_email = get_option('alert_user_login_admin_email');

            if($alert_login_admin_email== 1){

                if(!empty($token)){
                    $this->email_alert_handler->send_login_alert_email($user_login, $user_ip, $user_location, $recipient_admin_email, $loginTime);}else{

                    $this->email_alert_handler->send_login_alert_email_without_location($user_login, $user_ip,  $recipient_admin_email, $loginTime);}
            }

            if($alert_other_email_confirmation == 1 && !empty($recipient_email)){
                if(!empty($token)){
                    $this->email_alert_handler->send_login_alert_email($user_login, $user_ip, $user_location, $recipient_email, $loginTime); } else{

                    $this->email_alert_handler->send_login_alert_email_without_location($user_login, $user_ip, $recipient_email, $loginTime);
                }
            }
    }

    /**
     * Notify on new admin user registration
     *
     * @param int $user_id
     */
    public function notify_on_new_admin_user($user_id) {
        $current_user = wp_get_current_user();
        $user_login = $current_user->user_login;
        $loginTime = time();
        $user_ip =  filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        $token = get_option('user_loc');
        $user_location = $this->ipinfo_handler->get_location($user_ip);
        $user = get_userdata($user_id);
        $alert_on_new_user = get_option('alert_on_create_user');
        $recipient_email = get_option('alert_other_email');
        $recipient_admin_email = get_option('admin_email');
        $alert_other_email_confirmation = get_option('alert_other_email_confirmation');


            if ($alert_on_new_user == 1 && in_array('administrator', $user->roles)) {

                if(!empty($token)){
                    $this->email_alert_handler->new_user_create_alert_email($user_login, $user_ip, $user_location, $recipient_admin_email, $loginTime, $user);}else{

                    $this->email_alert_handler->new_user_create_alert_email_without_location($user_login, $user_ip, $recipient_admin_email, $loginTime, $user);
                }

            }

            if ($alert_on_new_user == 1 && $alert_other_email_confirmation == 1 && !empty($recipient_email) && in_array('administrator', $user->roles)) {

                if(!empty($token)){
                    $this->email_alert_handler->new_user_create_alert_email($user_login, $user_ip, $user_location, $recipient_email, $loginTime, $user);}else{

                    $this->email_alert_handler->new_user_create_alert_email_without_location($user_login, $user_ip, $recipient_email, $loginTime, $user);
                }
            }
    }
}

// Instantiate the plugin class
new InstantLoginAlerts();

