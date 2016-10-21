<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/6
 * Time: 23:03
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$act = !empty($_GET['act']) ? $_GET['act'] : '';
if ($act == 'unsubscribe'){
    $email = !empty($_GET['email']) ? $_GET['email'] : '';
    $sql = "UPDATE " . $ecs->table('email_list') .
        " SET stat = 2 WHERE stat <> 2 AND email = '" . compile_str($email) . "'";
    $db->query($sql);
}

$smarty->display('email.dwt');