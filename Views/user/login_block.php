<!--
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
-->
<div style="text-align:right">
<form action="<?php echo $GLOBALS['path']; ?>" method="post">
  <input type="hidden" name="form" value="login"/>
  <table>
  <tr><td>Username:</td><td><input type="text" name="username" style="width:125px"/></td></tr>
  <tr><td>Password:</td><td><input type="password" name="password" style="width:125px"/></td></tr>
  <tr><td></td><td><input type="submit" value="Login" /> or <a href="<?php echo $GLOBALS['path']; ?>user/register">register</a></td></tr>
  </table>
  <?php echo $error; ?>
</form>
</div>

