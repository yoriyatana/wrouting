#!/usr/bin/python

import concurrent.futures
from concurrent.futures import ProcessPoolExecutor, wait, as_completed
import MySQLdb
import paramiko
import re
import time

def db_execute( db_host, db_user, db_pass, bd_name, db_querry):
        db = MySQLdb.connect(db_host,db_user,db_pass,bd_name)
        cursor = db.cursor()

        try:
                cursor.execute(db_querry)
        except:
                print "Error: unable to execute command"

        db.close()
        return;

def db_read_querry(db_host, db_user, db_pass, bd_name, db_querry):
        db = MySQLdb.connect(db_host,db_user,db_pass,bd_name)
        cursor = db.cursor()

        try:
                cursor.execute(db_querry)
                output = cursor.fetchall()
        except:
                print "Error: unable to execute command"

        db.close()
        return output;


def db_update_querry( db_host, db_user, db_pass, bd_name, db_querry):
        db = MySQLdb.connect(db_host,db_user,db_pass,bd_name)
        cursor = db.cursor()

        try:
                cursor.execute(db_querry)
                db.commit()
        except:
                print "Error: unable to execute command"
                db.rollback()

        db.close()
        return;

def get_show_route_advertised_cmd(dev_id, dev_protocol, pee_remote_ip):
        if dev_protocol == "ssh":
                if dev_id == 5:
                        return "show route logical-system I1 advertising-protocol bgp %s | no-more" % pee_remote_ip
                elif dev_id == 6:
                        return "show route logical-system I2 advertising-protocol bgp %s | no-more" % pee_remote_ip
                elif dev_id == 7:
                        return "show route advertising-protocol bgp %s | no-more" % pee_remote_ip
                else:
                        print ("ERR: dev_id not valid!!!")
        else:
                print ("ERR: dev_protocol not support!!!")

        return;

def ssh_thread(dev_id, db_host, db_user, db_pass, db_name):
        sql = """SELECT * FROM `devices` WHERE `dev_id` = '%s'""" % dev_id
        results = db_read_querry(db_host, db_user, db_pass, db_name, sql)
        for row in results:
                dev_ip = row[2]
                dev_protocol = row[3]
                dev_port = row[4]
                dev_user = row[5]
                dev_pass = row[6]

        return_dict = {}
        try:
                ssh = paramiko.SSHClient()
                ssh.load_system_host_keys()
                ssh.set_missing_host_key_policy(paramiko.WarningPolicy())
                ssh.connect(dev_ip, port=dev_port, username=dev_user, password=dev_pass)

                sql = """SELECT * FROM `peers` WHERE `dev_id` = '%s'""" % dev_id
                results = db_read_querry(db_host, db_user, db_pass, db_name, sql)
                for row in results:
                        pee_remote_ip = row[4]
                        pee_id = row[0]
                        cmd = get_show_route_advertised_cmd(dev_id, dev_protocol, pee_remote_ip)
                        stdin, stdout, stderr = ssh.exec_command(cmd)
                        stdin.write('lol\n')
                        stdin.flush()
                        return_dict[pee_id] = stdout.read()

                ssh.close()
        except:
                print "Error: SSH session failed"

        return return_dict;

def parse_route_advertised_juniper(raw_input, pattern):
        searchObj = re.search(pattern, raw_input, re.M|re.I)

        if searchObj:
                return searchObj.group();
        else:
                return False;

def main():
        db_host = "localhost"
        db_user = "root"
        db_pass = "2010!mayman"
        db_name = "wrouting"

        start = time.time()

        sql = "DROP TABLE IF EXISTS `policies`"

        db_execute(db_host, db_user, db_pass, db_name, sql)

        sql_cmd = "CREATE TABLE IF NOT EXISTS `policies`(`pol_id` int(10) unsigned NOT NULL AUTO_INCREMENT,`pol_prefix` varchar(18) NOT NULL"

        sql = "SELECT * FROM `peers` ORDER BY `pee_id`"
        results = db_read_querry(db_host, db_user, db_pass, db_name, sql)
        for row in results:
                pee_id = str(row[0])
                sql_cmd += ", `%s` int(11) NULL" % pee_id

        sql_cmd = sql_cmd + ",PRIMARY KEY (`pol_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1"
        sql = sql_cmd
        db_execute(db_host, db_user, db_pass, db_name, sql)

        sql = "SELECT dev_id FROM `peers` GROUP BY `dev_id`"
        results = db_read_querry(db_host, db_user, db_pass, db_name, sql)
        device_count = len(results)

        future_to_groups = []
        with concurrent.futures.ProcessPoolExecutor(max_workers=device_count) as executor:
                for row in results:
                        dev_id = row[0]
                        future_to_groups.append(executor.submit(ssh_thread, dev_id, db_host, db_user, db_pass, db_name))
        for future in concurrent.futures.as_completed(future_to_groups):
                try:
                        ssh_output_dict = future.result()
                        pee_id_list = ssh_output_dict.keys()
                except Exception as exc:
                        print('ERR: generated an exception: %s' % (exc))
                else:
                        pattern = "(?:[0-9]{1,3}\.){3}[0-9]{1,3}(?:\/[0-9]{2})"
                        for pee_id in pee_id_list:
                                output = ssh_output_dict.get(pee_id).splitlines()
                                for row in output:
                                        prefix = parse_route_advertised_juniper(row, pattern)
                                        if prefix:
                                                sql = """SELECT * FROM `policies` WHERE `pol_prefix`='%s'""" % prefix
                                                results = db_read_querry(db_host, db_user, db_pass, db_name, sql)
                                                if len(results) > 0:
                                                        sql = "UPDATE `policies` SET `%s`='1' WHERE `pol_prefix`='%s'" % (pee_id, prefix)
                                                        db_execute(db_host, db_user, db_pass, db_name, sql)
                                                else:
                                                        sql = "INSERT INTO `policies`(`pol_prefix`, `%s`) VALUES ('%s', '1')" % (pee_id, prefix)
                                                        db_execute(db_host, db_user, db_pass, db_name, sql)
        print "Execution time: %s seconds" % (time.time() - start)

        return;

main()
