<?php  

//includes/class-email-alert.php


class Email_Alert_Handler {


    public function send_login_alert_email($user_login, $user_ip, $user_location, $recipient_email, $loginTime) {



        // Get the recipient's email address (you can customize this)
        $recipient_email = $recipient_email; // Change this to the desired recipient email address

        // Subject of the email
        $subject = __('Administrator Login Alert', 'wp-instant-login-alerts');

        // Compose the email message

        
 // Compose the email message
        $message = 'Administrator ' . $user_login . ' has logged into the WordPress admin area.' . "\n";
        $message .= 'IP Address: ' . $user_ip . "\n";
        $message .= 'Location: ' . $user_location->city . ', ' . $user_location->region . ', ' . $user_location->country . "\n";

        // Additional headers (optional)
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',

        );

        // Send the email
        $sent = wp_mail($recipient_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($sent) {
            // Log or handle the successful email sending (e.g., store in a log file)
            // Optionally, you can also add a success message for debugging or logging purposes.
            error_log('Login alert email sent successfully.');
        } else {
            // Handle the case where the email could not be sent (e.g., log the error)
            error_log('Login alert email failed to send.');
        }
    }


    public function new_user_create_alert_email($user_login, $user_ip, $user_location, $recipient_email, $loginTime, $user){

        
        // Get the recipient's email address (you can customize this)
        $recipient_email = $recipient_email; // Change this to the desired recipient email address

        // Subject of the email
        $subject = __('New Administrator User Created', 'wp-instant-login-alerts');
        
        
 // Compose the email message
        $message =  sprintf(
            __('A new administrator user (%s) has been created in WordPress. Email Used (%s)', 'wp-instant-login-alerts'),
            $user->user_login, $user->user_email
        );

        $message .= 'IP Address: ' . $user_ip . "\n";
        $message .= 'Location: ' . $user_location->city . ', ' . $user_location->region . ', ' . $user_location->country . "\n";

        // Additional headers (optional)
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',

        );

        // Send the email
        $sent = wp_mail($recipient_email, $subject, $message, $headers);

        // Check if the email was sent successfully
        if ($sent) {
            // Log or handle the successful email sending (e.g., store in a log file)
            // Optionally, you can also add a success message for debugging or logging purposes.
            error_log('Login alert email sent successfully.');
        } else {
            // Handle the case where the email could not be sent (e.g., log the error)
            error_log('Login alert email failed to send.');
        }
    }
}