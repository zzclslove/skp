<?php
/**
 * Created by PhpStorm.
 * User: zzc
 * Date: 2016/10/2
 * Time: 14:54
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
if (empty($_SESSION['user_id'])){
    die($_LANG['require_login']);
}

if (!isset($_REQUEST['order_sn']))
{
    ecs_header("Location: ./\n");
    exit;
}

$act = empty($_REQUEST['act'])?'':$_REQUEST['act'];
$order_sn = $_REQUEST['order_sn'];
if($act == 'set_invoice'){

    $smarty->assign('order_sn', $order_sn);

    $sql = "SELECT goods_amount FROM " . $GLOBALS['ecs']->table('order_info') . " where order_sn = '" . compile_str($order_sn) . "'";
    $amount = $db->getOne($sql);

    $sql = "SELECT count(o.goods_id) " .
        "FROM " . $ecs->table('order_goods') . " AS o ".
        "LEFT JOIN " . $ecs->table('order_info') . " AS g ON o.order_id = g.order_id " .
        "WHERE g.order_sn = '". compile_str($order_sn) . "'";
    $res = $db->getOne($sql);

    $count = (intval($res) / 2) < 1?1:intval(intval($res) / 2);
    $amount = intval($amount / 10);
    $danjia = intval($amount/$count);
    $total = $danjia * $count;

    $smarty->assign('count', $count);
    $smarty->assign('danjia', $danjia);
    $smarty->assign('total', $total);

    $sql = "select * from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
    $invoice = $db->getRow($sql);

    $smarty->assign('invoice', $invoice);
    $smarty->assign('datenow', date('M.d, Y', time()));

    $smarty->display('set_invoice.dwt');
}else if($act == 'set_invoice_done'){
    $invoice = array(
    'order_sn'=> compile_str($order_sn),
    'bill_company_name' => compile_str($_REQUEST['bill_company_name']),
    'bill_address' => compile_str($_REQUEST['bill_address']),
    'bill_contact_person' => compile_str($_REQUEST['bill_contact_person']),
    'bill_phone_fax' => compile_str($_REQUEST['bill_phone_fax']),
    'consignee' => compile_str($_REQUEST['consignee']),
    'company_name' => compile_str($_REQUEST['company_name']),
    'address' => compile_str($_REQUEST['address']),
    'contact_person' => compile_str($_REQUEST['contact_person']),
    'phone_fax_email' => compile_str($_REQUEST['phone_fax_email']),
    'city' => compile_str($_REQUEST['city']),
    'postal_code' => compile_str($_REQUEST['postal_code']),
    'state_country' => compile_str($_REQUEST['state_country']),
    'total_weight' => compile_str($_REQUEST['total_weight']),
    'signature' => compile_str($_REQUEST['signature']),
    'invoice_date' => compile_str($_REQUEST['invoice_date'])
    );
    $sql = "delete from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
    $db->query($sql);
    if($db->autoExecute($ecs->table('invoice'), $invoice, 'INSERT')){
        $smarty->assign('result', 'true');
    }else{
        $smarty->assign('result', 'false');
    }
    $smarty->display('set_invoice_done.dwt');
}


