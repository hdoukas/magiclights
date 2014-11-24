magiclights
===========

Philips Hue demo at TEDx Trento 2014, changing colour based on Tweets and user Smartphone movements.

The demo consists of:

	- A Philip Hue Lamp that changes color randomly when users Tweet using a special hashtag
	- A Phlip Hue Lamp that changes color based on user's smartphone movement
	- A webpage that detects the smartphone device orientation, displays the data, and generates a random HTML color code for controlling the light 
	- A [Node-RED] flow that runs on a RaspberryPi and monitors the Twitter feeds, 

This is the code that implements:

	- A webpage (index.php) that detects smarthone orientation and delivers a random color to a Philips Hue lamp
	- The script (magiclight.php) that acts as a MQTT client for delivering the random color from the webpage to the Philips Hue lamp
	- The [Node-RED] flow that receives color codes through MQTT and sends it to the Philips Hue bridge (using a Node-RED node for the Hue)





### Requirements

* A Web Server to host the smartphone webpage (index.php), and the MQTT client schipt (magiclight.php), PHP > v4
* A RaspberryPi (or a computer) with Internet connection (to fetch Tweets) being in the same netwotk with the Philips Hue Bridge
* [Node-RED] installation with the Philips Hue node (install [node-red-nodes])


License
----

Apache 2

[Node-RED]:http://node-red.org	
[node-red-nodes]:https://github.com/node-red/node-red-nodes
