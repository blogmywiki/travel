<?php
  require("OpenLDBWS.php");
  $OpenLDBWS = new OpenLDBWS("YOUR-API-KEY-HERE");
// use your 3 letter station codes here:
  $responseHGRLBG = $OpenLDBWS->GetDepartureBoard(10, "HGR", "LBG", "to");
  $responseHGRPMR = $OpenLDBWS->GetDepartureBoard(10, "HGR", "PMR", "to");
  date_default_timezone_set('Europe/London');
//  $now =  new \DateTime("now", new \DateTimeZone("Europe/London"));
  $now = new DateTime();
  $displayDate = $now->format('l H:i');

// page templates
$template["style"] = "<!doctype html>
<html>
<head>
  <meta charset=utf-8>
  <style type='text/css'>
    table {
      border-collapse: collapse;
      width:100%;
    }
    h1 {
      font-family: monospace;
      font-size: 5em;
      text-align: center;
    }
    caption,th,td {
      font-family: monospace;
      font-size: 2.25em;
      border: 3px solid #555;
      padding: 10px;
    }
    td.route{
      text-align: center;
      font-weight: bold;
    }
    th:nth-child(1),th:nth-child(2) {
      text-align: left;
    }
    th:nth-child(3),td:nth-child(3) {
      text-align: center;
    }
    th:nth-child(4),td:nth-child(4) {
      text-align: right;
    }
  </style>
  </head>
  <body>
  <h1>{$displayDate}</h1>
  ";

  $template["header"] = "
    <table>
    <caption>Trains</caption>
      <thead>
        <tr>
          <th>Time</th>
          <th>Destination</th>
          <th>Platform</th>
          <th>Expected</th>
        </tr>
      </thead>
      <tbody>
    ";
  $template["row"] = "
        <tr>
          <td>{std}</td>
          <td>{destination}</td>
          <td>{platform}</td>
          <td>{etd}</td>
        </tr>
  ";
  $template["tablefooter"] = "
      </tbody>
    </table>
  ";
  $template["footer"] = "
      </body>
    </html>
  ";
  print $template["style"];

// table 1

  if (isset($responseHGRLBG->GetStationBoardResult->trainServices->service))
  {
    if (is_array($responseHGRLBG->GetStationBoardResult->trainServices->service))
    {
      $services = $responseHGRLBG->GetStationBoardResult->trainServices->service;
    }
    else
    {
      $services = array($responseHGRLBG->GetStationBoardResult->trainServices->service);
    }

    print $template["header"];

    print "<tr><td colspan=4 class=route>Hither Green &gt; London Bridge</td>";

    foreach($services as $service)
    {
      $row = $template["row"];
      $destinations = array();
      if (is_array($service->destination->location))
      {
        $locations = $service->destination->location;
      }
      else
      {
        $locations = array($service->destination->location);
      }
      foreach($locations as $location)
      {
        $destinations[] = $location->locationName;
      }

// is there a platform listed?
			if (isset($service->platform)) {


// set up time calculation
        $row = $template["row"];
        $std = $service->std;
        $etd = $service->etd;
        $etd_date = new DateTime($now->format('Y-m-d ') . "");
        if ("On time" == $etd) {
          $etd_date = new DateTime($now->format('Y-m-d ') . $std);
        } else {
          $etd_date = new DateTime($now->format('Y-m-d ') . $etd);
        }
        $minutes = $now->diff($etd_date)->i;

// only show trains 10 minutes hence
        if ($minutes >= 10) {
          $row = str_replace("{std}",$service->std,$row);
          $row = str_replace("{destination}",implode(" and ",$destinations),$row);
          $row = str_replace("{platform}",(isset($service->platform)?$service->platform:"&nbsp;"),$row);
          $row = str_replace("{etd}",$service->etd,$row);
          print $row;
        }
    }
    }
}



    // table 2

    print "<tr><td colspan=4 class=route>Hither Green &gt; Peckham Rye</td>";


      if (isset($responseHGRPMR->GetStationBoardResult->trainServices->service))
      {
        if (is_array($responseHGRPMR->GetStationBoardResult->trainServices->service))
        {
          $services = $responseHGRPMR->GetStationBoardResult->trainServices->service;
        }
        else
        {
          $services = array($responseHGRPMR->GetStationBoardResult->trainServices->service);
        }

//        print $template["header"];
        foreach($services as $service)
        {
          $row = $template["row"];
          $destinations = array();
          if (is_array($service->destination->location))
          {
            $locations = $service->destination->location;
          }
          else
          {
            $locations = array($service->destination->location);
          }
          foreach($locations as $location)
          {
            $destinations[] = $location->locationName;
          }

    // is there a platform listed?
    			if (isset($service->platform)) {


    // set up time calculation
            $row = $template["row"];
            $std = $service->std;
            $etd = $service->etd;
            $etd_date = new DateTime($now->format('Y-m-d ') . "");
            if ("On time" == $etd) {
              $etd_date = new DateTime($now->format('Y-m-d ') . $std);
            } else {
              $etd_date = new DateTime($now->format('Y-m-d ') . $etd);
            }
            $minutes = $now->diff($etd_date)->i;

    // only show trains 10 minutes hence
            if ($minutes >= 10) {
              $row = str_replace("{std}",$service->std,$row);
              $row = str_replace("{destination}",implode(" and ",$destinations),$row);
              $row = str_replace("{platform}",(isset($service->platform)?$service->platform:"&nbsp;"),$row);
              $row = str_replace("{etd}",$service->etd,$row);
              print $row;
            }
        }
        }





    print $template["tablefooter"];
  }

?>

<script type = "text/javascript">

  function Get(yourUrl){
      var Httpreq = new XMLHttpRequest(); // a new request
      Httpreq.open("GET",yourUrl,false);
      Httpreq.send(null);
      return Httpreq.responseText;
  }


var json_obj = JSON.parse(Get('https://api.tfl.gov.uk/StopPoint/YOUR-BUS-STOP-ID-HERE/arrivals'));

json_obj.sort(function(a, b) {
  return parseFloat(a.timeToStation) - parseFloat(b.timeToStation);
});


document.writeln('<br /><br /><br /><br /><table><caption>Buses from YOUR BUS STOP</caption><tbody><thead><tr><th>route</th><th>Destination</th><th>Due</th></tr></thead>')
for (var i = 0; i < json_obj.length; i++) {
  if (json_obj[i].timeToStation>120) {
  document.writeln ('<tr><td>'+json_obj[i].lineName+'</td><td>'+json_obj[i].destinationName+'</td><td>'+Math.round(json_obj[i].timeToStation/60)+'</td></tr>');
}
}
document.writeln('</tbody></table>')

</script>


<?php
print $template["footer"];
?>
