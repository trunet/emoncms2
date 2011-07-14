<?php
  /*
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  function register_inputs($userid,$datapairs,$time)
  {

  //--------------------------------------------------------------------------------------------------------------
  // 2) Register incoming inputs
  //--------------------------------------------------------------------------------------------------------------
  $inputs = array();
  foreach ($datapairs as $datapair)       
  {
    $datapair = explode(":", $datapair);
    $name = $datapair[0]; 
    $value = $datapair[1];		

    $id = get_input_id($userid,$name);				// If input does not exist this return's a zero
    if ($id==0) {
      create_input_timevalue($userid,$name,$time,$value);	// Create input if it does not exist
    } else {			
      $inputs[] = array($id,$time,$value);	
      set_input_timevalue($id,$time,$value);			// Set time and value if it does
    }
  }

  return $inputs;
  }

  function process_inputs($inputs,$time)
  {
  //--------------------------------------------------------------------------------------------------------------
  // 3) Process inputs according to input processlist
  //--------------------------------------------------------------------------------------------------------------
  foreach ($inputs as $input)            
  {
    $id = $input[0];
    $processlist = explode(",", get_input_processlist($id));				
    $value = $input[2];
    foreach ($processlist as $inputprocess)    			        
    {
      $inputprocess = explode(":", $inputprocess); 		// Divide into process id and arg
      $processid = $inputprocess[0];				// Process id
      $arg = $inputprocess[1];	 				// Can be value or feed id

      if ($processid == 1) insert_feed_data($arg,$time,$value);	// 1. Log
      if ($processid == 2) $value *= $arg;			// 2. Scale
      if ($processid == 3) $value += $arg;			// 3. Offset
      if ($processid == 4) power_to_kwh($arg,$time,$value);
      if ($processid == 5) power_to_kwhd($arg,$time,$value);
      if ($processid == 6) $value = times_input($arg,$value);	// 6. Multiply with another input
      if ($processid == 7) input_ontime($arg,$value);
      if ($processid == 8) kwhinc_to_kwhd($arg,$time,$value);
      if ($processid == 9) kwh_to_kwhd($arg,$time,$value);

    }
  }
  }


?>
