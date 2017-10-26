<?php

require_once("/usr/local/www/iwip/firewall.class.php");

$ip = isset ($_REQUEST['ip']) && $_REQUEST['ip'] != "" ? $_REQUEST['ip'] : "";
$mac = isset ($_REQUEST['mac']) && $_REQUEST['mac'] != "" ? $_REQUEST['mac'] : "";
$table = isset ($_REQUEST['table']) && $_REQUEST['table'] != "" ? $_REQUEST['table'] : 0;
$zone = isset ($_REQUEST['zone']) && $_REQUEST['zone'] != "" ? $_REQUEST['zone'] : "";
$action = isset ($_REQUEST['action']) && $_REQUEST['action'] != "" ? $_REQUEST['action'] : "";


$fw = new firewall();

echo $fw->actionOnTable($action, $zone, $table, $ip, $mac) ? "Lanzado el comando: "."/sbin/ipfw -x ".$zoneid." table ".$table." ".$action." ".$ip." ".$mac."<br />\n" : "Ha habido un error.<br />\n";
/*if ($zone != "") {
	$zoneid = isset($config['captiveportal'][$zone]) ? $config['captiveportal'][$zone]['zoneid'] : 0;
	if ($ip != "" && $table > 0 && $zoneid > 0 && $action != "") {
		exec("/sbin/ipfw -x ".$zoneid." table ".$table." ".$action." ".$ip." ".$mac, $output, $return_var);

	}
} 

var_dump($return_var);
if (!isset($return_var) || $return_var > 0) {
	echo "Ha habido un error.<br />\n";
} else {
	echo "Lanzado el comando: "."/sbin/ipfw -x ".$zoneid." table ".$table." ".$action." ".$ip." ".$mac."<br />\n";
}*/
