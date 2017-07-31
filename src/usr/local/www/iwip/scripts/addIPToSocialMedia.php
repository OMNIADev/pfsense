<?php

require_once("/usr/local/www/iwip/cpsocialmedia.class.php");

$ip = isset ($_REQUEST['ip']) && $_REQUEST['ip'] != "" ? $_REQUEST['ip'] : "";
$mac = isset ($_REQUEST['mac']) && $_REQUEST['mac'] != "" ? $_REQUEST['mac'] : "";
$zone = isset ($_REQUEST['zone']) && $_REQUEST['zone'] != "" ? $_REQUEST['zone'] : "";
$response = array('success' => false);

$cp = new cpsocialmedia();

	if($cp->addIP($zone, $ip, $mac)) {
		$response['success'] = true;
		$response['data']['ip'] = $ip;
		$response['data']['mac'] = $mac;
		$response['message'] = "OK";
	}

echo json_encode($response);
