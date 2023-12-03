<?php 

/*
Plugin Name: WP Instant Login Alerts 
Plugin URI: https://giantwpsolutions.com
Description: Enhance your WordPress website's security with WP Login Alerts. This essential plugin provides you with real-time email notifications whenever someone logs into your WP-admin area. Stay informed and vigilant against unauthorized access to your website, giving you the peace of mind you deserve. With WP Login Alerts, you're always in control of your WordPress security.
Version: 1.1.0
Requires at least: 5.3
Author: Habibur Rahman
Author URI: https://habibr.me 
License: GPLv2 or later
Text Domain: wp-instant-login-alerts
Domain Path: /languages/
*/

class WPLoginAlerts{

    private $ipinfo_handler;
    private $email_alert_handler;

    public function __construct(){

        //Initialize IPInfo and Email Handler Class

        require_once plugin_dir_path(__FILE__).'includes/class-ipinfo-handler.php';
        require_once plugin_dir_path(__FILE__).'includes/class-email-alert.php';

        $token = get_option('user_loc');
        $this->email_alert_handler = new Email_Alert_Handler();
        $this->ipinfo_handler = new IPInfo_Handler($token);



        //Add Hook and Actions
        

        add_action('plugin_loaded', array($this, 'wpila_plugin_bootstraping'));

        add_action('activated_plugin', array($this, 'wpila_plugin_redirect_settings'));

        add_action('admin_menu', array($this, 'wpila_add_submenu_page'));

        add_action('admin_enqueue_scripts', array($this, 'wpila_plugin_asset_register'));
        
        add_action('wp_login', array($this, 'notify_admin_on_login'), 99, 2);

        add_action('user_register', array($this, 'notify_on_new_admin_user'));

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'wpila_plugin_actions'));
       
       
    }

     // Plugin Text Domain Loading

    public function wpila_plugin_bootstraping(){

        load_plugin_textdomain('wp-instant-login-alerts', false, plugin_dir_path(__FILE__).'/languages');
    }

    //Plugin Redirect to settings and action link

    public function wpila_plugin_redirect_settings($plugin){

        if(plugin_basename(__FILE__) == $plugin){
            wp_redirect(admin_url('tools.php?page=wpila_settings'));
            die();
        }
    }

    public function wpila_plugin_actions($links){
    
        $links[] = sprintf("<a href='%s'> %s </a>", admin_url('tools.php?page=wpila_settings'), __('Settings', 'wp-instant-login-alerts' ));
        return $links;
    }



    // Plugin Asset Register callback function 

    public function wpila_plugin_asset_register(){

    // Enqueue the JavaScript file

        wp_enqueue_script('wplia-js', plugin_dir_url(__FILE__).'/assets/js/main.js', array('jquery'), time(), true);

    // Enqueue the CSS file

        wp_enqueue_style('wpila-css', plugin_dir_url(__FILE__).'/assets/css/style.css', array(), '1.0');
        wp_enqueue_style('wpila-email-css', plugin_dir_url(__FILE__).'/assets/css/email.css', array(), '1.0');


    }

    // Callback function to add the submenu

    public function wpila_add_submenu_page(){

        add_submenu_page('tools.php', 
            'WP Login Alerts Settings', 
            'Instant Login Alerts',
            'manage_options', 
            'wpila_settings',
            array($this, 'wpila_settings_page') );
    }



    public function wpila_settings_page(){

      // Process and save the form data here
    

        if(isset($_POST['submit'])){



            $alert_on_create_user = isset($_POST['alert_on_new_user'] ) ? 1 : 0;

            update_option('alert_on_create_user', $alert_on_create_user);

            $alert_user_login_admin_email = isset($_POST['wpila_admin_email']) ? 1 : 0;

            update_option('alert_user_login_admin_email', $alert_user_login_admin_email);

            $alert_other_email_confirmation = isset($_POST['wpila_other_email']) ? 1 : 0;

            update_option('alert_other_email_confirmation', $alert_other_email_confirmation);

            $alert_user_login_other_email = isset($_POST['alert_other_email']) ? $_POST['alert_other_email'] : '';

            update_option('alert_other_email', $alert_user_login_other_email); 
            
            if($alert_other_email_confirmation === 0 ){
                delete_option('alert_other_email');
            }

            $user_location_token = isset($_POST['user_loc']) ? $_POST['user_loc'] : '';

            update_option('user_loc', $user_location_token);



        }


        // Retrieve saved options

        $alert_on_new_user = get_option('alert_on_create_user');

        $alert_login_admin_email  = get_option('alert_user_login_admin_email');

        $alert_other_confirmation = get_option('alert_other_email_confirmation');

        $alert_other_email_address = get_option('alert_other_email');

        $user_location_token_api = get_option('user_loc');



        // Checkbox Checked on save

        $checked = '';
        $checked2 = '';
        $checked3 = '';
        $class_disable = '';

        if($alert_on_new_user == 1){
            $checked = 'checked';
        }
        if($alert_login_admin_email == 1){
            $checked2 = 'checked';
        }

        if($alert_other_confirmation == 1){
            $checked3 = 'checked';
            
        }
        



    // Display Section One of the form
        ?>

        <div class="wrap">
      
            <h2>WP Login Instant Alert Settings</h2>

            <section>
                <form method="post" action="<?php echo esc_url(admin_url('tools.php?page=wpila_settings')); ?>">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('Alert on New Administrative User Create: ', 'wp-instant-login-alerts' ); ?>
                            </th>
                            <td>
                                <input type="checkbox" name="alert_on_new_user" id="alert_on_new_user" value="1" <?php echo esc_attr($checked); ?> />
                            </td>  
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <?php _e('Alert Email', 'wp-instant-login-alerts'); ?>
                            </th>
                            <td>
                             <label for="wpila_admin_email">
                                <input type="checkbox" value="1" name="wpila_admin_email" id="wpila_admin_email"  <?php echo esc_attr($checked2); ?>> <?php _e('Admin Email', 'wp-instant-login-alerts'); ?> 
                            </label> <br>
                            <label for="wpila_other_email"><input type="checkbox" value="1" name="wpila_other_email" id="wpila_other_email" <?php echo esc_attr($checked3); ?>> <?php _e('Others Email', 'wp-instant-login-alerts'); ?></label>
                        </td>
                    </tr>
                </table>
                
                <div class="wpila-other">
                    <table class="form-table">
                        <tr valign="top" >
                            <th scope="row"><?php _e('Your Alert Email Address ', 'wp-instant-login-alerts' ); ?> </th>
                            <td>
                                <input type="email" name="alert_other_email" id="alert_other_email" value="<?php echo $alert_other_email_address ; ?>"  />
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="wpila">
                    <table class="form-table">
                        <tr valign="top" >
                            <th scope="row"><?php _e('Get User Location Info', 'wp-instant-login-alerts' ); ?> </th>
                            <td>
                                <input type="user_loc" name="user_loc" id="user_loc" value="<?php echo  $user_location_token_api; ?>"  />
                            </td>
                        </tr>
                    </table>
                    <p class="font-italic"> <?php _e("To Get Location Info insert your <a target='_blank' src='https://ipinfo.io/account/token'> IPInfo</a> Token (It's Free)", "wp-instant-login-alerts");?></p>
                </div>

                
                <?php submit_button("Save Changes", 'primary', 'submit'); ?>

            </form>
        </section>
    </div>
    <?php    


}




    public function notify_admin_on_login(){

        $user = wp_get_current_user();

        $user_login = $user->user_login;

        $recipient_email = get_option('alert_other_email');

        $recipient_admin_email = get_option('admin_email');

        $loginTime = time();

        $user_ip =  $_SERVER['REMOTE_ADDR'];

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


    

    public function notify_on_new_admin_user($user_id){
        
        $current_user = wp_get_current_user();

        $user_login = $current_user->user_login;

        $loginTime = time();

        $user_ip =  $_SERVER['REMOTE_ADDR'];

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
new WPLoginAlerts();


