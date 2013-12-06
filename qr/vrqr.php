
<html>
<head>
    <title>vR qR</title>
    <style>
        body {
            background-color:black;
            color:white;
        }
        table, td, th {
            border:1px solid red;

        }

        th {
            text-align: center;
            background-color:black;
            color:#ffffff;
            padding:6px;
        }

        td {
            text-align: center;
            background-color:black;
            color:#ffffff; 
            padding:6px;
        }
    </style>

</head>
<center>
<?php
class Action
{
    const View = 0;
    const Create = 1;
}

error_reporting(E_ALL);

//db connect
$db = new mysqli('localhost', 'vr', 'b1gmed1a', 'vrqr');
if($db->connect_errno > 0) {
    errdie('err c0nnect1ng t0 d474 570r3 [' . $db->connect_error . ']');
}

//todo enum
if($_REQUEST['action'] == "create") {
    $action = Action::Create;
} else {
    $action = Action::View;
}

// view control
if($action == Action::Create) {
    qrCreateView();
} else {
    qrTableView();
}

// ** VIEWS ** //

function qrCreateView() {
    echo <<<CREATEHDR
<h3>cre4te vRqR</h3>
<form method="POST">
<table border="1">

<tr>
<td>Name:<br />(jfyi)</td>
<td><input type="text" name="new_name"></td>
</tr>

<tr>
<td>Max Usages:<br />(0=unlimited)</td>
<td><input type="text" name="new_max_usages" value="0"></td>
</tr>

<tr>
<td>Payload</td>
<td><textarea name="new_payload" rows=5 cols=20></textarea></td>
</tr>

</table>
<br />
<input type="hidden" name="action" value="create">
<input type="submit" name="m4k3 m3">
</form>
CREATEHDR;
}

function qrTableView() {
    global $db;

    //get all qr_entries
    $sql = <<<SQL
        SELECT *
        FROM `qr_entries`
SQL;
    if(!$qr_entries = $db->query($sql)){
        errdie('err w17h 7h3 qu3ry [' . $db->error . ']');
    }

    //no qr_entries
    if($qr_entries->num_rows == 0) {
        errdie('n0 vRqR!!');
    }

    //table of qr entries
    echo <<<TBLHDR
    <table border="1">
        <tr>
            <th>name</th>
            <th>max usages</th>
            <th>current usages</th>
            <th>active?</th>
            <th>qr</th>
        </tr>
TBLHDR;

    while($qr_entry = $qr_entries->fetch_assoc()) {
        $active = ($qr_entry['max_usages'] > $qr_entry['current_usages']) ? "YES" : "NO";

        $url = "http://virtuality.io/qr/" . $qr_entry['ref_hash'];
        $qrimg = googleQR($url,100,0);
        $qrlink = googleQR($url,500,1);
        echo <<<TBLROW
        <tr>
            <td>$qr_entry[ref_name]</td>
            <td>$qr_entry[max_usages]</td>
            <td>$qr_entry[current_usages]</td>
            <td>$active</td>
            <td><a href="$qrlink" target="_newqr"><img src="$qrimg" height="50" width="50" border="2"></a></td>
        </tr>
TBLROW;
    }
    echo "</table>";

    $qr_entries->free();
}

// ** FUNCTIONS ** //

// generate a short, unique, not-quite-guessable ID suitable for QR encoding (low data)
function generateShortId() {
    global $db;

    $sql = <<<SQL
    SELECT ref_hash
    FROM `qr_entries`
SQL;
    $qr_ref_hashes = $db->query($sql);

    $short_ids = array();
    while($qr_entry = $qr_ref_hashes->fetch_assoc()) {
        if($qr_entry['ref_hash']) {
            array_push($short_ids,$qr_entry['ref_hash']);
        }
    }

    do {
        $newId = randStr();
    } while(in_array($newId, $short_ids)); // TODO fix possible infinite loop; bail if too many tries

    return $newId;
}

function randStr($length = 6) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $str;
}

// googleQR: string, int --> string
// txt, pixel size --> string to img src
function googleQR($txt,$size,$spacing=0) {
    return "https://chart.googleapis.com/chart?chs=${size}x${size}&cht=qr&chl=" . urlencode($txt) . "&chld=H|${spacing}&choe=UTF-8";
}

function errdie($msg) {
    die("$msg \n</font></center></body></html>");
}
?>
</font>
</center>
</body>
</html>
