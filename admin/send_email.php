<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$sql = "SELECT sendcount from " .$ecs->table('email_list'). " where email = '" . $_REQUEST['email'] . "'";
$count = $db->GetOne($sql);
if(intval($count) > 0){
    echo 'false';
}else{
    $sql = "UPDATE " . $ecs->table('email_list') .
        " SET sendcount = sendcount + 1 WHERE email = '" . $_REQUEST['email'] . "'";
    $db->query($sql);
    echo 'true';
}
