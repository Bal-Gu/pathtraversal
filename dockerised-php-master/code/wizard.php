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

    $inputquery = $file;
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
        die("I'm sorry. My scrying did not reveal the following object in any known dimension: " . $inputquery);
    }


    header("Content-Disposition: attachment; filename=". basename($file));
    header("Content-Type: application/octet-stream");
    readfile($file);
    die;

}
echo '
<!DOCTYPE html>
<html>
<head>
<title> Betrail </title>
<style>

body  {
  background-size: cover;
  background-repeat: no-repeat;
  background-image: url("fantasy_mage_wizard_sorcerer_art_artwork_magic_magician_1920x1080.png");
  background-color: #FFFFFF;
}
</style>
<script> 
onload= alert(\'I see you have used your special Xray command.\n\t\t\t\t Come in!\');
</script>
</head>
<body>
<p hidden> If you are still in the view:source page just delete the view:source bevor the http:// </p>
</body>
</html>'

?>

