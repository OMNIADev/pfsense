<?php

$ip = isset($argv[1]) ? $argv[1] : null;
$mac = isset($argv[2]) ? $argv[2] : null;

if ($ip == null && $mac == null) {
        $ip = isset($_GET['ip']) ? $_GET['ip'] : null;
        $mac = isset($_GET['mac']) ? $_GET['mac'] : null;
}

if ($ip && $mac) {
        if (strlen(trim($mac)) < 17) {
          $mac_parts = explode(':', $mac);
          $mac = "";
          for($i = 0; $i < count($mac_parts); $i++) {
            if (strlen($mac_parts[$i]) == 1) {
              $mac_parts[$i] = '0'.$mac_parts[$i];
            }
          }
          $mac = join($mac_parts, ':');
        }

        require_once("auth.inc");
        require_once("functions.inc");
        require_once("captiveportal.inc");


        global $cpzone, $cpzoneid;
        $cpzone = strtolower('iwip');
        $cpcfg = $config['captiveportal'][$cpzone];
        $cpzoneid = $cpcfg['zoneid'];

        $interface = $config['interfaces'][$config['captiveportal'][$cpzone]['interface']]['if'];

        if (file_exists("{$g['vardb_path']}/captiveportal_radius_{$cpzone}.db")) {
                $radius_enable = TRUE;
                if (isset($cpcfg['radmac_enable'])) {
                        $radmac_enable = TRUE;
                }
        }

        if ($radmac_enable && $interface!="") {
                $radiusctx = 'first';


                if (portal_mac_radius($mac, $ip, $radiusctx)) {
                }
        }
}

