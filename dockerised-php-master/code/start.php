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
            die("We don't like swords. You can't hide them.");
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
        die("I'm sorry. My scrying did not reveal the following object in any known dimension: " . $file);
    }


    header("Content-Disposition: attachment; filename=". basename($file));
    header("Content-Type: application/octet-stream");
    readfile($file);
    die;

}
echo  '<!DOCTYPE html>
<html>
<head>
<title>Get your horse</title>
<style>
.middle {
  position: relative;
  text-align:center;
  top: 10px;  
  left: 100px;
  
  font-size: 18px;
  color: black;
}

.middle_underneath {
  position: relative;
  text-align:center;  
  top: 70px;  
  left: 100px;
  font-size: 18px;
  color: black;
}
body  {
  background-size: cover;
  background-repeat: repeat;
  background-image: url("ranche.jpg");
  background-color: #FFFFFF;
  width: 50%;
  higth: auto;
}
</style>
</head>
<body>


<div class=middle>
	<h1>Ah so you think that you are mighty enough to take me on. Well let’s test your strength, but first, let’s get you a horse.</h1>
</div>
<div class=middle_underneath>
   <h1> Here is the first hint. A hint might not be easy to find or you might find them in some path. To solve this first exercise we will guide you through. You will have to request the getfile that contains horse.jpg. (Getfile might be useful for the rest of the challenge)</h1>
</div>

</body>
</html>';
?>
