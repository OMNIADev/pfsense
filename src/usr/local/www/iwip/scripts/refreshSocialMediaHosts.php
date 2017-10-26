<?php

require_once("/usr/local/www/iwip/cpsocialmedia.class.php");

$cp = new cpsocialmedia();

$zone = 'iwip';
$cp->refreshSocialMediaHosts($zone);

