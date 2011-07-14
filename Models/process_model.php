<?php
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  //---------------------------------------------------------------------------------------
  // Times value by current value of another input
  //---------------------------------------------------------------------------------------
  function times_input($id,$value)
  {
    $result = db_query("SELECT value FROM input WHERE id = '$id'");
    $row = db_fetch_array($result);
    $value = $value * $row['value'];
    return $value;
  }

  //---------------------------------------------------------------------------------------
  // Power to kwh
  //---------------------------------------------------------------------------------------
  function power_to_kwh($feedid,$time_now,$value)
  {
    $feedname = "feed_".trim($feedid)."";
    $new_kwh = 0;

    // Get last value
    $result = db_query("SELECT * FROM $feedname ORDER BY time DESC LIMIT 1");
    $last_row = db_fetch_array($result);
    if ($last_row)
    {
      $last_time = strtotime($last_row['time']);
      $last_kwh = $last_row['data'];
      
      // kWh calculation
      $time_elapsed = ($time_now - $last_time);
      $kwh_inc = ($time_elapsed * $value) / 3600000;
      $new_kwh = $last_kwh + $kwh_inc;
    }

    // Insert new feed
    $time = date("Y-n-j H:i:s", $time_now);  
    db_query("INSERT INTO $feedname (`time`,`data`) VALUES ('$time','$new_kwh')");
    db_query("UPDATE feeds SET value = '$new_kwh', time = '$time' WHERE id='$feedid'");
  }

  //---------------------------------------------------------------------------------------
  // Power to kWh/d
  //---------------------------------------------------------------------------------------
  function power_to_kwhd($feedid,$time_now,$value)
  {
    $feedname = "feed_".trim($feedid)."";
    $new_kwh = 0;

    $time = date('y/m/d', mktime(0, 0, 0, date("m") , date("d") , date("Y")));

    // Get last value
    $result = db_query("SELECT * FROM $feedname WHERE time = '$time'");
    $last_row = db_fetch_array($result);

    if (!$last_row)
    {
      $result = db_query("INSERT INTO $feedname (time,data) VALUES ('$time','0.0')");

    $updatetime = date("Y-n-j H:i:s", $time_now);
    db_query("UPDATE feeds SET value = '0.0', time = '$updatetime' WHERE id='$feedid'");
    }
    else
    {
      $result = db_query("SELECT * FROM feeds WHERE id = '$feedid'");
      $last_row = db_fetch_array($result);

      $last_kwh = $last_row['value'];
      $last_time = strtotime($last_row['time']);
      // kWh calculation
      $time_elapsed = ($time_now - $last_time);
      $kwh_inc = ($time_elapsed * $value) / 3600;
      $new_kwh = $last_kwh + $kwh_inc;
    }

    // update kwhd feed
    db_query("UPDATE $feedname SET data = '$new_kwh' WHERE time = '$time'");

    $updatetime = date("Y-n-j H:i:s",     $time_now);
    db_query("UPDATE feeds SET value = '$new_kwh', time = '$updatetime' WHERE id='$feedid'");
  }

  //---------------------------------------------------------------------------------------
  // kWh increment to kWhd
  //---------------------------------------------------------------------------------------
  function kwhinc_to_kwhd($feedid,$time_now,$wh_inc)
  {
    $feedname = "feed_".trim($feedid)."";
    $new_wh = $wh_inc/1000;

    $time = date('y/m/d', mktime(0, 0, 0, date("m") , date("d") , date("Y")));

    // Get last value
    $result = db_query("SELECT * FROM $feedname WHERE time = '$time'");
    $last_row = db_fetch_array($result);

    if (!$last_row)
    {
      $result = db_query("INSERT INTO $feedname (time,data) VALUES ('$time','0.0')");

    $updatetime = date("Y-n-j H:i:s", $time_now);
    db_query("UPDATE feeds SET value = '0.0', time = '$updatetime' WHERE id='$feedid'");
    }
    else
    {
      $new_wh = $last_row['data'] + ($wh_inc/1000);
    }

    // update kwhd feed
    db_query("UPDATE $feedname SET data = '$new_wh' WHERE time = '$time'");

    $updatetime = date("Y-n-j H:i:s", $time_now);
    db_query("UPDATE feeds SET value = '$new_wh', time = '$updatetime' WHERE id='$feedid'");
  }

  //---------------------------------------------------------------------------------------
  // input on-time counter
  //---------------------------------------------------------------------------------------
  function input_ontime($feedid,$time_now,$value)
  {
    $feedname = "feed_".trim($feedid)."";
    $new_kwh = 0;

    $time = date('y/m/d', mktime(0, 0, 0, date("m") , date("d") , date("Y")));

    // Get last value
    $result = db_query("SELECT * FROM $feedname WHERE time = '$time'");
    $last_row = db_fetch_array($result);

    if (!$last_row)
    {
      $result = db_query("INSERT INTO $feedname (time,data) VALUES ('$time','0.0')");

      $updatetime = date("Y-n-j H:i:s", $time_now);
      db_query("UPDATE feeds SET value = '0.0', time = '$updatetime' WHERE id='$feedid'");
    }
    else
    {
      $result = db_query("SELECT * FROM feeds WHERE id = '$feedid'");
      $last_row = db_fetch_array($result);

      $last_kwh = $last_row['value'];
      $last_time = strtotime($last_row['time']);
      // time elapsed calculation
      $time_elapsed = ($time_now - $last_time);
      if ($value==1) {$new_kwh = $last_kwh + $time_elapsed;} else {$new_kwh = $last_kwh;}
    }

    db_query("UPDATE $feedname SET data = '$new_kwh' WHERE time = '$time'");

    $updatetime = date("Y-n-j H:i:s", $time_now);
    db_query("UPDATE feeds SET value = '$new_kwh', time = '$updatetime' WHERE id='$feedid'");

  }

  //---------------------------------------------------------------------------------
  // This method converts accumulated energy to kwhd
  //---------------------------------------------------------------------------------
  function kwh_to_kwhd($feedid,$time_now,$kwh)
  {
    // tmpkwhd table: rows of: feedid | kwh

    $kwh_today = 0;

    $result = db_query("SELECT * FROM tmpkwhd WHERE feedid = '$feedid'");
    $row = db_fetch_array($result);


    $start_day_kwh_value = $row['kwh'];
    if (!$row) db_query("INSERT INTO tmpkwhd (feedid,kwh) VALUES ('$feedid','0.0')");

    $feedname = "feed_".trim($feedid)."";

    $time = date('y/m/d', mktime(0, 0, 0, date("m") , date("d") , date("Y")));
    // Check if there is an entry for this day
    $result = db_query("SELECT * FROM $feedname WHERE time = '$time'");
    $entry = db_fetch_array($result);

    if (!$entry)
    {
      //Log start of day kwh
      db_query("UPDATE tmpkwhd SET kwh = '$kwh' WHERE feedid='$feedid'");
      $result = db_query("INSERT INTO $feedname (time,data) VALUES ('$time','0.0')");

      $updatetime = date("Y-n-j H:i:s", $time_now);
      db_query("UPDATE feeds SET value = '0.0', time = '$updatetime' WHERE id='$feedid'");
    }
    else
    {
      $kwh_today = $kwh - $start_day_kwh_value;
    }


    // update kwhd feed
    db_query("UPDATE $feedname SET data = '$kwh_today' WHERE time = '$time'");

    $updatetime = date("Y-n-j H:i:s", $time_now);
    db_query("UPDATE feeds SET value = '$kwh_today', time = '$updatetime' WHERE id='$feedid'");
  }
  //---------------------------------------------------------------------------------


?>

