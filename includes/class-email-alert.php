<?php  

//includes/class-email-alert.php


class Email_Alert_Handler {


    public function send_login_alert_email($user_login, $user_ip, $user_location, $recipient_email_address, $loginTime) {



        // Get the recipient's email address
        $recipient_email = $recipient_email_address; 
        // Subject of the email
        $subject = __('Administrator Login Alert', 'instant-login-alerts');
     
        // Compose the email message
        $message = 'Administrator ' . $user_login . ' has logged into the WordPress admin area.' . "\n";
        $message .= 'IP Address: ' . $user_ip . "\n";
        $message .= 'Location: ' . $user_location->city . ', ' . $user_location->region . ', ' . $user_location->country . "\n";

        // Additional headers 
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',

        );

        // Send the email
        $sent = wp_mail($recipient_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($sent) {
        // Log or handle the successful email sending

            error_log('Login alert email sent successfully.');
        } else {
        // Handle the case where the email could not be sent
            error_log('Login alert email failed to send.');
        }
    }

    public function send_login_alert_email_without_location($user_login, $user_ip,  $recipient_email_address, $loginTime) {

        // Get the recipient's email address
        $recipient_email = $recipient_email_address; 
        // Subject of the email
        $subject = __('Administrator Login Alert', 'instant-login-alerts');
   
        // Compose the email message
        $message = 'Administrator ' . $user_login . ' has logged into the WordPress admin area.' . "\n";
        $message .= 'IP Address: ' . $user_ip . "\n";

        // Additional headers (optional)
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',

        );

        // Send the email
        $sent = wp_mail($recipient_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($sent) {
        // Log or handle the successful email sending 
            error_log('Login alert email sent successfully.');
        } else {
        // Handle the case where the email could not be sent 
            error_log('Login alert email failed to send.');
        }
    }



    public function new_user_create_alert_email($user_login, $user_ip, $user_location, $recipient_email_address, $loginTime, $user){

        
        // Get the recipient's email address
        $recipient_email = $recipient_email_address;
        // Subject of the email
        $subject = __('New Administrator User Created', 'instant-login-alerts');
        
        
        // Compose the email message
        $message =  sprintf(
            __('A new administrator user (%s) has been created in WordPress. Email Used (%s) ', 'instant-login-alerts'),
            $user->user_login, $user->user_email
        );

        $message .= 'IP Address: ' . $user_ip . "\n";
        $message .= 'Location: ' . $user_location->city . ', ' . $user_location->region . ', ' . $user_location->country . "\n";

        // Additional headers 
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',

        );

        // Send the email
        $sent = wp_mail($recipient_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($sent) {
        // Log or handle the successful email sending
            
            error_log('Login alert email sent successfully.');
        } else {
            // Handle the case where the email could not be sent 
            error_log('Login alert email failed to send.');
        }
    }

     public function new_user_create_alert_email_without_location($user_login, $user_ip, $recipient_email_address, $loginTime, $user){

        
        // Get the recipient's email address 
        $recipient_email = $recipient_email_address; 
        // Subject of the email
        $subject = __('New Administrator User Created', 'instant-login-alerts');
        
        
        // Compose the email message
        $message =  sprintf(
            __('A new administrator user (%s) has been created in WordPress. Email Used (%s)', 'instant-login-alerts'),
            $user->user_login, $user->user_email
        );

        $message .= 'IP Address: ' . $user_ip . "\n";

        // Additional headers 
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',

        );

        // Send the email
        $sent = wp_mail($recipient_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($sent) {
            // Log or handle the successful email sending         
            error_log('Login alert email sent successfully.');
        } else {
            // Handle the case where the email could not be sent 
            error_log('Login alert email failed to send.');
        }
    }


   
}