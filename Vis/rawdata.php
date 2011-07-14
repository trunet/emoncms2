
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<!----------------------------------------------------------------------------------------------------
  
   All Emoncms code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

-------------------------------------------------------------------------------------->

 <?php
  error_reporting(E_ALL);
  ini_set('display_errors','On');

  $feedid = $_GET["feedid"];                 //Get the table ID so that we know what graph to draw
  $path = dirname("http://".$_SERVER['HTTP_HOST'].str_replace('Vis', '', $_SERVER['SCRIPT_NAME']))."/";

  $apikey = $_GET["apikey"];


 ?>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if IE]><script language="javascript" type="text/javascript" src="../excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="<?php echo $path;?>flot/jquery.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo $path;?>flot/jquery.flot.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo $path;?>flot/jquery.flot.selection.js"></script>
 </head>
 <body style="font-family:arial">

    <div id="graph_bound" style="height:100%; width:100%; position:relative; ">
      <div id="graph"></div>
      <div style="position:absolute; top:20px; right:20px;">
        <input class="time" type="button" value="D" time="1"/>
        <input class="time" type="button" value="W" time="7"/>
        <input class="time" type="button" value="M" time="30"/>
        <input class="time" type="button" value="Y" time="365"/> | 

        <input id="zoomin" type="button" value="+"/>
        <input id="zoomout" type="button" value="-"/>
        <input id="left" type="button" value="<"/>
        <input id="right" type="button" value=">"/>
      </div>
        <div id="loading" style="position:absolute; top:0px; left:0px; width:100%; height:100%; background-color: rgba(255,255,255,0.5);"></div>
        <h3 style="position:absolute; top:00px; left:60px;"><span id="stat"></span></h3>
    </div>

   <script id="source" language="javascript" type="text/javascript">
   //--------------------------------------------------------------------------------------
   var feedid = "<?php echo $feedid; ?>";				//Fetch table name
   var path = "<?php echo $path; ?>";
   var apikey = "<?php echo $apikey; ?>";
   //----------------------------------------------------------------------------------------
   // These start time and end time set the initial graph view window 
   //----------------------------------------------------------------------------------------
   var timeWindow = (3600000*24.0);				//Initial time window
   var start = ((new Date()).getTime())-timeWindow;		//Get start time
   var end = (new Date()).getTime();				//Get end time

   var paverage;
   var npoints;

   $(function () {

     var placeholder = $("#graph");

     //----------------------------------------------------------------------------------------
     // Get window width and height from page size
     //----------------------------------------------------------------------------------------
     $('#graph').width($('#graph_bound').width());
     $('#graph').height($('#graph_bound').height());
     //----------------------------------------------------------------------------------------

     var graph_data = [];                              //data array declaration

     vis_feed_data(apikey,feedid,start,end,10);

     //--------------------------------------------------------------------------------------
     // Plot flot graph
     //--------------------------------------------------------------------------------------
     
     function plotGraph(start, end)
     {
          $.plot(placeholder,[                    
          {
            data: graph_data ,				//data
            lines: { show: true, fill: true }		//style
          }], {
        xaxis: { mode: "time", 
                  min: ((start)),
		  max: ((end))
        },
        selection: { mode: "xy" }
     } ); 
     $('#loading').hide();
     }

     //--------------------------------------------------------------------------------------
     // Fetch Data
     //--------------------------------------------------------------------------------------
     function vis_feed_data(apikey,feedid,start,end,res)
     {
       $('#loading').show();
       $("#stat").html("Loading...  please wait about 5s");
       $.ajax({                                       //Using JQuery and AJAX
         url: path+'api/getfeed',                         
         data: "&apikey="+apikey+"&feedid="+feedid+"&start="+start+"&end="+end+"&resolution="+res,
         dataType: 'json',                            //and passes it through as a JSON    
         success: function(data) 
         {
           paverage = 0;
           npoints = 0;

           for (var z in data)                     //for all variables
           {
             paverage += parseFloat(data[z][1]);
             npoints++;
           }  
             var timeB  = Number(data[0][0])/1000.0;
             var timeA  = Number(data[data.length-1][0])/1000.0;

             var timeWindow = (timeB-timeA);
             var timeWidth = timeWindow / npoints;

             var kwhWindow = (timeWidth * paverage)/3600000;

           paverage = paverage / npoints;
           $("#stat").html((paverage).toFixed(1)+" W | "+(kwhWindow).toFixed(1)+" kWh");

           graph_data = [];   
           graph_data = data;
           plotGraph(start, end);
         } 
       });
     }

     //--------------------------------------------------------------------------------------
     // Graph zooming
     //--------------------------------------------------------------------------------------
     placeholder.bind("plotselected", function (event, ranges) 
     {
       // clamp the zooming to prevent eternal zoom
       if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) ranges.xaxis.to = ranges.xaxis.from + 0.00001;
       if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) ranges.yaxis.to = ranges.yaxis.from + 0.00001;
        
       start = ranges.xaxis.from;					//covert into usable time values
       end = ranges.xaxis.to;						//covert into usable time values

       var res = getResolution(start, end);
       $('.inc').html("Resolution: "+res);				//Output resolution

       vis_feed_data(apikey,feedid,start,end,res);			//Get new data and plot graph
     });

     //----------------------------------------------------------------------------------------------
     // Operate buttons
     //----------------------------------------------------------------------------------------------
     $("#zoomout").click(function () 
     { 
       var time_window = end - start;
       var middle = start + time_window / 2;
       time_window = time_window * 2;					// SCALE
       start = middle - (time_window/2);
       end = middle + (time_window/2);
       var res = getResolution(start, end);
       vis_feed_data(apikey,feedid,start,end,res);			//Get new data and plot graph
     });

     $("#zoomin").click(function () 
     {
       var time_window = end - start;
       var middle = start + time_window / 2;
       time_window = time_window * 0.5;					// SCALE
       start = middle - (time_window/2);
       end = middle + (time_window/2);
       var res = getResolution(start, end);
       vis_feed_data(apikey,feedid,start,end,res);			//Get new data and plot graph
     });

     $('#right').click(function () 
     {	
       var laststart = start; var lastend = end;
       var timeWindow = (end-start);
       var shiftsize = timeWindow * 0.2;
       start += shiftsize;
       end += shiftsize;
       var res = getResolution(start,end);
       vis_feed_data(apikey,feedid,start,end,res);
     });

     $('#left').click(function ()
     {	
       var laststart = start; var lastend = end;
       var timeWindow = (end-start);
       var shiftsize = timeWindow * 0.2;
       start -= shiftsize;
       end -= shiftsize;
       var res = getResolution(start,end);
       vis_feed_data(apikey,feedid,start,end,res);
     });

     $('.time').click(function () 
     {
       var time = $(this).attr("time");					//Get timewindow from button
       start = ((new Date()).getTime())-(3600000*24*time);			//Get start time
       end = (new Date()).getTime();					//Get end time
       var res = getResolution(start,end);
       vis_feed_data(apikey,feedid,start,end,res);			//Get new data and plot graph
     });

     function getResolution(start, end)
     {
       var res = Math.round( ((end-start)/8000000) );	//Calculate resolution
       if (res<1) res = 1;
       return res;
     }
     //-----------------------------------------------------------------------------------------------
  });
  //--------------------------------------------------------------------------------------
  </script>

  </body>
</html>  
