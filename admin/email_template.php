<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$smarty->display('send_email.htm');