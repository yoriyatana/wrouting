<?php

ob_start();
session_start();

if (isset($_SESSION['wr_id'])) {
    include("connect_db.php");
    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} logged out.','{$_SESSION['wr_id']}')";
    mysql_query($sql);
    unset($_SESSION['wr_id']);
    unset($_SESSION['wr_uname']);
    unset($_SESSION['wr_fname']);
}
header("location: login.php");
?>