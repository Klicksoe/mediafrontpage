<?php
$wdgtSabnzbd = array("type" => "ajax", "block" => "sabnzbdwrapper", "call" => "widgets/wSabnzbd.php?ajax=w", "interval" => 5000);
$wIndex["wSabnzbd"] = $wdgtSabnzbd;

?>
<?php
if (!empty($_GET['ajax']) && ($_GET['ajax'] == "w")) {
	require_once "../config.php";

	function sabQuery($command, $values = array()) {
		global $saburl, $sabapikey;

		$getParameter = "";
		foreach ($values as $key => $value) {
			$getParameter .= "&" . $key . "=" . $value;
		}
		
		$queryurl = $saburl."api?mode=".$command."&output=json"."&apikey=".$sabapikey;
		$queryurl .= $getParameter;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $queryurl);
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$results = json_decode(curl_exec($ch), true);
		curl_close($ch);
		
		return $results;
	}

?>
<html>
	<head>
		<title>Media Front Page - SABnzbd Status</title>
		<link rel='stylesheet' type='text/css' href='css/front.css'>
	</head>
	<body>
	<div id='sabnzbd'>
<?php
	$sabqueue = sabQuery("qstatus");
	//echo "<br/><pre>".print_r($sabqueue)."</pre>";

	echo "        <p>".$sabqueue["state"]."</p>";
	echo "        <p>".$sabqueue["speed"]."</p>";
	echo "        <p>".$sabqueue["timeleft"]."</p>";
	foreach($sabqueue["jobs"] as $slot) {
		echo "        <p>".$slot["filename"]."</p>";
		$total = (int)$slot["mb"];
		$remaining = (int)$slot["mbleft"];
		if($total > 0 && is_numeric($remaining)) {
			$percentage = (int)((($total - $remaining) / $total)*100);
			echo "        <div class='progressbar'><div class='progress' style='width:".$percentage."%';</div></div>";
		}
	}
?>
		</div>
	</body>
</html>
<?php
}
?>