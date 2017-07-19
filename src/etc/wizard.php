
<?php
require_once('/etc/inc/config.inc');
require_once('/etc/inc/functions.inc');

function addCDATA($lnode, $cData) {
  $node = dom_import_simplexml($lnode);
  $no = $node->ownerDocument;
  $node->appendChild($no->createCDATASection($cData));
}


function mask2cidr($mask){
  $long = ip2long($mask);
  $base = ip2long('255.255.255.255');
  return 32 - log(($long ^ $base) + 1, 2);
}

function render_interfaces($no, $ys){
  $iflist = get_interface_list();
  $list_ar = array();
  foreach ($iflist as $iface => $ifa) {
    array_push($list_ar, $iface);
  }
  $list_ar = array_diff($list_ar, $no);
  $list = "";
  foreach ($list_ar as &$iface) {
    $list = $list.$iface.", ";
  }
  foreach ($ys as &$add) {
    $list = $list.$add.", ";
    array_push($list_ar, $add);
  }
  $list = substr($list, 0, -2);
  return array($list, $list_ar);
}

function get_interface($no_inter, $ys_inter){
  while(true){
    $render = render_interfaces($no_inter, $ys_inter);

    $select = 'error';
    while (!in_array($select, $render[1])){
      $select = readline('   [+]Interface ('.$render[0].'): ');
      array_push($no_inter, $select);
    }
    return array($select, $no_inter);
  }
}

function save_conf($xml, $name, $conf){


  echo "[+]".strtoupper($name)."\n";

  echo "Interface -> ".$conf[0]."\n";

  if ($conf[1] == ''){
    echo "IP -> DHCP\n";
    //unset($xml->interfaces->$name);
    //unset($xml->interfaces->$name->ipaddr);
    $xml->interfaces->$name->addChild('enable', '');
    $xml->interfaces->$name->addChild('ipaddr', 'dhcp');
    $xml->interfaces->$name->addChild('dhcphostname', '');
    $xml->interfaces->$name->addChild('alias-address', '');
    $xml->interfaces->$name->addChild('alias-subnet', '32');
    $xml->interfaces->$name->addChild('dhcprejectfrom', '');
    $xml->interfaces->$name->addChild('adv_dhcp_pt_timeout', '');
		$xml->interfaces->$name->addChild('adv_dhcp_pt_retry', '');
		$xml->interfaces->$name->addChild('adv_dhcp_pt_select_timeout', '');
		$xml->interfaces->$name->addChild('adv_dhcp_pt_reboot', '');
		$xml->interfaces->$name->addChild('adv_dhcp_pt_backoff_cutoff', '');
		$xml->interfaces->$name->addChild('adv_dhcp_pt_initial_interval', '');
    $xml->interfaces->$name->addChild('adv_dhcp_pt_values', 'SavedCfg');
    $xml->interfaces->$name->addChild('adv_dhcp_send_options', '');
		$xml->interfaces->$name->addChild('adv_dhcp_request_options', '');
		$xml->interfaces->$name->addChild('adv_dhcp_required_options', '');
		$xml->interfaces->$name->addChild('adv_dhcp_option_modifiers', '');
		$xml->interfaces->$name->addChild('adv_dhcp_config_advanced', '');
		$xml->interfaces->$name->addChild('adv_dhcp_config_file_override', '');
		$xml->interfaces->$name->addChild('adv_dhcp_config_file_override_path', '');
    //$xml->interfaces->$name->addChild('descr', '');

  }else{
    echo "IP -> Static\n";
    $ip = $conf[1][0];
    $netmask = $conf[1][1];
    $gateway = $conf[1][2];
    $xml->interfaces->$name->addChild('enable', '');
    $xml->interfaces->$name->addChild('ipaddr', $ip);
    $xml->interfaces->$name->addChild('subnet', mask2cidr($netmask));
    $xml->interfaces->$name->addChild('gateway', strtoupper($name).'GW');
    $xml->interfaces->$name->addChild('spoofmac', '');
    //$xml->interfaces->$name->addChild('descr', '');

    $gw_num = $xml->gateways->gateway_item->count();
    //$xml->addChild('gateways', '');
    $xml->gateways->addChild('gateway_item', '');
    $xml->gateways->gateway_item[$gw_num]->addChild('interface', $name);
    $xml->gateways->gateway_item[$gw_num]->addChild('gateway', $gateway);
    $xml->gateways->gateway_item[$gw_num]->addChild('name', strtoupper($name).'GW');
    $xml->gateways->gateway_item[$gw_num]->addChild('weight', '');
    $xml->gateways->gateway_item[$gw_num]->addChild('ipprotocol', 'inet');
    $xml->gateways->gateway_item[$gw_num]->addChild('descr', '');
    $xml->gateways->gateway_item[$gw_num]->addChild('defaultgw', '');

  }

  if ($conf[2] == ''){
    echo "DHCPD -> NO\n";

  }else{
    echo "DHCPD -> YES\n";
    //unset($xml->dhcpd->$name);
    $xml->dhcpd->addChild($name, '');
    $xml->dhcpd->$name->addChild('range', '');
    $xml->dhcpd->$name->range->addChild('from', $conf[2][0]);
    $xml->dhcpd->$name->range->addChild('to', $conf[2][1]);
    $xml->dhcpd->$name->addChild('failover_peerip', '');
    $xml->dhcpd->$name->addChild('defaultleasetime', '');
    $xml->dhcpd->$name->addChild('maxleasetime', '');
    $xml->dhcpd->$name->addChild('netmask', '');
    $xml->dhcpd->$name->addChild('gateway', '');
    $xml->dhcpd->$name->addChild('domain', '');
		$xml->dhcpd->$name->addChild('domainsearchlist', '');
		$xml->dhcpd->$name->addChild('ddnsdomain', '');
    $xml->dhcpd->$name->addChild('ddnsdomainprimary', '');
  	$xml->dhcpd->$name->addChild('ddnsdomainkeyname', '');
  	$xml->dhcpd->$name->addChild('ddnsdomainkey', '');
  	$xml->dhcpd->$name->addChild('mac_allow', '');
    $xml->dhcpd->$name->addChild('mac_deny', '');
    $xml->dhcpd->$name->addChild('tftp', '');
    $xml->dhcpd->$name->addChild('ldap', '');
    $xml->dhcpd->$name->addChild('nextserver', '');
  	$xml->dhcpd->$name->addChild('filename', '');
  	$xml->dhcpd->$name->addChild('filename32', '');
	  $xml->dhcpd->$name->addChild('filename64', '');
  	$xml->dhcpd->$name->addChild('rootpath', '');
    $xml->dhcpd->$name->addChild('numberoptions', '');
    $xml->dhcpd->$name->addChild('dhcpleaseinlocaltime', '');
    $xml->dhcpd->$name->addChild('enable', '');

  }
  return $xml;

}

