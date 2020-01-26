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

