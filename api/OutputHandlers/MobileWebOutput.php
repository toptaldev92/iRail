<?php
/**
 * Description of MobileWebOutput
 *
 * @author pieterc
 */

include("ConnectionOutput.php");
class MobileWebOutput extends ConnectionOutput {
    private $connections;

    function __construct($c) {
        $this -> connections = $c;
    }

    public function printAll() {
        $this->printHeader();        
        $this->printBody();

    }

    private function printHeader() {
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
<title>iRail -'. $this->connections[0] -> getDepart() -> getStation() -> getName() . ' to ' . $this->connections[0] -> getArrival() -> getStation() -> getName() .'</title>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<link href="css/query.css" rel="stylesheet" type="text/css" />
<link rel="apple-touch-icon" href="./img/irail.png" />
<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico">
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<script type="application/x-javascript">
addEventListener(\'load\', function() { setTimeout(hideAddressBar, 0); }, false)
function hideAddressBar() { window.scrollTo(0, 1); }
</script>
</head><body>';
    }

    private function printBody(){
        echo '<body>
<table align="left" cellpadding="0" cellspacing="1" bgcolor="FFFFFF" summary="Train Info">
<tr>
<th>Map</th>
<th>Station </th>
<th>Date </th>
<th>Time </th>
<th>Duration </th>
<th>Delay</th>
<th>Transportation</th>
</tr>
'. $this->getConnectionsOutput();
    echo "<tr><td colspan=\"7\"><center><form name=\"return\" method=\"post\" action=\"national\"><input type=\"submit\" name=\"submit\" value=\"Back\"></center></td></tr></table>";
    include("includes/footer.php");
    echo '</body>
        </html>';
    }

    private function getConnectionsOutput(){
        $output= "";
        foreach($this->connections as $con){

            date_default_timezone_set("Europe/Brussels");
            $output .= "<tr>";
            $output .= "<td>". '<a href="http://maps.google.be/?saddr=Station '. $con -> getDepart() -> getStation() -> getName() . '&daddr=Station '. $con -> getArrival() -> getStation() -> getName() . '" target="_blank"><img border="0" class="icon" src="/HAFAS/img/icon_map.gif" width="14" height="14" alt="Local Map" /></a>' . "</td>";
            $output .= "<td>". $con -> getDepart() -> getStation() -> getName() . "<br/>". $con -> getArrival() -> getStation() -> getName() . "</td>";
            $output .= "<td>" . date("d/m/y", $con -> getDepart() -> getTime()) . "<br/>". date("d/m/y", $con -> getArrival() -> getTime()) ."</td>";
            $output .= "<td>" . date("H:i", $con -> getDepart() -> getTime()) . "<br/>". date("H:i", $con -> getArrival() -> getTime()) ."</td>";

            $minutes = $con -> getDuration()/60 % 60;
            $hours = floor($con -> getDuration() / 3600);
            if($minutes < 10){
                $minutes = "0" . $minutes;
            }
            $output .= "<td>" . $hours. ":" . $minutes ."</td>";

            $output .= "<td>" . $con ->getDepart() -> getDelay()/60 . "min</td>";

            $output .= "<td>" . $con -> getDepart() -> getVehicle() -> getInternalId() ."</td>";

            $output .= "</tr>";
        }
        return $output;
    }
}
?>