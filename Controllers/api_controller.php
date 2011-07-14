<?php 
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
function api_controller()
{
  require "Models/input_model.php";
  require "Models/feed_model.php";
  require "Models/process_model.php";
  require "Controllers/api_component.php";
 
  $args = $GLOBALS['args'];

  $userid = get_apikey_user($_GET['apikey']);			// basic apikey authentication
  if ($userid==0) { echo "valid apikey required"; die; }

  if ($args[1] == 'inputs') $data = get_user_inputs($userid);
  if ($args[1] == 'feeds') $data = get_user_feeds($userid);

  if ($args[1] == 'getfeed')
  {
    $feedid = $_GET['feedid'];
    $start = $_GET['start'];
    $end = $_GET['end'];
    $resolution = $_GET['resolution'];
    $data = get_feed_data($feedid,$start,$end,$resolution);
  }

  if ($args[1] == 'post')
  {
    $json = $_GET['json'];
    //db_query("INSERT INTO dump (text) VALUES ('$json')");

    $datapairs = validate_json($json);				// get and validate json

    $time = time();
    if (isset($_GET["time"])) $time = intval($_GET["time"]);	// use sent timestamp if present

    $inputs = register_inputs($userid,$datapairs,$time);
    process_inputs($inputs,$time);

    $data = "ok";
  }

  if ($args[1] == 'fetch')
  {
    $url = $_GET['url'];
    $json = file_get_contents($url);
    $datapairs = validate_json_cat($json);				// get and validate json

    $time = time();
    if (isset($_GET["time"])) $time = intval($_GET["time"]);	// use sent timestamp if present

    $inputs = register_inputs($userid,$datapairs,$time);
    process_inputs($inputs,$time);

    $data = "ok";
  }

  return json_encode($data);
}

?>


