<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/22/2019
 * Time: 12:07 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$mid = $_GET['mid'];
$victim_latlng = $_GET['victim_latlng'];

$lat = explode(",", $victim_latlng)[0];
settype($lat, "double");
$lng = explode(",", $victim_latlng)[1];
settype($lng, "double");


try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        $sql2 = "SELECT um.userid FROM admin.usermission um
                 LEFT JOIN admin.useraccount u ON um.userid = u.userid
                 WHERE u.utoken = '$token' AND um.mid = '$mid'";
        $query2 = $conn->query($sql2);
        $rows2 = $query2->fetchAll();
        if(check_permission($rows) && sizeof($rows2) > 0){
            $sql3 = "UPDATE admin.mission SET mendtime = SYSTIMESTAMP, mstatus = 2,
                     mvictimlat = $lat, mvictimlng = $lng
                     WHERE mid = '$mid' AND mstatus != 4 AND mstatus in (1, 2)";
            $query3 = $conn->prepare($sql3);
            $query3->execute();
            if($query3->rowCount() > 0){
                $json = array("status"=>0);
            }else{
                $json = array("status" => 1);
            }
        }else{
            $json = array("status"=>2);
        }
    }
}catch(PDOException $e){
    $json = array("status"=>3);
}
print(json_encode($json, JSON_PRETTY_PRINT));

/*
Status code:
    0: Set Success
    1: Set Failed
    2: Permission Denied
    3: PDO error
*/;