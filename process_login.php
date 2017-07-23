<?php

ob_start();
session_start();

if (isset($_POST['submit'])) {
    include("connect_db.php");
    $sql = "SELECT * FROM users WHERE (`use_user` = '{$_POST['user']}' AND `use_active` = '1')";
    $query = mysql_query($sql);

    if (mysql_num_rows($query)) {
        $row = mysql_fetch_array($query);

        if (md5($_POST['pass']) == $row['use_pass']) {
            $_SESSION['wr_id'] = $row['use_id'];
            $_SESSION['wr_uname'] = $row['use_user'];
            $_SESSION['wr_fname'] = $row['use_fullname'];
            $sql1 = "INSERT INTO logs VALUES ('',now(),'User {$row['use_user']} logged in.','{$row['use_id']}')";
            mysql_query($sql1);
            header("location: index.php");
        }
        else
            header("location: login.php");
    }
    else
        header("location: login.php");
}
?>