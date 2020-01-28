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


echo '<p></p>
<p></p>
<p>
<script src="http://use.edgefonts.net/butcherman.js"></script>
</p>
<header>
<title>You are lost</title>

</header>
<body>
<h1 style="text-align: center;">Lost in the GhostWorld</h1>
<center><br />
<table style="width: 100%; border-collapse: collapse; border-style: hidden; empty-cells: hidden;" border="1">
<tbody>
  
  <script>
 onload= document.body.style.backgroundColor = "gray";
 onload= window.alert(\'As your journey progresses throw the mythical labyrinth you start to get tired and take a nap. During your sleep you felt some kind of soft breeze that went through your body. You can feel that it happens again and again.\');
</script>
  
  
<tr style="height: 110px;">
<td style="width: 33.3768%; height: 120px;"><img src="ghoste reversed.png" alt="" style="float: right;" width="193" height="186" /></td><td style="width: 33.3768%; height: 120px; border-style: hidden;"><span style="color: #ff0000;"></span></td>
<td style="width: 33.3768%; height: 120px;"><img src="ghost.png" alt="" width="192" height="185" /></td>
</tr>
<tr style="height: 18px;">
<td style="width: 30%; height: 18px; border-style: hidden;"></td>
<td style="width: 40%; height: 18px; text-align: justify; border-style: hidden;font-family: butcherman"><h2><span style="color: #ff0000;">One of us has the key you desire. If you think that you have found it then pierce one of the five fallen soldiers with your sword. Will you guess the correct one?</span></h2></td>
<td style="width: 30%; height: 18px; border-style: hidden;"></td>
</tr>
<tr style="height: 20px;">
<td style="width: 33.3768%; height: 20px; border-style: hidden;"><img src="ghoste reversed.png" alt="" style="float: right" width="180" height="173" /></td>
<td style="width: 33.3768%; height: 20px; border-style: hidden;"></td>
<td style="width: 33.3768%; height: 20px; border-style: hidden;"><img src="ghost.png" alt="" style="display: block; margin-left: auto; margin-right: auto;" width="198" height="190" /></td>
</tr>
</tbody>
</table>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>

<p></p>
<p></p>
<p></p>
<a href="ghost.html"><img src="ghost.png" alt="" style="float: right;" width="11" height="9"";/></center></a>

</body>';
?>
