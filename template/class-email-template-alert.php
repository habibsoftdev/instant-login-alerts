<?php  

class Email_Template_Alert{

function custom_email_template($user_login, $loginTime, $user_ip, $user_location ){

  $email_html = '';

  $email_html .= <<<EOD


  <!doctype html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>Login Alert </title>
  
      <style>
        <?php echo file_get_contents(plugins_url('assets/css/email.css', __FILE__ )); ?>
      </style>
    </head>
    <body>
      <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
          <td>&nbsp;</td>
          <td class="container">
            <div class="content">
  
              <!-- START CENTERED WHITE CONTAINER -->
              <table role="presentation" class="main">
  
                <!-- START MAIN CONTENT AREA -->
                <tr>
                  <td class="wrapper">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td>
                          <p>Hi {$user_login}!</p>
                          <p>A login to your account was detected at {$loginTime} from the following details:</p>
                          <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                          <p>IP Address : {$user_ip}</p>
                          <p>Location   : {$user_location} </p>
                          </table>
                          <p>If you recognize this login and it was you who logged in, you can disregard this alert.</p>
                          <p>Thank You</p>
                          <p>WP Instant Login Alert</p>
                          <p></p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
  
              <!-- END MAIN CONTENT AREA -->
              </table>
              <!-- END CENTERED WHITE CONTAINER -->
  
              <!-- START FOOTER -->
              <div class="footer">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
  
                  <tr>
                    <td class="content-block powered-by">
                      Powered by <a href="https://giantwpsolutions.com">WP Instant Login Alert </a>.
                    </td>
                  </tr>
                </table>
              </div>
              <!-- END FOOTER -->
  
            </div>
          </td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </body>
  </html>

  EOD;

  echo $email_html;
}
}