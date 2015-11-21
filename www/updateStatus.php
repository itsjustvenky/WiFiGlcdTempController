<?php

  include_once("udpRequest.php");
//  include_once("accessDatabase.php");

  $tempStr           = "";
  $switchStr         = "";
  $unusedStr         = "";
  $switchjStr        = "";
  $maxTempPerLine    = 4;
  $chipName          = 3;
  $chipIDLoc         = 0;
  $chipInfoType      = 0;
  $chipInfoArrayData = 1;
  $chipInfoArrayName = 2;

  

  if(isset($_GET["udp_Port"]) && isset($_GET["ip_Address"]) && isset($_GET["netName"]) && isset($_GET["netID"]) ) 
  {
    $udp_Port = $_GET["udp_Port"];
    $ip_Address = $_GET["ip_Address"];
    $netName      = $_GET["netName"];
    $netID        = $_GET["netID"];
//      echo "updateStatus.php: netID = $netID, udp_Port = $udp_Port, ip_Address = $ip_Address, netName = $netName <br />";
  }
  
  if(isset($_POST["udp_Port"]) && isset($_POST["ip_Address"]) && isset($_POST["netName"]) && isset($_POST["netID"]) ) 
  {
    $udp_Port = $_POST["udp_Port"];
    $ip_Address = $_POST["ip_Address"];
    $netName      = $_POST["netName"];
    $netID        = $_POST["netID"];
//      echo "updateStatus.php: netID = $netID, udp_Port = $udp_Port, ip_Address = $ip_Address, netName = $netName <br />";
  }

  if(isset($_POST["modifyName"]))
  {
  }
  
  if(isset($_POST["updateName"]))
  {
    $udp_Port = $_POST["udp_Port"];
    $ip_Address = $_POST["ip_Address"];
    $netName      = $_POST["netName"];
    $netID        = $_POST["netID"];
    $chipName     = $_POST["chipName"];
    $chipAddress  = $_POST["chipAddress"];
    
    $chipName = str_replace(" ", "_", $chipName);
    $chipName = str_replace(",", "_", $chipName);
    $chipName = str_replace(";", "_", $chipName);

    $query = "SELECT name From chipNames WHERE address=$chipAddress AND netID=$netID";
    echo "\$query = $query <br />";
    $result = mysqli_query($link,$query);

    if($result === FALSE)
    {
      $query = "INSERT into chipNames SET 'name'='$chipName','address'='$chipAddress','netID'='$netID'";
      echo "\$query = $query <br />";
      mysqli_free_result($result);
      $result = mysqli_query($link,$query);
    }else{
      echo "query success<br />";
      $resultObj = mysqli_fetch_object($result);
      echo "MySQL chipName is ".$resultObj->name."<br />";
      if($chipName !== $resultObj->name)
      {
        $query = "UPDATE chipNames SET 'name'='$chipName' where 'address'='$chipAddress' AND 'netID'='$netID'";
        echo "\$query = $query <br />";
      }
      mysqli_free_result($result);
      $result = mysqli_query($link,$query);
    }
  }
    
  $in = $getStatus."\n";
//  echo "\$ip_Address = ".$ip_Address.", \$udp_Port = ".$udp_Port."<br />";
  $udpStatus = udpRequest($ip_Address, $udp_Port, $in);
//  echo "\$udpStatus = ".$udpStatus."<br />";

  $udpType = explode(",", $udpStatus);
  $tempStr .=
  "<div id=\"temp\"; position: relative; width: 100%>
     <td align=\"center\" width=\"25%\">
       Temperature
       <br /><br />
       <font size=\"10\"><strong>".$udpType[0]."&deg; C</strong></font>
       <br /><br />
     </td>
     <td align=\"center\" width=\"25%\">
       Temperature
       <br /><br />
       <font size=\"10\"><strong>".$udpType[1]."&deg; F</strong></font>
       <br /><br />
     </td>
     <td align=\"center\" width=\"25%\">
       Upper Switch
       <br /><br />
       <font size=\"10\"><strong>";
  if($udpType[2] === " N")
  {
    $tempStr .= "<font color=\"BLUE\">ON&nbsp;</font>";
  }else{
    $tempStr .= "<font color=\"GREEN\">OFF</font>";
  }        
  $tempStr .=
  "    </strong></font>
       <br /><br />
     </td>
     <td align=\"center\" width=\"25%\">
       Lower Switch
       <br /><br />
       <font size=\"10\"><strong>";
  if($udpType[3] === " N")
  {
    $tempStr .= "<font color=\"RED\">ON&nbsp</font>";
  }else{
    $tempStr .= "<font color=\"GREEN\">OFF</font>";
  }        
  $tempStr .=
  "    </strong></font>
       <br /><br />
     </td>
   </div>";

  echo "<table width=\"100%\" align=\"center\" border=\"2\">\n<tr>\n$tempStr</tr>\n</table>\n\n<table width=\"100%\" align=\"center\" border=\"2\">\n<tr>\n$switchStr</tr>\n</table>\n\n<!-- <table width=\"100%\" align=\"center\" border=\"2\">\n<tr>\n$unusedStr</tr>\n</table> -->";
?>
