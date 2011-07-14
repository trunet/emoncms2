<!--
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
-->
<form action="<?php echo $GLOBALS['path']; ?>" method="post">
  <input type="hidden" name="form" value="logout"/>
<span style="margin-right:20px;">Welcome! <b><?php echo $name; ?></b></span><input type="submit" value="Logout" style="float:right;"/>
</form>
