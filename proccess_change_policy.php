<?php

if (isset($_POST['prefix1'])) {
    include('functions.php');

    $prefix = $_POST['prefix1'];
    $sql = "SELECT * FROM `peers`";
    $query = mysql_query($sql);
    $count = mysql_num_rows($query);
    $elements[] = array($count);

    while ($row = mysql_fetch_array($query)) {
        if (isset($_POST['checkbox1' . $row["pee_id"]])) {
            $elements[$row['pee_id'] - 1] = true;
        } else {
            $elements[$row['pee_id'] - 1] = false;
        }
    }

    $sql = "SELECT * FROM `policies` WHERE (`pol_prefix` = '{$prefix}');";
    $query = mysql_query($sql);
    $count = mysql_num_rows($query);

    if ($count > 0) {
        $row = mysql_fetch_array($query);
        $sql1 = "SELECT * FROM `peers`;";
        $query1 = mysql_query($sql1);
        while ($row1 = mysql_fetch_array($query1)) {
            if ($elements[$row1['pee_id'] - 1] == true && $row["{$row1['pee_id']}"] == 0) {
                change_policy($prefix, $row1['pee_id'], false);
            }
            if ($elements[$row1['pee_id'] - 1] == false && $row["{$row1['pee_id']}"] == 1) {
                change_policy($prefix, $row1['pee_id'], true);
            }
        }

//            if ($ntt != $row['ntt'] || $ghk != $row['ghk'] || $vtni != $row['vtn'] || $telia != $row['telia'] || $telia2 != $row['telia-eu'] || $vtcdigi != $row['vtcdigi']) {
//                echo "<br>Cập nhật lại thông tin Äánh tuyến vào file";
//                include("functions.php");
//                updatedb();
        echo "<table class='a'><tbody><tr><td>Đã hoàn tất cập nhật thông tin định tuyến.</td></tr></tbody></table>";
//            }
        ob_start();
        session_start();
        $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} changed routing policy for prefix {$prefix}','{$_SESSION['wr_id']}')";
        mysql_query($sql);
    }
}
?>

