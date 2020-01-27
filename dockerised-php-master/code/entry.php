<?php
//get the file through the query parameter
$inputquery = $_GET["getfile"];
if ($inputquery) {


    //avoid hash injection for last task
    $inputquery = explode("#", $inputquery)[0];

    //prepare file for download
    $file = $inputquery;

    $filterpass = false;
    $nullbyte = false;
    $encode = false;

    //recursive filter function
    function recursivefilter ($file, $str) {
        while (strpos($file, $str) !== false) {
            $file = str_replace($str, "", $file);
        }
        return $file;
    }

    //whitelist files that may be leaked and adjust the filters for each task
    if (strpos($inputquery, "horse.jpg") !== false) {
        //no filter
        $filterpass = true;
    } else if (strpos($inputquery, "map.png") !== false) {
        //reverse slashes
        $filterpass = true;
        $file = str_replace("/", "REVERSESW0RD", $file);
        $file = str_replace("\\", "/", $file);
        $file = str_replace("REVERSESW0RD", "\\", $file);
    } else if (strpos($inputquery, "Xray.jpg") !== false) {
        //instead of really long blacklist, we whitelisted some payloads that should pass
        //since URL decode is not recurive, this is output of double-url-encoding, whitelisted payload
        if (strpos($inputquery, "%2e") !== false) {
            $filterpass = true;
            $encode = true;
        } else
        if (strpos($inputquery, "%2E") !== false) {
            $filterpass = true;
            $encode = true;
        } else
        if (strpos($inputquery, "%2f") !== false) {
            $filterpass = true;
            $encode = true;
        } else
        if (strpos($inputquery, "%2F") !== false) {
            $filterpass = true;
            $encode = true;
        } else {
            die($file);
        }
    } else if (strpos($inputquery, "Boss.jpg") !== false) {
        //no encodings here, but filters not recursive  nested payloads
        $file = str_replace("../", "", $file);
        $file = str_replace(".+.+/", "", $file);
        $file = str_replace(". . /", "", $file);
        $file = str_replace("..;/", "", $file);
        $file = str_replace("..\\/", "", $file);
        $file = recursivefilter ($file, "%");
        $filterpass = true;
    } else if (strpos($inputquery, "Victory.html") !== false) {
        //null byte injection attack
        $file = $file . ".txt";
        //we had to simulate the vulnerability, since we can't get the vulnerable php version running
        $nullbyte = true;
    } else {
        //we got it, Gandalf
        die("You shall not pass!");
    }

    $file = "items/items/items/items/items/items/items/" . $file;
    //simulate null byte injection from php 5.1.5
    if ($nullbyte) {
        $file = explode("%00", $file)[0];
    }
    
    if ($encode) {
        //manually decode a second time
        $file = str_replace("%2e", ".", $file);
        $file = str_replace("%2E", ".", $file);
        $file = str_replace("%2f", "/", $file);
        $file = str_replace("%2F", "/", $file);
    }
    
    if (!file_exists($file)) {
        http_response_code(404);
        die($file);
    }


    header("Content-Disposition: attachment; filename=". basename($file));
    header("Content-Type: application/octet-stream");
    readfile($file);
    die;

}

echo '<!DOCTYPE html>
<html>
<head>
<title> Read the RULES </title>
<style>
.Table1 {
  position: relative;
  width: 1080px;
  bottom: 320px;
  font-size: 40px;
} 

.Table2 {
  position: relative;
  width: 1080px;
  bottom: 640px;
  font-size: 40px;
} 

.Table3 {
  position: relative;
  width: 1080px;
  bottom: 960px;
  font-size: 40px;
} 


.Table4 {
  position: relative;
  width: 1080px;
  bottom: 1280px;
  font-size: 40px;
} 

.Table5 {
  position: relative;
  width: 1080px;
  bottom: 1600px;
  font-size: 40px;
} 
.Table6 {
  position: relative;
  width: 1080px;
  bottom: 1920px;
  font-size: 40px;
} 

.image{
	    position:absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    margin:100px;
}
.button {
  background-color: #d4b58a;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}
.bottom {
  position: fixed;
  left: 50%;
  bottom: 20px;
  transform: translate(-50%, -50%);
  margin: 0 auto;
}
body  {
  background-size: cover;
  background-repeat: repeat;
  background-image: url("Wall.jpg");
  background-color: #FFFFFF;
  width: 50%;
  higth: auto;
}
</style>
</head>
<body>
<div class=image>
	<img src="rotate papyrus.png" alt="" width="1080" height="1920">
		<div class=Table1> <strong> * </strong> Every world is separated by a dimension. Every dimension is how far you are away from your home.</div>
		<div class=Table2> <strong> * </strong> In every new URL you will find a hint to solve to get the next section of the maze</div>
		<div class=Table3> <strong> * </strong> The secret spell to cast a door through  another dimension is always {current URL}?getfile={the object you are looking for}</div>
		<div class=Table4> <strong> * </strong> All curse is only working in one specific dimension. So as long as you are on a specific URL the rules in the hint will apply but disappear if the URL changes.</div>
		<div class=Table5> <strong> * </strong> In case you are wondering you have a @xxxx[{::::::::::::::> that is represented as /.You will have to use it to cut through the dimension.</div>
		<div class=Table6> <strong> * </strong> If you want to hear the hints in our world you might have to disable NoScript etc...</div>
</div>
<div class="bottom">
	<a href="Mission3.html" class="button">
	Press if you\'re ready!
	</a>
</div>
</body>
</html>';
?>
