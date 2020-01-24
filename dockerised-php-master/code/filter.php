<?php

//get the file through the query parameter
$inputquery = $_GET["getfile"];
if (!$inputquery) {
    die("Dude, you didn't gimme anything ;(");
}

//avoid hash injection for last task
$inputquery = explode("#", $inputquery)[0]

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
        die("Path Traversal attack detected. This incident will be reported.")
    }
} else if (strpos($inputquery, "Boss.jpg") !== false) {
    //no encodings here, but filters not recursive --> nested payloads
    $file = str_replace("../", "", $file);
    $file = str_replace(".+.+/", "", $file);
    $file = str_replace(". . /", "", $file);
    $file = str_replace("..;/", "", $file);
    $file = str_replace("..\\/", "", $file);
    $file = recursivefilter ($file, "%")
    $filterpass = true
} else if (strpos($inputquery, "Victory.html") !== false) {
    //null byte injection attack
    $file = $file . ".txt"
} else {
    //we got it, Gandalf
    die("You shall not pass!")
}

if (!file_exists($file)) {
	http_response_code(404);
	die("U high, dude?");
}


header("Content-Disposition: attachment; filename='" . basename($file) . "'");
header("Content-Type: application/octet-stream");
readfile($file);
die;
