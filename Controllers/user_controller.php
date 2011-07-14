<?php
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  function user_controller()
  {
    $node = $GLOBALS['args'][1];
    $form = $_POST['form'];

    // Note: form results for login is handled by the login block controller
    if ($node == 'login') $content = view("user/login.php", array());

    if ($node == 'register') 
    {
      $show_register_box = 1;

      if ($form == 'register')
      {
        $username = db_real_escape_string($_POST['username']);
        $pass1 = db_real_escape_string($_POST['pass1']);
        $pass2 = db_real_escape_string($_POST['pass2']);

        $error = '';
        if (get_user_id($username)!=0) $error .= "Username already exists<br/>";
        if ($pass1 != $pass2) $error .= "Passwords do not match<br/>";
        if (strlen($username) < 4 || strlen($username) > 30) $error .= "Username must be between 4 and 30 characters long<br/>";
        if (strlen($pass1) < 4 || strlen($pass1) > 30) $error .= "Passwords must be between 4 and 30 characters long<br/>";

        $content = $error;
        if (!$error) {
          create_user($username,$pass1); 
          $content = "<h2>Confirmation</h2><p>You are now registered</p>";
          $show_register_box = 0;
        }
      }

      if ( $show_register_box == 1 ) $content .= view("user/register.php", array());
    }

    return $content;
  }

?>
