<?php


class cpfirewall {
	private $config = array();

	public function __construct() {
		require_once("captiveportal.inc");

		$this->config = isset($config) ? $config : array();
	}

	public function actionOnTable($action, $zone, $table, $ip, $mac="") {
		$outputCode = 99;
		if ($zone != "") {
			// Get Zone ID from the configuration file of pfSense
			$zoneid = isset($this->config['captiveportal'][$zone]['zoneid']) ? $this->config['captiveportal'][$zone]['zoneid'] : 0;
		        if (filter_var($ip, FILTER_VALIDATE_IP) && $table > 0 && $zoneid > 0 && ($action == "add" || $action == "delete")) {
				$macParam = "";
				if ($mac != "" && filter_var($mac, FILTER_VALIDATE_MAC) && $action=="add") $macParam = " mac ".$mac;
                		exec("/sbin/ipfw -x ".$zoneid." table ".$table." ".$action." ".$ip.$macParam, $output, $outputCode);
		        }
		}

		// TRUE: OK, FALSE: FAIL
		return true;
//		return !($outputCode > 0);
	}
	
	public function flushTable($zone, $table) {
		$outputCode = 99;
		if ($zone != "") {
			$zoneid = isset($this->config['captiveportal'][$zone]['zoneid']) ? $this->config['captiveportal'][$zone]['zoneid'] : 0;
			if ($zoneid > 0) {
                		exec("/sbin/ipfw -x ".$zoneid." table ".$table." flush", $output, $outputCode);				
			}
		}

		return !($outputCode > 0);
	}
	public function disconnectAListOfClients($zone, $ids) {
		require("functions.inc");
		require_once("filter.inc");
		require("shaper.inc");

		$cpzone = $zone;

		$config = $this->config;
		$GLOBALS['config'] = $config;				

		if (!is_array($config['captiveportal']))
		        $config['captiveportal'] = array();
		$a_cp =& $config['captiveportal'];

		if (count($a_cp) == 1)
			$cpzone = current(array_keys($a_cp));

		if (!array_key_exists($cpzone, $a_cp)) {
		        $cpzone = "";
		}

		if (isset($cpzone) && !empty($cpzone) && isset($a_cp[$cpzone]['zoneid']))
		        $cpzoneid = $a_cp[$cpzone]['zoneid'];

		$GLOBALS['cpzoneid'] = $cpzoneid;
		$GLOBALS['cpzone'] = $cpzone;
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$this->disconnectClient($id);
			}
			return true;
		} else {
			return false;
		}
	}

	public function disconnectClient($id) {
		captiveportal_disconnect_client($id);	      
	}

} 
