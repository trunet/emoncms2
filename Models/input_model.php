<?php
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  function create_input($user,$name)
  {
    db_query("INSERT INTO input (userid,name) VALUES ('$user','$name')");
  }

  function create_input_timevalue($user,$name,$time,$value)
  {
    db_query("INSERT INTO input (userid,name,time,value) VALUES ('$user','$name','$time','$value')");
  }

  function set_input_timevalue($id, $time, $value)
  {
    $time = date("Y-n-j H:i:s", $time);    
    db_query("UPDATE input SET time='$time', value = '$value' WHERE id = '$id'");
  }

  function set_input_processlist($id,$processlist) {
      $result = db_query("UPDATE input SET processList = '$processlist' WHERE id='$id'");
  }

  function add_input_process($id,$type,$arg)
  {
    $list = get_input_processlist($id);
    if ($list) $list .=',';
    $list .= $type.':'.$arg;
    set_input_processlist($id,$list);
  }

  function get_user_inputs($userid)
  {
    $result = db_query("SELECT * FROM input WHERE userid = '$userid'");
    $inputs = array();
    if ($result) {
      while ($row = db_fetch_array($result)) {
        $inputs[] = array($row['id'],$row['name'],$row['time'],$row['value']);
      }
    }
    return $inputs;
  }

  function get_input_id($user,$name)
  {
    $result = db_query("SELECT id FROM input WHERE name='$name' AND userid='$user'");
    if ($result) { $array = db_fetch_array($result); return $array['id']; } 
    else return 0;
  }

  function get_input_name($id)
  {
    $result = db_query("SELECT name FROM input WHERE id='$id'");
    if ($result) { $array = db_fetch_array($result); return $array['name']; } 
    else return 0;
  }

  function get_input_processlist($id)
  {
    $result = db_query("SELECT processList FROM input WHERE id='$id'");
    $array = db_fetch_array($result);
    return $array['processList'];
  }

  function get_input_processlist_desc($id)
  {
    $result = db_query("SELECT processList FROM input WHERE id='$id'");
    $array = db_fetch_array($result);

    $list = array();
    if ($array['processList']){		
      $array = explode(",", $array['processList']);
      foreach ($array as $row)    			// For all input processes
      {
        $row = explode(":", $row);    			// Divide into process id and arg

        $processid = $row[0];				// Process id
        $argA = $row[1];
        if ($processid==1) {$processDescription = "Log to feed: ";  $argA = get_feed_name($argA);}
        if ($processid==2) {$processDescription = "x: "; }
        if ($processid==3) $processDescription = "+: ";
        if ($processid==4) {$processDescription = "Power to kWh: ";  $argA = get_feed_name($argA);}
        if ($processid==5) {$processDescription = "Power to kWh/d ";  $argA = get_feed_name($argA);}
        if ($processid==6) $processDescription = "x input";
        if ($processid==7) {$processDescription = "input on-time: ";  $argA = get_feed_name($argA);}
        if ($processid==8) {$processDescription = "kWhinc to kWh/d: ";  $argA = get_feed_name($argA);}
        if ($processid==9) {$processDescription = "kWh to kWh/d: ";  $argA = get_feed_name($argA);}


        $list[]=array($processDescription,$argA);			// Populate list array
      }
    }
    return $list;
  }

?>
