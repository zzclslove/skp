<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$smarty->assign('email', $_REQUEST['email']);
$smarty->display('send_email.htm');