<?php 
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  function menu_block_controller()
  {
    $path = $GLOBALS['path'];
    $menu = '';

    if ($_SESSION['valid'])
    {
      $menu .= "<a href='".$path."home'>Home</a> | ";
      $menu .= "<a href='".$path."feed'>Feeds</a>";
    } 
    else 
    {
      $menu .= "<a href='".$path."home'>Home</a>";
    }

    return $menu;
  }


?>
