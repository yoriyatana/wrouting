<?php

ob_start();
session_start();
include("connect_db.php");

set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib0.3.6');
include('Net/SSH2.php');

function updatedb() {
    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} thực hiện cập nhật chính sách định tuyến.','{$_SESSION['wr_id']}')";
    mysql_query($sql);
    $sql1 = "SELECT * FROM `users` WHERE `use_operate` = 1";
    $query1 = mysql_query($sql1);
    $count1 = mysql_num_rows($query1);
    if ($count1 == 1) {
        $row1 = mysql_fetch_array($query1);
        $sql = "INSERT INTO logs VALUES ('',now(),'Hiện user {$row1['use_user']} đang thao tác trên hệ thống.','{$_SESSION['wr_id']}')";
        mysql_query($sql);
        echo "<script>alert('Hiện user {$row1['use_user']} đang thao tác trên hệ thống.')</script>";
    } else {
	$start = microtime(true);
        $sql = "UPDATE `users` SET `use_operate`=1 WHERE `use_id`={$_SESSION['wr_id']}";
        mysql_query($sql);

	$output = shell_exec('/usr/local/bin/python2.7 functions.py');
	echo "<script>alert('{$output}')</script>";

        $sql = "UPDATE `users` SET `use_operate`=0 WHERE `use_id`={$_SESSION['wr_id']}";
        mysql_query($sql);

        $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} hoàn tất cập nhật chính sách định tuyến.','{$_SESSION['wr_id']}')";
        mysql_query($sql);
    }
}

