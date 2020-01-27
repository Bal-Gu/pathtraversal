<?php

//get the file through the query parameter
$inputquery = $_GET["getfile"];
if ($inputquery) {


    //avoid hash injection for last task
    //$inputquery = explode("#", $inputquery)[0];

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
echo "<!DOCTYPE html>
<html><head>

<script src='http://use.edgefonts.net/nosifer.js'></script>
<title>You're going to die</title>
<style>
.bottom-left {
  position: absolute;
  bottom: 50px;
  left: 130px;
  font-size: 18px;
  font-family: nosifer;
  color: red;
}
.container {
  position: relative;
  text-align: center;
  color: white;
}

.spellbook{
   
    position: absolute;
	left: 10px;	
	bottom: 10px;
	}
body  {
  background-size: cover;
  background-repeat: no-repeat;
  background-image: url('1078094-beautiful-the-lich-king-wallpaper-1920x1080-high-resolution.jpg');
  background-color: #FFFFFF;
}
</style>
</head>
<body>";

    echo "<div class='spellbook'>
        <a href='spellbook.html'><img src='toppng.com-spell-book-674x313.png'></a>
    </div>
    <div class=bottom-left>
        Click the Spellbook for help
    </div>
    
</body>
</html>";
?>