$no_inter = array();
$ys_inter = array();
$vl_add = array();

/*--------------VLAN---------------*/

$xml = simplexml_load_file('/cf/conf/config.xml', NULL, LIBXML_NOCDATA);

unset($xml->interfaces);
$xml->addChild('interfaces', '');

unset($xml->vlans);
$xml->addChild('vlans', '');

unset($xml->dhcpd);
$xml->addChild('dhcpd', '');

unset($xml->gateways);
$xml->addChild('gateways', '');

$vlan_counter = 1;
while (true) {
  $vlans_yn = readline("\n[*]VLAN? (y/n): ");

  if (in_array($vlans_yn, array('y', 'Y'))) {
    $result = get_interface($no_inter, $ys_inter);
    $no_inter = $result[1];
    $vlan_inter = $result[0];

    $vlan_id = 'error';
    while (!is_numeric($vlan_id)) {
      $vlan_id = readline('   [+]Tag: ');                          #INPUT
    }

    array_push($no_inter, $vlan_inter."_vlan".$vlan_id);
    array_push($vl_add, $vlan_inter."_vlan".$vlan_id);
    $name = 'opt'.$vlan_counter;

    $xml->vlans->addChild('vlan', '');
    $xml->vlans->vlan[$vlan_counter - 1]->addChild('if', $vlan_inter);
    $xml->vlans->vlan[$vlan_counter - 1]->addChild('tag', $vlan_id);
    $xml->vlans->vlan[$vlan_counter - 1]->addChild('pcp', '');
    $xml->vlans->vlan[$vlan_counter - 1]->addChild('descr', '');
    $xml->vlans->vlan[$vlan_counter - 1]->addChild('vlanif', $vlan_inter."_vlan".$vlan_id);

    $xml->interfaces->addChild($name, '');
    #$xml->interfaces->$name->addChild('descr', '');
    //$xml->interfaces->$name->addChild('enable', '');
    //$xml->interfaces->$name->descr->addChild('![CDATA['.strtoupper($name).']]', '');
    $xml->interfaces->$name->addChild('if', $vlan_inter."_vlan".$vlan_id);

    $vlan_counter++;

  }else{
    break;
  }
}

file_put_contents("/tmp/custom.xml", $xml->asXML());�
restore_backup("/tmp/custom.xml");

echo "Guardando VLANs....";
sleep(2);

$num_inter = count($no_inter) + 1;

/*---------------WAN---------------*/

