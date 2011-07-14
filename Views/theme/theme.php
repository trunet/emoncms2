<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

  <!--
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  -->

<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="<?php print $GLOBALS['path']; ?>Views/theme/style.css" />
  <title>emoncms</title>

  </head>
  <body>
  <div id="bound">
    
   <div id="header" style="height:75px; padding:20px;">
        <div style="float:left; padding-top:20px;">
        <img src="<?php print $GLOBALS['path']; ?>Views/theme/emoncmslogo.png" width="300px">
        </div>
    <div style="float:right; "><?php echo $user; ?></div>
   </div>

   <div id='maintext'>
   <?php print $menu; ?>
   </div>

   <div id='maintext'>

   <?php print $content; ?>

      </div>
      <div id="footer"></div>
      </div>
    </div>

 </body>

</html>
