<?php
/*

Copyright 2014 Charalampos Doukas - @buildingiot

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

*/

// This is the script for sending the color code through MQTT 
// To the Philips Hue bridge (via a RaspberryPi)
// Requires the phpMQTT.php MQTT client from:
//https://github.com/bluerhinos/phpMQTT

?>

<?php

require("phpMQTT.php");

$MQTT_Broker = ""; //PUT YOUR MQTT BROKER IP/DOMAIN HERE
$MQTT_PORT = 1883;


if(isset($_POST['color'])) {

	$mqtt = new phpMQTT($MQTT_Broker, $MQTT_PORT, "php_magiclight"); //Change client name to something unique
	if ($mqtt->connect()) {
		$mqtt->publish("magiclight",$_POST['color'],0);
		$mqtt->close();

		echo "You sent: #".$_POST['color'];
	}

}

/*else {
	
$mqtt = new phpMQTT("", 1883, "php_magiclight"); //Change client name to something unique
if ($mqtt->connect()) {
	$mqtt->publish("magiclight",date("r"),0);
	$mqtt->close();
}
*/




?>