$ys_inter = $vl_add;
$no_inter = array();

echo "\n[*]WAN: \n";
$result = get_interface($no_inter, $ys_inter);
$no_inter = $result[1];
$wan_inter = $result[0];

$dhcp_yn = readline('   [*]Configure by DHCP? (y/n): ');
if (in_array($dhcp_yn, array('y', 'Y'))) {
  $wan_ip = '';                                                   #INPUT

}else{

  $ip = 'error';
  while(!is_ipaddr($ip)){
    $ip = readline('      [+]IP: ');
  }

  $mask = 'error';
  while(!is_ipaddr($mask)){
    $mask = readline('      [+]Mask: ');
  }

  $gateway = 'error';
  while(!is_ipaddr($gateway)){
    $gateway = readline('      [+]Gateway: ');
  }

  $wan_ip = array($ip, $mask, $gateway);                          #INPUT

}

//$xml = unset($xml->interfaces->wan);
//$xml->interfaces->removeChild('wan');
$xml->interfaces->addChild('wan', '');
$xml->interfaces->wan->addChild('descr', '');
addCData($xml->interfaces->wan->descr, strtoupper('wan'));
$xml->interfaces->wan->addChild('if', $wan_inter);

/*---------------LAN---------------*/

echo "\n[*]LAN: \n";

$result = get_interface($no_inter, $ys_inter);
$no_inter = $result[1];
$lan_inter = $result[0];

$ip = 'error';
while(!is_ipaddr($ip)){
  $ip = readline('      [+]IP: ');
}

$mask = 'error';
while(!is_ipaddr($mask)){
  $mask = readline('      [+]Mask: ');
}

$gateway = 'error';
while(!is_ipaddr($gateway)){
  $gateway = readline('      [+]Gateway: ');
}

$lan_ip = array($ip, $mask, $gateway);                            #INPUT

#$num_inter = 3;

if ($num_inter == 2){
  echo "\n[*]DHCP Server:\n";
  $dhcpd_yn = 'y';
}else{
  $dhcpd_yn = readline("\n[*]Setup DHCP Server? (y/n): ");
}

if (in_array($dhcpd_yn, array('y', 'Y'))) {

  $dhcpd_from = 'error';
  while(!is_ipaddr($dhcpd_from)){
    $dhcpd_from = readline('      [+]From: ');
  }

  $dhcpd_to = 'error';
  while(!is_ipaddr($dhcpd_to)){
    $dhcpd_to = readline('      [+]To: ');
  }

  $dhcpd_lan = array($dhcpd_from, $dhcpd_to);                     #INPUT

}else{
  $dhcpd_lan = '';                                                #INPUT

}

//unset($xml->interfaces->lan);
//$xml->interfaces->removeChild('lan');
$xml->interfaces->addChild('lan', '');
$xml->interfaces->lan->addChild('descr', '');
addCData($xml->interfaces->lan->descr, strtoupper('lan'));
$xml->interfaces->lan->addChild('if', $lan_inter);

/*---------------OPT1---------------*/


if ($num_inter > 2){

  echo "\n[*]OTP1: \n";
  $result = get_interface($no_inter, $ys_inter);
  $no_inter = $result[1];
  $opt1_inter = $result[0];

  $ip = 'error';
  while(!is_ipaddr($ip)){
    $ip = readline('      [+]IP: ');
  }

  $mask = 'error';
  while(!is_ipaddr($mask)){
    $mask = readline('      [+]Mask: ');
  }

  $gateway = 'error';
  while(!is_ipaddr($gateway)){
    $gateway = readline('      [+]Gateway: ');
  }

  $opt1_ip = array($ip, $mask, $gateway);

  echo "\n[*]DHCP Server:\n";

  $dhcpd_from = 'error';
  while(!is_ipaddr($dhcpd_from)){
    $dhcpd_from = readline('      [+]From: ');
  }

  $dhcpd_to = 'error';
  while(!is_ipaddr($dhcpd_to)){
    $dhcpd_to = readline('      [+]To: ');
  }

  $dhcpd_opt1 = array($dhcpd_from, $dhcpd_to);

}

//$xml = simplexml_load_file('/cf/conf/config.xml');

$xml = save_conf($xml, 'wan', array($wan_inter, $wan_ip));
$xml = save_conf($xml, 'lan', array($lan_inter, $lan_ip, $dhcpd_lan));

if ($num_inter > 2){
  $xml = save_conf($xml, 'opt1', array($opt1_inter, $opt1_ip, $dhcpd_opt1));
}

file_put_contents("/tmp/custom.xml", $xml->asXML());�
restore_backup("/tmp/custom.xml");

system_reboot();

?>
