<?
require_once("config.inc");
require_once("services.inc");

$config = parse_xml_config("{$g['conf_path']}/config.xml", $g['xml_rootobj']);
write_config();
?>
