<!--
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
-->
<h2>Register</h2>
     
<form action="<?php echo $GLOBALS['path']; ?>user/register" method="post">
  <input type="hidden" name="form" value="register"/>

  <table>
  <tr><td>Username:</td><td><input type="text" name="username" /></td></tr>
  <tr><td>Password:</td><td><input type="password" name="pass1" /></td></tr>
  <tr><td>once again:</td><td><input type="password" name="pass2" /></td></tr>
  <tr><td></td><td><input type="submit" value="register" /></td></tr>
  </table>

</form>
