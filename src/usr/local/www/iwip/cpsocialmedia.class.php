<?php

require_once("/usr/local/www/iwip/cpfirewall.class.php");


class cpsocialmedia {

	const SOCIAL_MEDIA_HOSTS_TABLE = "iwip_sm_sites";
	const SOCIAL_MEDIA_USERS_TABLE = "iwip_allowed_sm_users";


	const ACTION_ADD = "add";
	const ACTION_DELETE = "delete";
	const ACTION_FLUSH = "flush";

	public function __construct() {
	}

	public function addIP($zone, $ip, $mac="") {
		return $this->actionOnSocialMediaTable(self::ACTION_ADD, $zone, $ip, $mac);
	}

	public function deleteIP($zone, $ip, $mac="") {
		return $this->actionOnSocialMediaTable(self::ACTION_DELETE, $zone, $ip, $mac);
	}

	public function refreshSocialMediaHosts($zone) {
		$fileContent = file_get_contents('/usr/local/www/iwip/hosts.txt');
		$hosts = explode(',', $fileContent);
		$fw = new cpfirewall();
		foreach ($hosts as $host) {
			$name = trim($host);
			$ips = gethostbynamel(trim($host));
			if (is_array($ips) && count($ips) > 0) {
				foreach ($ips as $ip) {
					$fw->actionOnTable(self::ACTION_ADD, $zone, self::SOCIAL_MEDIA_HOSTS_TABLE, $ip);
				}
			}
		}

		return true;
	}

	private function actionOnSocialMediaTable($action, $zone, $ip, $mac="") {
		$fw = new cpfirewall();
		return $fw->actionOnTable($action, $zone, self::SOCIAL_MEDIA_USERS_TABLE, $ip, $mac);
	}
}

