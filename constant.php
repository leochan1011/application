<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/22/2019
 * Time: 4:41 AM
 */

if (!$required) {
    die("Cannot be executed independently.");
}

$tns = "mysql:host=database.cjzuwj48cotq.ap-southeast-1.rds.amazonaws.com;port=3306;dbname=admin";

$db_username = "admin";
$db_password = "leo12345";

function build_sql_update($table, $data, $where)
{
    $cols = array();

    foreach($data as $key=>$val) {
        if (gettype($val) == "string"){
            $cols[] = "$key = '$val'";
        }else{
            $cols[] = "$key = $val";
        }
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    return($sql);
}

function check_admin_permission($rows){
    if(sizeof($rows) == 1 && $rows[0]["ROLE"] == "admin" && $rows[0]["STATUS"] == 0){
        return true;
    }
    return false;
}

function check_permission($rows){
    if(sizeof($rows) == 1 && $rows[0]["STATUS"] == 0){
        return true;
    }
    return false;
}

function get_permission_sql($token){
    return "SELECT userid, role,status FROM admin.useraccount WHERE UToken='$token'";
}