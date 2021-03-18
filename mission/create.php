<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/18/2019
 * Time: 10:12 PM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$m_name = $_GET['m_name'];
$m_desc = $_GET['m_desc'];
$m_loc_name = $_GET['loc_name'];
$m_loc_latlng = $_GET['latlng'];
$searching_radius = $_GET['searching_radius'];
$altitude = $_GET['altitude'];
settype($searching_radius, "double");
$lat = explode(",", $m_loc_latlng)[0];
settype($lat, "double");
$lng = explode(",", $m_loc_latlng)[1];
settype($lng, "double");
//print("$lat,$lng\n");

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $userid = $rows[0]["USERID"];
            $sql2 = "INSERT INTO admin.mission
                     (mid,mname,mdesc,mcreator,mcreatetime,mlocationname,centerlat,centerlng,mrange, maltitude,mstatus)
                     VALUES (admin.seq_mid.nextval,'$m_name','$m_desc','$userid',SYSTIMESTAMP,'$m_loc_name',$lat,$lng,$searching_radius,$altitude,0)";
            $query2 = $conn->prepare($sql2);
            $query2->execute();
            if($query2->rowCount() > 0){
                $sql3 = "select mid from admin.mission order by mcreatetime desc";
                $query3 = $conn->query($sql3);
                $result = $query3->fetchAll();
                $json = array("status"=>0, "mid"=> $result[0]['MID']);
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
    0: Create Mission Success
    1: Mission Parameters Error
    2: Permission Denied
    3: PDO error
*/