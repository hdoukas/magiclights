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

// This is the main demo webpage
// It displays smartponse sensor data
// And generated a random color each time the phone is tilted
// The color is communicated to a PHP script over HTTP POST

?>


<?php

//Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


//Check if user has had activity for more then 3 minutes
//in that case chane the 'used' value and disable access to the demo
//This is done to create a one-time demo slot per user
//otherwise if many people use the app at the same time it can get messy
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 240)) {
   
    $_SESSION['used']="true";
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp


//Display the demo webpage
if($_SESSION['used']==null || $_SESSION['used']!="true"){


?>

<!DOCTYPE html>
<html lang="en"> 
<head>
<meta charset="utf-8">
<title>CREATE-NET DEMO</title>


<style type="text/css">
        body {
            font-family:sans-serif
        }
        .main {
            border:1px solid #000;
            box-shadow:10px 10px 5px #888;
            border-radius:12px;
            padding:20px;
            background-color:#fff;
            margin:25px;
            width:650px;
            margin-left:auto;
            margin-right:auto
        }
        .logo {
            width:400px;
            margin-left:auto;
            margin-right:auto;
            display:block;
            padding:15px
        }
        .container {
            -webkit-perspective:300;
            perspective:300
        }
        
        .center {
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

    </style>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.1/jquery.min.js"></script>
    

    <script type="text/javascript">
    //This part is borrowed from the typical HTML5 device orientation example:
    //http://www.html5rocks.com/en/tutorials/device/orientation/
    //by Pete Lepage
        var tmp_tiltLR = 0;
        var tmp_tiltFB = 0;
        var tmp_dir = 0;

        var temp_time;

        var tiltLR;
        var tiltFB;
        var direction;

        var accelerometerX, accelerometerY, accelerometerZ;


        function init() {
            
        	temp_time = new Date().getTime();
        	console.log(temp_time);
            if (window.DeviceOrientationEvent) {
                console.log("DeviceOrientation is supported");
                //document.getElementById("doEvent").innerHTML = "DeviceOrientation";
                // Listen for the deviceorientation event and handle the raw data
                window.addEventListener('deviceorientation', function (eventData) {
                    // gamma is the left-to-right tilt in degrees, where right is positive
                    var tiltLR = eventData.gamma;

                    // beta is the front-to-back tilt in degrees, where front is positive
                    var tiltFB = eventData.beta;

                    // alpha is the compass direction the device is facing in degrees
                    var dir = eventData.alpha

                    if (tmp_tiltLR != tiltLR) {
                    	if(Math.abs(tmp_tiltLR - tiltLR)>3) {
                    		checktime();
                    	}
                        tmp_tiltLR = tiltLR;
                    }
                    if (tmp_tiltFB != tiltFB) {
                        tmp_tiltFB = tiltFB;
                       	
                    }
                    if (tmp_dir != dir) {
                        tmp_dir = dir;
                    }


                    document.getElementById("doTiltLR").innerHTML = Math.round(tiltLR);
                    document.getElementById("doTiltFB").innerHTML = Math.round(tiltFB);
                    document.getElementById("doDirection").innerHTML = Math.round(dir);

                    document.tiltLR = Math.round(tiltLR);
                    document.tiltFB = Math.round(tiltFB);
                    document.direction = Math.round(dir);


                    

                    // Apply the transform to the image
                    var logo = document.getElementById("imgLogo");
                    logo.style.webkitTransform = "rotate(" + tiltLR + "deg) rotate3d(1,0,0, " + (tiltFB * -1) + "deg)";
                    logo.style.MozTransform = "rotate(" + tiltLR + "deg)";
                    logo.style.transform = "rotate(" + tiltLR + "deg) rotate3d(1,0,0, " + (tiltFB * -1) + "deg)";



                }, false);

				
            } else {
                
                console.log("DeviceOrientation Not supported");
            }

            if (window.DeviceMotionEvent) {
					window.addEventListener('devicemotion', deviceMotionHandler, false);
			} 
			else {
  					document.getElementById("dmEvent").innerHTML = "Not supported."
			}

        }

        function deviceMotionHandler(eventData) {
		  var info, xyz = "[X, Y, Z]";

		  // Grab the acceleration from the results
		  var acceleration = eventData.acceleration;
		  info = xyz.replace("X", Math.round(acceleration.x));
		  info = info.replace("Y", Math.round(acceleration.y));
		  info = info.replace("Z", Math.round(acceleration.z));
		  document.getElementById("moAccel").innerHTML = info;

		  document.accelerometerX = Math.round(acceleration.x);
		  document.accelerometerY = Math.round(acceleration.y);
		  document.accelerometerZ = Math.round(acceleration.z);


		  // Grab the acceleration including gravity from the results
		  acceleration = eventData.accelerationIncludingGravity;
		  info = xyz.replace("X", Math.round(acceleration.x));
		  info = info.replace("Y", Math.round(acceleration.y));
		  info = info.replace("Z", Math.round(acceleration.z));
		  document.getElementById("moAccelGrav").innerHTML = info;


		  

		  // Grab the rotation rate from the results
		  var rotation = eventData.rotationRate;
		  info = xyz.replace("X", Math.round(rotation.alpha));
		  info = info.replace("Y", Math.round(rotation.beta));
		  info = info.replace("Z", Math.round(rotation.gamma));
		  document.getElementById("moRotation").innerHTML = info;

		    

		}

        //Generate random HTML color codes
        function changeColor() {
            var letters = '0123456789ABCDEF'.split('');
            var color = '';
            for (var i = 0; i < 6; i++ ) {
                color += letters[Math.floor(Math.random() * 16)];
            }

            return color;
        }
		
        var lampColor;

        //Every 500ms send the generated color code through the magiclight.php script
		function checktime(){
			var time = new Date().getTime();
			if((time - temp_time) > 500) {
				temp_time = new Date().getTime();
				document.lampColor = changeColor();
                console.log(document.lampColor);
                var c = document.getElementById("myCanvas");
                var ctx = c.getContext("2d");
                ctx.beginPath();
                ctx.fillStyle = "#"+document.lampColor;
                ctx.arc(95,100,90,0,2*Math.PI);
                ctx.stroke();
                ctx.fill();

				$.post("magiclight.php", {color:document.lampColor},
						function(result){
    						console.log(result);
  						}
					);
			}

		}
		
    </script>
</head>


<body onload="init()">

    <div class="main">
    	<div align="middle" font-size:large;>
        <h2>CREATE-NET Mobile Demo</h2>
        <p>(Please tilt or move/shake your smartphone to change the Lamp color!!)</p>
    </div>

        <br />
        <div class="container" style="perspective: 300;">
        	<img src="http://www.create-net.org/sites/all/themes/rt_solarsentinel_d6/images/header/blue/logo.png" id="imgLogo" class="logo">
    	</div>
    	<br />
    	<br />
        <table>
            <tr><td>
                    <tr>
                        <td>Tilt Left/Right [gamma]</td>
                        <td id="doTiltLR"></td>
                    </tr>
                    <tr>
                        <td>Tilt Front/Back [beta]</td>
                        <td id="doTiltFB"></td>
                    </tr>
                    <tr>
                        <td>Direction [alpha]</td>
                        <td id="doDirection"></td>
                    </tr>
                    <tr>
              <td>Event Supported</td>
              <td id="dmEvent"></td>
            </tr>
            <tr>
              <td>acceleration</td>
              <td id="moAccel"></td>
            </tr>
            <tr>
              <td>accelerationIncludingGravity</td>
              <td id="moAccelGrav"></td>
            </tr>
            <tr>
              <td>rotationRate</td>
              <td id="moRotation"></td>
            </tr>
            </td>
        </tr>
        <tr>
            <td>
                <br />
                <br />
                <br />
                <br />
                <div class="center">
                    <canvas id="myCanvas" width="300" height="300" style="border:0px">
                </div>
            </td>
        </tr>

        </table>

    </div>

    


</body>

</html>



<?php
}

else {
	//User has already exceeded the 3min demo session

?>
<!DOCTYPE html>
<html lang="en"> 
<head>
<meta charset="utf-8">
<title>CREATE-NET DEMO</title>
</head>

Thank you for using <a href="http://www.create-net.org">CREATE-NET</a> Demo!

</html>


<?php

}
?>