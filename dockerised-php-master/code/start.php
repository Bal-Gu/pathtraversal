<?php

//get the file through the query parameter
$inputquery = $_GET["getfile"];
if ($inputquery) {


    //avoid hash injection for last task
    $inputquery = explode("#", $inputquery)[0];

    //prepare file for download
    $file = "items/" . $inputquery;

    $filterpass = false;

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
        if (strpos($inputquery, "%252e") !== false) {
            $filterpass = true;
        } else
        if (strpos($inputquery, "%252E") !== false) {
            $filterpass = true;
        } else
        if (strpos($inputquery, "%252f") !== false) {
            $filterpass = true;
        } else
        if (strpos($inputquery, "%252F") !== false) {
            $filterpass = true;
        } else
        if (strpos($inputquery, "%e0%40%ae") !== false) {
            $filterpass = true;
        } else
        if (strpos($inputquery, "%e0%80%af") !== false) {
            $filterpass = true;
        } else {
            die("Path Traversal attack detected. This incident will be reported.");
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
    } else {
        //we got it, Gandalf
        die("You shall not pass!");
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
