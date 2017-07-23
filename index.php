<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <body>
        <?php include('top.php'); ?>
        <!--<meta http-equiv="refresh" content="200"> -->
        <title>Routing Policy Page</title>
        <?php include('connect_db.php'); ?>
        <div id="content">
            <div class="wrap">
                <?php
                $sql = "SELECT * FROM `policies` ORDER BY INET_ATON(SUBSTRING_INDEX(`pol_prefix`,'/',1)),SUBSTRING_INDEX(`pol_prefix`,'/',-1)";
                $query = mysql_query($sql);
                $numcol = mysql_num_fields($query);
                if (mysql_num_rows($query) == "") {
                    ?>
                    <table class="t" cellpadding="0" cellspacing="0"><tbody><tr><td colspan=<?php echo $numcol; ?> align=center>Nothing to display</td></tr></tbody></table>
                    <?php
                } else {
                    ?>
                    <table class="t" cellpadding="0" cellspacing="0">
                        <tr>
                            <th align="center" width="50">No.</th>
                            <th align="center">LỚP MẠNG</th>
                            <?php
                            $sql1 = "SELECT * FROM `upstreams` ORDER BY `ups_id`";
                            $query1 = mysql_query($sql1);
                            while ($row1 = mysql_fetch_array($query1)) {
                                $sql2 = "SELECT * FROM `peers` WHERE `ups_id`='{$row1['ups_id']}' ORDER BY `pee_id`";
                                $query2 = mysql_query($sql2);
                                $numcol2 = mysql_num_fields($query2);
                                ?>
                                <th align="center" width="13%">
                                    <table class="t" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <th align="center" <?php if ($numcol2 != 0) echo "colspan='$numcol2'" ?>><?php echo $row1['ups_name'] ?></th>
                                        </tr>
                                        <tr>
                                            <?php while ($row2 = mysql_fetch_array($query2)) { ?>
                                                <th align="center"><?php echo $row2['pee_location'] ?></th>
                                            <?php } ?>
                                        </tr>
                                    </table>
                                </th>
                            <?php } ?>  
                        </tr>
                        <?php $stt = 0;
                        while ($row = mysql_fetch_array($query)) {
                        ?>
                        <tr>
                            <td><div align='center'><?php echo ++$stt ?></td>
                            <td><?php echo $row['pol_prefix'] ?></td>
                            <?php
                            $sql1 = "SELECT * FROM `upstreams` ORDER BY `ups_id`";
                            $query1 = mysql_query($sql1);
                            while ($row1 = mysql_fetch_array($query1)) {
                                ?>
                                <td><table class="t" cellpadding="0" cellspacing="0"><tr>
                                            <?php
                                            $sql2 = "SELECT * FROM `peers` WHERE `ups_id`='{$row1['ups_id']}' ORDER BY `pee_id`";
                                            $query2 = mysql_query($sql2);
                                            while ($row2 = mysql_fetch_array($query2)) {
                                                ?>
                                                <td><div align='center'><section><div class="checkboxFour"><input type="checkbox" disabled <?php
                                                                if ($row["{$row2["pee_id"]}"] != 0) {
                                                                    echo "checked";
                                                                }
                                                                ?> id="checkbox<?php echo $stt ?><?php echo $row2['pee_id'] ?>" /><label for="checkbox<?php echo $stt ?><?php echo $row2['pee_id'] ?>"></label></div></div></section></td>
                                                            <?php } ?>
                                        </tr></table></td>
                            <?php } $stt;?>
                        </tr>
                        <?php
                    }
              
                }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php include('bottom.php'); ?>

    </body>
</html>
