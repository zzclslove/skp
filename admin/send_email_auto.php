<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$smarty->assign('email',    $_REQUEST['email']);




$sql = "UPDATE " . $ecs->table('email_list') .
    " SET sendcount = sendcount + 1 WHERE email = '" . $_REQUEST['email'] . "'";
$db->query($sql);
$smarty->display('send_email.htm');