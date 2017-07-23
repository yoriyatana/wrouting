<?php
session_start();
ob_start();
if (!isset($_SESSION['wr_id']))
    header("location:login.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=11" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta http-equiv="content-style-type" content="text/css" />
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script language="javascript" src="scripts/scripts.js"></script>

    </head>
    <body>
        <div id="topBar"><div class="pageWrap"> <div id="toplinks" class="toplinks"> <ul class="isuser"> <li><a rel="nofollow" href="signup.php" >Signup</a></li> <li><a rel="nofollow" href="logout.php" onclick="return log_out('Bạn có muốn thoát khỏi tài Khoản <?php echo $_SESSION['wr_fname'] ?> không?')">Thoát</a></li> <li class="welcomelink">Xin chào, <a><?php echo $_SESSION['wr_fname'] ?></a>.</li>    </ul> </div> </div></div>
        <div id="top">
            <div class="wrap">
                <h1><a href="#"><img src="css/images/logo.png" align="absmiddle">Routing Policy Page</a></h1>
            </div>
            <div id="nav">
                <ul>
                    <li class="b"><a href="index.php">Home</a></li>
                    <li><a href="update-policy.php">Update</a></li>
                    <li><a href="change-policy.php">Change</a></li>
                    <li><a href="logs.php">logs</a></li>
                </ul>
            </div>
            <br style="clear:both;" />
        </div>
        <!--/top-->