function change_policy($prefix, $pee_id, $deny) {
    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} thực hiện thay đổi chính sách định tuyến lớp {$prefix}.','{$_SESSION['wr_id']}')";
    mysql_query($sql);
    $sql1 = "SELECT * FROM `users` WHERE `use_operate` = 1";
    $query1 = mysql_query($sql1);
    $count1 = mysql_num_rows($query1);

    if ($count1 == 1) {
        $row1 = mysql_fetch_array($query1);
        $sql = "INSERT INTO logs VALUES ('',now(),'Hiện user {$row1['use_user']} đang thao tác trên hệ thống.','{$_SESSION['wr_id']}')";
        mysql_query($sql);
        echo "<script>alert('Hiện user {$row1['use_user']} đang thao tác trên hệ thống.')</script>";
    } else {
        $sql = "UPDATE `users` SET `use_operate`=1 WHERE `use_id`={$_SESSION['wr_id']}";
        mysql_query($sql);

        $sql = "SELECT pee_id,pee_name,pee_location,pee_remote_ip,pee_denyPrefix,devices.dev_id,dev_ip,dev_protocol,dev_port,dev_user,dev_pass,dev_pass_en,dev_type,ups_name FROM `peers` JOIN `devices` ON `peers`.`dev_id`=`devices`.`dev_id` JOIN `upstreams` ON `peers`.`ups_id`=`upstreams`.`ups_id` WHERE (`pee_id`='{$pee_id}')";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_array($query)) {
            switch ($row['dev_protocol']) {
                case "ssh":
                    $ssh = new Net_SSH2("{$row['dev_ip']}", $row['dev_port']);
                    if (!$ssh->login("{$row['dev_user']}", "{$row['dev_pass']}")) {
                        echo "SSH has failed to {$row['dev_ip']}.";
                    } else {
                        switch ($row['dev_type']) {
//                            case "qua":
//                                if ($deny) {
//                                    $ssh->exec("vtysh -d bgpd -c 'conf t' -c 'ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix . "'");
//                                    $ssh->exec("vtysh -d bgpd -c 'clear ip bgp " . $row['pee_remote_ip'] . " soft out'");
//                                    $ssh->disconnect();
//                                    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã ngắt quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
//                                    mysql_query($sql);
//                                    echo "<table class='a'><tbody><tr><td>Đã ngắt quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
//                                } else {
//                                    $ssh->exec("vtysh -d bgpd -c 'conf t' -c 'no ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix . "'");
//                                    $ssh->exec("vtysh -d bgpd -c 'clear ip bgp " . $row['pee_remote_ip'] . " soft out'");
//                                    $ssh->disconnect();
//                                    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
//                                    mysql_query($sql);
//                                    echo "<table class='a'><tbody><tr><td>Đã quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
//                                }
//                                break;
                            case "jun":
                                switch ($row['dev_id']) {
                                    case 5:
                                        if ($deny) {
                                            $ssh->exec("configure private;
                                                        set logical-systems I1 policy-options policy-statement " . $row['pee_denyPrefix'] . " term 1 from route-filter " . $prefix . " exact;
                                                        commit and-quit;");
                                            $ssh->disconnect();
                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã ngắt quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
                                            mysql_query($sql);
                                            echo "<table class='a'><tbody><tr><td>Đã ngắt quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
                                        } else {
                                            $ssh->exec("configure private;
                                                        delete logical-systems I1 policy-options policy-statement " . $row['pee_denyPrefix'] . " term 1 from route-filter " . $prefix . " exact;
                                                        commit and-quit;");
                                            $ssh->disconnect();
                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
                                            mysql_query($sql);
                                            echo "<table class='a'><tbody><tr><td>Đã quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
                                        }
                                        break;
                                    case 6:
                                        if ($deny) {
                                            $ssh->exec("configure private;
                                                        set logical-systems I2 policy-options policy-statement " . $row['pee_denyPrefix'] . " term 1 from route-filter " . $prefix . " exact;
                                                        commit and-quit;");
                                            $ssh->disconnect();
                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã ngắt quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
                                            mysql_query($sql);
                                            echo "<table class='a'><tbody><tr><td>Đã ngắt quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
                                        } else {
                                            $ssh->exec("configure private;
                                                        delete logical-systems I2 policy-options policy-statement " . $row['pee_denyPrefix'] . " term 1 from route-filter " . $prefix . " exact;
                                                        commit and-quit;");
                                            $ssh->disconnect();
                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
                                            mysql_query($sql);
                                            echo "<table class='a'><tbody><tr><td>Đã quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
                                            break;
                                        }
                                        break;
				    case 7:
                                        if ($deny) {
                                            $ssh->exec("configure private;
                                                        set policy-options policy-statement " . $row['pee_denyPrefix'] . " term 1 from route-filter " . $prefix . " exact;
                                                        commit and-quit;");
                                            $ssh->disconnect();
                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã ngắt quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
                                            mysql_query($sql);
                                            echo "<table class='a'><tbody><tr><td>Đã ngắt quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
                                        } else {
                                            $ssh->exec("configure private;
                                                        delete policy-options policy-statement " . $row['pee_denyPrefix'] . " term 1 from route-filter " . $prefix . " exact;
                                                        commit and-quit;");
                                            $ssh->disconnect();
                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
                                            mysql_query($sql);
                                            echo "<table class='a'><tbody><tr><td>Đã quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
                                            break;
                                        }
                                        break;
                                }
                                break;
//                            case "telnet":
//                                $telnet = new CiscoTelnet();
//                                $result = $telnet->Connect("{$row['dev_ip']}", $row['dev_port'], "{$row['dev_user']}", "{$row['dev_pass']}");
//                                switch ($result) {
//                                    case 0:
//                                        if ($deny) {
//                                            $telnet->enable("{$row['dev_pass_en']}");
//                                            $telnet->DoCommand("conf t");
//                                            $telnet->DoCommand("ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix);
//                                            $telnet->DoCommand("end");
//                                            $telnet->DoCommand("clear ip bgp " . $row['pee_remote_ip'] . " soft out");
//                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã ngắt quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
//                                            mysql_query($sql);
//                                            echo "<table class='a'><tbody><tr><td>Đã ngắt quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
//                                        } else {
//                                            $telnet->enable("{$row['dev_pass_en']}");
//                                            $telnet->DoCommand("conf t");
//                                            $telnet->DoCommand("no ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix);
//                                            $telnet->DoCommand("end");
//                                            $telnet->DoCommand("clear ip bgp " . $row['pee_remote_ip'] . " soft out");
//                                            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} đã quảng bá prefix {$prefix} hướng {$row['ups_name']}-{$row['pee_location']}','{$_SESSION['wr_id']}')";
//                                            mysql_query($sql);
//                                            echo "<table class='a'><tbody><tr><td>Đã quảng bá prefix " . $prefix . " hướng " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table>";
//                                        }
//                                        break;
//                                    case 1:
//                                        echo '[PHP Telnet] Connect failed: Unable to open network connection';
//                                        break;
//                                    case 2:
//                                        echo '[PHP Telnet] Connect failed: Unknown host';
//                                        break;
//                                    case 3:
//                                        echo '[PHP Telnet] Connect failed: Login failed';
//                                        break;
//                                    case 4:
//                                        echo '[PHP Telnet] Connect failed: Your PHP version does not support PHP Telnet';
//                                        break;
//                                }
//                                $telnet->Disconnect();
//                                break;
                            default:
                                echo "<script>alert('Giao thức {$row['dev_protocol']} chưa được hỗ trợ.')</script>";
                                $sql = "INSERT INTO logs VALUES ('',now(),'Giao thức {$row['dev_protocol']} chưa được hỗ trợ.')";
                                mysql_query($sql);
                        }
                        $sql = "UPDATE `users` SET `use_operate`=0 WHERE `use_id`={$_SESSION['wr_id']}";
                        mysql_query($sql);

                        $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} hoàn tất thay đổi chính sách định tuyến lớp {$prefix}.','{$_SESSION['wr_id']}')";
                        mysql_query($sql);
                    }
            }
        }
    }
}

function show_policy($prefix, $pee_id, $deny) {
//    $sql = "SELECT pee_id,pee_name,pee_location,pee_denyPrefix,dev_ip,dev_protocol,dev_port,dev_user,dev_pass,dev_pass_en,pee_remote_ip,ups_name FROM `peers` JOIN `devices` ON `peers`.`dev_id`=`devices`.`dev_id` JOIN `upstreams` ON `peers`.`ups_id`=`upstreams`.`ups_id` WHERE (`pee_id`='{$pee_id}')";
//    $query = mysql_query($sql);
//
//    while ($row = mysql_fetch_array($query)) {
//        if ($row['dev_protocol'] == "ssh") {
//            echo "<table class='a'><tbody><tr><td>SSH to {$row['dev_ip']}.<br>";
//            if ($deny) {
//                echo "vtysh -d bgpd -c 'conf t' -c 'ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix . "'<br>";
//                echo "vtysh -d bgpd -c 'clear ip bgp " . $row['pee_remote_ip'] . " soft out'<br>";
//                echo "ssh->disconnect()<br>";
//                echo "Da ngat quang ba prefix " . $prefix . " huong " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table><br>";
//            } else {
//                echo "vtysh -d bgpd -c 'conf t' -c 'no ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix . "'<br>";
//                echo "vtysh -d bgpd -c 'clear ip bgp " . $row['pee_remote_ip'] . " soft out'<br>";
//                echo "ssh->disconnect()<br>";
//                echo "Da quang ba prefix " . $prefix . " huong " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table><br>";
//            }
//        } else {
//            if ($row['dev_protocol'] == "telnet") {
//                echo "<table class='a'><tbody><tr><td>Telnet to {$row['dev_ip']}.<br>";
//                if ($deny) {
//                    echo "enable<br>";
//                    echo "conf t<br>";
//                    echo "ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix . "<br>";
//                    echo "end<br>";
//                    echo "clear ip bgp " . $row['pee_remote_ip'] . " soft out<br>";
//                    echo "Da ngat quang ba prefix " . $prefix . " huong " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table><br>";
//                } else {
//                    echo "enable<br>";
//                    echo "conf t<br>";
//                    echo "no ip prefix-list " . $row['pee_denyPrefix'] . " permit " . $prefix . "<br>";
//                    echo "end<br>";
//                    echo "clear ip bgp " . $row['pee_remote_ip'] . " soft out<br>";
//                    echo "Da quang ba prefix " . $prefix . " huong " . $row['ups_name'] . "-" . $row['pee_location'] . ".</td></tr></tbody></table><br>";
//                }
//                echo "telnet->Disconnect()<br>";
//            }
//        }
//    }
}

?>

