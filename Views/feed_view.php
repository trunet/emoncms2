<!--
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
-->

<h2>My feeds</h2>

<div class='lightbox' style='margin-bottom:20px'>

  <table>
    <tr>
      <td><b>Your API key: </b><?php echo $apikey; ?></td>
      <td>
        <form action="" method="post">
          <input type="hidden" name="form" value="newapi">
          <input type="submit" value="new" >
        </form>
      </td>
    </tr>
  </table>

  <?php
  $testjson = $GLOBALS['path']."api/post?apikey=".$apikey."&json={power:252.4,temperature:15.4}"
  ?>

  <p><b>API url: </b><?php echo $GLOBALS['path']; ?>api/post.php</p>
  <p><b>Copy this to your web browser or send from a nanode: </b><br/><?php echo $testjson; ?></p>
</div>

<div class='lightbox' style='margin-bottom:20px'>
  <h3>1) Inputs</h3>

  <?php if ($inputs) { ?>
    <table class='catlist'>
    <tr><th>Name</th><th>Last Value</th><th>Action</th></tr>
    <?php $i=0; foreach ($inputs as $input) { $i++; ?>

    <tr class="<?php echo 'd'.($i & 1); ?> " >
      <td><?php echo $input[1]; ?></td>
      <td><?php echo $input[3]; ?></td>      
      <td>
        <form action="" method="post">
          <input type="hidden" name="form" value="input">
	  <input type="hidden" name="id" value="<?php echo $input[0]; ?>">
          <input type="submit" value=">" class="buttonLook"/>
        </form>
      </td>
    </tr>
    
  <?php } echo "</table>"; } else { ?>
    <p>You have no inputs, to get started connect up your monitoring hardware</p>
  <?php } ?>
</div>


<div class='lightbox' style='margin-bottom:20px'>
  <h3>2) Input Configuration:   <?php echo get_input_name($inputsel); ?></h3>



  <?php 
  if (isset($processlist))
  {
  ?>

  <table class='catlist'><tr><th>Order</th><th>Process</th><th>Arg</th><th>Actions:</th></tr>
  
  <?php $i = 0;
     

          foreach ($processlist as $inputProcess)    		// For all input processes
          {
            
            $processDescription = $inputProcess[0];				// Process id
            $argA = $inputProcess[1];
            $i++;
            echo "<tr class='d" . ($i & 1) . "' >";
            echo "<td>".$i."</td><td>".$processDescription."</td><td>".$argA."</td>";
            echo "<td><button type='button'>Edit</button><button type='button'>Del</button></td></tr>";
          }
        
   ?>
        <tr><td>New</td><td>
        <form action="" method="post">
        <input type="hidden" name="form" value="process">
        <input type="hidden" name="id" value="<?php echo $inputsel; ?>">
        <select class="processSelect" name="sel">

        <option value="1">Log</option>
        <option value="2">x</option>
        <option value="3">+</option>
        <option value="4">Power to kWh</option>
        <option value="5">Power to kWh/d</option>
        <option value="6">x input</option>
        <option value="7">input on-time</option>
        <option value="8">kwhinc to kWh/d</option>
        <option value="9">kwh to kWh/d</option>

        </select></td>
        <td><input type="text" name="arg" class="processBox" style="width:100px;" /></td>
        <td><input type="submit" value="add" /></form></td>
        </tr></table>

  <?php } ?>

</div>

<div class='lightbox' style='margin-bottom:20px'>
  <h3>3) Feeds</h3>

  <?php if ($feeds) { ?>
  <table class='catlist'><tr><th>id</th><th>Name</th><th>updated</th><th>Value</th><th>Visualise</th></tr>
  <?php 
    $i = 0;
    foreach ($feeds as $feed)
    {
      $timenow = time();
      $time = strtotime($feed[2]);
      $updated = ($timenow - $time)."s ago";
      if (($timenow - $time)>3600) $updated = "inactive";
      $i++;
      ?>
      <tr class="<?php echo 'd'.($i & 1); ?> " >
      <td><?php echo $feed[0]; ?></td>
      <td><?php echo $feed[1]; ?></td>
      <td><?php echo $updated; ?></td>
      <td><?php echo $feed[3]; ?></td>
      <td>
      <form action="graph" method="post">
        <input type="hidden" name="form" value="graph">
        <input type="hidden" name="feedid" value="<?php echo $feed[0]; ?>">
        <input type="hidden" name="feedname" value="<?php echo $feed[1]; ?>">
        <select name="sel">
          <option value="1">Realtime</option>
          <option value="2">Raw data</option>
          <option value="3">Bar graph</option>
        </select>
        <input type="submit" value="view" class="buttonLook"/>
      </form>
      </td>
      </tr>
    <?php } ?>
    </table>
    <?php } else { ?>
      <p>You have no feeds</p>
    <?php } ?>
    </div>




