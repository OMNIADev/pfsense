<?php

require_once("/usr/local/www/iwip/cpfirewall.class.php");

$ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : "";
$zone = isset ($_REQUEST['zone']) && $_REQUEST['zone'] != "" ? $_REQUEST['zone'] : "";
$response = array('success' => false);

$fw = new cpfirewall();

$response['data']['ids'] = array();

$fw->disconnectAListOfClients($zone, $ids);
$response['data']['zone'] = $zone;
$response['success'] = true;
$response['message'] = "OK";


echo json_encode($response);
