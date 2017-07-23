<?php
$prefix = $_POST["prefix"];
if (isset($prefix)) {
    ob_start();
    session_start();
//Load prefixes from file to array
    require("functions.php");
    //updatedb();

    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} searched prefix {$prefix}','{$_SESSION['wr_id']}')";
    mysql_query($sql);

    $sql = "SELECT * FROM `policies` WHERE (`pol_prefix` = '{$prefix}');";
    $query = mysql_query($sql);
    $count = mysql_num_rows($query);

    if ($count <= 0) {
        ?>

        <table class="a" cellpadding="0" cellspacing="0"  width="99%">
            <tbody>
                <tr>
                    <td><div align='center'>Lớp mạng vừa tìm không tồn tại!</div></td>
                    <?php
                    $sql = "INSERT INTO logs VALUES ('',now(),'Prefix {$prefix}' was not exist.'{$_SESSION['wr_id']}')";
                    mysql_query($sql);
                    ?>
                </tr>
            </tbody>
        </table>
        <?php
    } else {
        $stt = 1;
        $row = mysql_fetch_array($query);
        ?>
        <form  action="" name="frmChange" id="frmChange">
            <table class="t" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <th align="center" width="50" rowspan="2">No.</th>
                        <th align="center" rowspan="2">LỚP MẠNG</th>
                        <?php
                        $sql1 = "SELECT * FROM `upstreams` ORDER BY `ups_id`";
                        $query1 = mysql_query($sql1);
                        while ($row1 = mysql_fetch_array($query1)) {
                            ?>
                            <th align="center" width="13%">HƯỚNG <?php echo $row1['ups_name'] ?></th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <?php
                        $sql1 = "SELECT * FROM `upstreams` ORDER BY `ups_id`";
                        $query1 = mysql_query($sql1);
                        while ($row1 = mysql_fetch_array($query1)) {
                            $sql2 = "SELECT * FROM `peers` WHERE `ups_id`='{$row1['ups_id']}' ORDER BY `pee_id`";
                            $query2 = mysql_query($sql2);
                            ?>
                            <th><table class="t" cellpadding="0" cellspacing="0"><tr align="center">
                            <?php
                            while ($row2 = mysql_fetch_array($query2)) {
                                ?>
                                <th>
                                    <?php echo $row2['pee_location'] ?>
                                </th>
                            <?php } ?>
                        </tr></table></th>
                <?php } ?>
                </tr>
                <tr>
                    <td><div align='center'><?php echo $stt++ ?></td>
                    <td><?php echo $row['pol_prefix']; ?> <input type='hidden' name='prefix<?php echo $stt - 1; ?>' value='<?php echo $row['pol_prefix'] ?>'></td>
                    <?php
                    $sql1 = "SELECT * FROM `upstreams` ORDER BY `ups_id`";
                    $query1 = mysql_query($sql1);
                    while ($row1 = mysql_fetch_array($query1)) {
                        $sql2 = "SELECT * FROM `peers` WHERE `ups_id`='{$row1['ups_id']}' ORDER BY `pee_id`";
                        $query2 = mysql_query($sql2);
                        ?>
                        <td><table class="t" cellpadding="0" cellspacing="0"><tr>
                                    <?php while ($row2 = mysql_fetch_array($query2)) { ?>
                                        <td><div align='center'><section><div class="checkboxFour"><input type="checkbox" <?php
                                                        if ($row["{$row2['pee_id']}"] == 1) {
                                                            echo "checked";
                                                        }
                                                        ?> id="checkbox<?php echo $stt - 1 ?><?php echo $row2['pee_id'] ?>" name="checkbox<?php echo $stt - 1 ?><?php echo $row2['pee_id'] ?>" /><label for="checkbox<?php echo $stt - 1 ?><?php echo $row2['pee_id'] ?>"></label></div></div></section></td>
                                                                                                      <?php } ?>
                                </tr></table></td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
            <table class="a" cellpadding="0" cellspacing="0" >
                <tbody>
                    <tr>
                        <td><input type="button" name="btnShow" id="btnShow" value="Show">
                            <input type="button" name="btnSave" id="btnSave" value="Execute"></td>
                    </tr>
                </tbody>
            </table>
        </form>
        <div id="ShowResultArea" align="center"></div>
        <div id="ExcuteResultArea" align="center"></div>
        <?php
    }
}
?>
