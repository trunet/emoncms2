<?php 
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
function feed_controller()
{

  if (!$_SESSION['valid']) return "Sorry, you must be logged in to see this page";

  require "Models/input_model.php";
  require "Models/feed_model.php";

  $userid = $_SESSION['userid'];

  $apikey = get_apikey($userid);

  if ($_POST["form"] == "newapi" || !$apikey)
  { 
    $apikey = md5(uniqid(rand(), true));
    set_apikey($userid, $apikey);
  }

  if ($_POST["form"] == "input")
  { 
    $inputid = intval($_POST["id"]);
    $processlist = get_input_processlist_desc($inputid);
  }

  if ($_POST["form"] == "process")
  { 
    $inputid = intval($_POST["id"]);
    $processType = intval($_POST["sel"]);			// get process type
    $arg = $_POST["arg"];
    if ($processType==2) $arg = floatval($arg); 
    if ($processType==3) $arg = floatval($arg); 
    if ($processType==6) $arg = get_input_id($userid,$arg);
    if ($processType == 1 || $processType == 4 || $processType == 5 || $processType == 7 || $processType == 8 || $processType == 9 )
    {
      $id = get_feed_id($userid,$arg);
      if ($id==0)  $id = create_feed($userid,$arg);
      $arg = $id;
    }
    add_input_process($inputid,$processType,$arg);
    $processlist = get_input_processlist_desc($inputid);
  }

  $inputs = get_user_inputs($userid);
  $feeds = get_user_feeds($userid);

  // Render view
  $content = view("feed_view.php",array('apikey' => $apikey, 'inputs' => $inputs, 'inputsel' => $inputid, 'feeds' => $feeds, 'processlist' => $processlist));

  return $content;
}

?>


