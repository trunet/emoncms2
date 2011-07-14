<?php
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  function user_block_controller()
  {
    $form = $_POST['form'];

    if ($form == 'login')
    {
      $username = db_real_escape_string($_POST['username']);
      $password = db_real_escape_string($_POST['password']);
      $result = user_logon($username,$password);
      if ($result == 0) $error = "Invalid username or password";
    }

    if ($form == 'logout') user_logout();

    if ($_SESSION['valid']) {
      $name = get_user_name($_SESSION['userid']);
      $content = view("user/account_block.php", array('name' => $name));
    }

    if (!$_SESSION['valid']) $content = view("user/login_block.php", array('error'=>$error));

    return $content;
  }

?>
